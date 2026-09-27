<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Auth;
use Hash;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function adminLogin(Request $request)
    {
        return view('login');
    }
    public function managerLogin(Request $request)
    {
        return view('login');
    }
    public function salesmanLogin(Request $request)
    {
        return view('login');
    }

    public function create()
    {
        return view('register');
    }
    public function showLoginForm()
    {
        return view('login');
    }
    public function showLoginForm2()
    {
        return view('login');
    }
    public function seller()
    {
        return view('seller2');
    }
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'phone' => ['required'],
            'password' => ['required'],
        ]);

        if (Auth::attempt(['phone' => $credentials['phone'], 'password' => $credentials['password']])) {
            $userRole = auth()->user()->role ?? 'user';

            if (session()->has('url.intended')) {
                $intended = session('url.intended');
                if (!in_array($userRole, ['admin', 'manager']) && (str_contains($intended, '/dashboard') || str_contains($intended, '/admin'))) {
                    session()->forget('url.intended');
                }
            }

            if ($userRole === 'admin' || $userRole === 'manager') {
                $request->session()->regenerate();
                return redirect()->intended('dashboard');
            }
            if ($userRole === 'user') {
                $request->session()->regenerate();
                return redirect()->intended(route('userdashboard'));
            }
            if ($userRole === 'salesman') {
                $request->session()->regenerate();
                return redirect()->intended(route('salesmandashboard'));
            }
            if (in_array($userRole, ['seller', 'subdealer'])) {
                $request->session()->regenerate();
                return redirect()->intended(route('sellerdashboard'));
            }
            Auth::logout();
            return redirect()->back()->with('fail', 'Your account does not have CRM login access.');
        }
        return redirect()->back()->with('fail', 'Invalid Credentials');
    }



    //     $credentials = $request->validate([
    //         'phone' => ['required'],
    //         'password' => ['required'],
    //     ]);

    //     if (Auth::attempt(['phone' => $credentials['phone'], 'password' => $credentials['password']])) {

    //         if (auth()->user()->role === 'admin' || auth()->user()->role === 'manager') {
    //             $request->session()->regenerate();
    //             return redirect()->intended('dashboard');
    //         }
    //         Auth::logout();
    //         return redirect()->back()->with('fail', 'Your account does not have CRM login access.');
    //     }

    //     return redirect()->back()->with('fail', 'Invalid Credentials');
    // }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login_page');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'digits:10', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        User::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'password' => $validated['password'],
            'role' => 'user',
        ]);

        return redirect()->route('login_page')
            ->with('success', 'Account created successfully. Please login.');
    }
}
