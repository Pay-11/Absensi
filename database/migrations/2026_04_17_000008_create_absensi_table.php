<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesi_absen_id')->constrained('sesi_absen')->cascadeOnDelete();
            $table->foreignId('murid_id')->constrained('users')->cascadeOnDelete();
            
            $table->enum('status', ['hadir','izin','sakit','alpha','terlambat']);
            $table->timestamp('waktu_scan')->nullable();
            
            $table->timestamp('created_at')->nullable();
            
            $table->unique(['sesi_absen_id', 'murid_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};
