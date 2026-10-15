# Changelog

All notable changes to **Real Props** are documented here. Format loosely follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/); this project uses [Semantic Versioning](https://semver.org/).

## [Unreleased] — 2.0.0

Complete production-ready rewrite of the platform on Laravel 11 / Livewire 3 / Tailwind.

### Added

#### Platform
- Multi-role authentication (guest, member, agent, admin) with Spatie Permissions; roles/permissions derived from `App\Enums\UserRole` and synced by `RoleSeeder`.
- Agent verification workflow (`agent_profiles.verified_at` set by admins; unverified agents are hidden from public profiles).
- Account suspension (`is_suspended`, `suspended_at`, `suspension_reason`) enforced by `EnsureUserIsActive` middleware.
- Custom error pages: 403, 404, 419, 429, 500 (shared `x-errors.error` component inside the site layout).
- Security headers / CSP middleware (`SecurityHeaders`), throttle middleware aliases for inquiries and contact forms.

#### Public site
- Home page with hero search, stats, featured/new listings and testimonials.
- Property search (Livewire `PropertyList` + `PropertyFilters`): keyword, listing type, property type, state/city, bedrooms, bathrooms, price range, amenities, sort, pagination — all URL-persisted.
- Property detail: gallery, amenities, map, video/virtual-tour links, related listings, Livewire inquiry form, tour-request form.
- Compare up to 4 listings side by side (session-driven).
- Agent directory and public agent profile pages.
- Blog index, post detail, category and tag archives.
- CMS pages (about, faq, terms, privacy + dynamic `/pages/{slug}`), contact form with admin inbox.
- Dark mode with pre-paint theme bootstrap.

#### Member area
- Dashboard, favorites, saved searches (with match notification job), compare list, inquiry history, profile management.

#### Agent area
- Dashboard + Chart.js analytics (listing views, inquiries, monthly series, top listings).
- Listing CRUD with multi-image upload (Spatie Media Library), amenities, moderation status.
- Inquiry inbox with reply, status updates and deletion.
- Tour-request schedule with status management.
- Subscription plans via Laravel Cashier (Stripe Checkout + Customer Portal).
- Agent profile editing (agency, license, WhatsApp, bio).

#### Admin area
- Analytics dashboard (Chart.js: monthly listings, listing types, top agents, revenue).
- Listing moderation: approve / reject with reason / feature / delete, with activity logging.
- User management: view, role display, suspend/unsuspend, delete.
- Agent management: verify/unverify, listing and inquiry stats.
- Master data CRUD (amenities, property types, states, cities, categories, tags) via generic config-driven `MasterDataController` + Blade views.
- Blog posts CRUD (publish/schedule/draft, categories, tags, featured image, SEO).
- CMS pages CRUD.
- Subscription plans CRUD (price, interval, limits, Stripe price ID, features).
- Settings UI (general, SEO, listings, social) backed by the `settings` table with model caching.
- Contact-message inbox (status workflow, internal notes).
- Reports page with summary charts.
- Activity log browser (filter by event, search).

#### API
- Sanctum-authenticated JSON API under `/api/v1`: register/login/logout, profile, properties (index/featured/search/show), agents, posts, categories, tags, plans, favorites, inquiries, tours.
- Stripe webhook endpoint (`/api/v1/webhooks/stripe`).

#### Search & notifications
- Laravel Scout integration on properties (`SCOUT_DRIVER` configurable; `collection` default).
- Notifications: new inquiry (agent), inquiry reply (buyer), property status changes, saved-search matches, tour requests, contact messages (admin).

#### Tooling & ops
- Pest 3 test suite (auth flows, profile, smoke test rendering **every** GET route for each role, plus unit tests for enums, Eloquent scopes and reCAPTCHA).
- Laravel Pint (`pint.json`) and Larastan level 6 (`phpstan.neon` + `phpstan-baseline.neon`).
- GitHub Actions CI: tests, Pint, PHPStan, Docker image build.
- Multi-stage Dockerfile (Node assets → Composer deps → `php:8.2-fpm-alpine`) with nginx, MySQL, Redis, **Meilisearch**, **Mailhog**, queue worker and scheduler in `docker-compose.yml`.
- Seeders: roles, master data, plans, users, properties, blog, pages, settings, contact messages.

#### UX polish (final pass)
- Recently viewed properties (session-tracked via `TrackRecentlyViewed` middleware; shown on property page + member dashboard).
- Breadcrumbs component (`x-breadcrumbs`) on property, blog post, agent, contact and CMS pages.
- Floating toast notifications on the public site layout.
- Skeleton loaders (animate-pulse) while Livewire search requests are in flight.
- Map view toggle on property search (Leaflet + OpenStreetMap pins with preview popups, URL-persisted `?view=map`).
- API rate limiting per spec: 60 req/min authenticated, 30 req/min guest.

### Changed
- Route files are wrapped in the `web` middleware group (auth/agent/admin routes previously loaded without it).
- Agent `PropertyController` uses `HasMiddleware` + `can:` middleware instead of `authorizeResource()` in the constructor (compatible with Laravel 11's slim base controller).
- Livewire `Url` attributes use the plain `#[Url]` form (supported since Livewire 3).
- Activity log eager-loads `causer` (not `causator`) and renders `causer` in the admin view.
- Master-data routes pass `resource` via route defaults so the generic controller resolves it.

### Fixed
- Blade anonymous components: `layouts/site.blade.php` moved to `components/site/layout.blade.php`, `layouts/panel.blade.php` moved to `components/panel/layout.blade.php` with default Agent/Admin menus.
- Bare `Setting::` references in blades now use `\App\Models\Setting::`.
- `PostStatus::Scheduled` enum case added; `ContactMessageController` and dashboard views compare enum casts correctly.
- Agent dashboard stat keys aligned with `AnalyticsService` output.
- Chart.js exposed as `window.Chart` from `resources/js/app.js`; chart init wrapped in `DOMContentLoaded`; doughnut/pie tooltips and scales handled.

## [1.0.0]

Initial Laravel skeleton (not shipped).
