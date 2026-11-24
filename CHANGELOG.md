# Changelog

This project follows [Semantic Versioning](https://semver.org/).

## [2.0.0] - Unreleased

### Breaking

- Requires PHP 8.4 or newer.
- The web root moved to `frontend/public`.
- Page URLs are now `/`, `/about`, and `/contact`. The former Spanish-named PHP file URLs no longer exist.
- The unused catalog menu link, which had no page in this repository, was removed.
- Bootstrap CSS classes are no longer available.

### Added

- Layered `backend/` and `frontend/` structure.
- Single-file page and menu configuration.
- About entry in the menu.
- 404 and 405 handling, and a generic 500 response.
- `aria-current`, responsive styling, and dark mode.
- PHPUnit unit and integration suites, PHPStan, committed test report, and CI.
- Optional SQL schema, architecture guide, security policy, and security audit.

### Changed

- All code, content, filenames, and documentation are now in English.
- Active-page detection now compares normalized paths.

### Security

- Removed reliance on the `Host` header.
- Added Content Security Policy and related response headers.
- Removed the end-of-life Bootstrap 3.0.3 stylesheet.
- Moved source files out of the web root.

## Before 2.0.0 (2013-2018)

- Original PHP include-based menu with an active class.
