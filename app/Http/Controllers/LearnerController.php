<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // Menggunakan model User
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LearnerController extends Controller
{
    /**
     * Display a listing of the users with the 'anggota' role.
     */
    public function index()
    {
        if (auth()->user()->hasRole('learner') || auth()->user()->hasRole('anggota')) {
            return view('learner.dashboard');
        }

        // For admin: Get all users with the 'anggota' role
        $learners = User::role('anggota')->orderBy('name')->get();
        return view('admin.learners.index', compact('learners'));
    }

    /**
     * Store a newly created user with the 'anggota' role in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'grade_level' => 'required|string',
            'section' => 'required|string',
        ]);

        // Create the user
        $user = User::create([
            'name' => $request->fname . ' ' . $request->lname,
            'fname' => $request->fname,
            'mname' => $request->mname,
            'lname' => $request->lname,
            'email' => $request->email,
            'password' => Hash::make(Str::random(10)), // Generate a random password
            'grade_level' => $request->grade_level,
            'section' => $request->section,
            'email_verified_at' => now(), // Auto-verify email for admin-created users
        ]);

        // Assign the 'anggota' role
        $user->assignRole('anggota');

        return redirect()->back()->with('success', 'Anggota berhasil ditambahkan.');
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'grade_level' => 'required|string',
            'section' => 'required|string',
        ]);

        $user->update([
            'name' => $request->fname . ' ' . $request->lname,
            'fname' => $request->fname,
            'mname' => $request->mname,
            'lname' => $request->lname,
            'email' => $request->email,
            'grade_level' => $request->grade_level,
            'section' => $request->section,
        ]);

        return redirect()->back()->with('success', 'Data anggota berhasil diperbarui.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->back()->with('success', 'Anggota berhasil dihapus.');
    }
}