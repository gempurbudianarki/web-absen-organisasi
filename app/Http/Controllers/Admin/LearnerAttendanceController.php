<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User; // Menggunakan User
use App\Models\LearnerAttendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LearnerAttendanceController extends Controller
{
    public function index()
    {
        // Get attendance records for today, now with the 'user' relationship
        $today = Carbon::today()->toDateString();
        $attendances = LearnerAttendance::with('user')
            ->where('date', $today)
            ->orderByDesc('am_in')
            ->paginate(10); 

        return view('admin.attendance.index', compact('attendances', 'today'));
    }

    public function lookupLearner(Request $request)
    {
        $request->validate(['qr_code' => 'required|string']);

        // Search for a user with the given qr_code
        $user = User::where('qr_code', $request->qr_code)->first();

        if (!$user) {
            return response()->json(['status' => 'not_found'], 404);
        }

        // Return a consistent JSON structure, using 'learner' as key for compatibility with JS
        return response()->json([
            'status' => 'found',
            'learner' => [
                'id' => $user->id,
                'name' => $user->name, // Directly use the 'name' attribute from User model
            ]
        ]);
    }

   public function store(Request $request)
    {
        // The learner_id from the request is now a user_id
        $request->validate([
            'learner_id' => 'required|exists:users,id',
            'session' => 'required|in:am_in,am_out,pm_in,pm_out',
        ]);

        // Record the attendance using the user_id (passed as learner_id)
        $attendance = LearnerAttendance::firstOrCreate([
            'learner_id' => $request->learner_id,
            'date' => today(),
        ]);

        // Prevent duplicate logging for the same session
        if (!is_null($attendance->{$request->session})) {
            return $request->expectsJson()
                ? response()->json(['status' => 'warning', 'message' => 'Sesi ini sudah di-log sebelumnya.'], 200)
                : redirect()->back()->with('warning', 'Sesi ini sudah di-log sebelumnya.');
        }

        $attendance->{$request->session} = now();
        $attendance->save();

        return $request->expectsJson()
            ? response()->json(['status' => 'success', 'message' => 'Absensi berhasil dicatat.'], 200)
            : redirect()->back()->with('success', 'Absensi berhasil dicatat.');
    }
}