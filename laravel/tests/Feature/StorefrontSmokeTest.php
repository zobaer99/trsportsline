<?php

namespace Tests\Feature;

use Tests\TestCase;

class StorefrontSmokeTest extends TestCase
{
    public function test_health_endpoint_is_available(): void
    {
        $this->get('/up')->assertOk();
    }

    public function test_admin_catalog_requires_sanctum_authentication(): void
    {
        $this->getJson('/api/admin/products')->assertUnauthorized();
    }

    public function test_private_capi_settings_require_the_service_token(): void
    {
        $this->getJson('/api/tracking/capi-settings')->assertForbidden();
    }

    public function test_order_tracking_rejects_invalid_bangladesh_phone_numbers(): void
    {
        $this->postJson('/api/orders/track', ['order_number' => 'TR-261118-AB12', 'phone' => '123'])->assertUnprocessable();
    }
}
