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
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('kegiatan_id')->constrained('kegiatans')->onDelete('cascade');
            $table->enum('status', ['hadir', 'izin', 'sakit', 'alpa']);
            $table->timestamp('waktu_absen');
            $table->text('keterangan')->nullable(); // Untuk alasan izin/sakit
            $table->timestamps();

            // Mencegah satu user absen lebih dari sekali di kegiatan yang sama
            $table->unique(['user_id', 'kegiatan_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};