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
            $table->foreignId('toko_id')->constrained('tokos')->cascadeOnDelete();
            $table->string('nomor_faktur')->nullable();
            $table->date('tanggal_nota')->nullable();
            $table->date('tanggal_pembayaran')->nullable();
            $table->string('metode_bayar')->nullable();
            $table->decimal('total_tagihan', 15, 2)->nullable();
            $table->decimal('total_dibayar', 15, 2)->default(0);
            $table->enum('status', ['lunas', 'belum_lunas'])->default('belum_lunas');
            $table->string('foto_url')->nullable();
            $table->foreignId('sales_id')->nullable()->constrained('users')->onDelete('cascade');
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
