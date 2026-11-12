<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tokos', function (Blueprint $table) {
            $table->foreignId('admin_id')
                ->nullable()
                ->after('id')
                ->constrained('users')
                ->nullOnDelete();
        });

        // Backfill kepemilikan toko lama berdasarkan admin dari sales yang
        // pernah menginput faktur atau memegang toko tersebut.
        DB::table('tokos')
            ->whereNull('admin_id')
            ->orderBy('id')
            ->chunkById(100, function ($tokos) {
                foreach ($tokos as $toko) {
                    $adminId = DB::table('fakturs')
                        ->join('users', 'users.id', '=', 'fakturs.sales_id')
                        ->where('fakturs.toko_id', $toko->id)
                        ->where('users.role', 'sales')
                        ->whereNotNull('users.parent_admin_id')
                        ->value('users.parent_admin_id');

                    if (! $adminId) {
                        $adminId = DB::table('sales_tokos')
                            ->join('users', 'users.id', '=', 'sales_tokos.user_id')
                            ->where('sales_tokos.toko_id', $toko->id)
                            ->where('users.role', 'sales')
                            ->whereNotNull('users.parent_admin_id')
                            ->value('users.parent_admin_id');
                    }

                    if ($adminId) {
                        DB::table('tokos')
                            ->where('id', $toko->id)
                            ->update(['admin_id' => $adminId]);
                    }
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tokos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('admin_id');
        });
    }
};
