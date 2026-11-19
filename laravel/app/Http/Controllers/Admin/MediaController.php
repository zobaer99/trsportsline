<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function index(): JsonResponse
    {
        $files = collect(Storage::disk('public')->files('storefront'))->map(fn ($path) => [
            'name' => basename($path),
            'path' => $path,
            'url' => Storage::disk('public')->url($path),
            'size' => Storage::disk('public')->size($path),
        ])->sortByDesc('name')->values();
        return response()->json(['data' => $files]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate(['file' => ['required','file','image','mimes:jpg,jpeg,png,webp','max:10240']]);
        $path = $data['file']->storePublicly('storefront', 'public');
        return response()->json(['data' => [
            'name' => basename($path),
            'path' => $path,
            'url' => Storage::disk('public')->url($path),
            'size' => Storage::disk('public')->size($path),
        ]], 201);
    }
}
