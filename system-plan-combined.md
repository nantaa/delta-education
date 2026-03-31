# System Suite Plan: Webinar, Payments, Blasting, Selling, Monitoring

This document summarizes what is needed to build the system suite (1 developer), a realistic solo timeline, required components, pre-filled answers to the earlier requirements questionnaire, and the finalized external services & architecture decisions.

The scope covers:
- Webinar registration system
- Payment system
- Email & WhatsApp blasting system
- Ebook & minicourse selling system
- Monitoring system for all of the above

---

## 1. What Needs to Be Built

### 1.1 Webinar Registration System

**Goal:** Allow users to register for webinars, capture their data, and trigger confirmations and reminders.

**Key features:**
- Public webinar listing page (upcoming events, basic details)
- Webinar detail page with registration form (name, email, phone, etc.)
- Server-side validation and anti-duplicate registration logic
- Integration with blasting system to send confirmation and reminders
- Admin panel for:
  - Creating/editing webinars (title, date, capacity, description, links)
  - Viewing and exporting registrants
  - Marking attendance if needed

**Non-functional needs:**
- Fast page load for registration (< 2 seconds typical target)
- Reliable form submission with very low error rate
- Secure storage of personal data (contact info)

---

### 1.2 Payment System

**Goal:** Process payments for paid webinars, ebooks, and minicourses reliably and securely.

**Key features:**
- Integration with one or more payment gateways (Midtrans / Xendit / DOKU — see Section 8 for fee comparison)
- Checkout pages that:
  - Show order summary and price
  - Support relevant payment methods (VA, e-wallets, cards, minimarkets, QRIS)
- Webhook handling for payment confirmation and failure
- Idempotent payment processing (no double charge if webhook is retried)
- Order status tracking (pending, paid, failed, refunded)
- Admin panel for transactions:
  - Filter/search transactions
  - Export reports (CSV/Excel)
  - Manual adjustments (refund/mark paid with audit log)

**Non-functional needs:**
- High payment success and low error rate
- Secure handling of payment data (never store raw card data)
- Clear audit logs for each payment event

---

### 1.3 Blasting System (Email & WhatsApp)

**Goal:** Send confirmations, reminders, and marketing campaigns via email and WhatsApp.

**Key features:**
- Message templates (registration confirmation, payment receipt, webinar reminder, upsell, etc.)
- Simple template variables (e.g., `{{name}}`, `{{webinar_title}}`, `{{date}}`)
- Integrations:
  - Email: Gmail API with Google Workspace (up to 2,000 emails/day; upgrade provider if needed)
  - WhatsApp: WABLAS REST API gateway
- Queue-based sending with retries and backoff
- Scheduling (send now or at a specific time)
- Basic campaign monitoring:
  - Sent, failed, bounced (email)
  - Delivered, failed (WhatsApp)

**Non-functional needs:**
- Respect provider rate limits to avoid blocking
- Reliable queue so no messages are lost on failure

---

### 1.4 Selling System (Ebook & Minicourse)

**Goal:** Let users browse, purchase, and access ebooks and minicourses.

**Key features:**
- Product catalog:
  - Ebook and minicourse listing pages
  - Product details (title, description, price, media, preview)
- Cart/checkout flow for one-time purchases
- Integration with payment system
- Access control:
  - Ebook: secured download links with expiry
  - Minicourse: authenticated access to lessons/modules, video hosting integration or embedded platform
- User account area:
  - Order history
  - Download links and course progress
- Admin panel for products and orders

**Non-functional needs:**
- Protect downloadable files from public direct access
- High availability for content access

---

### 1.5 Monitoring System (for 1–4)

**Goal:** Provide observability and alerting across all subsystems (registration, payment, blasting, selling).

**Key features:**
- Centralized logging (structured logs, correlation IDs)
- Metrics collection:
  - Uptime and response time per service
  - Error rates, queue sizes, payment failures, blast failures
