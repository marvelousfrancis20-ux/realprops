<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Google reCAPTCHA v3 verification (optional, disabled when RECAPTCHA_ENABLED=false).
 */
class RecaptchaService
{
    public function enabled(): bool
    {
        return (bool) config('services.recaptcha.enabled');
    }

    public function passes(Request $request): bool
    {
        if (! $this->enabled()) {
            return true;
        }

        $token = $request->input('g-recaptcha-response');

        if (blank($token)) {
            return false;
        }

        try {
            $response = Http::asForm()->timeout(5)->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => config('services.recaptcha.secret'),
                'response' => $token,
                'remoteip' => $request->ip(),
            ]);

            $body = $response->json() ?? [];

            return (bool) ($body['success'] ?? false)
                && (float) ($body['score'] ?? 0) >= 0.5;
        } catch (\Throwable $e) {
            report($e);

            return false;
        }
    }
}
