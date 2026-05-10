<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Resources\VendorResource;
use App\Http\Traits\ApiResponse;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    use ApiResponse;

    /** POST /api/auth/register */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name'     => $request->input('name'),
            'email'    => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role'     => 'vendor',
        ]);

        $vendor = Vendor::create([
            'user_id'             => $user->id,
            'company_name'        => $request->input('company_name'),
            'phone'               => $request->input('phone'),
            'address'             => $request->input('address'),
            'verification_status' => 'pending',
        ]);

        $token = Str::random(60);
        $user->forceFill(['remember_token' => $token])->save();

        return $this->created([
            'token'  => $token,
            'vendor' => new VendorResource($vendor->load('user')),
        ], 'Registrasi berhasil. Akun Anda menunggu verifikasi admin.');
    }

    /** POST /api/auth/login */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->input('email'))->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return $this->error('Email atau password salah.', null, 401);
        }

        if ($user->role !== 'vendor') {
            return $this->error('Akun ini bukan akun vendor.', null, 403);
        }

        $token = Str::random(60);
        $user->forceFill(['remember_token' => $token])->save();

        return $this->success([
            'token'  => $token,
            'vendor' => new VendorResource($user->vendor()->with('user')->first()),
        ], 'Login berhasil.');
    }

    /** POST /api/auth/logout */
    public function logout(Request $request): JsonResponse
    {
        $user = auth()->user();
        $user->forceFill(['remember_token' => null])->save();

        return $this->success(null, 'Logout berhasil.');
    }

    /** GET /api/auth/me */
    public function me(Request $request): JsonResponse
    {
        $vendor = auth()->user()->vendor()->with('user')->first();

        return $this->success(new VendorResource($vendor));
    }

    /** POST /api/auth/forgot-password */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return $this->success(null, 'Link reset password telah dikirim ke email Anda.');
        }

        return $this->error('Gagal mengirim link reset password.', null, 422);
    }

    /** POST /api/auth/reset-password */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return $this->success(null, 'Password berhasil direset.');
        }

        return $this->error('Token tidak valid atau sudah kedaluwarsa.', null, 422);
    }

    /** PUT /api/auth/change-password */
    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'min:8', 'confirmed'],
        ]);

        $user = auth()->user();

        if (!Hash::check($request->input('current_password'), $user->password)) {
            return $this->error('Password lama tidak sesuai.', null, 422);
        }

        $user->forceFill(['password' => Hash::make($request->input('password'))])->save();

        return $this->success(null, 'Password berhasil diubah.');
    }
}
