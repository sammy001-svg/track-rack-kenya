# Tack Rack Kenya

A modern quote-request e-commerce website and admin console for **Tack Rack Limited** —
Kenya's equestrian supplier since 1997, based at the MacNaughton Business Centre on
Ngong Road, Nairobi.

Built with **HTML/CSS/JS on the front end, PHP 8 on the back end, and MySQL/MariaDB**
for data. No frameworks, no Composer, no build step.

---

## What it is

A **hybrid catalog**: quote-request for anything that needs pricing by hand, direct
purchase for anything that doesn't.

- **Quote-only items** (the default) — saddles, made-to-order rugs, anything needing a
  fitting. The customer builds a quote list; staff price it and reply. This matches how
  Tack Rack actually sells: a saddle depends on the horse, a rug on the measurement, and
  imported stock moves with freight and duty.
- **Buyable items** — mark a product `buyable` with a visible price and customers can pay
  for it immediately by M-Pesa.

One list serves both. At checkout it splits automatically: the priced items go to payment,
everything else stays for a quote request. Neither path blocks the other.

On top of the shop sit the two services that actually distinguish the business —
**saddle fitting** (booked, scheduled and tracked) and **workshop repairs** (photographed,
assessed, quoted and tracked) — plus **customer accounts** that tie a person's quotes,
orders, fittings, repairs and saved horse measurements together.

### Catalog structure

The three pillars from the brief, each with its own sub-categories:

| Pillar | Sub-categories |
|---|---|
| **Rider** | Footwear (short boots, paddock boots, chaps), Riding Jackets & Vests, Breeches & Tights, Gloves & Accessories |
| **Horse** | Saddles & Accessories, Bridles/Bits/Reins, Saddle Pads & Blankets, Halters & Lead Ropes, Horse Health & Supplements |
| **Stable** | Grooming Kits & Supplies, Stable Equipment, Leather Care & Maintenance |

---

## Requirements

