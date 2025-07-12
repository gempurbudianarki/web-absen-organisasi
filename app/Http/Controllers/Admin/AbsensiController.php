<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LearnerAttendance;
use App\Models\User;
use App\Models\Devisi;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    /**
     * Display the attendance report page.
     */
    public function index(Request $request)
    {
        // Set default date range to today
        $startDate = $request->input('start_date', Carbon::today()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());
        $selectedUserId = $request->input('user_id');
        $selectedDevisiId = $request->input('devisi_id');

        // --- START OF MODIFIED CODE ---

        // Query now uses the 'user' relationship defined in LearnerAttendance model
        $query = LearnerAttendance::with(['user.devisi'])
            ->whereBetween('date', [$startDate, $endDate]);

        // Apply filter by specific user if selected
        if ($selectedUserId) {
            // The relationship is now direct via 'learner_id' which points to users table
            $query->where('learner_id', $selectedUserId);
        }

        // Apply filter by devisi if selected
        if ($selectedDevisiId) {
            $query->whereHas('user.devisi', function ($q) use ($selectedDevisiId) {
                $q->where('id', $selectedDevisiId);
            });
        }
        
        // --- END OF MODIFIED CODE ---

        // Paginate the results
        $absensiLogs = $query->latest('date')->paginate(15);

        // Get data for filter dropdowns
        $users = User::role('anggota')->orderBy('name')->get();
        $devisis = Devisi::orderBy('nama_devisi')->get();

        // Pass data to the view
        return view('admin.absensi.index', [
            'absensiLogs' => $absensiLogs,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'users' => $users,
            'devisis' => $devisis,
            'selectedUserId' => $selectedUserId,
            'selectedDevisiId' => $selectedDevisiId,
        ]);
    }
    
    /**
     * Show the form for creating a new attendance record.
     */
    public function create()
    {
        // We now fetch users with the 'anggota' role for the dropdown
        $users = User::role('anggota')->orderBy('name')->get();
        return view('admin.absensi.create', ['learners' => $users]); // Pass as 'learners' for now to match view
    }

    /**
     * Store a newly created attendance record in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'learner_id' => 'required|exists:users,id', // Validate against users table
            'date' => 'required|date',
            'am_in' => 'nullable|date_format:H:i',
            'am_out' => 'nullable|date_format:H:i',
            'pm_in' => 'nullable|date_format:H:i',
            'pm_out' => 'nullable|date_format:H:i',
        ]);

        LearnerAttendance::updateOrCreate(
            [
                'learner_id' => $request->learner_id, // This column now correctly points to a user's ID
                'date' => $request->date,
            ],
            [
                'am_in' => $request->am_in,
                'am_out' => $request->am_out,
                'pm_in' => $request->pm_in,
                'pm_out' => $request->pm_out,
            ]
        );

        return redirect()->route('admin.absensi.index')->with('success', 'Data absensi manual berhasil disimpan.');
    }
}