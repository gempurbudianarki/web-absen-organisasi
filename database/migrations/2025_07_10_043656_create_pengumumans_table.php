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
            $table->string('judul');
            $table->text('isi');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Siapa yang membuat pengumuman
            $table->foreignId('devisi_id')->nullable()->constrained('devisis')->onDelete('cascade'); // Untuk menargetkan devisi tertentu, bisa null jika untuk semua
            $table->timestamp('waktu_publish')->useCurrent();
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