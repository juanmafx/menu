# menu

A small, dependency-free PHP site shell in which every page shares one navigation menu, and the entry for the current page is highlighted automatically.

> **Project history:** originally created in 2013, modernized in 2026.

## Purpose

Multi-page sites without a framework often copy their navigation into each file, so one label change means editing every page, and the "you are here" state drifts. This project keeps the menu in a single configuration file and derives the active entry from the request.

## Why this project exists

It began in 2013 as a personal learning exercise in reusing a PHP include to build a menu with an `active` class. Version 2 keeps that small idea and rebuilds it with strict types, explicit boundaries, automated tests, and safe HTTP defaults. It is a reference template, not a CMS.

## What it does

- Renders Home, About, and Contact pages inside a shared layout.
- Marks the current entry with `class="active"` and `aria-current="page"`.
- Normalizes absolute-form request targets, trailing slashes, duplicate slashes, query strings, fragments, and `index.php`.
- Answers unknown paths with a 404 page that never reflects the input.
- Accepts only `GET` and `HEAD`, and answers other methods with 405.
- Sends a restrictive Content Security Policy and related security headers.

It has no forms, accounts, sessions, cookies, database access, or client-side JavaScript. The Contact page is a placeholder for your public contact channels.

## Architecture

```text
frontend/public/index.php  ->  backend Application  ->  Menu + TemplateRenderer  ->  frontend/templates
```

```text
backend/    Configuration, HTTP handling, navigation, and rendering
frontend/   Web root, stylesheet, and HTML templates
db/         Optional SQL schema
tests/      Unit and integration suites
reports/    Committed test report
scripts/    Report and schema verification helpers
docs/       Architecture and security audit
```

See [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md) for details.

## Quick start

The site itself needs only PHP 8.4 or newer and has no runtime dependencies.

```sh
php -S 127.0.0.1:8080 -t frontend/public backend/dev/router.php
```

Open <http://127.0.0.1:8080>. PHP's built-in server is for local development only.

## Usage guide

To add a page:

1. Create `frontend/templates/pages/pricing.php` with the page content.
2. Add an entry to `backend/config/pages.php`:

   ```php
   new Page('/pricing', 'Pricing', 'Pricing', 'Plans and prices.', 'pricing'),
   ```

The menu, route, and active state all follow from that entry. Menu order is the order of the array.

## Configuration

| Setting | Location | Notes |
| --- | --- | --- |
| Pages and menu order | `backend/config/pages.php` | Paths must be normalized and unique |
| Security headers | `backend/src/Http/SecurityHeaders.php` | HSTS is sent only over HTTPS and does not cover subdomains |
| Styling | `frontend/public/assets/css/main.css` | Follows the system light or dark preference |

There are no environment variables or secrets. The site expects to be served from the root of its host.

## Database

The application does **not** use a database. [db/schema.sql](db/schema.sql) is an optional starting point for storing menu entries if you outgrow the configuration file. Nothing reads it at runtime. It has been executed on SQLite 3.46, PostgreSQL 17, and MySQL 8.4.

```sh
python3 scripts/verify_schema.py db/schema.sql
```

## Testing

Development tools require Composer and the PHP `dom`, `mbstring`, `xml`, and `xmlwriter` extensions.

```sh
composer install --no-scripts --no-plugins
vendor/bin/phpunit
vendor/bin/phpstan analyse --no-progress
```

If those extensions are missing locally, run the suite in a container:

```sh
composer install --no-scripts --no-plugins --ignore-platform-req=ext-dom --ignore-platform-req=ext-xml --ignore-platform-req=ext-xmlwriter
docker run --rm --network none --user "$(id -u):$(id -g)" -v "$PWD:/app" -w /app php:8.5-cli vendor/bin/phpunit
```

`scripts/test-report.sh` regenerates the committed report in [reports/tests/](reports/tests/).

## Security notes

- Configured values are escaped through a single helper, and routes come from an allowlist.
- Request input never selects a PHP file or template, and the `Host` header is not used.
- The policy blocks scripts, framing, and form submissions.

See [SECURITY.md](SECURITY.md) and [docs/SECURITY_AUDIT.md](docs/SECURITY_AUDIT.md).

## Credits and license

Versions before 2.0.0 bundled Bootstrap 3.0.3 (Apache-2.0) and normalize.css 2.1.3 (MIT) in a single stylesheet. Version 2.0.0 replaced it with a purpose-written stylesheet.

This repository has no license file, so default copyright applies.
