<?php

use App\Actions\Owners\SearchOwnersAction;

test('SearchOwnersAction can be instantiated', function () {
    $action = new SearchOwnersAction();

    expect($action)->toBeInstanceOf(SearchOwnersAction::class);
});

test('SearchOwnersAction is invokable with string parameter', function () {
    $action     = new SearchOwnersAction();
    $reflection = new ReflectionMethod($action, '__invoke');
    $parameters = $reflection->getParameters();

    expect($parameters)->toHaveCount(1)
                       ->and($parameters[0]->getName())->toBe('query')
                       ->and($parameters[0]->getType()?->getName())->toBe('string');
});

test('SearchOwnersAction has correct class structure', function () {
    $reflection = new ReflectionClass(SearchOwnersAction::class);

    expect($reflection->getNamespaceName())->toBe('App\Actions\Owners')
                                           ->and($reflection->isInstantiable())->toBeTrue()
                                           ->and($reflection->hasMethod('__invoke'))->toBeTrue();
});

test('SearchOwnersAction method signature is correct', function () {
    $reflection = new ReflectionMethod(SearchOwnersAction::class, '__invoke');

    expect($reflection->isPublic())->toBeTrue()
                                   ->and($reflection->getNumberOfParameters())->toBe(1)
                                   ->and($reflection->getNumberOfRequiredParameters())->toBe(1);
});

test('SearchOwnersAction validates input without throwing exceptions', function () {
    $action = new SearchOwnersAction();

    expect(fn() => $action('John'))
        ->not()->toThrow(Exception::class);

    expect(fn() => $action(''))
        ->not()->toThrow(Exception::class);

    expect(fn() => $action('john doe'))
        ->not()->toThrow(Exception::class);

    expect(fn() => $action('123'))
        ->not()->toThrow(Exception::class);
});
