<?php

namespace App\Http\Controllers;

use App\Models\DeliveryZone;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\StoreSetting;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'customer.name' => ['required','string','max:120'], 'customer.phone' => ['required','regex:/^01[3-9][0-9]{8}$/'], 'customer.district' => ['required','string','max:80'], 'customer.address' => ['required','string','max:1000'],
            'delivery_zone' => ['required','string','exists:delivery_zones,name'], 'payment_method' => ['required','in:Cash on Delivery,bKash,Nagad,SSLCommerz'], 'payment_reference' => ['nullable','string','max:80'],
            'items' => ['required','array','min:1','max:30'], 'items.*.product_slug' => ['required','string','exists:products,slug'], 'items.*.size' => ['required','in:S,M,L,XL,XXL'], 'items.*.edition' => ['required','in:Fan Edition,Player Edition'],
            'items.*.custom_name' => ['nullable','string','regex:/^[a-zA-Z ]{1,14}$/'], 'items.*.custom_number' => ['nullable','digits_between:1,2'], 'items.*.patches' => ['nullable','array','max:3'], 'items.*.patches.*' => ['in:Champions League,Premier League,World Cup'], 'items.*.quantity' => ['required','integer','min:1','max:20'],
        ]);
        return DB::transaction(function () use ($data): JsonResponse {
            $zone = DeliveryZone::where('name', $data['delivery_zone'])->where('is_active', true)->firstOrFail();
            $fees = StoreSetting::whereIn('key', ['customization_fee','edition_surcharge','patch_fee'])->pluck('value','key');
            $customizationFee = (int) ($fees['customization_fee'] ?? 3); $editionSurcharge = (int) ($fees['edition_surcharge'] ?? 5); $patchFee = (int) ($fees['patch_fee'] ?? 2);
            $prepared = []; $subtotal = 0; $hasCustom = false;
            foreach ($data['items'] as $item) {
                $product = Product::where('slug', $item['product_slug'])->where('is_active', true)->lockForUpdate()->firstOrFail();
                if ($product->stock < $item['quantity']) throw ValidationException::withMessages(['items' => "{$product->name} is not available in that quantity."]);
                $variantSizes = data_get($product->variants, 'sizes', ['S','M','L','XL','XXL']);
                if (!in_array($item['size'], $variantSizes, true)) throw ValidationException::withMessages(['items' => "Size {$item['size']} is unavailable for {$product->name}."]);
                $patches = array_values(array_unique($item['patches'] ?? [])); $custom = !empty($item['custom_name']) || !empty($item['custom_number']); $hasCustom = $hasCustom || $custom;
                $unit = $product->price + ($item['edition'] === $product->edition ? 0 : ($item['edition'] === 'Player Edition' ? $editionSurcharge : -$editionSurcharge)) + ($custom ? $customizationFee : 0) + count($patches) * $patchFee;
                $unit = max(0, $unit); $line = $unit * $item['quantity']; $subtotal += $line;
                $prepared[] = [$product, $item, $patches, $unit, $line];
            }
            $advanceEnabled = StoreSetting::where('key', 'partial_advance_enabled')->value('value') !== '0';
            $advance = $hasCustom && $advanceEnabled ? (int) $zone->charge : 0;
            if ($hasCustom && $advanceEnabled && !in_array($data['payment_method'], ['bKash','Nagad'], true)) throw ValidationException::withMessages(['payment_method' => 'Custom print orders require delivery charge advance via bKash or Nagad.']);
            if (in_array($data['payment_method'], ['bKash','Nagad'], true) && empty($data['payment_reference'])) throw ValidationException::withMessages(['payment_reference' => 'Enter the transaction ID for your mobile payment.']);
            if ($data['payment_method'] === 'SSLCommerz') throw ValidationException::withMessages(['payment_method' => 'SSLCommerz is not configured yet. Select Cash on Delivery, bKash, or Nagad.']);
            $customer = Customer::firstOrNew(['phone' => $data['customer']['phone']]);
            $customer->name = $data['customer']['name'];
            $customer->district = $data['customer']['district'];
            $customer->address = $data['customer']['address'];
            $guestAccessCode = null;
            if (!$customer->exists || $customer->is_guest) {
                $customer->is_guest = true;
                $guestAccessCode = strtoupper(Str::random(10));
                $customer->guest_access_token = Hash::make($guestAccessCode);
            }
            $customer->save();
            $order = Order::create(['customer_id' => $customer->id, 'order_number' => 'TR-'.now()->format('ymd').'-'.strtoupper(bin2hex(random_bytes(4))), 'customer_name' => $data['customer']['name'], 'phone' => $data['customer']['phone'], 'district' => $data['customer']['district'], 'address' => $data['customer']['address'], 'delivery_zone' => $zone->name, 'delivery_charge' => $zone->charge, 'payment_method' => $data['payment_method'], 'payment_reference' => $data['payment_reference'] ?? null, 'payment_status' => in_array($data['payment_method'], ['bKash','Nagad'], true) ? 'pending_review' : 'pending', 'advance_payment' => $advance, 'subtotal' => $subtotal, 'total' => $subtotal + $zone->charge]);
            foreach ($prepared as [$product,$item,$patches,$unit,$line]) { $order->items()->create(['product_id' => $product->id,'product_name' => $product->name,'size' => $item['size'],'edition' => $item['edition'],'custom_name' => $item['custom_name'] ?? null,'custom_number' => $item['custom_number'] ?? null,'patches' => $patches,'quantity' => $item['quantity'],'unit_price' => $unit,'line_total' => $line]); $product->decrement('stock', $item['quantity']); }
            return response()->json(['data' => $order->load('items'), 'order_number' => $order->order_number, 'guest_access_code' => $guestAccessCode], 201);
        });
    }

    public function track(Request $request): JsonResponse
    {
        $data = $request->validate(['order_number' => ['required','string','max:40'], 'phone' => ['required','regex:/^01[3-9][0-9]{8}$/']]);
        $order = Order::where('order_number', $data['order_number'])->where('phone', $data['phone'])->first();
        if (!$order) return response()->json(['message' => 'We could not find an order matching that ID and phone number.'], 404);
        return response()->json(['data' => ['order_number' => $order->order_number,'status' => $order->status,'status_label' => str($order->status)->replace('_', ' ')->title(),'courier' => $order->courier,'tracking_code' => $order->courier_tracking_code,'tracking_url' => $order->courier_tracking_url,'created_at' => $order->created_at]]);
    }
}
