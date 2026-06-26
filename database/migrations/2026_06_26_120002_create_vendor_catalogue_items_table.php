<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalogue_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->timestamps();
            $table->foreign('parent_id')->references('id')->on('catalogue_categories')->nullOnDelete();
        });

        Schema::create('vendor_catalogue_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('catalogue_categories')->nullOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price_estimate', 18, 2)->nullable();
            $table->string('unit')->nullable()->default('unit');
            $table->json('specs')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('vendor_catalogue_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catalogue_item_id')->constrained('vendor_catalogue_items')->cascadeOnDelete();
            $table->string('photo_path');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });

        // Seed default categories
        \DB::table('catalogue_categories')->insert([
            ['name' => 'Elektronik & IT', 'slug' => 'elektronik-it', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Konstruksi & Bangunan', 'slug' => 'konstruksi-bangunan', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kendaraan & Transportasi', 'slug' => 'kendaraan-transportasi', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Peralatan & Mesin', 'slug' => 'peralatan-mesin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Jasa & Konsultansi', 'slug' => 'jasa-konsultansi', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Furnitur & Interior', 'slug' => 'furnitur-interior', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ATK & Perlengkapan Kantor', 'slug' => 'atk-kantor', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Lainnya', 'slug' => 'lainnya', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_catalogue_photos');
        Schema::dropIfExists('vendor_catalogue_items');
        Schema::dropIfExists('catalogue_categories');
    }
};
