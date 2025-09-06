<?php

use App\Actions\Assets\ActionAssignOwnerToAsset;
use App\Models\{Asset, AssetOwnerAssignment, Owner};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

it('does nothing when assigning the same owner (no change)', function () {
    $owner = Owner::factory()->create();
    $asset = Asset::factory()->create([
        'current_owner_id'   => $owner->id,
        'current_owned_from' => Carbon::now()->utc(),
    ]);

    AssetOwnerAssignment::create([
        'asset_id'   => $asset->id,
        'owner_id'   => $owner->id,
        'owned_from' => Carbon::now()->utc()->subMinute(),
        'owned_to'   => null,
    ]);

    $action = app(ActionAssignOwnerToAsset::class);
    $result = $action($asset, $owner, Carbon::now()->utc());

    expect($result->changed)->toBeFalse()->and(AssetOwnerAssignment::open()->forAsset($asset->id)->count())->toBe(1);

    $asset->refresh()->load('owner');
    expect(optional($asset->owner)->id)->toBe($owner->id);
});

it('closes previous and opens a new assignment on owner change', function () {
    $ownerA = Owner::factory()->create();
    $ownerB = Owner::factory()->create();
    $asset  = Asset::factory()->create([
        'current_owner_id'   => $ownerA->id,
        'current_owned_from' => Carbon::parse('2025-09-01 12:00:00', 'UTC'),
    ]);

    AssetOwnerAssignment::create([
        'asset_id'   => $asset->id,
        'owner_id'   => $ownerA->id,
        'owned_from' => Carbon::parse('2025-09-01 12:00:00', 'UTC'),
        'owned_to'   => null,
    ]);

    $t = Carbon::parse('2025-09-02 08:00:00', 'UTC');

    $action = app(ActionAssignOwnerToAsset::class);
    $result = $action($asset, $ownerB, $t);

    expect($result->changed)->toBeTrue();

    $closedA = AssetOwnerAssignment::forAsset($asset->id)->forOwner($ownerA->id)->first();
    expect($closedA->owned_to)->not()->toBeNull();

    $openB = AssetOwnerAssignment::open()->forAsset($asset->id)->first();
    expect($openB)->not()->toBeNull()
                  ->and($openB->owner_id)->toBe($ownerB->id)
                  ->and($openB->owned_from->equalTo($t))->toBeTrue();

    $asset->refresh()->load('owner');
    expect(optional($asset->owner)->id)->toBe($ownerB->id)->and($asset->current_owned_from->equalTo($t))->toBeTrue();
});

it('removes current owner (closes open assignment and nulls current fields)', function () {
    $owner = Owner::factory()->create();
    $asset = Asset::factory()->create([
        'current_owner_id'   => $owner->id,
        'current_owned_from' => Carbon::parse('2025-09-01 12:00:00', 'UTC'),
    ]);

    AssetOwnerAssignment::create([
        'asset_id'   => $asset->id,
        'owner_id'   => $owner->id,
        'owned_from' => Carbon::parse('2025-09-01 12:00:00', 'UTC'),
        'owned_to'   => null,
    ]);

    $t = Carbon::parse('2025-09-03 09:30:00', 'UTC');

    $action = app(ActionAssignOwnerToAsset::class);
    $result = $action($asset, null, $t);

    expect($result->changed)->toBeTrue()->and(AssetOwnerAssignment::open()->forAsset($asset->id)->count())->toBe(0);

    $asset->refresh();
    expect($asset->current_owner_id)->toBeNull()->and($asset->current_owned_from)->toBeNull();
});
