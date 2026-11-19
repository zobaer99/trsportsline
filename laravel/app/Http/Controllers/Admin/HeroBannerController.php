<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroBanner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HeroBannerController extends Controller
{
    public function index(): JsonResponse { return response()->json(HeroBanner::orderBy('sort_order')->get()); }
    public function store(Request $request): JsonResponse { return response()->json(['data' => HeroBanner::create($this->validated($request))], 201); }
    public function show(HeroBanner $banner): JsonResponse { return response()->json(['data' => $banner]); }
    public function update(Request $request, HeroBanner $banner): JsonResponse { $banner->update($this->validated($request, true)); return response()->json(['data' => $banner->fresh()]); }
    public function destroy(HeroBanner $banner): JsonResponse { $banner->delete(); return response()->json(['message' => 'Banner deleted']); }
    private function validated(Request $request, bool $partial = false): array { return $request->validate(['title' => [$partial ? 'sometimes' : 'required','string','max:180'],'subtitle' => ['nullable','string','max:500'],'desktop_image' => [$partial ? 'sometimes' : 'required','url','max:2048'],'mobile_image' => ['nullable','url','max:2048'],'button_label' => ['nullable','string','max:60'],'button_url' => ['nullable','string','max:2048'],'sort_order' => ['sometimes','integer','min:0'],'is_active' => ['sometimes','boolean']]); }
}
