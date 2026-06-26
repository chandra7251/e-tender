<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\InstansiSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class InstansiSettingController extends Controller {
    /** GET api/settings - public (dipakai mobile untuk white-label) */
    public function publicSettings(): JsonResponse {
        $keys = ['instansi_name','instansi_tagline','instansi_address','instansi_phone','instansi_email','instansi_logo','primary_color','sanggahan_window_days'];
        $settings = InstansiSetting::whereIn('key',$keys)->get()->mapWithKeys(fn($s)=>[$s->key=>$s->value]);
        return $this->success($settings);
    }
    /** GET api/admin/settings - semua settings */
    public function index(): JsonResponse {
        return $this->success(InstansiSetting::all()->mapWithKeys(fn($s)=>[$s->key=>['value'=>$s->value,'type'=>$s->type]]));
    }
    /** PUT api/admin/settings */
    public function update(Request $req): JsonResponse {
        $req->validate(['settings'=>'required|array']);
        foreach ($req->settings as $key => $value) {
            InstansiSetting::set($key, $value);
        }
        return $this->success(null,'Pengaturan berhasil disimpan.');
    }
    /** POST api/admin/settings/logo */
    public function uploadLogo(Request $req): JsonResponse {
        $req->validate(['logo'=>'required|image|max:2048']);
        $path = $req->file('logo')->store('instansi','public');
        InstansiSetting::set('instansi_logo', Storage::url($path));
        return $this->success(['logo_url'=>Storage::url($path)],'Logo berhasil diperbarui.');
    }
}