- Dashboards (e.g., with Grafana/Metabase-like tool)
- Alerts:
  - Threshold-based (e.g., error rate > X%, payment failures > Y in 10 minutes)
  - Channel integration: email, WhatsApp, Telegram, etc.
- Admin monitoring capabilities:
  - Link Forms Monitoring
  - Clients/Attendees monitoring
  - Dashboards for total revenue per period and per event
  - Visibility into payment gateway fees and net revenue
  - Transaction lists with export to CSV/Excel for finance

**Non-functional needs:**
- Monitoring service more reliable than what it monitors (99.99% target uptime)
- Minimal performance overhead on the main systems

---

## 2. Requirements Needed (High-Level)

### 2.1 Functional Requirements

Examples for this project:
- Users **can** register for a webinar via a public form.
- The system **must** prevent duplicate registrations by email + webinar ID.
- After successful payment, the system **must** automatically grant access to the purchased ebook/minicourse.
- The blasting system **must** send a registration confirmation within 1 minute of successful registration.
- The monitoring system **must** raise an alert if payment success rate drops below a configured threshold.

### 2.2 Non-Functional Requirements

- **Performance:**
  - Registration & product pages load in < 2 seconds on average connections.
  - API endpoints critical to checkout respond in < 500ms at p95.
- **Security:**
  - All traffic served over HTTPS.
  - No sensitive payment data stored; rely on gateway tokens.
- **Reliability:**
  - Overall system uptime ≥ 99.5%; payment and content delivery services ≥ 99.9%.
- **Scalability:**
  - System can handle at least 2× expected peak traffic.

### 2.3 Technical & Integration Requirements

- Stack assumption (example):
  - Frontend: React/Next.js
  - Backend: Node.js/NestJS or Laravel
  - Database: PostgreSQL
  - Message queue: Redis/RabbitMQ
- Integration points:
  - Payment gateway API (Midtrans / Xendit / DOKU)
  - Email: Gmail API (Google Workspace)
  - WhatsApp: WABLAS API
  - Monitoring stack (Prometheus-compatible, or external APM service)

---

## 3. Solo Developer Timeline (High-Level)

Below is a **realistic solo developer MVP plan (~10–14 weeks)** assuming focused work and reuse of libraries/services.

### Phase 1 – Discovery & Design (1–2 weeks)

- Refine scope using the questionnaire.
- Define data models (users, webinars, orders, products, blasts, logs, etc.).
- Sketch core screens and flows.
- Decide final tech stack and integrations.

### Phase 2 – Core Infrastructure & Auth (1–2 weeks)

- Set up repo, CI/CD, environments.
- Implement authentication (login, registration, roles).
- Configure database and basic admin layout.

### Phase 3 – Webinar Registration MVP (1–2 weeks)

- Build webinar CRUD (admin).
- Build public listing + detail + registration form.
- Wire up confirmation email/WhatsApp via blasting system stub.

### Phase 4 – Payment & Selling Flow (3–4 weeks)

- Integrate payment gateway.
- Implement order model & checkout flows.
- Implement product catalog (ebooks/minicourses).
- Implement access control (downloads, course content).

### Phase 5 – Blasting System (1–2 weeks)

- Implement queue + workers for email/WhatsApp.
- Create templates and scheduling.
- Record send logs and simple campaign stats.

### Phase 6 – Monitoring & Hardening (2–3 weeks)

- Add structured logging and metrics.
- Build monitoring dashboards and alerts.
- Load testing for key flows.
- Fix performance and reliability issues.

**Total: ~10–14 weeks** of focused solo development for an end-to-end MVP, with room for iteration.

---

## 4. Success Criteria / KPIs (Developer-Focused)

### 4.1 Webinar Registration

- Registration form submission success rate ≥ 99%
- Registration page load time < 2 seconds (median)
- API response time (submit endpoint) < 500ms (p95)
- Confirmation message (email/WA) delivered ≥ 99% within 1 minute
- Error rate (5xx) < 0.1%

