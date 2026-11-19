<?php

namespace App\Http\Controllers;

use App\Models\StoreSetting;
use Illuminate\Http\JsonResponse;

class TrackingSettingsController extends Controller
{
    public function publicSettings(): JsonResponse
    {
        $values = StoreSetting::whereIn('key', ['facebook_pixel_id','gtm_id'])->pluck('value','key');
        return response()->json(['data' => ['pixel_id' => $values['facebook_pixel_id'] ?? null,'gtm_id' => $values['gtm_id'] ?? null]])->header('Cache-Control', 'public, max-age=300');
    }

    public function capiSettings(): JsonResponse
    {
        $values = StoreSetting::whereIn('key', ['facebook_pixel_id','facebook_capi_access_token','facebook_test_event_code'])->pluck('value','key');
        return response()->json(['pixel_id' => $values['facebook_pixel_id'] ?? null,'capi_access_token' => $values['facebook_capi_access_token'] ?? null,'test_event_code' => $values['facebook_test_event_code'] ?? null]);
    }
}
