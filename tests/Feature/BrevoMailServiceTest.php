<?php

namespace Tests\Feature;

use App\Mail\OtpMail;
use App\Services\BrevoMailService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class BrevoMailServiceTest extends TestCase
{
    public function test_it_sends_otp_via_brevo_api_when_api_key_is_configured(): void
    {
        config()->set('services.brevo.key', 'test-key');
        config()->set('mail.from.address', 'sender@example.com');
        config()->set('mail.from.name', 'Sales Ledger');

        Http::fake([
            'api.brevo.com/*' => Http::response(['messageId' => '1'], 201),
        ]);
        Mail::fake();

        app(BrevoMailService::class)->sendVerificationCode('user@example.com', 123456);

        Http::assertSent(fn ($request) => $request->url() === 'https://api.brevo.com/v3/smtp/email'
            && $request['to'][0]['email'] === 'user@example.com'
            && $request['sender']['email'] === 'sender@example.com'
            && str_contains($request['htmlContent'], '123456'));

        Mail::assertNothingSent();
    }

    public function test_it_falls_back_to_laravel_mail_without_api_key(): void
    {
        config()->set('services.brevo.key', null);
        Mail::fake();

        app(BrevoMailService::class)->sendVerificationCode('user@example.com', 123456);

        Mail::assertSent(OtpMail::class, fn ($mail) => $mail->hasTo('user@example.com'));
    }
}
