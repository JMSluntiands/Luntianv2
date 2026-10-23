<?php

namespace App\Http\Controllers;

use App\Support\ClientLogos;
use App\Support\LogoTrimmer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ClientLogoController extends Controller
{
    public function index()
    {
        $logos = ClientLogos::all();
        $clients = [];
        foreach (ClientLogos::modules() as $key => $label) {
            $clients[] = [
                'key' => $key,
                'label' => $label,
                'url' => ClientLogos::url($key),
                'updated_at' => isset($logos[$key]) ? $logos[$key]->updated_at : null,
            ];
        }

        return view('settings.logo-management', [
            'sidebar_active' => 'settings.logo_management',
            'clients' => $clients,
        ]);
    }

    public function store(Request $request, string $module)
    {
        abort_unless(array_key_exists($module, ClientLogos::modules()), 404);

        $request->validate([
            'logo' => ['required', 'file', 'mimes:png,jpg,jpeg,webp,gif', 'max:2048'],
        ], [
            'logo.mimes' => 'Logo must be a PNG, JPG, WEBP, or GIF image.',
            'logo.max' => 'Logo must be 2 MB or smaller.',
        ]);

        $path = $this->trimmedLogoPath($request->file('logo')->store('client-logos', 'public'));
        $existing = DB::table('client_logos')->where('module_key', $module)->first();
        $now = now('Asia/Manila');

        if ($existing) {
            DB::table('client_logos')->where('id', $existing->id)->update([
                'logo_path' => $path,
                'updated_at' => $now,
            ]);
            Storage::disk('public')->delete($existing->logo_path);
        } else {
            DB::table('client_logos')->insert([
                'module_key' => $module,
                'logo_path' => $path,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
        ClientLogos::forget();

        $label = ClientLogos::modules()[$module];

        return redirect()
            ->route('settings.logo_management')
            ->with('success', $existing ? "{$label} logo updated." : "{$label} logo added.");
    }

    public function destroy(string $module)
    {
        abort_unless(array_key_exists($module, ClientLogos::modules()), 404);

        $existing = DB::table('client_logos')->where('module_key', $module)->first();
        if ($existing) {
            DB::table('client_logos')->where('id', $existing->id)->delete();
            Storage::disk('public')->delete($existing->logo_path);
            ClientLogos::forget();
        }

        return redirect()
            ->route('settings.logo_management')
            ->with('success', ClientLogos::modules()[$module].' logo removed. The default icon is shown again.');
    }

    private function trimmedLogoPath(string $path): string
    {
        $disk = Storage::disk('public');
        if (! LogoTrimmer::trimToPng($disk->path($path))) {
            return $path;
        }

        if (strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'png') {
            return $path;
        }

        $pngPath = preg_replace('/\.[^.\/]+$/', '', $path).'.png';
        $disk->move($path, $pngPath);

        return $pngPath;
    }

    public function image(string $module)
    {
        $logo = DB::table('client_logos')->where('module_key', $module)->first();
        if (! $logo || ! Storage::disk('public')->exists($logo->logo_path)) {
            abort(404);
        }

        return Storage::disk('public')->response($logo->logo_path, null, [
            'Cache-Control' => 'private, max-age=31536000',
        ]);
    }
}
