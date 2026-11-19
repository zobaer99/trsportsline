<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryZone;
use App\Models\StoreSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoreSettingsController extends Controller
{
    public function index(): JsonResponse
    {
        $settings = StoreSetting::all()->mapWithKeys(fn ($setting) => [$setting->key => $setting->is_secret && $setting->value ? '********' : $setting->value]);
        return response()->json(['settings' => $settings,'delivery_zones' => DeliveryZone::orderBy('id')->get()]);
    }
    public function update(Request $request): JsonResponse
    {
        $data = $request->validate(['settings' => ['sometimes','array'],'settings.*' => ['nullable','string','max:10000'],'delivery_zones' => ['sometimes','array'],'delivery_zones.*.name' => ['required_with:delivery_zones','string','max:80'],'delivery_zones.*.charge' => ['required_with:delivery_zones','integer','min:0'],'delivery_zones.*.is_active' => ['sometimes','boolean']]);
        DB::transaction(function () use ($data) {
            foreach ($data['settings'] ?? [] as $key => $value) { $setting = StoreSetting::where('key', $key)->firstOrFail(); if ($setting->is_secret && $value === '********') continue; $setting->update(['value' => $value]); }
            foreach ($data['delivery_zones'] ?? [] as $zone) DeliveryZone::updateOrCreate(['name' => $zone['name']], ['charge' => $zone['charge'],'is_active' => $zone['is_active'] ?? true]);
        });
        return $this->index();
    }
}
