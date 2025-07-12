<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use App\Models\AnnouncementTarget;
use App\Models\AnnouncementLog;
use App\Models\User; // Menggunakan User, bukan Learner
use App\Mail\AnnouncementEmail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $announcements = Announcement::latest()->get();
        return view('admin.announcements.index', compact('announcements'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'sent_by' => 'required|string'
        ]);

        Announcement::create($request->only('title', 'content', 'sent_by'));

        return redirect()->back()->with('success', 'Pengumuman berhasil dibuat!');
    }

    public function sendForm()
    {
        $announcements = Announcement::latest()->get();
        // Mengambil data dari model User, bukan Learner
        $gradeLevels = User::role('anggota')->whereNotNull('grade_level')->select('grade_level')->distinct()->pluck('grade_level');
        $sections = User::role('anggota')->whereNotNull('section')->select('section')->distinct()->pluck('section');

        return view('admin.announcements.send', compact('announcements', 'gradeLevels', 'sections'));
    }

    public function processSend(Request $request)
    {
        $request->validate([
            'announcement_id' => 'required|exists:announcements,id',
            'grade_level' => 'nullable|string',
            'section' => 'nullable|string',
        ]);

        $announcement = Announcement::findOrFail($request->announcement_id);

        // Get recipients from User model based on role and filters
        $query = User::role('anggota');
        if ($request->grade_level) {
            $query->where('grade_level', $request->grade_level);
        }
        if ($request->section) {
            $query->where('section', $request->section);
        }
        $recipients = $query->get();

        foreach ($recipients as $recipient) {
            try {
                Mail::to($recipient->email)->send(new AnnouncementEmail($announcement));

                // Log the successful email sending to the correct user_id
                AnnouncementLog::create([
                    'announcement_id' => $announcement->id,
                    'learner_id' => $recipient->id, // This column now holds a user_id
                    'is_sent' => true,
                    'sent_at' => Carbon::now(),
                ]);

            } catch (\Exception $e) {
                // Log a failed attempt if needed
                AnnouncementLog::create([
                    'announcement_id' => $announcement->id,
                    'learner_id' => $recipient->id,
                    'is_sent' => false,
                    'sent_at' => null,
                ]);
            }
        }

        return redirect()->route('admin.announcements.sendForm')
                        ->with('success', 'Pengumuman berhasil dikirim dan dicatat.');
    }

    public function logs()
    {
        // Eager load the 'user' relationship from the AnnouncementLog model
        $logs = AnnouncementLog::with('user')
            ->orderByDesc('sent_at')
            ->paginate(10);

        return view('admin.announcements.logs', compact('logs'));
    }

    // Metode send() dan lainnya bisa dihapus atau di-refactor jika tidak digunakan lagi
    // Untuk saat ini kita biarkan dulu agar tidak ada route yang error.
}