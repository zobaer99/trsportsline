<?php

namespace App\Http\Controllers;

use App\Models\DeliveryZone;
use App\Models\HeroBanner;
use App\Models\StoreSetting;
use App\Models\StoreSection;
use Illuminate\Http\JsonResponse;

class StorefrontController extends Controller
{
    public function deliveryZones(): JsonResponse { return response()->json(['data' => DeliveryZone::where('is_active', true)->orderBy('id')->get(['name','charge','is_active'])])->header('Cache-Control', 'public, max-age=300'); }
    public function banners(): JsonResponse { return response()->json(['data' => HeroBanner::where('is_active', true)->orderBy('sort_order')->get()])->header('Cache-Control', 'public, max-age=300'); }
    public function sections(string $page): JsonResponse
    {
        abort_unless(in_array($page, ['home', 'product'], true), 404);
        return response()->json(['data' => StoreSection::where('page', $page)->where('is_active', true)->orderBy('sort_order')->get()])->header('Cache-Control', 'public, max-age=60');
    }
    public function settings(): JsonResponse
    {
        $values = StoreSetting::pluck('value', 'key');
        return response()->json(['data' => [
            'partial_advance_enabled' => ($values['partial_advance_enabled'] ?? '1') !== '0',
            'customization_fee' => (int) ($values['customization_fee'] ?? 3),
            'edition_surcharge' => (int) ($values['edition_surcharge'] ?? 5),
            'patch_fee' => (int) ($values['patch_fee'] ?? 2),
            'bkash_merchant_number' => $values['bkash_merchant_number'] ?? null,
            'nagad_merchant_number' => $values['nagad_merchant_number'] ?? null,
            'sslcommerz_enabled' => false,
            'site_title' => $values['seo_site_title'] ?? null,
            'site_description' => $values['seo_site_description'] ?? null,
            'site_keywords' => $values['seo_site_keywords'] ?? null,
            'product_page_badge' => $values['product_page_badge'] ?? 'Customize your match-day kit',
            'product_page_delivery_note' => $values['product_page_delivery_note'] ?? 'Delivery across Bangladesh in 2–4 days.',
            'product_page_size_note' => $values['product_page_size_note'] ?? 'Measurements are garment measurements in inches.',
            'footer_logo_url' => $values['footer_logo_url'] ?? '/brand/tr-crest.webp',
            'footer_tagline' => $values['footer_tagline'] ?? 'Built for the match. Made for your team.',
            'footer_address' => $values['footer_address'] ?? 'Dhaka, Bangladesh · Delivery nationwide',
            'footer_phone' => $values['footer_phone'] ?? '',
            'footer_email' => $values['footer_email'] ?? '',
            'footer_whatsapp' => $values['footer_whatsapp'] ?? '8801700000000',
            'footer_instagram' => $values['footer_instagram'] ?? 'https://instagram.com',
            'footer_facebook' => $values['footer_facebook'] ?? 'https://facebook.com',
        ]])->header('Cache-Control', 'public, max-age=60');
    }
}
