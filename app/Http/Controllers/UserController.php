<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Devisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\CustomMessageMail;
use Illuminate\Validation\Rules\Password;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class UserController extends Controller
{
    /**
     * Menampilkan daftar semua pengguna dengan filter dan paginasi.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
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

    /**
     * Menampilkan profil detail seorang pengguna.
     * Menggunakan Route Model Binding untuk keamanan dan kemudahan.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function show(User $user)
    {
        // Eager load relasi untuk menghindari N+1 query problem
        $user->load(['devisi', 'roles', 'attendance' => function ($query) {
            $query->latest('date')->take(10);
        }]);
        
        return view('users.show', compact('user'));
    }

    /**
     * Mengupdate data pengguna di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|exists:roles,name',
            'devisi_id' => 'nullable|exists:devisis,id'
        ]);

        // Menggunakan transaksi database untuk memastikan integritas data.
        DB::transaction(function () use ($request, $user) {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'devisi_id' => $request->devisi_id,
            ]);

            // Mensinkronkan role, menghapus role lama dan menetapkan yang baru.
            $user->syncRoles([$request->role]);
        });

        return redirect()->route('admin.users.index')->with('success', 'Data pengguna berhasil diperbarui!');
    }

    /**
     * Menghapus pengguna dari database.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        // Melarang admin menghapus akunnya sendiri
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus.');
    }

    /**
     * Mereset password untuk seorang pengguna.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.users.show', $user->id)->with('success', 'Password untuk ' . $user->name . ' berhasil direset.');
    }

    /**
     * Melakukan aksi massal pada pengguna yang dipilih.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|string|in:change_devisi,delete',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'devisi_id' => 'nullable|required_if:action,change_devisi|exists:devisis,id'
        ]);

        $userIds = $request->user_ids;
        $action = $request->action;

        if ($action === 'delete') {
            // Filter untuk mencegah admin menghapus dirinya sendiri dalam aksi massal
            $filteredUserIds = array_diff($userIds, [auth()->id()]);
            $deletedCount = User::whereIn('id', $filteredUserIds)->delete();
            $message = $deletedCount . ' pengguna berhasil dihapus.';
            if (count($filteredUserIds) < count($userIds)) {
                $message .= ' Akun Anda tidak ikut terhapus.';
            }
            return back()->with('success', $message);
        }
        
        if ($action === 'change_devisi') {
            User::whereIn('id', $userIds)->update(['devisi_id' => $request->devisi_id]);
            $devisi = Devisi::find($request->devisi_id);
            $devisiName = $devisi ? $devisi->nama_devisi : 'Tidak Ada Devisi';
            return back()->with('success', count($userIds) . ' pengguna berhasil dipindahkan ke devisi ' . $devisiName . '.');
        }

        return back()->with('error', 'Aksi tidak valid.');
    }

    /**
     * Menampilkan form untuk mengirim email kustom.
     *
     * @return \Illuminate\View\View
     */
    public function customEmailForm()
    {
        $users = User::orderBy('name')->get();
        return view('custom-email', compact('users'));
    }

    /**
     * Mengirim email kustom ke pengguna yang dipilih.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendCustomEmail(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'recipients' => 'required|array|min:1',
            'recipients.*' => 'exists:users,id',
        ]);

        $users = User::whereIn('id', $request->recipients)->get();

        foreach ($users as $user) {
            // Antrikan email untuk dikirim di background agar tidak memperlambat UI
            Mail::to($user->email)->queue(new CustomMessageMail(
                $request->subject,
                $request->content
            ));
        }
        
        $userCount = count($users);
        return redirect()->back()->with('emailSuccess', "Email telah dimasukkan ke dalam antrian untuk dikirim ke {$userCount} penerima.");
    }
    
    /**
     * Menghasilkan QR Code untuk seorang pengguna.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function generateQrCode($id)
    {
        $user = User::findOrFail($id);

        // Data yang disematkan di QR Code dibuat lebih aman dan terstruktur
        $qrData = json_encode([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'generated_at' => now()->toIso8601String(), // Menambah stempel waktu
        ]);

        $qrCode = QrCode::size(300)->margin(10)->generate($qrData);

        return response($qrCode)->header('Content-Type', 'image/svg+xml');
    }
}