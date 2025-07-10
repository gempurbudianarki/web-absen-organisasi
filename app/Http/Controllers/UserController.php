<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;
use App\Mail\CustomMessageMail;
use App\Models\EmailLog;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(10);
        return view('users.index', compact('users'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|exists:roles,name',
        ]);

        DB::transaction(function () use ($request, $user) {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            $user->syncRoles([$request->role]);
        });

        return redirect()->route('admin.users.index')
                         ->with('success', 'User berhasil diperbarui!');
    }


    public function destroy(User $user)
    {
        $user->delete();

        // PERBAIKAN DI SINI: Menggunakan nama route yang benar
        return redirect()->route('admin.users.index')
                         ->with('success', 'User berhasil dihapus.');
    }

     /**
     * Send welcome email to selected users.
     */
    public function sendMail(Request $request)
    {
        $request->validate([
            'recipients'   => 'required|array|min:1',
            'recipients.*' => 'integer|exists:users,id',
        ]);

        $users = User::whereIn('id', $request->input('recipients'))->get();

        foreach ($users as $user) {
            Mail::to($user->email)->send(new WelcomeMail($user));

             EmailLog::create([
                'user_id' => $user->id,
                'email'   => $user->email,
                'subject' => 'Welcome to Email Sender',
                'sent_at' => now(),
            ]);
        }

        $emails = $users->pluck('email')->implode(', ');

        return redirect()
            ->back()
            ->with('emailSuccess', "Welcome email terkirim ke: {$emails}");
    }

    public function customEmailForm()
    {
        // Fitur ini belum kita tautkan di menu mana pun, jadi kita biarkan dulu
        $users = User::orderBy('name')->get();
        return view('custom-email', compact('users'));
    }

    public function sendCustomEmail(Request $request)
    {
        // Fitur ini belum kita tautkan di menu mana pun, jadi kita biarkan dulu
        $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'recipients' => 'required|array'
        ]);

        $users = User::whereIn('id', $request->recipients)->get();

        foreach ($users as $user) {
            Mail::to($user->email)->send(new CustomMessageMail(
                $request->subject,
                $request->content
            ));
        }
        $emails = $users->pluck('email')->take(5)->implode(', ');
        $more = $users->count() > 5 ? ' and others' : '';

        return redirect()
            ->back()
            ->with('emailSuccess', "Custom message has been queued for: {$emails}{$more}");
    }
}