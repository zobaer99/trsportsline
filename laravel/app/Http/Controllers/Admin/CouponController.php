<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index(): JsonResponse { return response()->json(Coupon::latest()->paginate(50)); }
    public function store(Request $request): JsonResponse { return response()->json(['data' => Coupon::create($this->validated($request))], 201); }
    public function show(Coupon $coupon): JsonResponse { return response()->json(['data' => $coupon]); }
    public function update(Request $request, Coupon $coupon): JsonResponse { $coupon->update($this->validated($request, true)); return response()->json(['data' => $coupon->fresh()]); }
    public function destroy(Coupon $coupon): JsonResponse { $coupon->delete(); return response()->json(['message' => 'Coupon deleted']); }
    private function validated(Request $request, bool $partial = false): array { return $request->validate(['code' => [$partial ? 'sometimes' : 'required','string','max:50','unique:coupons,code,'.($request->route('coupon')?->id ?? 'NULL')],'type' => ['sometimes','in:fixed,percent'],'value' => [$partial ? 'sometimes' : 'required','integer','min:1'],'min_order' => ['sometimes','integer','min:0'],'starts_at' => ['nullable','date'],'expires_at' => ['nullable','date','after:starts_at'],'usage_limit' => ['nullable','integer','min:1'],'is_active' => ['sometimes','boolean']]); }
}
