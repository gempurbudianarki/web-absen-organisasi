<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\EmailLog;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class RegisterController extends Controller
{
    public function showForm()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        /*
        // Menonaktifkan sementara pengecekan MailboxLayer
        $response = Http::get('https://apilayer.net/api/check', [
            'access_key' => env('MAILBOXLAYER_API_KEY'),
            'email' => $request->email,
            'smtp' => 1,
            'format' => 1,
        ]);

        if (isset($response['smtp_check']) && !$response['smtp_check']) {
            return redirect()->back()->withInput()->withErrors([
                'email' => 'This email appears to be undeliverable or fake.',
            ]);
        }
        */
        
        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            $user->assignRole('anggota'); // Default role 'anggota'

            $user->sendEmailVerificationNotification();

            EmailLog::create([
                'user_id' => $user->id,
                'email'   => $user->email,
                'subject' => 'Welcome Message',
                'sent_at' => now(),
            ]);

            DB::commit();
            Auth::login($user);
            return redirect()->route('verification.notice');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Registration error: '.$e->getMessage());

            return redirect()->back()->withInput()->withErrors([
                'email' => 'Registration failed. Unable to send verification email. Please check your email address.',
            ]);
        }
    }

    public function showAdminRegisterForm()
    {
        return view('admin.register-user');
    }

    public function registerByAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:admin,employee,anggota,pj',
        ]);

        /*
        // Menonaktifkan sementara pengecekan MailboxLayer
        $response = Http::get('https://apilayer.net/api/check', [
            'access_key' => env('MAILBOXLAYER_API_KEY'),
            'email' => $request->email,
            'smtp' => 1,
            'format' => 1,
        ]);

        if (isset($response['smtp_check']) && !$response['smtp_check']) {
            return redirect()->back()->withErrors([
                'email' => 'This email appears to be undeliverable or fake.',
            ])->withInput();
        }
        */

        $otp = rand(100000, 999999);

        Session::put('pending_registration', [
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'otp' => Hash::make($otp),
        ]);

        Session::put('otp_created_at', now());

        Mail::html(<<<HTML
            <p>Dear {$request->name},</p>
            <p>You are receiving this email because a registration has been initiated for your account in the <strong>Learner and Employee Management System (LEMS)</strong>.</p>
            <p>Your One-Time Password (OTP) is:</p>
            <h2 style="color:rgb(18, 2, 251); font-weight: bold; letter-spacing: 2px;">$otp</h2>
            <p>Please enter this 6-digit code on the OTP verification page <strong>within 10 minutes</strong> to complete your registration.</p>
            <p>If you did not request this, you can safely ignore this message.</p>
            <br>
            <p>Thank you,<br>LEMS Administrator</p>
        HTML
        , function ($message) use ($request) {
            $message->to($request->email)
                    ->subject('Your One-Time Password (OTP) for LEMS Registration');
        });

        return redirect()->route('admin.otp.verify.form')->with('otpSent', 'An OTP has been sent to the user’s email address.');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $createdAt = Session::get('otp_created_at');

        if (!$createdAt || now()->diffInMinutes($createdAt) > 10) {
            Session::forget(['pending_registration', 'otp_created_at']);
            return redirect()->route('admin.register.form')->withErrors([
                'otp' => 'OTP expired. Please register the user again.',
            ]);
        }

        $data = Session::get('pending_registration');

       if (!$data || !Hash::check($request->otp, $data['otp'])) {
            return redirect()->back()->withErrors(['otp' => 'Invalid OTP. Please try again.']);
        }

        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
            ]);

            $user->assignRole($data['role']);

            Mail::to($user->email)->send(new WelcomeMail($user));

            EmailLog::create([
                'user_id' => $user->id,
                'email' => $user->email,
                'subject' => 'Welcome Email Sent After OTP Verification',
                'sent_at' => now(),
            ]);

            DB::commit();
            Session::forget(['pending_registration', 'otp_created_at']);

            return redirect()->route('admin.register.form')->with('emailSuccess', 'User verified and registered successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('OTP Registration Error: ' . $e->getMessage());

            return redirect()->back()->withErrors([
                'otp' => 'Registration failed after OTP. Please try again.',
            ]);
        }
    }
    
    public function showOtpForm()
    {
        return view('admin.verify-otp');
    }
}