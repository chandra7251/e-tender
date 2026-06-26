<?php
namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admins = [
            [
                'name'     => 'Super Admin ZETA',
                'email'    => 'superadmin@vandrafcy.my.id',
                'password' => Hash::make('SuperAdmin@2026'),
                'role'     => 'super_admin',
            ],
            [
                'name'     => 'Admin Utama',
                'email'    => 'admin@vandrafcy.my.id',
                'password' => Hash::make('rahasia'),
                'role'     => 'admin',
            ],
            [
                'name'     => 'Procurement Manager',
                'email'    => 'procurement@vandrafcy.my.id',
                'password' => Hash::make('Procurement@2026'),
                'role'     => 'procurement_manager',
            ],
            [
                'name'     => 'Evaluator Tender',
                'email'    => 'evaluator@vandrafcy.my.id',
                'password' => Hash::make('Evaluator@2026'),
                'role'     => 'evaluator',
            ],
            [
                'name'     => 'Verifikator Dokumen',
                'email'    => 'verifikator@vandrafcy.my.id',
                'password' => Hash::make('Verifikator@2026'),
                'role'     => 'verifikator',
            ],
            [
                'name'     => 'Auditor ZETA',
                'email'    => 'auditor@vandrafcy.my.id',
                'password' => Hash::make('Auditor@2026'),
                'role'     => 'auditor',
            ],
        ];

        foreach ($admins as $adminData) {
            User::updateOrCreate(
                ['email' => $adminData['email']],
                array_merge($adminData, ['email_verified_at' => now()])
            );
        }

        $this->command->info('✅ Semua akun admin berhasil dibuat.');
    }
}
