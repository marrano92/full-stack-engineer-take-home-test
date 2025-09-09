<?php

use App\Models\Asset;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

test('Asset model has correct fillable attributes', function () {
    $asset    = new Asset();
    $fillable = $asset->getFillable();

    expect($fillable)->toContain('reference')
                     ->toContain('serial_number')
                     ->toContain('description')
                     ->toContain('current_owner_id')
                     ->toContain('current_owned_from');
});

test('Asset model has current_owned_from cast to datetime', function () {
    $asset = new Asset();
    $casts = $asset->getCasts();

    expect($casts)->toHaveKey('current_owned_from')
                  ->and($casts['current_owned_from'])->toBe('datetime');
});

test('Asset can be instantiated', function () {
    $asset = new Asset();

    expect($asset)->toBeInstanceOf(Asset::class);
});

test('Asset fillable attributes can be mass assigned correctly', function () {
    $data = [
        'reference'        => 'REF-001',
        'serial_number'    => 'SN123456',
        'description'      => 'Test Asset Description',
        'current_owner_id' => 1,
    ];

    $asset = new Asset($data);

    expect($asset->getAttributes())->toMatchArray($data);
});

test('Asset owner relationship method exists and has correct signature', function () {
    $asset = new Asset();

    expect(method_exists($asset, 'owner'))->toBeTrue();

    $reflection = new ReflectionMethod($asset, 'owner');
    expect($reflection->getReturnType()?->getName())->toBe(BelongsTo::class);
});

test('Asset assignments relationship method exists and has correct signature', function () {
    $asset = new Asset();

    expect(method_exists($asset, 'assignments'))->toBeTrue();

    $reflection = new ReflectionMethod($asset, 'assignments');
    expect($reflection->getReturnType()?->getName())->toBe(HasMany::class);
});

test('Asset current_owned_from cast configuration is correct', function () {
    $asset = new Asset();
    $casts = $asset->getCasts();

    expect($casts['current_owned_from'])->toBe('datetime');
});

test('Asset handles null current_owned_from gracefully', function () {
    $asset = new Asset(['current_owned_from' => null]);

    expect($asset->current_owned_from)->toBeNull();
});

test('Asset validates required fields are fillable', function () {
    $fillableFields = ['reference', 'serial_number', 'description', 'current_owner_id', 'current_owned_from'];
    $asset          = new Asset();

    foreach ($fillableFields as $field) {
        expect($asset->isFillable($field))->toBeTrue("Field {$field} should be fillable");
    }
});

test('Asset non-fillable fields are protected', function () {
    $asset = new Asset();

    expect($asset->isFillable('id'))->toBeFalse()
                                    ->and($asset->isFillable('created_at'))->toBeFalse()
                                    ->and($asset->isFillable('updated_at'))->toBeFalse();
});
