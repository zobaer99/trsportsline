<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function index(Request $request): JsonResponse { $orders = Order::with('items')->latest()->paginate(50); return response()->json($orders); }
    public function update(Request $request, Order $order): JsonResponse { $data = $request->validate(['status' => ['sometimes','in:order_placed,printing,handed_to_courier,delivered,cancelled'],'payment_status' => ['sometimes','in:pending,pending_review,paid,failed'],'courier' => ['nullable','string','max:80'],'courier_tracking_code' => ['nullable','string','max:100'],'courier_tracking_url' => ['nullable','url','max:2048'],'notes' => ['nullable','string','max:4000']]); $order->update($data); return response()->json(['data' => $order->fresh()->load('items')]); }
}
