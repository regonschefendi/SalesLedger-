<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fakturs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_toko');
            $table->date('tanggal_nota')->nullable();
            $table->decimal('total_tagihan', 15, 2)->nullable();
            $table->decimal('nominal_tagihan', 15, 2)->nullable();
            $table->text('catatan')->nullable();
            // $table->foreignId('sales_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fakturs');
    }
};
