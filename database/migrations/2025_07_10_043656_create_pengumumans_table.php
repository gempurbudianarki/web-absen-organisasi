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
        Schema::create('pengumumans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('judul');
            $table->text('isi'); // Konsisten menggunakan 'isi'
            
            // --- PERBAIKAN KRUSIAL DI SINI ---
            $table->enum('target', ['semua', 'devisi'])->default('semua');
            
            $table->foreignId('devisi_id')->nullable()->constrained('devisis')->onDelete('cascade');
            $table->timestamp('publish_at')->nullable()->comment('Waktu mulai tayang');
            $table->timestamp('expires_at')->nullable()->comment('Waktu berhenti tayang, null berarti selamanya');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengumumans');
    }
};