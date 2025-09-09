<?php

use App\Models\OtpCode;

test('generateCode returns a 6-digit string', function () {
    $code = OtpCode::generateCode();

    expect($code)->toBeString()
                 ->toHaveLength(6)
                 ->toMatch('/^\d{6}$/');
});

test('generateCode returns different codes on multiple calls', function () {
    $code1 = OtpCode::generateCode();
    $code2 = OtpCode::generateCode();

    expect($code1)->not()->toBe($code2);
});

test('generateCode handles edge cases correctly', function () {
    // Test multiple generations to ensure they're all valid
    for ($i = 0; $i < 10; $i++) {
        $code = OtpCode::generateCode();
        expect($code)->toMatch('/^\d{6}$/');
    }
});

test('isExpired method exists and is callable', function () {
    $otpCode = new OtpCode();

    expect(method_exists($otpCode, 'isExpired'))->toBeTrue();
});

test('isUsed method exists and is callable', function () {
    $otpCode = new OtpCode();

    expect(method_exists($otpCode, 'isUsed'))->toBeTrue();
});

test('markAsUsed method exists and is callable', function () {
    $otpCode = new OtpCode();

    expect(method_exists($otpCode, 'markAsUsed'))->toBeTrue();
});

test('findValidOtp static method exists', function () {
    expect(method_exists(OtpCode::class, 'findValidOtp'))->toBeTrue();
});

test('createForEmail static method exists', function () {
    expect(method_exists(OtpCode::class, 'createForEmail'))->toBeTrue();
});
