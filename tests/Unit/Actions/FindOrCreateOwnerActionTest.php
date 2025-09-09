<?php

use App\Actions\Owners\FindOrCreateOwnerAction;

test('FindOrCreateOwnerAction can be instantiated', function () {
    $action = new FindOrCreateOwnerAction();
    
    expect($action)->toBeInstanceOf(FindOrCreateOwnerAction::class);
});

test('throws exception for empty name', function () {
    $action = new FindOrCreateOwnerAction();

    expect(fn() => $action(''))
        ->toThrow(InvalidArgumentException::class, 'Owner name cannot be empty');
});

test('throws exception for whitespace only name', function () {
    $action = new FindOrCreateOwnerAction();

    expect(fn() => $action('   '))
        ->toThrow(InvalidArgumentException::class, 'Owner name cannot be empty');
});

test('action is invokable with string parameter', function () {
    $action = new FindOrCreateOwnerAction();
    $reflection = new ReflectionMethod($action, '__invoke');
    $parameters = $reflection->getParameters();
    
    expect($parameters)->toHaveCount(1)
        ->and($parameters[0]->getName())->toBe('fullName')
        ->and($parameters[0]->getType()?->getName())->toBe('string');
});

test('action returns array with expected structure', function () {
    $reflection = new ReflectionMethod(FindOrCreateOwnerAction::class, '__invoke');
    $returnType = $reflection->getReturnType();
    
    expect($returnType?->getName())->toBe('array');
});

test('action validates input properly', function () {
    $action = new FindOrCreateOwnerAction();
    
    expect(fn() => $action('John Doe'))
        ->not()->toThrow(InvalidArgumentException::class);
        
    expect(fn() => $action('Madonna'))
        ->not()->toThrow(InvalidArgumentException::class);
        
    expect(fn() => $action('Mary Jane Watson'))
        ->not()->toThrow(InvalidArgumentException::class);
        
    expect(fn() => $action('  John   Doe  '))
        ->not()->toThrow(InvalidArgumentException::class);
});

test('action class has proper namespace and uses', function () {
    $reflection = new ReflectionClass(FindOrCreateOwnerAction::class);
    
    expect($reflection->getNamespaceName())->toBe('App\Actions\Owners')
        ->and($reflection->isInstantiable())->toBeTrue()
        ->and($reflection->hasMethod('__invoke'))->toBeTrue();
});

test('action method signature matches expected interface', function () {
    $reflection = new ReflectionMethod(FindOrCreateOwnerAction::class, '__invoke');
    
    expect($reflection->isPublic())->toBeTrue()
        ->and($reflection->getNumberOfParameters())->toBe(1)
        ->and($reflection->getNumberOfRequiredParameters())->toBe(1);
});
