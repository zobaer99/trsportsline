<?php

namespace App\Http\Controllers;

use App\Models\CustomerReview;
use Illuminate\Http\JsonResponse;

class ReviewController extends Controller { public function index(): JsonResponse { return response()->json(['data' => CustomerReview::where('is_published', true)->latest()->limit(12)->get()])->header('Cache-Control', 'public, max-age=300'); } }
