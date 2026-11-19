<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index(): JsonResponse { return response()->json(Product::query()->where('is_active', true)->orderByDesc('created_at')->paginate(100)); }
    public function show(string $slug): JsonResponse { $product = Product::query()->where('is_active', true)->where('slug', $slug)->firstOrFail(); return response()->json(['data' => $product]); }
}
