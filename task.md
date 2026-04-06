# Task Tracker

- [ ] Phase 1: Foundation & Application Core
  - [x] Initialize Laravel 11 project
  - [x] Install dependencies (Filament v3, Livewire, Spatie packages)
  - [x] Create Database Migrations (users, roles, permissions, webinars, registrations, products, orders, order_items, payments, blast_campaigns, blast_logs, activity_logs)
  - [x] Setup Authentication (Laravel Breeze, Filament Auth, Spatie Roles, Filament Shield)
- [ ] Phase 2: Webinar Registration System
  - [x] Build `WebinarResource` in Filament
  - [x] Build Public Livewire components (`/webinars`, `/webinars/{slug}`)
  - [x] Implement anti-duplicate and capacity validations
  - [x] Create stub queued jobs for registration events
- [ ] Phase 3: Payment System Integration
  - [x] Build `PaymentGatewayService` (Midtrans)
  - [x] Implement Order Creation and Checkout logic
  - [x] Implement Webhook Idempotent handlers
  - [x] Build `TransactionResource` and Revenue Widgets
- [ ] Phase 4 (Pivot): Landing Page & CMS Polish
  - [ ] Configure `WebinarResource` in Filament
  - [ ] Configure `OrderResource` in Filament
  - [ ] Build Premium Landing Page UIry
- [ ] Phase 6: Monitoring & Observability
  - [ ] Set up JSON Logging and Sentry
  - [ ] Install Laravel Telescope
  - [ ] Implement health-check endpoints and System Dashboard
  - [ ] Integrate `TelegramAlertService`

  Phase 5(Later)
Unified Checkout System:
Created the App\Services\PaymentGatewayService to easily contact Midtrans.
Implemented a unified Checkout Livewire view (/checkout/...) that natively checks capacity, avoids duplicates, generates the pending Order, and populates the Midtrans Snap modal popup. Free items skip the payment gateway and generate the registration automatically.
Webhook Integrations:
Scaffolded the /webhook/midtrans endpoint (bypassing CSRF).
Designed the Midtrans Controller payload validation logic to listen to Settlement/Capture intents. Once paid, the controller automatically flags the order and delegates access provision via the OrderPaid listener mapping (e.g., auto-generates a webinar registration for the user or opens ebook access).
