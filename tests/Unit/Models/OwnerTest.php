<?php

use App\Models\Owner;
use Illuminate\Database\Eloquent\Relations\HasMany;

test('Owner model has correct fillable attributes', function () {
    $owner    = new Owner();
    $fillable = $owner->getFillable();

    expect($fillable)->toContain('first_name')
                     ->toContain('last_name');
});

test('Owner can be instantiated', function () {
    $owner = new Owner();

    expect($owner)->toBeInstanceOf(Owner::class);
});

test('Owner uses SoftDeletes trait', function () {
    $owner  = new Owner();
    $traits = class_uses_recursive(get_class($owner));

    expect($traits)->toHaveKey('Illuminate\Database\Eloquent\SoftDeletes');
});

test('Owner fillable attributes can be mass assigned correctly', function () {
    $data = [
        'first_name' => 'John',
        'last_name'  => 'Doe',
    ];

    $owner = new Owner($data);

    expect($owner->first_name)->toBe('John')
                              ->and($owner->last_name)->toBe('Doe');
});

test('Owner assets relationship method exists and has correct signature', function () {
    $owner = new Owner();

    expect(method_exists($owner, 'assets'))->toBeTrue();

    $reflection = new ReflectionMethod($owner, 'assets');
    expect($reflection->getReturnType()?->getName())->toBe(HasMany::class);
});

test('Owner assignments relationship method exists and has correct signature', function () {
    $owner = new Owner();

    expect(method_exists($owner, 'assignments'))->toBeTrue();

    $reflection = new ReflectionMethod($owner, 'assignments');
    expect($reflection->getReturnType()?->getName())->toBe(HasMany::class);
});

test('Owner SoftDeletes trait provides correct methods', function () {
    $owner = new Owner();

    expect(method_exists($owner, 'trashed'))->toBeTrue()
                                            ->and(method_exists($owner, 'restore'))->toBeTrue()
                                            ->and(method_exists($owner, 'forceDelete'))->toBeTrue()
                                            ->and(method_exists($owner, 'delete'))->toBeTrue();
});

test('Owner fillable fields validation', function () {
    $fillableFields = ['first_name', 'last_name'];
    $owner          = new Owner();

    foreach ($fillableFields as $field) {
        expect($owner->isFillable($field))->toBeTrue("Field {$field} should be fillable");
    }
});

test('Owner non-fillable fields are protected', function () {
    $owner = new Owner();

    expect($owner->isFillable('id'))->toBeFalse()
                                    ->and($owner->isFillable('created_at'))->toBeFalse()
                                    ->and($owner->isFillable('updated_at'))->toBeFalse()
                                    ->and($owner->isFillable('deleted_at'))->toBeFalse();
});

test('Owner handles empty names gracefully', function () {
    $data = [
        'first_name' => '',
        'last_name'  => '',
    ];

    $owner = new Owner($data);
    
    expect($owner->first_name)->toBe('')
                              ->and($owner->last_name)->toBe('');
});

test('Owner handles null names gracefully', function () {
    $data = [
        'first_name' => null,
        'last_name'  => null,
    ];

    $owner = new Owner($data);

    expect($owner->first_name)->toBeNull()
                              ->and($owner->last_name)->toBeNull();
});

test('Owner handles single name correctly', function () {
    $data = [
        'first_name' => 'Madonna',
        'last_name'  => '',
    ];

    $owner = new Owner($data);

    expect($owner->first_name)->toBe('Madonna')
                              ->and($owner->last_name)->toBe('');
});
