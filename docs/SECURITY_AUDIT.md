# Security Audit

**Scope:** all tracked files at the start of the 2026 modernization and the rebuilt 2.0.0 tree, plus a gitleaks scan of the branch history.
**Method:** manual code review, automated tests, PHPStan, `composer audit`, gitleaks, and HTTP probes against the local development server. No third-party penetration test was performed.

## Findings

| ID | Severity | Finding | Status |
| --- | --- | --- | --- |
| S1 | Low | The menu's active state was computed from the client-controlled `Host` header | Fixed: the header is no longer used |
| S2 | Medium | No security response headers were sent | Fixed for PHP responses |
| S3 | Low | The stylesheet bundled the end-of-life Bootstrap 3.0.3 (CSS only, no Bootstrap JavaScript) | Fixed: replaced with a purpose-written stylesheet |
| S4 | Low | A short open tag could expose source code as plain text where `short_open_tag` is disabled | Fixed: templates and classes use full tags |
| S5 | Low | Source files were all located in the web root | Fixed: only `frontend/public` is exposed |
| S6 | Low | Menu links used hardcoded `http://` URLs | Fixed: links are root-relative |
| S7 | Low | Malformed HTML made browser parsing unpredictable | Fixed: documents were rewritten |
| S8 | Low | The 2.0.0 draft sent HSTS with `includeSubDomains`, committing every subdomain of a deployment to HTTPS | Fixed: HSTS covers only the serving host |

## Categories with no finding

| Category | Result |
| --- | --- |
| SQL injection | No database access |
| Cross-site scripting | No request data is rendered, and configured values are escaped |
| CSRF | No forms, cookies, or state-changing endpoints |
| File inclusion | Template names come from validated configuration |
| Unsafe `eval` | Not used |
| Weak hashing | No hashing or stored credentials |
| Hardcoded secrets | None found by gitleaks in tracked files or branch history |
| Dependencies | No runtime dependencies; `composer audit` reported no advisories for development dependencies |

## Open items

No vulnerabilities are known to remain open.

## Operator responsibilities

- Serve the site over HTTPS.
- Set security headers for static assets served directly by the web server.
- Do not use PHP's built-in development server in production.
