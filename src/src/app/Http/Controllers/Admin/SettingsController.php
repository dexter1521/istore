<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('valor', 'clave')->toArray();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'nombre_negocio' => 'required|string|max:255',
            'whatsapp_numero' => 'required|string|max:20',
            'whatsapp_mensaje' => 'nullable|string',
            'moneda' => 'required|string|max:10',
        ]);

        foreach ($validated as $clave => $valor) {
            Setting::updateOrCreate(
                ['clave' => $clave],
                ['valor' => $valor]
            );
        }

        // Checkbox para mostrar precios (si no viene, guardamos 0)
        $mostrar = $request->has('mostrar_precios') ? '1' : '0';
        Setting::updateOrCreate(['clave' => 'mostrar_precios'], ['valor' => $mostrar]);

        return redirect()->route('admin.settings.index')->with('success', 'Configuración actualizada exitosamente.');
    }
}
