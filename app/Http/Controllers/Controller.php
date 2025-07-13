<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    /**
     * PERBAIKAN UTAMA:
     * Dengan menggunakan trait ini, semua controller turunan (seperti AbsensiController,
     * KegiatanController, dll.) akan secara otomatis memiliki akses ke method
     * seperti $this->authorize(), $this->validate(), dll.
     */
    use AuthorizesRequests, ValidatesRequests;
}