### 4.2 Payment System

- Payment success rate ≥ 99%
- Transaction processing latency < 3 seconds (p95)
- Webhook processing success ≥ 99.9%
- Idempotency: 0 double-charged transactions
- Reconciliation: 100% match between internal records and gateway

### 4.3 Blasting System

- Email delivery (accepted by receiving server) ≥ 99%
- Email hard bounce rate < 2%
- WhatsApp delivery rate ≥ 95%
- Scheduling accuracy within ±2 minutes of target time
- At least 3 retries on transient failures with backoff

### 4.4 Selling System (Ebooks & Minicourses)

- Checkout flow completion (no broken steps) for 100% of valid sessions
- Access provisioning after successful payment < 60 seconds
- Download/content availability ≥ 99.9% uptime
- No public direct access to paid files

### 4.5 Monitoring System

- Monitoring coverage: 100% of critical services instrumented
- Alerting delay from incident to alert < 1 minute for P1
- False positive alerts < 5%; false negatives 0% for P1 incidents
- Monitoring system uptime ≥ 99.99%

### 4.6 Cross-System Engineering KPIs

- Cycle time (dev to production) reasonable and trending down over time
- Change failure rate < 5% of deployments
- Mean Time to Detect (MTTD) for P1 incidents < 5 minutes
- Mean Time to Resolve (MTTR) for P1 incidents < 30 minutes

---

## 5. Questionnaire With Suggested Answers

### 5.1 Filled Questionnaire

- **Project name**  
  Integrated Webinar & Digital Product Platform

- **Date**  
  (Fill with project start date)

- **Stakeholders / sponsor**  
  Internal education/business team; technical owner (you); finance/operations for payments.

- **Business objective / problem to solve**  
  Centralize webinar registration, payments, digital product sales, and communication into one platform to reduce manual work, errors, and missed revenue.

- **Success criteria / KPIs**  
  High registration and payment success rates, fast and reliable confirmations and content access, minimal incidents, and clear monitoring/alerting (see detailed KPIs above).

- **Primary user roles**  
  Public users (participants/customers), admins/content managers, finance/ops, and technical owner.

- **In-scope items**  
  Webinar registration, payment processing, email/WhatsApp blasting, ebook/minicourse selling, monitoring/observability for all modules.

- **Out-of-scope items**  
  Full CRM, marketing automation beyond basic campaigns, complex affiliate systems, native mobile apps.

- **Most critical features**  
  Reliable registration and payment flows, automatic access delivery, and robust monitoring/alerting.

- **Current process / system**  
  Likely a mix of manual forms, spreadsheets, separate payment links, manual sending of emails/WhatsApp.

- **Main pain points today**  
  Manual work, inconsistent data, missed or delayed confirmations, human error in sending links and tracking payments.

- **Key business rules**  
  No access granted before payment confirmation; one registration per email per webinar; secure, time-limited links for downloads; refunds follow defined policy.

- **Data to capture and store**  
  User identity and contact details, webinar details and registrations, orders and payments, product metadata, blast logs, monitoring metrics/logs.

- **Reports / dashboards needed**  
  Webinar registration counts, payment/transaction reports, product sales, blast performance, system uptime and error dashboards.

- **Integrations with other systems**  
  Payment gateway (Midtrans/Xendit/DOKU), Gmail API, WABLAS WhatsApp API, optional analytics (e.g., GA) and external monitoring/APM.

- **Performance expectations**  
  Key pages load < 2 seconds; critical APIs < 500ms (p95); payment confirmation and access provisioning within 1 minute.

- **Security & privacy concerns**  
  Protect personal data (names, emails, phone numbers), secure payments, restrict file access, comply with relevant privacy expectations/laws.

- **Regulations / compliance**  
  Payment gateway and banking rules, general data protection principles, internal policies for data retention and audit.

