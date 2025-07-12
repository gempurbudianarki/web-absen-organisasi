<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\EmailLog;
use App\Models\Devisi;
use App\Models\Kegiatan;
use App\Models\LearnerAttendance;
use App\Models\Announcement;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        // KPI Cards Data
        $userCount = User::count();
        $anggotaCount = User::role('anggota')->count(); // Data yang lebih akurat
        $devisiCount = Devisi::count();
        $kegiatanCount = Kegiatan::count();
        $announcementCount = Announcement::count();
        $mailLogCount = EmailLog::count();
        
        $attendanceTodayCount = LearnerAttendance::whereDate('date', Carbon::today())->count();
        
        // Data for Charts
        $userRoleData = User::select('roles.name as role', DB::raw('count(*) as count'))
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->groupBy('roles.name')
            ->pluck('count', 'role');
            
        $attendanceLabels = [];
        $attendanceData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $attendanceLabels[] = $date->isoFormat('dddd');
            $attendanceData[] = LearnerAttendance::whereDate('date', $date)->count();
        }

        return view('admin.dashboard', compact(
            'userCount',
            'anggotaCount', // Mengirim data baru
            'devisiCount',
            'kegiatanCount',
            'announcementCount',
            'mailLogCount',
            'attendanceTodayCount',
            'userRoleData',
            'attendanceLabels',
            'attendanceData'
        ));
    }
}