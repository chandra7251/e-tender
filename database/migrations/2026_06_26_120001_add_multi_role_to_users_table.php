<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah kolom role dari string biasa menjadi mendukung semua role
        // Tidak perlu alter jika sudah string — hanya pastikan default tetap 'vendor'
        // Tambah kolom admin_permissions (JSON) untuk granular permission per user
        Schema::table('users', function (Blueprint $table) {
            $table->json('admin_permissions')->nullable()->after('role');
            $table->string('department')->nullable()->after('admin_permissions');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['admin_permissions', 'department']);
        });
    }
};
