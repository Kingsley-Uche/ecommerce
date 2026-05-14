<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        if (auth()->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    /**
     * Handle admin logout
     */
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
    /**
     * Handle admin login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        // Authenticate user
        if (Auth::attempt($credentials, $request->remember)) {
            
            // Check if user is admin
            $this->EnsureAdmin();
            

            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['email' => 'Invalid login credentials']);
    }

    /**
     * Dashboard view
     */
    public function dashboard()
    {
        //load all necessary data for dashboard view

        return view('admin.dashboard.pages.home.index');
    }

    /**
     * Show change password form
     */
    public function changePassword()
    {
        return view('admin.auth.change_password');
    }
    private function EnsureAdmin(){
        if (auth()->user()->is_admin != 1) {
                Auth::logout();
                return back()->withErrors(['email' => 'Access denied. Admins only.']);
            }
            return true;


    }
}
