# Release Notes: 2.0.0

Status: prepared and not yet tagged or published.

Version 2.0.0 rebuilds the original 2013 PHP menu as a small, typed, tested application while keeping its core idea: one menu shared by every page, with the current page highlighted automatically.

## Highlights

- Separate `backend/` and `frontend/` structure with a dedicated web root.
- Menu, routes, and page metadata defined in one configuration file.
- Strict security headers, safe error handling, and allowlisted routing.
- Accessible, responsive markup with no third-party CSS or JavaScript.
- Automated tests, static analysis, and a least-privilege CI workflow.

## Upgrade notes

1. Install PHP 8.4 or newer.
2. Point the web server at `frontend/public` and route requests to `index.php`.
3. Move page content into `frontend/templates/pages/`.
4. Declare pages in `backend/config/pages.php`.
5. Update inbound links to `/`, `/about`, and `/contact`.

## Verification

The test results for this release are in [reports/tests/](reports/tests/).
