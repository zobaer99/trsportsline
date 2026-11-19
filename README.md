# TR Sports Line

Mobile-first Bangladesh sportswear storefront built with Next.js 15, TypeScript, Tailwind CSS, shadcn/ui primitives, Zustand and a Laravel 12 REST API.

## Frontend

```powershell
npm.cmd install
Copy-Item .env.example .env.local
npm.cmd run dev
```

Set `NEXT_PUBLIC_SITE_URL` and `NEXT_PUBLIC_API_URL` in `.env.local`. When Laravel is unavailable the storefront uses its bundled sample catalog and delivery charges.

## Laravel 12 API

```powershell
Set-Location laravel
composer install
Copy-Item .env.example .env
php artisan key:generate
# Create the MySQL database named in DB_DATABASE, then:
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Use the same long random value for `TRACKING_SETTINGS_SERVICE_TOKEN` in the frontend and Laravel `.env` files. The frontend calls Laravel at `NEXT_PUBLIC_API_URL`; the private Meta settings handoff calls `TRACKING_SETTINGS_URL` with that service token.

Create an admin dashboard login from the Laravel directory:

```powershell
php artisan admin:create-user
```

Open the dashboard at `http://localhost:3000/admin` after starting Next.js and Laravel. Dashboard login issues a scoped Sanctum token. Admin APIs cover products/variants/stock, orders, pricing tables, page sections, media uploads, banners, coupons, reviews, delivery charges, and store/tracking settings. Product variants store available sizes in `variants.sizes`.

## Storefront/API routes

- `GET /api/products`, `GET /api/products/{slug}`, `GET /api/reviews`, `GET /api/pricing`
- `GET /api/banners`, `GET /api/delivery-zones`, `GET /api/settings/storefront`, `GET /api/settings/tracking`
- `GET /api/content/{home|product}`, `POST /api/account/register|login|guest-login`
- `GET|PUT /api/account/profile`, `GET /api/account/orders`
- `POST /api/orders`, `POST /api/orders/track`, `POST /api/custom-orders`
- `/api/admin/*` routes require a scoped admin Sanctum token; customer tokens cannot access the admin endpoints.
- `GET /api/tracking/capi-settings` is private and only accepts the configured service bearer token.

## Tracking and checkout configuration

Set `FACEBOOK_GRAPH_VERSION`, `NEXT_PUBLIC_FACEBOOK_PIXEL_ID`, `NEXT_PUBLIC_GTM_ID`, `FB_CAPI_ACCESS_TOKEN`, and optionally `FB_TEST_EVENT_CODE`; or save the Pixel ID, CAPI token, test code, GTM ID, and global SEO fields in admin settings. Browser events and server events use the same `event_id`. Meta's Graph API version defaults to `v27.0` and remains environment-configurable.

Bangladesh checkout validates `01[3-9]XXXXXXXX`, loads delivery charges from Laravel, and requires bKash/Nagad delivery-charge advance on custom name/number orders while that rule is enabled. Wallet checkout asks for a transaction ID and marks it pending review for admin verification. SSLCommerz checkout is shown as unavailable until its gateway and callback are integrated. Courier name and tracking details can be managed in admin; Steadfast/Pathao booking adapters still need provider accounts, credentials, and implementation.

## SEO

Product metadata and JSON-LD are rendered server-side. `/sitemap.xml` refreshes product slugs from Laravel with sample-catalog fallback; `/robots.txt` excludes checkout and API routes.
