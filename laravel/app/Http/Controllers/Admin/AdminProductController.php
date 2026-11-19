<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminProductController extends Controller
{
    public function index(): JsonResponse { return response()->json(Product::orderByDesc('updated_at')->paginate(50)); }
    public function store(Request $request): JsonResponse { $data = $this->validateProduct($request); $product = Product::create($data); return response()->json(['data' => $product], 201); }
    public function show(Product $product): JsonResponse { return response()->json(['data' => $product]); }
    public function update(Request $request, Product $product): JsonResponse { $product->update($this->validateProduct($request, true)); return response()->json(['data' => $product->fresh()]); }
    public function destroy(Product $product): JsonResponse { $product->update(['is_active' => false]); return response()->json(['message' => 'Product archived']); }
    private function validateProduct(Request $request, bool $partial = false): array
    {
        return $request->validate(['slug' => [$partial ? 'sometimes' : 'required','string','max:180','unique:products,slug,'.($request->route('product')?->id ?? 'NULL')],'name' => [$partial ? 'sometimes' : 'required','string','max:180'],'club' => ['nullable','string','max:120'],'category' => [$partial ? 'sometimes' : 'required','string','max:80'],'description' => ['nullable','string'],'price' => [$partial ? 'sometimes' : 'required','integer','min:0'],'compare_at_price' => ['nullable','integer','min:0'],'image_url' => ['nullable','string','max:2048'],'badge' => ['nullable','string','max:60'],'edition' => ['sometimes','in:Fan Edition,Player Edition'],'variants' => ['nullable','array'],'variants.sizes' => ['nullable','array'],'variants.sizes.*' => ['in:S,M,L,XL,XXL'],'stock' => ['sometimes','integer','min:0'],'is_active' => ['sometimes','boolean'],'meta_title' => ['nullable','string','max:180'],'meta_description' => ['nullable','string','max:500'],'rating_value' => ['nullable','numeric','between:1,5'],'review_count' => ['nullable','integer','min:0']]);
    }
}
