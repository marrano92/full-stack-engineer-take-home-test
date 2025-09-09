<?php

use App\Models\AssetOwnerAssignment;

test('AssetOwnerAssignment model has correct fillable attributes', function () {
    $assignment = new AssetOwnerAssignment();
    $fillable   = $assignment->getFillable();

    expect($fillable)->toContain('asset_id')
                     ->toContain('owner_id')
                     ->toContain('owned_from')
                     ->toContain('owned_to');
});

test('AssetOwnerAssignment model has correct casts', function () {
    $assignment = new AssetOwnerAssignment();
    $casts      = $assignment->getCasts();

    expect($casts)->toHaveKey('owned_from')
                  ->and($casts['owned_from'])->toBe('datetime')
                  ->and($casts)->toHaveKey('owned_to')
                  ->and($casts['owned_to'])->toBe('datetime');
});

test('AssetOwnerAssignment can be instantiated', function () {
    $assignment = new AssetOwnerAssignment();

    expect($assignment)->toBeInstanceOf(AssetOwnerAssignment::class);
});

test('AssetOwnerAssignment has asset relationship method', function () {
    $assignment = new AssetOwnerAssignment();

    expect(method_exists($assignment, 'asset'))->toBeTrue();
});

test('AssetOwnerAssignment has owner relationship method', function () {
    $assignment = new AssetOwnerAssignment();

    expect(method_exists($assignment, 'owner'))->toBeTrue();
});

test('AssetOwnerAssignment has scopeOpen method', function () {
    $assignment = new AssetOwnerAssignment();

    expect(method_exists($assignment, 'scopeOpen'))->toBeTrue();
});

test('AssetOwnerAssignment has scopeForAsset method', function () {
    $assignment = new AssetOwnerAssignment();

    expect(method_exists($assignment, 'scopeForAsset'))->toBeTrue();
});

test('AssetOwnerAssignment has scopeForOwner method', function () {
    $assignment = new AssetOwnerAssignment();

    expect(method_exists($assignment, 'scopeForOwner'))->toBeTrue();
});

test('AssetOwnerAssignment constructor accepts fillable attributes', function () {
    $data = [
        'asset_id' => 1,
        'owner_id' => 1,
        'owned_to' => null
    ];

    // Test that constructor completes successfully
    $assignment = new AssetOwnerAssignment($data);
    expect($assignment)->toBeInstanceOf(AssetOwnerAssignment::class);
});
