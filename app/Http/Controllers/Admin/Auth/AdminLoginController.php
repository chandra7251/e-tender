<?php
namespace App\Http\Controllers\Admin\Auth;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
class AdminLoginController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check() && Auth::user()->isAdminRole()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.auth.login');
    }
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);
        if (! Auth::attempt($credentials)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Email atau password salah.']);
        }
        if (! Auth::user()->isAdminRole()) {
            Auth::logout();
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Akun ini tidak memiliki akses admin.']);
        }
        $request->session()->regenerate();
        ActivityLog::log('login', 'auth', 'Admin login berhasil.');
        return redirect()->intended(route('admin.dashboard'));
    }
    public function logout(Request $request): RedirectResponse
    {
        ActivityLog::log('logout', 'auth', 'Admin logout.');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
