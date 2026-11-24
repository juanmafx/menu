# Architecture

## Overview

The project is a server-rendered PHP application with no runtime dependencies. The backend decides what to serve, and the frontend owns how it looks. Only `frontend/public` is exposed to the web.

## Module structure

```text
backend/
  bootstrap.php              Class autoloader
  config/pages.php           Single source of truth for pages and menu order
  dev/router.php             Local development router
  src/
    Application.php          Request handling and error boundary
    Http/
      Request.php            Immutable request built from server values
      Response.php           Status, headers, and body
      SecurityHeaders.php    Response hardening policy
    Navigation/
      Menu.php               Ordered page lookup and active-state rule
      Page.php               Validated page definition
      PathNormalizer.php     Canonical request path
    View/
      Html.php               Context-safe HTML escaping
      TemplateRenderer.php   Validated template rendering
frontend/
  public/
    index.php                Front controller and web root
    assets/css/main.css      Stylesheet
  templates/
    layout.php               Document shell and menu
    pages/                   Page content
db/schema.sql                Optional persistence schema
tests/Unit/                  Isolated class tests
tests/Integration/           HTTP tests against a real local server
```

## Request flow

1. `frontend/public/index.php` builds the menu and renderer.
2. `Request::fromGlobals` reads the method and normalizes the path.
3. `Application::handle` rejects unsupported methods and looks up the path.
4. The page template is rendered and wrapped in the shared layout.
5. `Response::send` emits the status, security headers, and body.

## Design decisions

| Decision | Reason |
| --- | --- |
| Compare paths instead of full URLs | The result no longer depends on the client-controlled `Host` header |
| Exact-match route allowlist | Request input cannot choose a file to include |
| Configuration instead of a database | A static menu does not justify a runtime data store |
| Separate web root | Source, configuration, and tests are not reachable over HTTP |
| No JavaScript | Allows a strict Content Security Policy and keeps the page light |
| No framework | The domain is small enough that a framework would outweigh it |

## Recommended deployment

Serve `frontend/public` over HTTPS through a web server and PHP-FPM. Example nginx configuration, which has not been run as part of this project:

```nginx
server {
    listen 443 ssl;
    http2 on;
    server_name example.com;
    root /srv/menu/frontend/public;

    location /assets/ {
        try_files $uri =404;
    }

    location / {
        try_files /index.php =404;
        fastcgi_pass unix:/run/php/php8.5-fpm.sock;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root/index.php;
    }
}
```

Static assets served directly by the web server do not pass through the PHP security headers, so add equivalent headers there.

## Optional persistence

`db/schema.sql` defines a `menu_items` table that mirrors the `Page` definition. To adopt it, implement a loader that returns `Page` objects and pass them to `Menu`. The rest of the application would not change.
