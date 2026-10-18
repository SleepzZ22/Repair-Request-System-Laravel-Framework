<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        $technicians = User::where('role', 'technician')->get();
        $admin = User::where('role', 'admin')->first();
        $user = User::where('role', 'user')->first();

        return view('auth.login', [
            'technicians' => $technicians,
            'admin' => $admin,
            'user' => $user,
        ]);
    }

    /**
     * Handle standard login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return $this->redirectBasedOnRole(Auth::user())
                ->with('success', 'เข้าสู่ระบบสำเร็จ ยินดีต้อนรับ ' . Auth::user()->name);
        }

        return back()->withErrors([
            'email' => 'อีเมลหรือรหัสผ่านไม่ถูกต้อง',
        ])->onlyInput('email');
    }

    /**
     * Quick Switcher for Testing / Demonstration
     */
    public function quickLogin($userId)
    {
        $user = User::findOrFail($userId);
        Auth::login($user);
        session()->regenerate();

        return $this->redirectBasedOnRole($user)
            ->with('success', 'สลับบัญชีผู้ใช้งานเป็น: ' . $user->name . ' (' . $this->getRoleLabel($user->role) . ') เรียบร้อยแล้ว');
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'ออกจากระบบเรียบร้อยแล้ว');
    }

    /**
     * Redirect user based on their role
     */
    protected function redirectBasedOnRole(User $user)
    {
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isTechnician()) {
            return redirect()->route('technician.dashboard');
        } else {
            return redirect()->route('tickets.create');
        }
    }

    private function getRoleLabel($role)
    {
        return match ($role) {
            'admin' => 'ผู้ดูแลระบบ (Admin)',
            'technician' => 'ช่างซ่อมไอที (Technician)',
            'user' => 'พนักงานทั่วไป (User)',
            default => $role,
        };
    }
}
