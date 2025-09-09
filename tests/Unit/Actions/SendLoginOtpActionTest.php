<?php

use App\Actions\Auth\SendLoginOtpAction;

test('SendLoginOtpAction can be instantiated', function () {
    $action = new SendLoginOtpAction();
    
    expect($action)->toBeInstanceOf(SendLoginOtpAction::class);
});

test('SendLoginOtpAction has execute method', function () {
    $action = new SendLoginOtpAction();
    
    expect(method_exists($action, 'execute'))->toBeTrue();
});

test('SendLoginOtpAction execute method requires email and password parameters', function () {
    $action = new SendLoginOtpAction();
    $reflection = new ReflectionMethod($action, 'execute');
    $parameters = $reflection->getParameters();
    
    expect(count($parameters))->toBe(2)
        ->and($parameters[0]->getName())->toBe('email')
        ->and($parameters[1]->getName())->toBe('password');
});

test('SendLoginOtpAction has sendOtpEmail method', function () {
    $action = new SendLoginOtpAction();
    $reflection = new ReflectionClass($action);
    
    expect($reflection->hasMethod('sendOtpEmail'))->toBeTrue();
});

test('sendOtpEmail method is private', function () {
    $action = new SendLoginOtpAction();
    $reflection = new ReflectionMethod($action, 'sendOtpEmail');
    
    expect($reflection->isPrivate())->toBeTrue();
});

test('sendOtpEmail method has correct parameters', function () {
    $action = new SendLoginOtpAction();
    $reflection = new ReflectionMethod($action, 'sendOtpEmail');
    $parameters = $reflection->getParameters();
    
    expect(count($parameters))->toBe(2)
        ->and($parameters[0]->getName())->toBe('email')
        ->and($parameters[1]->getName())->toBe('otpCode');
});