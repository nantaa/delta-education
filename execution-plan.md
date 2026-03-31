# Execution Plan — Integrated Webinar & Digital Product Platform
## deltaindo.co.id · Solo Dev · Laravel Stack · ~12 Weeks

> **Stack Lock:** Laravel 11 · Filament v3 · Livewire + Alpine.js · PostgreSQL · Redis · Nginx + PHP-FPM · IDCloudHost VPS

---

## Pre-Flight Checklist (Before Writing a Single Line of Code)

- [ ] Register/verify subdomain `events.deltaindo.co.id` with DNS
- [ ] Spin up IDCloudHost VPS (4 vCPU / 4 GB RAM / 40 GB SSD)
- [ ] Choose payment gateway — Midtrans, Xendit, or DOKU (sign up, get sandbox keys)
- [ ] Activate WABLAS account, get API key + sender number
- [ ] Set up Google Workspace account for Gmail API + OAuth credentials
- [ ] Set up GitHub repo + `.env.example` with all required keys
- [ ] Decide on Zoom account tier (for webinar delivery)
- [ ] Set up Sentry account (free tier) for error tracking

---

## Phase 1 — Foundation & Infrastructure
### Duration: Week 1–2

**Goal:** Working server, deployable app, auth system, and admin shell.

### 1.1 Server Setup (VPS)
- [ ] Install: Nginx, PHP 8.3-FPM, PostgreSQL, Redis, Supervisor, Certbot
- [ ] Configure Nginx vhost for `events.deltaindo.co.id`
- [ ] Enable SSL via Certbot (Let's Encrypt)
- [ ] Set up swap (2 GB recommended for 4 GB RAM VPS)
- [ ] Configure firewall (UFW): open ports 22, 80, 443 only
- [ ] Set up deployment user (non-root) with SSH key

### 1.2 Laravel Project Bootstrap
- [ ] `composer create-project laravel/laravel platform`
- [ ] Configure `.env`: DB (PostgreSQL), Redis, queue driver, mail, app URL
- [ ] Install core packages:
  ```
  composer require filament/filament:"^3.0"
  composer require livewire/livewire
  composer require spatie/laravel-permission
  composer require spatie/laravel-medialibrary
  composer require spatie/laravel-activitylog
  ```
- [ ] Run `php artisan filament:install --panels`
- [ ] Set up GitHub Actions CI/CD (basic: lint → test → deploy via SSH)

### 1.3 Database Schema — Core Tables
Design and migrate in this order:
```
users → roles/permissions → webinars → registrations
→ products → orders → order_items → payments
→ blast_campaigns → blast_logs → activity_logs
```
- [ ] Create all migrations (schema-first, no logic yet)
- [ ] Seed roles: `super_admin`, `admin`, `finance`, `content_manager`
- [ ] Seed 1 super admin user

### 1.4 Authentication & Roles
- [ ] Filament admin panel login (email + password)
- [ ] Spatie Permission: define roles and assign to Filament resources
- [ ] Public-facing auth: Laravel Breeze (register/login for customers)
- [ ] Email verification on registration

### 1.5 Base Admin Panel Shell
- [ ] Filament navigation groups: Webinars · Products · Payments · Blasting · Monitoring
- [ ] Install Filament Shield for role-based resource access
- [ ] Confirm admin panel accessible at `/admin`

**Phase 1 Exit Criteria:**
- VPS live, HTTPS working
- Admin panel login works
- All base migrations run cleanly
- CI/CD deploys on `main` push

---

## Phase 2 — Webinar Registration System
### Duration: Week 3–4

**Goal:** Full webinar lifecycle — create, publish, register, confirm.

### 2.1 Webinar Management (Admin)
- [ ] Filament Resource: `WebinarResource`
  - Fields: title, slug, description, date/time, capacity, Zoom link, status (draft/published/closed)
  - Actions: Publish, Close Registration, Export Registrants (CSV)
- [ ] Webinar image upload via Spatie Media Library

### 2.2 Public-Facing Pages (Blade + Livewire)
- [ ] `/webinars` — Upcoming webinars listing page
- [ ] `/webinars/{slug}` — Webinar detail + registration form
  - Fields: name, email, phone, (optional: organization)
  - Livewire component for real-time validation + submission
- [ ] Anti-duplicate logic: unique constraint on `(email, webinar_id)`
- [ ] Capacity check: reject if `registrations.count >= webinar.capacity`
- [ ] Thank-you/confirmation page after successful registration

### 2.3 Registration Admin Panel
- [ ] Filament Table: view all registrants per webinar
- [ ] Filters: by webinar, by date range, by status
- [ ] Bulk actions: export CSV, mark attended
- [ ] Manual registration add (for offline/phone registrations)

### 2.4 Confirmation Blast (Stub — full blast in Phase 5)
- [ ] Queue job: `SendRegistrationConfirmation`
- [ ] Dispatch on registration success
- [ ] Log to `blast_logs` (status: queued/sent/failed)
- [ ] Stub the actual send — just log for now, wire up in Phase 5

**Phase 2 Exit Criteria:**
- Admin can create and publish a webinar
- Public user can register, gets confirmation page
- Duplicate registration rejected
- Capacity limit enforced
- Registrant list viewable and exportable in admin

---

## Phase 3 — Payment System
### Duration: Week 5–7

**Goal:** End-to-end payment flow for paid webinars and digital products.

### 3.1 Payment Gateway Integration
> **Recommendation:** Start with **Midtrans** (best PHP SDK, widest Indonesian coverage)

- [ ] Install Midtrans PHP SDK: `composer require midtrans/midtrans-php`
- [ ] Wrap in `PaymentGatewayService` (abstraction layer — makes switching later easier)
- [ ] Sandbox testing for: Virtual Account, QRIS, e-wallet, credit card
- [ ] Configure webhook endpoint: `POST /webhooks/midtrans`

### 3.2 Order & Checkout Flow
- [ ] Order model: `orders` table with status enum (`pending` → `paid` → `failed` → `refunded`)
- [ ] `OrderItem` polymorphic to `Webinar` or `Product`
- [ ] Checkout controller: create order → call gateway → redirect to payment page
- [ ] Payment status page (pending/success/failed views)
- [ ] Webhook handler:
  - Verify signature
  - Idempotency check (skip if order already paid)
  - Update order status
  - Dispatch `OrderPaid` event

### 3.3 Order Paid Event Listeners
- [ ] `GrantProductAccess` — create `user_product_access` record
- [ ] `SendPaymentReceipt` — queue blast job (email + WhatsApp)
- [ ] `GrantWebinarAccess` — link paid registration to order

### 3.4 Payment Admin Panel
- [ ] Filament Resource: `TransactionResource`
  - Columns: order ID, customer, amount, method, status, date
  - Filters: status, date range, gateway method
  - Export: CSV / Excel (use `maatwebsite/excel`)
  - Actions: Mark Paid (manual, with audit log), Refund (with note)
- [ ] Activity log on every manual action (Spatie Activity Log)

### 3.5 Revenue Dashboard Widget (Filament)
- [ ] Total revenue widget (today / this week / this month)
- [ ] Revenue per webinar / per product
- [ ] Gateway fee estimate vs net revenue (hardcoded fee % per method)

**Phase 3 Exit Criteria:**
- Full sandbox payment flow works end-to-end
- Webhook correctly updates order status
- No double-charge on retried webhooks (idempotency verified)
- Admin can view, filter, export all transactions
- Manual pay override logged with audit trail

---

## Phase 4 — Selling System (Ebooks & Minicourses)
### Duration: Week 7–8 (overlaps with Phase 3 hardening)

**Goal:** Product catalog, purchase flow, and access control.

### 4.1 Product Catalog (Admin)
- [ ] Filament Resource: `ProductResource`
  - Type: `ebook` | `minicourse`
  - Fields: title, slug, description, price, thumbnail, status
  - Ebook: upload PDF (private S3 bucket or local `/storage/private/`)
  - Minicourse: structured modules + lessons (JSONB or separate tables)
  - Video: store as YouTube unlisted URL per lesson

### 4.2 Public Product Pages (Blade + Livewire)
- [ ] `/products` — product listing with type filter
- [ ] `/products/{slug}` — product detail with buy button
- [ ] Checkout integration (reuse Phase 3 flow)

### 4.3 Access Control
**Ebooks:**
- [ ] Signed temporary download URL: `Storage::temporaryUrl()` with 30-min expiry
- [ ] Route: `GET /downloads/{order_item_id}` — validate ownership → stream file
- [ ] Never expose direct storage path

**Minicourses:**
- [ ] Middleware: `EnsureUserHasAccess` — check `user_product_access` table
- [ ] Route group: `/courses/{slug}/lessons/{lesson_slug}`
- [ ] Lesson view: embed YouTube unlisted iframe
- [ ] Track progress: `course_progress` table (lesson_id, completed_at)

### 4.4 User Dashboard
- [ ] `/dashboard` — authenticated customer area
  - My Orders (status, date, amount)
  - My Downloads (ebook links, time-limited)
  - My Courses (progress, continue button)

**Phase 4 Exit Criteria:**
- Product can be created and published in admin
- Paid ebook download link works, expires after 30 min
- Minicourse content locked behind payment check
- Customer dashboard shows purchases and progress

---

## Phase 5 — Blasting System (Email & WhatsApp)
### Duration: Week 8–9

**Goal:** Reliable, queued, template-based messaging for all triggers.

### 5.1 Queue Infrastructure
- [ ] Set Redis as queue driver in `.env`
- [ ] Configure Supervisor to run `php artisan queue:work` workers (2–3 processes)
- [ ] Set queue: `blasts` (separate from default) for isolation
- [ ] Failed jobs table: `php artisan queue:failed-table`
- [ ] Retry policy: 3 attempts, exponential backoff (60s → 300s → 900s)

### 5.2 Message Templates
- [ ] `blast_templates` table: name, channel (email/whatsapp), subject, body, variables list
- [ ] Template engine: simple `str_replace` for `{{name}}`, `{{webinar_title}}`, etc.
- [ ] Filament Resource: `BlastTemplateResource` — create/edit templates

### 5.3 Email Blasting (Gmail API)
- [ ] Install Google API PHP client: `composer require google/apiclient`
- [ ] OAuth2 flow to authorize Gmail API (one-time setup, store token)
- [ ] `GmailMailer` service — wrap API send call
- [ ] Daily limit guard: track sent count, pause if approaching 2,000/day
- [ ] Laravel Mailable as fallback for low-volume transactional email (SMTP)

### 5.4 WhatsApp Blasting (WABLAS)
- [ ] `WablasService` — HTTP client wrapper (Guzzle)
- [ ] Methods: `sendMessage(phone, message)`, `sendBulk([])`
- [ ] Phone number formatter: auto-prefix `62` for Indonesia
- [ ] Rate limit: respect WABLAS limits (check their docs — typically ~30 msg/min)

### 5.5 Blast Jobs (Wire Up All Stubs from Earlier Phases)
- [ ] `SendRegistrationConfirmation` — email + WhatsApp (both channels)
- [ ] `SendPaymentReceipt` — email only
- [ ] `SendWebinarReminder` — scheduled: 1 day before, 1 hour before
- [ ] `SendUpsellMessage` — triggered manually by admin post-webinar

### 5.6 Campaign Blasting (Manual Broadcast)
- [ ] Filament: `BlastCampaignResource`
  - Select template, select audience (all registrants of webinar X, all customers, etc.)
  - Schedule: send now or pick datetime
  - Preview before send
- [ ] Background job: loop audience, dispatch individual send jobs
- [ ] Log every send to `blast_logs`: recipient, channel, status, sent_at, error

### 5.7 Blast Monitoring
- [ ] Filament widgets: sent count, failed count, delivery rate per campaign
- [ ] Alert if failure rate > 10% on a campaign (Sentry alert or Telegram webhook)

**Phase 5 Exit Criteria:**
- Registration confirmation auto-sends on new registration
- Payment receipt auto-sends on `OrderPaid` event
- Webinar reminders scheduled correctly
- Admin can create and send a broadcast campaign
- All sends logged with status

---

## Phase 6 — Monitoring System
### Duration: Week 10–11

**Goal:** Full observability — logs, metrics, dashboards, alerts.

### 6.1 Structured Logging
- [ ] Configure Laravel to log as JSON (for log aggregation readiness)
- [ ] Correlation ID middleware: attach `X-Request-ID` to every request + log context
- [ ] Log levels used consistently: `debug` (dev only), `info` (events), `warning` (soft failures), `error` (hard failures)
- [ ] Sentry integration: `composer require sentry/sentry-laravel`
  - Captures uncaught exceptions automatically
  - Custom capture for payment failures, blast failures

### 6.2 Laravel Telescope (Dev/Staging)
- [ ] Install: `composer require laravel/telescope --dev`
- [ ] Restrict access to admin users only
- [ ] Use for: request inspection, queue monitoring, failed jobs, slow queries

### 6.3 Health Check Endpoints
- [ ] `GET /health` — returns system status JSON:
  ```json
  {
    "db": "ok",
    "redis": "ok",
    "queue_workers": 2,
    "disk_free_gb": 22.4
  }
  ```
- [ ] `GET /health/payment` — last successful webhook timestamp
- [ ] `GET /health/blast` — queue size, last failed job

### 6.4 Filament Monitoring Dashboard
- [ ] Filament page: **System Dashboard** (admin-only)
  - Widget: Uptime / last error (from Sentry API or manual ping)
  - Widget: Queue depth (Redis `LLEN` call)
  - Widget: Failed jobs count (last 24h)
  - Widget: Payment success rate (last 24h, 7d)
  - Widget: Blast delivery rate (last campaign)
  - Widget: Active registrations today
- [ ] Filament page: **Revenue Dashboard**
  - Total revenue: today / this week / this month / all time
  - Revenue by product / by webinar
  - Gateway fee deduction estimate
  - Net revenue calculation
  - Export report to CSV

### 6.5 Alerting
- [ ] Telegram bot for alerts (simple, reliable, free):
  - `TelegramAlertService` — POST to Bot API
  - Alert triggers:
    - Payment webhook fails 3+ times in 10 minutes
    - Blast failure rate > 10%
    - Queue depth > 100 jobs
    - Health check endpoint returns non-200
    - Uncaught exception in Sentry (Sentry → Telegram webhook)
- [ ] Runbook comment in each alert: what to check first

### 6.6 Database Monitoring
- [ ] Enable PostgreSQL slow query log (> 1 second threshold)
- [ ] Daily backup cron: `pg_dump` → compress → store in `/backups/` + optional off-site copy
- [ ] Backup verification: weekly test restore to staging

**Phase 6 Exit Criteria:**
- Sentry capturing all production errors
- Health endpoints return correct status
- Monitoring dashboard shows live metrics
- Telegram alert fires on simulated payment failure
- Backup running daily and verified

---

## Phase 7 — Hardening, Testing & Launch
### Duration: Week 12

**Goal:** Production-ready. No known critical bugs. Performance verified.

### 7.1 Testing
- [ ] Feature tests (PHPUnit):
  - Registration flow (success, duplicate, capacity full)
  - Payment webhook (success, retry idempotency, signature fail)
  - Download link (valid, expired, wrong user)
  - Course access (paid, unpaid, wrong user)
- [ ] Artisan tinker smoke tests for blast jobs
- [ ] Manual end-to-end test on staging: register → pay → access content → receive messages

### 7.2 Performance
- [ ] Enable Laravel route caching: `php artisan route:cache`
- [ ] Enable config caching: `php artisan config:cache`
- [ ] Enable view caching: `php artisan view:cache`
- [ ] Add database indexes on: `email + webinar_id`, `order status`, `created_at` on large tables
- [ ] Test page load on mobile (3G throttle in DevTools) — target < 2s
- [ ] Test `/checkout` under simulated 50 concurrent users (Apache Bench or k6)

### 7.3 Security Checklist
- [ ] All routes with user data behind `auth` middleware
- [ ] Payment webhook: Midtrans signature verified on every request
- [ ] CSRF protection on all forms (Laravel default)
- [ ] Rate limiting on registration and login endpoints
- [ ] File downloads: no direct public URL, always through signed route
- [ ] `.env` not committed, secrets in environment only
- [ ] Admin panel: strong password + optional 2FA (Filament 2FA plugin)

### 7.4 Go-Live Steps
- [ ] Point `events.deltaindo.co.id` DNS to VPS IP
- [ ] Switch payment gateway from sandbox → production keys
- [ ] Switch WABLAS to production sender
- [ ] Final `php artisan migrate --force` on production
- [ ] Seed production roles + first admin user
- [ ] Verify SSL certificate active
- [ ] Run health check: `curl https://events.deltaindo.co.id/health`
- [ ] Send 1 test registration + 1 test payment (real gateway, small amount)
- [ ] Confirm Telegram alert channel receiving

---

## Ongoing After Launch

| Task | Frequency |
|---|---|
| Check Sentry for new errors | Daily |
| Review failed queue jobs | Daily |
| Check Telegram alerts | Real-time |
| Database backup verification | Weekly |
| Security patches (`composer update`) | Weekly |
| Revenue report review | Weekly |
| Load test before large webinar | Per event |
| Gateway reconciliation (internal vs gateway) | Monthly |

---

## Package Reference

| Package | Purpose |
|---|---|
| `filament/filament` | Admin panel framework |
| `livewire/livewire` | Reactive frontend components |
| `spatie/laravel-permission` | Role & permission system |
| `spatie/laravel-medialibrary` | File/image uploads |
| `spatie/laravel-activitylog` | Audit trail for admin actions |
| `midtrans/midtrans-php` | Payment gateway SDK |
| `google/apiclient` | Gmail API integration |
| `maatwebsite/excel` | CSV / Excel export |
| `laravel/telescope` | Dev debugging & queue inspector |
| `sentry/sentry-laravel` | Error tracking & alerting |
| `guzzlehttp/guzzle` | HTTP client for WABLAS API |

---

## Key Architecture Notes (for My Reference)

**Queue isolation:** Run `blasts` queue on separate Supervisor process from `default`. Blast failures should not block order processing.

**Webhook idempotency:** Always check `orders.status !== 'paid'` before processing a payment webhook. Log every webhook hit regardless.

**File access pattern:** Private files live in `storage/app/private/`. Never in `public/`. Always serve via `Storage::temporaryUrl()` or a signed controller route with ownership check.

**Event-driven access grants:** All post-payment actions hang off `OrderPaid` event. Never grant access inside the webhook handler directly — always via queued listeners.

**Blast rate limits:** Gmail API: ~2,000/day. WABLAS: check plan limits. Use a rate-limiting job middleware on blast jobs to respect these automatically.

**Staging environment:** Mirror production exactly. Use a separate `staging.events.deltaindo.co.id` subdomain. All gateway tests run on staging with sandbox keys before every production deploy.

---

*Last updated: Phase planning for solo dev — adjust timeline per actual velocity.*
