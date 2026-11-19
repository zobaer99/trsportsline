<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerReview;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    public function index(): JsonResponse { return response()->json(CustomerReview::latest()->paginate(50)); }
    public function store(Request $request): JsonResponse { return response()->json(['data' => CustomerReview::create($this->validated($request))], 201); }
    public function show(CustomerReview $review): JsonResponse { return response()->json(['data' => $review]); }
    public function update(Request $request, CustomerReview $review): JsonResponse { $review->update($this->validated($request, true)); return response()->json(['data' => $review->fresh()]); }
    public function destroy(CustomerReview $review): JsonResponse { $review->delete(); return response()->json(['message' => 'Review deleted']); }
    private function validated(Request $request, bool $partial = false): array { return $request->validate(['customer_name' => [$partial ? 'sometimes' : 'required','string','max:120'],'district' => [$partial ? 'sometimes' : 'required','string','max:80'],'review' => [$partial ? 'sometimes' : 'required','string','max:2000'],'photo_url' => ['nullable','url','max:2048'],'rating' => ['sometimes','integer','between:1,5'],'is_verified' => ['sometimes','boolean'],'is_published' => ['sometimes','boolean']]); }
}