- PHP **8.1+** with `pdo_mysql`, `mbstring`, `fileinfo` and `openssl`
- MySQL **5.7+** or MariaDB **10.3+**
- Apache with `mod_rewrite` (or nginx, or PHP's built-in server for development)

XAMPP, WAMP and Laragon all satisfy this out of the box.

---

## Installation

### 1. Configure

Open `config/config.php` and check the `db` block. The defaults suit a stock XAMPP
install (`root`, no password, database `tackrack`).

For anything other than local development, copy the file rather than editing it:

```bash
cp config/config.php config/config.local.php
```

`config.local.php` is merged over `config.php` and is git-ignored, so credentials never
reach the repository. In production set:

```php
'app'     => ['env' => 'production', 'debug' => false, 'url' => 'https://tackrack.co.ke'],
'session' => ['secure' => true],
'mail'    => ['enabled' => true],
```

### 2. Install the database

```bash
php bin/install.php
```

This creates the database, applies `database/schema.sql`, loads `database/seed.sql`
(30 products, the full category tree, 5 brands, 5 written pages and the site settings),
and reports what it created.

| Flag | Effect |
|---|---|
| *(none)* | Create if missing, then apply schema, seed and migrations |
| `--fresh` | **Drop** and recreate everything — destroys all data |
| `--schema` | Schema and migrations only, no demo catalog |

You can also load the SQL by hand through phpMyAdmin: run `schema.sql`, then `seed.sql`,
then each file in `database/migrations/` in filename order.

### Upgrading an existing install

`schema.sql` is the Phase 1 baseline; everything added since lives in
`database/migrations/` and is applied on top. To bring an existing database up to date
**without touching your data**:

```bash
php bin/migrate.php --status   # show what is pending
php bin/migrate.php            # apply it
```

Each migration is recorded in a `migrations` table, so re-running is safe.

### 3. Serve it

**PHP's built-in server** (quickest for development):

```bash
php -S localhost:8000 -t public bin/router.php
```

**XAMPP / Apache** — point a virtual host's `DocumentRoot` at the `public/` directory:

```apache
<VirtualHost *:80>
    ServerName tackrack.local
    DocumentRoot "C:/Track Rack Kenya/public"
    <Directory "C:/Track Rack Kenya/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Then add `127.0.0.1  tackrack.local` to your hosts file.

> **Only `public/` should be web-accessible.** If you must drop the project inside
> `htdocs`, the application still works from `/Track Rack Kenya/public/`, but `app/`,
> `config/` and `database/` would then also be reachable over HTTP. Use a virtual host
> on any real deployment.

### 4. Sign in

    /admin/login
    admin@tackrack.co.ke
    TackRack@2026

**Change that password immediately** under *Admin → My account*.

---

## Layout

```
app/
  bootstrap.php          Paths, autoloader, error handling, session, DB
  Core/                  Database, Router, Controller, Model, Csrf, Session,
                         Validator, Uploader, ImageProcessor, QuoteList,
                         Auth (staff), CustomerAuth (storefront),
                         Mailer, Smtp, Mpesa, helpers
  Controllers/           Public: Home, Shop, Product, Quote, Checkout,
                         Service, Account, Contact, Page, Seo
  Controllers/Admin/     Dashboard, Product, Category, Brand, Quote, Order,
                         Booking, Repair, Customer, Service, Message, Page,
                         Setting, User, Import
  Models/                Product, Category, Brand, Quote, Order, Payment,
                         Booking, RepairRequest, Customer, Service,
                         Message, Page, Setting, User
  Views/
    layouts/             site.php, admin.php, blank.php
    partials/            header, footer, product-card, account-nav,
                         admin-pagination
    site/                Public pages
    admin/               Admin screens
config/
  config.php             App configuration (config.local.php overrides it)
database/
  schema.sql             Phase 1 baseline — tables, keys, constraints
  seed.sql               Catalog, pages, settings, admin user
  migrations/            Additive changes applied on top of the baseline
public/                  >>> the web root <<<
  index.php              Front controller — every route is declared here
  .htaccess              Rewrites, caching, security headers
  uploads/.htaccess      Refuses to execute anything in the upload directory
  assets/css|js|img      main.css + account.css, admin.css, JS, SVG artwork
  uploads/               Uploaded images (products, brands, categories,
                         services, repairs, site)
storage/logs/            PHP, mail and M-Pesa logs
bin/
  install.php            Installer (schema + seed + migrations)
  migrate.php            Migration runner for existing installs
  router.php             Router for PHP's built-in server
```

Routes live in one place: **`public/index.php`**.

---

## The admin console

| Screen | What it does |
|---|---|
| **Dashboard** | Open quote requests, 14-day request chart, pipeline by status, most-requested and most-viewed products, plus a "needs attention" panel for products missing photographs and empty categories |
| **Quote requests** | Filter by status, open a request, set status and quoted total, keep internal notes, print a quote sheet, reply by email or WhatsApp |
| **Messages** | Contact-form inbox with read/unread state |
| **Products** | Full CRUD, multi-image galleries with a selectable primary image, size/colour options, stock status, optional visible pricing, featured and new flags, per-product SEO |
| **Categories** | Manage the pillar → sub-category tree, taglines, images, ordering, visibility |
| **Brands** | Logos and descriptions for the homepage brand wall and the shop filter |
| **Pages** | Edit Heritage, How to Order, Quote Process, Privacy and Terms |
| **Orders** | Filter by status and payment state, see cleared vs outstanding revenue, record bank or cash payments taken off-platform, print a receipt, email the customer on dispatch |
| **Saddle fittings** | Booking pipeline with an "coming up" view, confirm and schedule a date, set a fee, keep private notes, email the customer on confirmation |
| **Workshop repairs** | Assess damage from customer photographs, add your own, quote, track through the workshop, email at each stage |
| **Customers** | Registered accounts with their full history and saved horse measurements; disable or delete an account without losing its business records |
| **Services** | Edit the copy behind the saddle fitting and repairs pages |
| **Import & export** | Bulk CSV product import with a dry-run mode, plus CSV export of products, quotes and orders |
| **Settings** | Contact details, opening hours, social links, WhatsApp number, quote notifications, delivery pricing, M-Pesa credentials and SMTP |
| **Staff accounts** | Add users, set Administrator or Manager role, reset passwords, disable accounts |

Two roles: **Administrator** (everything, including settings and accounts) and
**Manager** (catalog, quotes, orders, services, messages, pages).

---

## Services

**Saddle fitting** (`/services/saddle-fitting`) — a booking form that captures the horse,
the discipline, the current saddle, whether we travel to the yard, and preferred dates.
Signed-in customers get one-click fill from their saved horses. Staff confirm a date in the
admin, which emails the customer; the booking then appears on their account.

**Workshop repairs** (`/services/repairs`) — a request form taking up to six photographs of
the damage, the item type and urgency. Staff assess, add their own photographs, quote a
figure and move it through *assessing → quoted → approved → in the workshop → ready*. Each
stage can email the customer with copy written for that stage.

---

## Payments

M-Pesa via Safaricom's **Daraja STK push**. The customer enters their number, gets a PIN
prompt, and the page polls until it clears — no manual refresh.

Configure it under **Settings → mpesa**: environment (sandbox or production), short code,
consumer key and secret, and passkey. Leave `mpesa_enabled` at `0` and the payment page
falls back to bank transfer and pay-on-collection.

The callback endpoint is `POST /checkout/mpesa/callback`. It is the one route exempt from
CSRF, because Safaricom posts to it server to server; it is protected instead by requiring
a `CheckoutRequestID` we generated and are still waiting on. Settlement is **idempotent** —
Safaricom retries, and a repeated callback for an already-settled payment changes nothing.

Every request and callback is logged to `storage/logs/mpesa.log`.

> Safaricom must be able to reach the callback URL, so it will not fire against
> `localhost`. For local testing use a tunnel (ngrok or similar) and set `app.url` to the
> tunnel address, or record payments manually from the admin order screen.

---

## Email

Transactional email prefers **SMTP** (Settings → mail) and falls back to PHP `mail()`.
There is a small SMTP client in `app/Core/Smtp.php` supporting STARTTLS, implicit SSL and
AUTH LOGIN — no dependency required.

Messages are wrapped in a branded, table-based HTML shell that survives Outlook, with a
plain-text alternative generated automatically.

**Nothing is ever lost to a mail failure.** Every quote, order, booking and repair is
written to the database *before* mail is attempted, and failures are logged to
`storage/logs/mail.log` rather than thrown.

---

## Design

A high-end editorial system rather than a generic shop theme.

- **Palette** — bone paper `#F7F4EF`, ink `#14110E`, saddle-tan accent `#8A5A2B`, brass `#B99149`
- **Type** — Fraunces (display serif) over Inter (UI sans), both from Google Fonts
- **Homepage** — split-screen hero, discipline marquee, three-pillar curation grid with
  hover-revealed sub-category links, dark high-contrast product spotlight, craft strip,
  brand wall, and the tan "Effortless Action" CTA banner
- **Motion** — IntersectionObserver scroll reveals with sibling stagger, header that
  turns transparent over the hero and hides on downward scroll, wipe-underline links,
  fill-from-below buttons. Everything respects `prefers-reduced-motion`
- **Responsive** — fluid `clamp()` type and spacing, no horizontal overflow, a full
  mobile drawer, and card quick-add always visible on touch devices

All product artwork ships as hand-drawn SVG placeholders in the brand palette, so the
site looks finished before a single photograph is uploaded. Upload real images and they
take over automatically.

---

## Security

- **Prepared statements everywhere** — no string-interpolated SQL. Table and column
  names are never taken from user input
- **CSRF tokens** on every POST, verified centrally in the front controller before
  routing; failures return 419
- **Output escaping** via `e()` on every dynamic value. The one exception is CMS page
  bodies, which are sanitised on save — scripts, iframes, forms, inline event handlers
  and `javascript:` URLs are stripped, and only a formatting tag whitelist survives
- **Passwords** hashed with `password_hash()` (bcrypt), rehashed on login when the
  algorithm moves on
- **Login throttling** — six failed attempts triggers a ten-minute lockout
- **Uploads** validated by real MIME type via `finfo`, not the client-supplied name;
  stored with generated filenames; PHP execution disabled in `uploads/` by `.htaccess`
- **Sessions** — HttpOnly, SameSite=Lax, regenerated on login, `secure` flag ready for HTTPS
- **Honeypot fields** on every public form
- **Last-administrator guard** — the final active admin cannot be deleted, demoted or disabled
- **Separate customer and staff sessions** — different session keys, models and guards, so a
  storefront login can never be mistaken for a staff login
- **Password reset tokens** stored as SHA-256 hashes with a one-hour expiry; the reset
  endpoint never reveals whether an address is registered
- **Order ownership** — an order is viewable only by the account that owns it or the guest
  session that placed it
- **Login throttling** on both the staff (6 attempts / 10 min) and customer (8 / 15 min) sides

---

## Notes on behaviour

- **Quote requests are always written to the database first**, then a notification email
  is attempted. Mail is disabled by default (`mail.enabled`) because PHP's `mail()` fails
  silently on most XAMPP installs — no enquiry is ever lost to a mail failure. Failures
  are logged to `storage/logs/mail.log`
- **Deleting a product does not damage past quotes.** Quote lines store a snapshot of the
  product name and SKU, so historical records stay readable
- **Quote references** are `TR-YYMMDD-XXXX`, checked for collisions before use. The prefix
  is editable in Settings
- **The quote list** lives in the session, caps at 60 lines, and silently drops items whose
  product has since been deleted or unpublished
- **Buyable requires a real price.** Ticking "allow direct purchase" without a visible price
  above zero is refused and reported, so nothing can ever be sold at KSh 0
- **Stock is optional.** Leave `stock_qty` blank for made-to-order items. When it is set,
  paid orders decrement it and the product flips to "currently unavailable" at zero
- **Registering claims your history.** Past quotes, bookings and repairs sent from the same
  email address are attached to the new account automatically
- **Images are optimised where possible.** GD downscales anything over 1600px and writes a
  WebP alongside. Where GD is missing, files are stored as uploaded rather than failing

---

## Before going live

1. Change the admin password, and delete or rename the seeded account
2. Create `config/config.local.php` with production credentials; set `debug` to `false`
   and `env` to `production`
3. Point the document root at `public/`, and serve over HTTPS with `session.secure = true`
4. Set `app.url` to the canonical domain — M-Pesa callbacks and reset links depend on it
5. Replace the seeded demo catalog with the real inventory and photographs
   (Admin → Import & export takes a CSV; run it as a dry run first)
6. Add the Google Maps embed URL and Instagram link in Settings
7. Configure SMTP and send yourself a test — quote and order confirmations depend on it
8. Set delivery pricing and the free-delivery threshold under Settings → commerce
9. Enter live Daraja credentials and switch `mpesa_env` to `production`. Test with one real
   low-value transaction before announcing it
10. Decide which products are `buyable`. Everything stays quote-only until you say otherwise
11. Submit `/sitemap.xml` to Google Search Console
