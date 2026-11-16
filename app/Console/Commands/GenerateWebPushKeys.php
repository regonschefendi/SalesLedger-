<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Minishlink\WebPush\VAPID;
use Throwable;

class GenerateWebPushKeys extends Command
{
    protected $signature = 'webpush:keys';

    protected $description = 'Buat pasangan kunci VAPID untuk notifikasi web push.';

    public function handle(): int
    {
        try {
            $keys = VAPID::createVapidKeys();
        } catch (Throwable $e) {
            $this->error('Gagal membuat kunci VAPID: '.$e->getMessage());

            if (PHP_OS_FAMILY === 'Windows') {
                $this->line('Di Windows, set environment OPENSSL_CONF ke file openssl.cnf bawaan PHP sebelum menjalankan perintah ini.');
            }

            return self::FAILURE;
        }

        $this->info('Tambahkan baris berikut ke file .env:');
        $this->newLine();
        $this->line('WEBPUSH_VAPID_PUBLIC_KEY='.$keys['publicKey']);
        $this->line('WEBPUSH_VAPID_PRIVATE_KEY='.$keys['privateKey']);
        $this->newLine();
        $this->warn('Jangan bagikan atau commit private key ini.');

        return self::SUCCESS;
    }
}
