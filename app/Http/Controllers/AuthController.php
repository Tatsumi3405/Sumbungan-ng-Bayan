<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function landing(Request $request)
    {
        if ($user = $request->user()) {
            return redirect()->route($user->isAdmin() ? 'admin.dashboard' : 'citizen.dashboard');
        }
        return view('landing');
    }

    public function showLogin()         { return view('auth.login'); }
    public function showRegister()      { return view('auth.register'); }
    public function showAdminLogin()    { return view('auth.admin-login'); }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required'    => 'Please enter your email',
            'email.email'       => 'Enter a valid email address',
            'password.required' => 'Please enter your password',
        ]);

        if (! Auth::attempt($data, $request->boolean('remember'))) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Invalid email or password.']);
        }

        $request->session()->regenerate();
        $user = Auth::user();

        // Admins must use the admin portal (same rule as the Flutter app).
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->intended(route('citizen.dashboard'));
    }

    public function adminLogin(Request $request)
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required'    => 'Please enter both email and password.',
            'password.required' => 'Please enter both email and password.',
        ]);

        if (! Auth::attempt($data)) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Invalid email or password.']);
        }

        if (! Auth::user()->isAdmin()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Access Denied: This account is not an admin.']);
        }

        $request->session()->regenerate();
        return redirect()->route('admin.dashboard');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'min:2', 'max:100'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required'      => 'Please enter your full name',
            'name.min'           => 'Name is too short',
            'email.unique'       => 'An account with this email already exists.',
            'email.email'        => 'Enter a valid email address',
            'password.min'       => 'Password must be at least 6 characters',
            'password.confirmed' => 'Passwords do not match',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => 'citizen',   // never trust a client-sent role
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('citizen.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('landing');
    }
}
