<?php

namespace Database\Seeders;

use App\Models\DeliveryZone;
use App\Models\Product;
use App\Models\PriceTableGroup;
use App\Models\StoreSetting;
use App\Models\StoreSection;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['custom-team-kit', 'Custom Team Performance Kit', 'Made for your squad', 'Custom Team Jerseys', 'Fan Edition', 18, '/products/custom-team-kit.webp', 'FROM $18', 'Custom team jerseys start at $18 per piece for orders of 10 or more. Add your team colors, crest, names and numbers.'],
            ['legacy-cross-kit', 'TR Legacy Cross Jersey', 'TR Football Club', 'Retro Classics', 'Fan Edition', 24, '/products/legacy-cross-kit.webp', 'TR SIGNATURE', 'A crisp heritage look with a comfortable fan fit, made for the stands and the street.'],
            ['blue-heritage-kit', 'TR Blue Heritage Jersey', 'TR Football Club', 'Player Edition', 'Player Edition', 28, '/products/blue-heritage-kit.webp', 'NEW DROP', 'A vivid blue player fit with lightweight, breathable performance fabric.'],
            ['midnight-performance-kit', 'TR Midnight Performance Jersey', 'TR Cricket Club', 'Fan Edition', 'Fan Edition', 26, '/products/midnight-performance-kit.webp', 'MATCH READY', 'Deep teal performance fabric and an easy regular fit for training or match day.'],
        ];

        Product::whereNotIn('slug', array_column($products, 0))->update(['is_active' => false]);
        foreach ($products as [$slug, $name, $club, $category, $edition, $price, $image, $badge, $description]) {
            Product::updateOrCreate(['slug' => $slug], [
                'name' => $name,
                'club' => $club,
                'category' => $category,
                'description' => $description,
                'price' => $price,
                'image_url' => $image,
                'badge' => $badge,
                'edition' => $edition,
                'variants' => ['sizes' => ['S', 'M', 'L', 'XL', 'XXL']],
                'stock' => 100,
                'is_active' => true,
            ]);
        }

        foreach ([['Inside Dhaka', 3], ['Sub-Dhaka', 4], ['Outside Dhaka', 6]] as [$name, $charge]) {
            DeliveryZone::updateOrCreate(['name' => $name], ['charge' => $charge, 'is_active' => true]);
        }

        if (PriceTableGroup::count() === 0) {
            $tables = [
                ['Jersey', 'BDT', [
                    ['Plain Polyester', null, 280, null], ['Mesh', null, 300, null], ['Birdseye', null, 330, null], ['Honeycomb', null, 360, null], ['Leaf Jacquard', null, 380, null], ['China Spandex', null, 460, null], ['Loop Honeycomb', null, 500, null], ['Tiger Jacquard', null, 520, null], ['Brush Jacquard', null, 550, null], ['Nike Jacquard', null, 600, null], ['Diamond Jacquard Ultra Max', null, 750, 700],
                ], [
                    ['sleeve', 'Half Sleeve', 0], ['sleeve', 'Full Sleeve', 30], ['sleeve', 'Sleeveless', 0],
                    ['neck', 'Round Neck', 0], ['neck', 'Polo', 30], ['neck', 'V Neck', 0], ['neck', 'Cross V', 0], ['neck', 'Cross Round', 0], ['neck', 'Double V', 0], ['neck', 'V Polo', 30], ['neck', 'Vintage Polo', 30], ['neck', 'Y Neck', 0],
                ]],
                ['Short Pant', 'BDT', [
                    ['Plain Polyester', null, 250, null], ['Tricord', null, 450, null],
                ], [
                    ['addon', 'Half', 0], ['addon', '3 Quarter', 60], ['addon', 'Pocket', 50], ['addon', 'Chain', 80],
                ]],
                ['Trouser', 'BDT', [
                    ['Plain Polyester', null, 450, null], ['Tricord', null, 650, null],
                ], [
                    ['addon', 'Chain', 70],
                ]],
            ];
            foreach ($tables as $groupIndex => [$name, $currency, $entries, $addons]) {
                $group = PriceTableGroup::create(['name' => $name, 'slug' => \Illuminate\Support\Str::slug($name), 'currency' => $currency, 'note' => $name === 'Jersey' ? 'Main chart prices are for round-neck, half-sleeve jerseys. Add-ons are listed separately.' : null, 'sort_order' => $groupIndex, 'is_active' => true]);
                foreach ($entries as $index => [$fabric, $moq, $price, $offerPrice]) $group->entries()->create(['fabric' => $fabric, 'moq' => $moq, 'price' => $price, 'offer_price' => $offerPrice, 'sort_order' => $index]);
                foreach ($addons as $index => [$category, $label, $amount]) $group->addons()->create(['category' => $category, 'label' => $label, 'amount' => $amount, 'sort_order' => $index]);
            }
        }

        if (StoreSection::count() === 0) {
            $sections = [
                ['home', 'hero', 'Made for your|match day.', 'Premium football and cricket jerseys built around your colours, crest and squad.', '', '', 'Shop the lineup', '/#shop', 0],
                ['home', 'categories', 'Choose your fit.', 'Explore the TR Sports Line collections.', '', '', '', '', 10],
                ['home', 'trending', 'Trending kits.', 'The line-up fans are wearing now.', '', '', 'Shop all', '/#shop', 20],
                ['home', 'team_promo', 'Your team. Your colours. Your kit.', 'Custom names, numbers and club crests. Team jerseys from $18 each for orders of 10 or more.', '', '', 'Create your kit', '/custom-jersey', 30],
                ['home', 'new_drops', 'New drops.', 'Fresh kits, just off the press.', '', '', 'View collection', '/#shop', 40],
                ['home', 'reviews', 'Worn with pride.', 'From our stands to yours.', '', '', '', '', 50],
                ['home', 'features', 'Store benefits', '', 'Nationwide delivery|Quality checked kits|bKash · Nagad · COD|Real human support', '', '', '', 5],
                ['product', 'product_message', 'Built for the match.', 'Choose your edition, size and personal details. Your live total updates as you customize.', '', '', '', '', 0],
            ];
            foreach ($sections as [$page, $key, $title, $subtitle, $body, $image, $buttonLabel, $buttonUrl, $order]) StoreSection::create(['page' => $page, 'section_key' => $key, 'title' => $title, 'subtitle' => $subtitle, 'body' => $body, 'image_url' => $image ?: null, 'button_label' => $buttonLabel ?: null, 'button_url' => $buttonUrl ?: null, 'sort_order' => $order, 'is_active' => true]);
            foreach ([['Player Edition', '/products/blue-heritage-kit.webp', '/category/player-edition'], ['Fan Edition', '/products/midnight-performance-kit.webp', '/category/fan-edition'], ['Custom Team Jerseys', '/products/custom-team-kit.webp', '/custom-jersey'], ['Retro Classics', '/products/legacy-cross-kit.webp', '/category/retro-classics'], ['Sports Gear', '/products/midnight-performance-kit.webp', '/category/sports-gear']] as $index => [$title, $image, $url]) StoreSection::create(['page' => 'home', 'section_key' => 'category_'.$index, 'title' => $title, 'image_url' => $image, 'button_url' => $url, 'sort_order' => 60 + $index, 'is_active' => true]);
        }

        foreach ([
            ['partial_advance_enabled', '1', false], ['partial_advance_mode', 'delivery_charge', false],
            ['customization_fee', '3', false], ['edition_surcharge', '5', false], ['patch_fee', '2', false],
            ['facebook_pixel_id', '', false], ['facebook_capi_access_token', '', true], ['facebook_test_event_code', '', true],
            ['gtm_id', '', false], ['bkash_merchant_number', '', false], ['nagad_merchant_number', '', false],
            ['seo_site_title', 'TR Sports Line — Built for the Match', false],
            ['seo_site_description', 'Custom football and cricket jerseys, team kits and sportswear from $18.', false],
            ['seo_site_keywords', 'football jerseys, cricket jerseys, custom team kits, TR Sports Line', false],
            ['product_page_badge', 'Customize your match-day kit', false],
            ['product_page_delivery_note', 'Delivery across Bangladesh in 2–4 days.', false],
            ['product_page_size_note', 'Measurements are garment measurements in inches.', false],
            ['footer_tagline', 'Built for the match. Made for your team.', false],
            ['footer_logo_url', '/brand/tr-crest.webp', false],
            ['footer_address', 'Dhaka, Bangladesh · Delivery nationwide', false],
            ['footer_phone', '', false], ['footer_email', '', false],
            ['footer_whatsapp', '8801700000000', false], ['footer_instagram', 'https://instagram.com', false], ['footer_facebook', 'https://facebook.com', false],
        ] as [$key, $value, $secret]) {
            StoreSetting::updateOrCreate(['key' => $key], ['value' => $value, 'is_secret' => $secret]);
        }
    }
}
