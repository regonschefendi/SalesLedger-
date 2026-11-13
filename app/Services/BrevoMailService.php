<?php

namespace App\Services;

use App\Mail\OtpMail;
use App\Mail\ResetPasswordMail;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

class BrevoMailService
{
    public function sendVerificationCode(string $email, int $code): void
    {
        $this->send(
            $email,
            'Kode Verifikasi Sales Ledger',
            'emails.otp',
            ['otpCode' => $code],
            new OtpMail($code)
        );
    }

    public function sendResetCode(string $email, int $code): void
    {
        $this->send(
            $email,
            'Kode Atur Ulang Kata Sandi - Sales Ledger',
            'emails.reset-password',
            ['otpCode' => $code],
            new ResetPasswordMail($code)
        );
    }

    private function send(
        string $email,
        string $subject,
        string $view,
        array $data,
        Mailable $fallback
    ): void {
        $apiKey = config('services.brevo.key');

        if (blank($apiKey)) {
            Mail::to($email)->send($fallback);

            return;
        }

        $fromAddress = config('mail.from.address');

        if (blank($fromAddress)) {
            throw new RuntimeException('MAIL_FROM_ADDRESS belum diatur.');
        }

        try {
            $response = Http::withHeaders([
                'api-key' => $apiKey,
                'accept' => 'application/json',
            ])
                ->timeout((int) config('services.brevo.timeout', 15))
                ->post('https://api.brevo.com/v3/smtp/email', [
                    'sender' => [
                        'name' => config('mail.from.name', 'Sales Ledger App'),
                        'email' => $fromAddress,
                    ],
                    'to' => [
                        ['email' => $email],
                    ],
                    'subject' => $subject,
                    'htmlContent' => view($view, $data)->render(),
                ]);
        } catch (ConnectionException $e) {
            Log::error('Tidak bisa terhubung ke Brevo API.', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);

            throw new RuntimeException('Tidak bisa terhubung ke Brevo API. Periksa koneksi internet server.', 0, $e);
        }

        if ($response->failed()) {
            $detail = $response->json('message') ?? $response->body();

            Log::error('Brevo API gagal mengirim email.', [
                'email' => $email,
                'status' => $response->status(),
                'body' => $response->json() ?? $response->body(),
            ]);

            throw new RuntimeException("Brevo API gagal ({$response->status()}): {$detail}");
        }
    }
}
