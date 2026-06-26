<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminUserController extends Controller
{
    // Role hierarchy definitions
    public const ADMIN_ROLES = [
        'super_admin'         => ['label' => 'Super Admin',          'color' => 'bg-red-100 text-red-700',     'desc' => 'Akses penuh semua fitur'],
        'admin'               => ['label' => 'Admin',                'color' => 'bg-purple-100 text-purple-700','desc' => 'Admin utama sistem'],
        'procurement_manager' => ['label' => 'Procurement Manager',  'color' => 'bg-blue-100 text-blue-700',   'desc' => 'Kelola tender & pilih pemenang'],
        'evaluator'           => ['label' => 'Evaluator',            'color' => 'bg-yellow-100 text-yellow-700','desc' => 'Nilai teknis & harga penawaran'],
        'verifikator'         => ['label' => 'Verifikator',          'color' => 'bg-green-100 text-green-700', 'desc' => 'Verifikasi dokumen vendor'],
        'auditor'             => ['label' => 'Auditor / Viewer',     'color' => 'bg-gray-100 text-gray-700',   'desc' => 'Lihat semua, tidak bisa aksi'],
    ];

    public const ROLE_PERMISSIONS = [
        'super_admin'         => ['*'],
        'admin'               => ['dashboard','vendors','tenders','submissions','complaints','contracts','reports','audit-logs','settings'],
        'procurement_manager' => ['dashboard','tenders','complaints','contracts','reports'],
        'evaluator'           => ['dashboard','tenders.evaluations','tenders.ranking','tenders.envelope'],
        'verifikator'         => ['dashboard','vendors','submissions'],
        'auditor'             => ['dashboard','tenders.view','vendors.view','reports.view'],
    ];

    public function index(): View
    {
        $users = User::whereIn('role', array_keys(self::ADMIN_ROLES))
            ->latest()->paginate(20);
        $roles = self::ADMIN_ROLES;
        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create(): View
    {
        $roles = self::ADMIN_ROLES;
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'       => 'required|string|max:100',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|string|min:8|confirmed',
            'role'       => 'required|in:' . implode(',', array_keys(self::ADMIN_ROLES)),
            'department' => 'nullable|string|max:100',
        ]);

        User::create([
            'name'                  => $data['name'],
            'email'                 => $data['email'],
            'password'              => Hash::make($data['password']),
            'role'                  => $data['role'],
            'department'            => $data['department'] ?? null,
            'email_verified_at'     => now(),
            'admin_permissions'     => self::ROLE_PERMISSIONS[$data['role']] ?? [],
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User admin berhasil dibuat.');
    }

    public function edit(User $user): View
    {
        $roles = self::ADMIN_ROLES;
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name'       => 'required|string|max:100',
            'role'       => 'required|in:' . implode(',', array_keys(self::ADMIN_ROLES)),
            'department' => 'nullable|string|max:100',
            'password'   => 'nullable|string|min:8|confirmed',
        ]);

        $update = [
            'name'              => $data['name'],
            'role'              => $data['role'],
            'department'        => $data['department'] ?? null,
            'admin_permissions' => self::ROLE_PERMISSIONS[$data['role']] ?? [],
        ];

        if (!empty($data['password'])) {
            $update['password'] = Hash::make($data['password']);
        }

        $user->update($update);

        return redirect()->route('admin.users.index')
            ->with('success', 'User admin berhasil diupdate.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa hapus akun sendiri.');
        }
        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', 'User admin berhasil dihapus.');
    }
}
