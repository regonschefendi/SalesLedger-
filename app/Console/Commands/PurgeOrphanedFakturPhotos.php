<?php

namespace App\Console\Commands;

use App\Models\Faktur;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Throwable;

class PurgeOrphanedFakturPhotos extends Command
{
    protected $signature = 'faktur:purge-orphans
                            {--dry-run : Tampilkan file yang akan dihapus tanpa benar-benar menghapus}
                            {--older-than=60 : Hanya hapus file yang lebih tua dari N menit (0 = semua)}';

    protected $description = 'Hapus foto faktur di Supabase Storage yang datanya sudah tidak ada di database.';

    public function handle(): int
    {
        try {
            $disk = Storage::disk('supabase');
            $files = $disk->allFiles();
        } catch (Throwable $e) {
            $this->error('Tidak bisa membaca Supabase Storage: '.$e->getMessage());

            return self::FAILURE;
        }

        if (count($files) === 0) {
            $this->info('Tidak ada file di storage.');

            return self::SUCCESS;
        }

        // Kumpulkan seluruh path foto yang masih dipakai oleh data faktur.
        $referenced = [];

        Faktur::query()
            ->select(['id', 'foto_path', 'foto_url'])
            ->orderBy('id')
            ->chunk(500, function ($fakturs) use (&$referenced) {
                foreach ($fakturs as $faktur) {
                    $path = $faktur->fotoStoragePath();

                    if ($path) {
                        $referenced[$path] = true;
                    }
                }
            });

        $olderThanMinutes = max(0, (int) $this->option('older-than'));
        $cutoff = now()->subMinutes($olderThanMinutes)->getTimestamp();
        $dryRun = (bool) $this->option('dry-run');
        $orphans = [];

        foreach ($files as $file) {
            if (isset($referenced[$file])) {
                continue;
            }

            // Lewati file baru agar upload yang sedang berjalan tidak ikut terhapus.
            if ($olderThanMinutes > 0) {
                try {
                    if ($disk->lastModified($file) > $cutoff) {
                        continue;
                    }
                } catch (Throwable) {
                    // Kalau metadata tidak tersedia, tetap anggap kandidat.
                }
            }

            $orphans[] = $file;
        }

        if (count($orphans) === 0) {
            $this->info('Tidak ada foto yatim yang perlu dihapus.');

            return self::SUCCESS;
        }

        $this->info(count($orphans).' foto yatim ditemukan.');

        foreach (array_chunk($orphans, 100) as $chunk) {
            if ($dryRun) {
                foreach ($chunk as $file) {
                    $this->line('- '.$file);
                }

                continue;
            }

            try {
                $disk->delete($chunk);
            } catch (Throwable $e) {
                $this->error('Gagal menghapus sebagian file: '.$e->getMessage());
            }
        }

        $this->info($dryRun ? 'Dry run selesai. Tidak ada file yang dihapus.' : 'Pembersihan selesai.');

        return self::SUCCESS;
    }
}
