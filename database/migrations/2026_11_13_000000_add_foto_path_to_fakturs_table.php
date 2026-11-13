<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fakturs', function (Blueprint $table) {
            $table->string('foto_path')->nullable()->after('foto_url');
        });

        // Backfill path dari foto_url lama agar file lama juga bisa dibersihkan.
        $marker = '/storage/v1/object/public/';

        DB::table('fakturs')
            ->whereNull('foto_path')
            ->whereNotNull('foto_url')
            ->orderBy('id')
            ->chunkById(100, function ($fakturs) use ($marker) {
                foreach ($fakturs as $faktur) {
                    $position = strpos((string) $faktur->foto_url, $marker);

                    if ($position === false) {
                        continue;
                    }

                    // Format URL: .../public/<bucket>/<path>. Buang segmen bucket.
                    $segments = explode('/', substr((string) $faktur->foto_url, $position + strlen($marker)), 2);

                    if (count($segments) < 2 || $segments[1] === '') {
                        continue;
                    }

                    $path = ltrim(rawurldecode($segments[1]), '/');

                    if ($path !== '') {
                        DB::table('fakturs')->where('id', $faktur->id)->update(['foto_path' => $path]);
                    }
                }
            });
    }

    public function down(): void
    {
        Schema::table('fakturs', function (Blueprint $table) {
            $table->dropColumn('foto_path');
        });
    }
};
