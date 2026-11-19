<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CustomerStorefrontTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_requires_a_valid_phone_number_and_full_address(): void
    {
        $response = $this->postJson('/api/orders', [
            'customer' => ['name' => 'Test buyer', 'phone' => '', 'district' => 'Dhaka', 'address' => ''],
            'delivery_zone' => 'Inside Dhaka',
            'payment_method' => 'Cash on Delivery',
            'items' => [],
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors(['customer.phone', 'customer.address']);
    }

    public function test_guest_checkout_creates_a_customer_account_and_returns_its_access_code(): void
    {
        $this->seed();
        $response = $this->postJson('/api/orders', [
            'customer' => ['name' => 'Test buyer', 'phone' => '01712345678', 'district' => 'Dhaka', 'address' => '12 Example Road'],
            'delivery_zone' => 'Inside Dhaka',
            'payment_method' => 'Cash on Delivery',
            'items' => [['product_slug' => 'legacy-cross-kit', 'size' => 'M', 'edition' => 'Fan Edition', 'quantity' => 1]],
        ]);

        $response->assertCreated()->assertJsonStructure(['data' => ['customer_id'], 'guest_access_code']);
        $this->assertNotNull($response->json('data.customer_id'));
        $this->assertMatchesRegularExpression('/^[A-Z0-9]{10}$/', $response->json('guest_access_code'));
        $this->assertDatabaseHas('customers', ['phone' => '01712345678', 'is_guest' => true]);

        $login = $this->postJson('/api/account/guest-login', ['phone' => '01712345678', 'access_code' => $response->json('guest_access_code')]);
        $login->assertOk()->assertJsonPath('data.is_guest', true);
        $this->withToken($login->json('token'))->getJson('/api/account/orders')->assertOk()->assertJsonCount(1, 'data');
    }

    public function test_guest_customer_can_upgrade_to_a_password_account(): void
    {
        $customer = Customer::create(['name' => 'Guest buyer', 'phone' => '01712345678', 'is_guest' => true]);

        $this->postJson('/api/account/register', ['name' => 'Guest buyer', 'phone' => '01712345678', 'password' => 'a-strong-password', 'password_confirmation' => 'a-strong-password'])
            ->assertOk()->assertJsonPath('data.is_guest', false);

        $this->assertDatabaseHas('customers', ['phone' => '01712345678', 'is_guest' => false]);
    }

    public function test_customer_token_cannot_access_admin_routes(): void
    {
        $customer = Customer::create(['name' => 'Customer', 'phone' => '01712345678', 'is_guest' => true]);
        $token = $customer->createToken('customer-dashboard', ['customer'])->plainTextToken;

        $this->withToken($token)->getJson('/api/admin/products')->assertForbidden();
    }

    public function test_scoped_admin_token_can_access_admin_routes(): void
    {
        $admin = User::create(['name' => 'Store admin', 'email' => 'admin@example.com', 'password' => 'a-strong-password']);
        $token = $admin->createToken('admin-dashboard', ['admin'])->plainTextToken;

        $this->withToken($token)->getJson('/api/admin/products')->assertOk();
    }

    public function test_admin_media_upload_returns_a_single_public_storage_url(): void
    {
        Storage::fake('public', ['url' => 'https://api.trsportsline.com/storage']);

        $admin = User::create(['name' => 'Store admin', 'email' => 'admin@example.com', 'password' => 'a-strong-password']);
        $token = $admin->createToken('admin-dashboard', ['admin'])->plainTextToken;

        $response = $this->withToken($token)->postJson('/api/admin/media', [
            'file' => UploadedFile::fake()->image('jersey.jpg'),
        ]);
        $path = $response->json('data.path');

        $response->assertCreated()
            ->assertJsonPath('data.url', 'https://api.trsportsline.com/storage/'.$path);
        Storage::disk('public')->assertExists($path);
    }

    public function test_storefront_product_exposes_front_back_and_gallery_images(): void
    {
        Product::create([
            'slug' => 'gallery-jersey',
            'name' => 'Gallery Jersey',
            'category' => 'Fan Edition',
            'price' => 28,
            'image_url' => 'https://api.trsportsline.com/storage/front.jpg',
            'back_image_url' => 'https://api.trsportsline.com/storage/back.jpg',
            'gallery_images' => ['https://api.trsportsline.com/storage/detail.jpg'],
            'variants' => ['sizes' => ['M', 'L']],
            'stock' => 10,
        ]);

        $this->getJson('/api/products/gallery-jersey')
            ->assertOk()
            ->assertJsonPath('data.back_image_url', 'https://api.trsportsline.com/storage/back.jpg')
            ->assertJsonPath('data.gallery_images.0', 'https://api.trsportsline.com/storage/detail.jpg');
    }
}
