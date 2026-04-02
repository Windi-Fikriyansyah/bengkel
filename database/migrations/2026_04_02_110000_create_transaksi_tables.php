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
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('pelanggan_id')->nullable()->constrained('pelanggan')->onDelete('set null');
            
            // Data manual jika pelanggan tidak terdaftar/input langsung
            $table->string('nama_pelanggan')->nullable();
            $table->string('no_wa')->nullable();
            $table->string('no_polisi')->nullable();
            
            $table->decimal('total_harga', 15, 2)->default(0);
            $table->enum('status', ['pending', 'lunas', 'batal'])->default('pending');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('transaksi_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_id')->constrained('transaksi')->onDelete('cascade');
            
            // Bisa berupa layanan_id atau sparepart_id
            $table->unsignedBigInteger('item_id')->nullable(); 
            $table->enum('tipe', ['layanan', 'sparepart']);
            
            $table->string('nama_item');
            $table->decimal('harga', 15, 2);
            $table->integer('jumlah')->default(1);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_detail');
        Schema::dropIfExists('transaksi');
    }
};
