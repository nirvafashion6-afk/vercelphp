# BHAVYA ENTERPRISE — PHP version

A PHP port of the original Next.js storefront. Pages render server-side
PHP HTML; the cart/checkout state lives in `localStorage` (same as the
original); the API endpoints are PHP serverless functions.

## Project layout

```
php-site/
├── api/                      Serverless PHP endpoints
│   ├── products.php          GET — paginated product list + recommendations
│   ├── upichange.php         GET — { upi, pixelId } for the front-end loader
│   ├── facebookPixel.php     GET/POST/PUT — pixel HTML upsert
│   ├── initiate-payment.php  POST — builds UPI/Paytm/PhonePe intents
│   ├── verify-payment.php    POST — returns transaction status
│   └── payment-notification.php  Webhook for scraped txn events
├── includes/                 PHP partials (header, footer, side menu, product card)
├── styles/globals.css        Verbatim copy of the Next.js globals
├── assets/, uploads/         Static images from public/
├── _data/                    products.json + categories.json
├── index.php                 Home (product grid)
├── category.php              Category listing
├── product.php               Product detail
├── cart.php                  Cart (client-side state)
├── checkout.php              Address form
├── order-summary.php         Cart + address review
├── payment.php               COD payment screen
├── payment-success.php       Order confirmed
├── payment-failed.php        Retry payment
├── about-us.php / contact-us.php / privacy-policy.php / ...   Static info
├── vercel.json               Vercel routes + PHP runtime config
└── .env.example              Environment variables for /api endpoints
```

## Deploying to Vercel

1. Push the `php-site/` folder to a Git repo (or use `vercel` CLI to deploy
   the folder directly).
2. In the Vercel dashboard, **Project Settings → Build & Output**:
   - Framework Preset: **Other**
   - Build Command: leave blank
   - Output Directory: leave blank
3. **Environment Variables** (Settings → Environment Variables) — copy
   from `.env.example` and fill in:
   - `PAYTM_UPI_ID`
   - `PAYTM_MERCHANT_NAME`
   - `PAYTM_MID`
   - `PAYMENT_NOTIFICATION_TOKEN`
4. Deploy. Vercel will detect `vercel.json`, install the `vercel-php@0.7.3`
   community runtime, and route every PHP file as a serverless function.

The runtime ships with PHP 8.1 and the common extensions (json, mbstring,
curl). It does NOT include the MongoDB driver, so the persistence layer
in `/api/initiate-payment`, `/api/verify-payment`, and
`/api/payment-notification` is intentionally simplified to use the
serverless `/tmp` filesystem. For real production payment tracking, point
those endpoints at a hosted DB (Upstash, PlanetScale, Neon, etc).

## Local development

If you have PHP 8.1+ installed:

```
php -S 127.0.0.1:8000 -t php-site
```

Then visit http://127.0.0.1:8000/. The built-in server will resolve
`/category` to `category.php` etc. automatically because the request
mirrors the actual filename — same as the Vercel routes table.
