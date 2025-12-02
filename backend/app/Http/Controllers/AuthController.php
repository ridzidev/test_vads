<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Admin;
use App\Services\CaptchaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show login form with CAPTCHA
     */
    public function showLoginForm()
    {
        $captcha = CaptchaService::generate();
        session(['captcha_code' => $captcha['code']]);
        
        return view('login', ['captcha_image' => $captcha['image']]);
    }
    
    /**
     * Handle login
     */
    public function login(Request $request)
    {
        // Validate input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
            'captcha' => 'required|string',
        ], [
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 6 karakter',
            'captcha.required' => 'CAPTCHA harus diisi',
        ]);
        
        // Verify CAPTCHA
        if (!CaptchaService::verify($request->captcha, session('captcha_code'))) {
            return back()
                ->withInput($request->except('password', 'captcha'))
                ->with('error', 'CAPTCHA tidak valid. Silakan coba lagi.');
        }

        // =========================================================================
        // PERUBAHAN UTAMA: Cek Admin (prioritas tinggi) DULU
        // =========================================================================

        // Check if admin exists in admins table
        $admin = Admin::where('email', $request->email)
            ->where('is_active', true)
            ->first();
        
        if ($admin && Hash::check($request->password, $admin->password)) {
            // Update last login
            $admin->update(['last_login_at' => now()]);
            Auth::login($admin, false, 'admin');
            
            // REDIRECT ADMIN: Ke /service-desk-dashboard
            return redirect('/service-desk-dashboard')->with('success', 'Login berhasil sebagai Admin');
        }

        // =========================================================================
        // Cek User (Customer) KEDUA
        // =========================================================================
        
        // Check if user exists in users table (customer)
        $user = User::where('email', $request->email)
            ->where('is_active', true)
            ->first();
        
        if ($user && Hash::check($request->password, $user->password)) {
            // Update last login
            $user->update(['last_login_at' => now()]);
            Auth::login($user);
            
            // REDIRECT CUSTOMER: Ke /chat-room
            return redirect('/chat-room')->with('success', 'Login berhasil sebagai Customer');
        }
        
        // User not found or password incorrect
        return back()
            ->withInput($request->except('password', 'captcha'))
            ->with('error', 'Email atau password salah, atau akun tidak aktif.');
    }
    
    /**
     * Show register form with CAPTCHA
     */
    public function showRegisterForm()
    {
        $captcha = CaptchaService::generate();
        session(['captcha_code' => $captcha['code']]);
        
        return view('register', ['captcha_image' => $captcha['image']]);
    }
    
    /**
     * Handle register
     */
    public function register(Request $request)
    {
        // Validate input
        $request->validate([
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'captcha' => 'required|string',
        ], [
            'name.required' => 'Nama harus diisi',
            'name.min' => 'Nama minimal 3 karakter',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak sesuai',
            'captcha.required' => 'CAPTCHA harus diisi',
        ]);
        
        // Verify CAPTCHA
        if (!CaptchaService::verify($request->captcha, session('captcha_code'))) {
            return back()
                ->withInput($request->except('password', 'password_confirmation', 'captcha'))
                ->with('error', 'CAPTCHA tidak valid. Silakan coba lagi.');
        }
        
        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'role' => 'customer',
            'is_active' => true,
        ]);
        
        // Login user
        Auth::login($user);
        
        return redirect('/dashboard')->with('success', 'Registrasi berhasil! Selamat datang.');
    }
    
    /**
     * Refresh CAPTCHA
     */
    public function refreshCaptcha()
    {
        $captcha = CaptchaService::generate();
        session(['captcha_code' => $captcha['code']]);
        
        return response()->json([
            'image' => $captcha['image'],
        ]);
    }
    
    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/')->with('success', 'Logout berhasil.');
    }
}