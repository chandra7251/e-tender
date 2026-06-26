<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\InstansiSetting;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index(): View
    {
        $settings = InstansiSetting::allAsArray();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'instansi_name'         => 'nullable|string|max:255',
            'instansi_tagline'      => 'nullable|string|max:255',
            'instansi_address'      => 'nullable|string|max:500',
            'instansi_phone'        => 'nullable|string|max:50',
            'instansi_email'        => 'nullable|email|max:255',
            'document_header'       => 'nullable|string|max:255',
            'document_footer'       => 'nullable|string|max:500',
            'sanggahan_window_days' => 'nullable|integer|min:1|max:30',
            'primary_color'         => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
            'enable_2fa'            => 'nullable|in:true,false',
        ]);

        $textFields = [
            'instansi_name', 'instansi_tagline', 'instansi_address',
            'instansi_phone', 'instansi_email', 'document_header',
            'document_footer', 'sanggahan_window_days', 'primary_color',
        ];

        foreach ($textFields as $key) {
            if ($request->has($key)) {
                InstansiSetting::set($key, $request->input($key));
            }
        }

        // Checkbox: jika tidak ada di request berarti false
        InstansiSetting::set('enable_2fa', $request->input('enable_2fa', 'false'));

        return redirect()->route('admin.settings.index')
            ->with('success', 'Pengaturan berhasil disimpan.');
    }

    public function uploadLogo(Request $request): RedirectResponse
    {
        $request->validate([
            'logo' => 'required|image|mimes:png,jpg,jpeg,svg|max:2048',
        ]);

        $path = $request->file('logo')->store('instansi', 'public');
        InstansiSetting::set('instansi_logo', Storage::url($path));

        return redirect()->route('admin.settings.index')
            ->with('success', 'Logo berhasil diupload.');
    }
}
