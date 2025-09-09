<?php

use App\Actions\Assets\AssignOwnerResultDTO;
use App\Models\Asset;
use App\Models\AssetOwnerAssignment;

test('AssignOwnerResultDTO can be instantiated with required parameters', function () {
    $asset = new Asset();

    $dto = new AssignOwnerResultDTO(
        changed: true,
        asset: $asset
    );

    expect($dto)->toBeInstanceOf(AssignOwnerResultDTO::class)
                ->and($dto->changed)->toBeTrue()
                ->and($dto->asset)->toBe($asset)
                ->and($dto->newAssignment)->toBeNull()
                ->and($dto->reason)->toBeNull();
});

test('AssignOwnerResultDTO can be instantiated with all parameters', function () {
    $asset      = new Asset();
    $assignment = new AssetOwnerAssignment();

    $dto = new AssignOwnerResultDTO(
        changed: false,
        asset: $asset,
        newAssignment: $assignment,
        reason: 'No change needed'
    );

    expect($dto)->toBeInstanceOf(AssignOwnerResultDTO::class)
                ->and($dto->changed)->toBeFalse()
                ->and($dto->asset)->toBe($asset)
                ->and($dto->newAssignment)->toBe($assignment)
                ->and($dto->reason)->toBe('No change needed');
});

test('AssignOwnerResultDTO properties are publicly accessible', function () {
    $asset      = new Asset();
    $assignment = new AssetOwnerAssignment();

    $dto = new AssignOwnerResultDTO(
        changed: true,
        asset: $asset,
        newAssignment: $assignment,
        reason: 'Owner changed'
    );

    expect($dto->changed)->toBeTrue()
                         ->and($dto->asset)->toBeInstanceOf(Asset::class)
                         ->and($dto->newAssignment)->toBeInstanceOf(AssetOwnerAssignment::class)
                         ->and($dto->reason)->toBe('Owner changed');
});

test('AssignOwnerResultDTO changed property accepts boolean values', function () {
    $asset = new Asset();

    $dtoTrue  = new AssignOwnerResultDTO(changed: true, asset: $asset);
    $dtoFalse = new AssignOwnerResultDTO(changed: false, asset: $asset);

    expect($dtoTrue->changed)->toBeTrue()
                             ->and($dtoFalse->changed)->toBeFalse();
});

test('AssignOwnerResultDTO newAssignment can be null', function () {
    $asset = new Asset();

    $dto = new AssignOwnerResultDTO(
        changed: true,
        asset: $asset,
        newAssignment: null
    );

    expect($dto->newAssignment)->toBeNull();
});

test('AssignOwnerResultDTO reason can be null', function () {
    $asset = new Asset();

    $dto = new AssignOwnerResultDTO(
        changed: true,
        asset: $asset,
        reason: null
    );

    expect($dto->reason)->toBeNull();
});

test('AssignOwnerResultDTO uses readonly properties', function () {
    $reflection  = new ReflectionClass(AssignOwnerResultDTO::class);
    $constructor = $reflection->getConstructor();
    $parameters  = $constructor->getParameters();

    expect($parameters)->toHaveCount(4);

    foreach ($parameters as $parameter) {
        expect($parameter->isPromoted())->toBeTrue("Parameter {$parameter->getName()} should be promoted");
    }
});
