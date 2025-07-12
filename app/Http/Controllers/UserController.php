<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Devisi;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;
use App\Mail\CustomMessageMail;
use App\Models\EmailLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles', 'devisi');

        if ($request->filled('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        if ($request->filled('devisi_id')) {
            $query->where('devisi_id', $request->devisi_id);
        }

        $users = $query->orderBy('name', 'asc')->paginate(10);
        $roles = Role::pluck('name', 'name');
        $devisis = Devisi::pluck('nama_devisi', 'id');

        return view('users.index', compact('users', 'roles', 'devisis'));
    }

    public function show($id)
    {
        $user = User::with(['devisi', 'roles', 'attendance' => function ($query) {
            $query->orderBy('date', 'desc')->take(10);
        }])->findOrFail($id);
        
        return view('users.show', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|exists:roles,name',
            'devisi_id' => 'nullable|exists:devisis,id'
        ]);

        DB::transaction(function () use ($request, $user) {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'devisi_id' => $request->devisi_id,
            ]);

            $user->syncRoles([$request->role]);
        });

        return redirect()
            ->route('users.index')
            ->with('success', 'User updated successfully!');
    }


    public function destroy(User $user)
    {
        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'User deleted successfully.');
    }

    public function resetPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('users.show', $user->id)->with('success', 'Password untuk ' . $user->name . ' berhasil direset.');
    }

    /**
     * Perform bulk actions on selected users.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|string',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'devisi_id' => 'nullable|required_if:action,change_devisi|exists:devisis,id'
        ]);

        $userIds = $request->user_ids;
        $action = $request->action;

        if ($action === 'delete') {
            User::whereIn('id', $userIds)->delete();
            return back()->with('success', count($userIds) . ' pengguna berhasil dihapus.');
        }
        
        if ($action === 'change_devisi') {
            User::whereIn('id', $userIds)->update(['devisi_id' => $request->devisi_id]);
            $devisi = Devisi::find($request->devisi_id);
            return back()->with('success', count($userIds) . ' pengguna berhasil dipindahkan ke devisi ' . $devisi->nama_devisi . '.');
        }

        return back()->with('error', 'Aksi tidak valid.');
    }

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
            ->with('emailSuccess', "Sent to: {$emails}");
    }

    public function customEmailForm()
    {
        $users = User::orderBy('name')->get();
        return view('custom-email', compact('users'));
    }

    public function sendCustomEmail(Request $request)
    {
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
    
    public function generateQrCode($id)
    {
        $user = User::findOrFail($id);

        $qrData = json_encode([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);

        $qrCode = QrCode::size(300)->margin(10)->generate($qrData);

        return response($qrCode)->header('Content-Type', 'image/svg+xml');
    }
}