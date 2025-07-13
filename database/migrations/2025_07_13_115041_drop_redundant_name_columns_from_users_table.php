<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // Sebelum menghapus kolom, kita coba selamatkan datanya terlebih dahulu.
        // Kita gabungkan fname, mname, lname ke dalam kolom 'name' jika 'name' kosong.
        DB::table('users')->whereNull('name')->orWhere('name', '')->cursor()->each(function ($user) {
            $fullName = trim(($user->fname ?? '') . ' ' . ($user->mname ?? '') . ' ' . ($user->lname ?? ''));
            if (!empty($fullName)) {
                DB::table('users')->where('id', $user->id)->update(['name' => $fullName]);
            }
        });

        Schema::table('users', function (Blueprint $table) {
            // Hapus kolom-kolom yang tidak lagi diperlukan.
            $table->dropColumn(['fname', 'mname', 'lname']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Jika migrasi di-rollback, buat kembali kolomnya agar tidak error.
            $table->string('fname')->nullable()->after('password');
            $table->string('mname')->nullable()->after('fname');
            $table->string('lname')->nullable()->after('mname');
        });
    }
};