- **Timeline & milestones**  
  MVP in ~10–14 weeks for one experienced developer (see Section 3 for phase breakdown).

- **Budget / resource constraints**  
  One primary developer plus SaaS/service costs (payment gateway fees, email/WhatsApp provider, VPS hosting, monitoring tools).

- **Major risks**  
  Scope creep, payment or messaging provider changes, underestimating integration complexity, lack of test coverage, production incidents without monitoring.

- **Key assumptions**  
  Stable access to payment and messaging APIs, ability to use HTTPS and a modern stack, stakeholder availability for feedback.

- **Dependencies on other teams/systems**  
  Finance for payment setup, content team for products and webinar content, operations for support processes.

- **Support & maintenance plan**  
  Owner monitors dashboards and alerts, applies patches, maintains backups, and iterates on features and reliability.

- **Training & rollout plan**  
  Internal training for admins (short documentation and demo session), limited pilot with a few webinars/products, then broader rollout.

- **Open questions / notes**  
  Exact providers to use (payment, email, WhatsApp, monitoring), detailed permission model, multi-language needs, and any future roadmap items.

---

## 6. Domain & Subdomain

The platform will be served under a subdomain of `deltaindo.co.id`, such as **`events.deltaindo.co.id`**.

- Using a subdomain keeps branding consistent and allows separate staging environments if needed.
- Separate staging environments can be created under additional subdomains (e.g., `staging.events.deltaindo.co.id`).

---

## 7. Hosting Infrastructure

The application and database will run on an **IDCloudHost VPS** in Indonesia.

| Resource | Specification |
|---|---|
| CPU | 4 vCPU |
| RAM | 4 GB |
| Storage | 40 GB SSD |
| Location | Indonesia |

Scale up as traffic and data grow. The VPS handles both app server and database.

---

## 8. External Services & Architecture Decisions

This section summarizes the chosen external services and key architecture decisions.

### 8.1 Overview of Selected Services

| Category | Selected Service |
|---|---|
| Payment Gateway | Midtrans / Xendit / DOKU (choose 1) |
| WhatsApp Blasting | WABLAS API Gateway |
| Email Blasting | Gmail API (Google Workspace) |
| Hosting | IDCloudHost VPS |
| Live Video | Zoom |
| Video Recordings | YouTube (unlisted) |
| Domain | subdomain of `deltaindo.co.id` |

### 8.2 Payment Gateway Fee Comparison

| Payment Method | Midtrans | Xendit | DOKU |
|---|---|---|---|
| Virtual Account | ~Rp 4.000 | ~Rp 4.500 (aggregator) / Rp 2.000 + bank (switcher) | ~Rp 4.500 |
| QRIS | ~0.7% MDR | ~0.7% MDR | ~0.7% MDR |
| E-wallets | 1.5% – 2% | 1.5% – 4% | 1.5% – 4% (depends on provider) |
| Credit Cards | 2.9% + Rp 2.000 | 2.9% + Rp 2.000 | 2.8% + Rp 2.000 |
| Minimarkets | ~Rp 5.000 – Rp 6.000 | ~Rp 5.000 – Rp 6.000 | ~Rp 5.000 – Rp 6.000 |

### 8.3 Messaging & Email

- **WhatsApp blasting** will use **WABLAS** (simple REST API gateway).
- **Email blasting and notifications** will use the **Gmail API with Google Workspace** accounts.
  - Limit: up to **2,000 emails/day**. Switch to another provider (e.g., SendGrid, Mailgun) if volume exceeds this limit.

### 8.4 Webinar Delivery

- **Live webinars** will be delivered via **Zoom**.
- **Recordings**, when needed, will be stored as **unlisted YouTube videos** and linked from the user dashboard.

---

*You can further customize this document to your exact stack, providers, and internal constraints, then track it in your repo as a living SRS-lite for your solo project.*
