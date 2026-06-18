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
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    use ApiResponse;

    // Fungsi buat daftar akun vendor baru, sekalian otomatis bikinin profil vendor yang statusnya masih 'pending'
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name'     => $request->input('name'),
            'email'    => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role'     => 'vendor',
        ]);

        Vendor::create([
            'user_id'             => $user->id,
            'company_name'        => $request->input('company_name'),
            'phone'               => $request->input('phone'),
            'address'             => $request->input('address'),
            'verification_status' => 'pending',
        ]);

        // Kirim email verifikasi, tapi jangan gagalkan registrasi jika SMTP error
        try {
            $user->sendEmailVerificationNotification();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Email verifikasi gagal dikirim: ' . $e->getMessage(), ['user_id' => $user->id]);
        }

        return $this->created(null, 'Registrasi berhasil. Silakan cek kotak masuk email Anda untuk verifikasi akun sebelum login.');
    }

    // Fungsi buat login, ngecek password sama ngecek emailnya udah diverifikasi atau belum
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->input('email'))->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return $this->error('Email atau password salah.', null, 401);
        }

        if ($user->role !== 'vendor') {
            return $this->error('Akun ini bukan akun vendor.', null, 403);
        }

        if (!$user->hasVerifiedEmail()) {
            return $this->error('Email belum diverifikasi. Silakan cek kotak masuk email Anda.', null, 403);
        }

        $token = JWTAuth::fromUser($user);

        return $this->success([
            'token'      => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
            'user'       => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
            ],
        ], 'Login berhasil.');
    }

    // Fungsi buat logout, hapus token biar ga bisa dipake lagi
    public function logout(Request $request): JsonResponse
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return $this->success(null, 'Logout berhasil.');
    }

    // Fungsi buat perpanjang token kalo masa berlakunya udah mau abis
    public function refresh(): JsonResponse
    {
        try {
            $newToken = auth('api')->refresh();

            return $this->success([
                'token'      => $newToken,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60,
            ], 'Token berhasil diperbarui.');

        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return $this->error('Sesi berakhir. Silakan login kembali.', null, 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return $this->error('Token tidak valid.', null, 401);
        }
    }

    // Fungsi buat ngambil data profil user yang lagi login sekarang
    public function me(Request $request): JsonResponse
    {
        $vendor = auth('api')->user()?->vendor()->with('user')->first();

        if (!$vendor) {
            return $this->error('Profil vendor tidak ditemukan.', null, 404);
        }

        return $this->success(new VendorResource($vendor));
    }

    // Fungsi pas user lupa password, kita kirimin email isinya token reset
    public function forgotPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
        ]);

        if ($validator->fails()) {
            return $this->error('Validasi gagal.', $validator->errors(), 422);
        }

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return $this->success(null, 'Link reset password telah dikirim ke email Anda.');
        }

        return $this->error('Gagal mengirim link reset password.', null, 422);
    }

    // Fungsi eksekusi reset password setelah user masukin token dari email
    public function resetPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return $this->error('Validasi gagal.', $validator->errors(), 422);
        }

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

    // Fungsi buat ganti password pas lagi keadaan login (bukan lupa password)
    public function changePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password' => ['required'],
            'password'         => ['required', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return $this->error('Validasi gagal.', $validator->errors(), 422);
        }

        $user = auth('api')->user();

        if (!Hash::check($request->input('current_password'), $user->password)) {
            return $this->error('Password lama tidak sesuai.', null, 422);
        }

        $user->forceFill(['password' => Hash::make($request->input('password'))])->save();

        return $this->success(null, 'Password berhasil diubah.');
    }

    // Fungsi yang dipanggil otomatis pas user nge-klik link verifikasi dari emailnya
    public function verifyEmail(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return response('Link verifikasi tidak valid atau sudah kedaluwarsa.', 403);
        }

        if ($user->hasVerifiedEmail()) {
            return response('Email Anda sudah diverifikasi sebelumnya. Silakan kembali ke aplikasi ZETA untuk Login.', 200)
                ->header('Content-Type', 'text/html; charset=UTF-8');
        }

        if ($user->markEmailAsVerified()) {
            event(new \Illuminate\Auth\Events\Verified($user));
        }

        return response('
            <div style="font-family: sans-serif; text-align: center; margin-top: 50px;">
                <h1 style="color: #4CAF50;">✅ Verifikasi Berhasil!</h1>
                <p>Email Anda berhasil diverifikasi. Silakan kembali ke aplikasi ZETA untuk Login.</p>
            </div>
        ', 200)->header('Content-Type', 'text/html; charset=UTF-8');
    }

    // Fungsi buat kirim ulang email verifikasi kalo misalkan emailnya ga masuk atau kedaluwarsa
    public function resendVerificationEmail(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
        ]);

        if ($validator->fails()) {
            return $this->error('Validasi gagal.', $validator->errors(), 422);
        }

        $user = User::where('email', $request->input('email'))->first();

        if (!$user) {
            return $this->error('Email tidak terdaftar.', null, 404);
        }

        if ($user->hasVerifiedEmail()) {
            return $this->error('Email ini sudah diverifikasi.', null, 422);
        }

        $user->sendEmailVerificationNotification();

        return $this->success(null, 'Link verifikasi telah dikirim ulang ke email Anda.');
    }
}
