<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('instansi_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string|boolean|json|file
            $table->timestamps();
        });
        // Seed default values
        DB::table('instansi_settings')->insert([
            ['key'=>'instansi_name','value'=>'ZETA E-Procurement','type'=>'string','created_at'=>now(),'updated_at'=>now()],
            ['key'=>'instansi_tagline','value'=>'Sistem Pengadaan Barang & Jasa Digital','type'=>'string','created_at'=>now(),'updated_at'=>now()],
            ['key'=>'instansi_address','value'=>'','type'=>'string','created_at'=>now(),'updated_at'=>now()],
            ['key'=>'instansi_phone','value'=>'','type'=>'string','created_at'=>now(),'updated_at'=>now()],
            ['key'=>'instansi_email','value'=>'','type'=>'string','created_at'=>now(),'updated_at'=>now()],
            ['key'=>'instansi_logo','value'=>null,'type'=>'file','created_at'=>now(),'updated_at'=>now()],
            ['key'=>'primary_color','value'=>'#3F51B5','type'=>'string','created_at'=>now(),'updated_at'=>now()],
            ['key'=>'document_header','value'=>'ZETA E-Procurement','type'=>'string','created_at'=>now(),'updated_at'=>now()],
            ['key'=>'document_footer','value'=>'Dokumen ini diterbitkan secara elektronik dan sah tanpa tanda tangan basah.','type'=>'string','created_at'=>now(),'updated_at'=>now()],
            ['key'=>'enable_2fa','value'=>'false','type'=>'boolean','created_at'=>now(),'updated_at'=>now()],
            ['key'=>'sanggahan_window_days','value'=>'5','type'=>'string','created_at'=>now(),'updated_at'=>now()],
        ]);
    }
    public function down(): void { Schema::dropIfExists('instansi_settings'); }
};
