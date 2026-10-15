<?php

use App\Services\RecaptchaService;
use Illuminate\Http\Request;

it('passes when recaptcha is disabled', function () {
    config(['services.recaptcha.enabled' => false]);

    $request = Request::create('/contact', 'POST', ['g-recaptcha-response' => '']);

    expect(app(RecaptchaService::class)->passes($request))->toBeTrue();
});

it('fails when recaptcha is enabled but no token is present', function () {
    config([
        'services.recaptcha.enabled' => true,
        'services.recaptcha.secret' => 'test-secret',
    ]);

    $request = Request::create('/contact', 'POST', []);

    expect(app(RecaptchaService::class)->passes($request))->toBeFalse();
});

it('exposes its enabled flag', function () {
    config(['services.recaptcha.enabled' => true]);
    expect(app(RecaptchaService::class)->enabled())->toBeTrue();

    config(['services.recaptcha.enabled' => false]);
    expect(app(RecaptchaService::class)->enabled())->toBeFalse();
});
