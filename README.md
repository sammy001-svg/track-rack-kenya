# Tack Rack Kenya

A modern quote-request e-commerce website and admin console for **Tack Rack Limited** —
Kenya's equestrian supplier since 1997, based at the MacNaughton Business Centre on
Ngong Road, Nairobi.

Built with **HTML/CSS/JS on the front end, PHP 8 on the back end, and MySQL/MariaDB**
for data. No frameworks, no Composer, no build step.

---

## What it is

The site is a **catalog-plus-quote** storefront rather than a checkout store. Customers
browse the catalog, build a quote list, and send it through; staff price it in the admin
console and reply. That matches how Tack Rack actually sells — saddles depend on the
horse, rugs on the measurement, and imported stock moves with freight and duty.

Prices can still be shown per-product (`price_visible`) when a fixed price makes sense.

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
| *(none)* | Create if missing, then apply schema and seed |
| `--fresh` | **Drop** and recreate everything — destroys all data |
| `--schema` | Schema only, no demo catalog |

You can also load the SQL by hand through phpMyAdmin: run `schema.sql` first, then
`seed.sql`.

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
  Core/                  Database, Router, Controller, Model, Auth, Csrf,
                         Session, Validator, Uploader, QuoteList, Mailer, helpers
  Controllers/           Public site controllers
  Controllers/Admin/     Admin console controllers
  Models/                Product, Category, Brand, Quote, Message, Page, Setting, User
  Views/
    layouts/             site.php, admin.php, blank.php
    partials/            header, footer, product-card, admin-pagination
    site/                Public pages
    admin/               Admin screens
config/
  config.php             App configuration (config.local.php overrides it)
database/
  schema.sql             Tables, keys, constraints
  seed.sql               Catalog, pages, settings, admin user
public/                  >>> the web root <<<
  index.php              Front controller — every route is declared here
  .htaccess              Rewrites, caching, upload hardening
  assets/css|js|img      main.css / admin.css, main.js / admin.js, SVG artwork
  uploads/               Uploaded images (products, brands, categories)
storage/logs/            PHP and mail logs
bin/
  install.php            Installer
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
| **Settings** | Contact details, opening hours, social links, WhatsApp number, quote notification address and reference prefix |
| **Staff accounts** | Add users, set Administrator or Manager role, reset passwords, disable accounts |

Two roles: **Administrator** (everything, including settings and accounts) and
**Manager** (catalog, quotes, messages, pages).

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
- **Honeypot fields** on both public forms
- **Last-administrator guard** — the final active admin cannot be deleted, demoted or disabled

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

---

## Before going live

1. Change the admin password, and delete or rename the seeded account
2. Create `config/config.local.php` with production credentials; set `debug` to `false`
   and `env` to `production`
3. Point the document root at `public/`, and serve over HTTPS with `session.secure = true`
4. Set `app.url` to the canonical domain
5. Replace the seeded demo catalog with the real inventory and photographs
6. Add the Google Maps embed URL and Instagram link in Settings
7. Enable mail once the server can actually send it
