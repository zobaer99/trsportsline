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

## Deploying to cPanel

The frontend needs a cPanel Node.js application. In **Setup Node.js App**, create a production app with Node.js 22, application root `trsportsline.axeocon.com/trsportsline`, application URL `trsportsline.axeocon.com`, and startup file `server.js`. Use the HTTPS URL for the site after SSL is enabled. The app root is relative to the cPanel home directory.

The shell may not expose `node` or `npm` until the Node.js app exists. After creating it, copy the virtual-environment activation command shown on the cPanel app page, run it in Terminal, then from the application root run:

```bash
npm install --include=dev
npm run build
```

Set `NEXT_PUBLIC_SITE_URL` and `NEXT_PUBLIC_API_URL` before building. For this domain, use `https://trsportsline.axeocon.com` for the site URL and, if Laravel is hosted on its own subdomain, `https://api.trsportsline.axeocon.com/api` for the API URL. Add the private tracking variables in the cPanel app environment; keep them out of Git and never prefix them with `NEXT_PUBLIC_`. Restart the Node.js app after the build.

For Laravel, create an API subdomain whose document root points to the repository's `laravel/public` directory. Create a MySQL database and user in cPanel, put their values in `laravel/.env` on the server, set `APP_URL` to the API subdomain and `FRONTEND_URL` to `https://trsportsline.axeocon.com`, then install PHP dependencies and initialize the app:

```bash
cd ~/trsportsline.axeocon.com/trsportsline/laravel
php composer.phar install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan config:cache
```

If Composer is not installed, install a project-local `composer.phar` in the Laravel directory using Namecheap's shared-hosting Composer instructions. PHP 8.2 is available in the current cPanel shell; select PHP 8.2 or newer and enable Laravel's required extensions in **Select PHP Version**. Never upload production `.env` files or database credentials to GitHub.
