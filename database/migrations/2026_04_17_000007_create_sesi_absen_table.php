<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sesi_absen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_id')->constrained('jadwal')->cascadeOnDelete();
            $table->date('tanggal');
            $table->enum('tipe', ['masuk','mapel','pulang']);
            $table->string('token_qr')->nullable();
            
            $table->foreignId('dibuka_oleh')->constrained('users')->cascadeOnDelete();
            $table->timestamp('dibuka_pada')->nullable();
            $table->boolean('is_closed')->default(false);
            
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesi_absen');
    }
};
