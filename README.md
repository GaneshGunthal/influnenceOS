# InfluenceOS – Creator & Agency Intelligence Management System (CAIMS)

## 🎯 Project Overview

**InfluenceOS (CAIMS)** is a full-stack web application for managing influencer marketing operations. It provides creators, agencies, and brands with a centralized platform for analytics, campaign management, deal valuation, financial tracking, and performance reporting.

- **Project Type:** Web Application (Academic + SaaS-ready)
- **University:** Savitribai Phule Pune University
- **Degree:** BBA-CA (TYBBA-CA), Semester VI
- **Students:** Ganesh Gunthal & Kiran Jadhav
- **Guide:** Prof. Sachin Ponde
- **Academic Year:** 2025–26

---

## 🛠️ Tech Stack

| Layer        | Technology              |
|--------------|-------------------------|
| Frontend     | HTML5, CSS3, JavaScript |
| Charts       | Chart.js v4             |
| Backend      | PHP 8+                  |
| Database     | MySQL 8                 |
| Server       | Apache (XAMPP/WAMP)     |
| PDF Export   | mPDF or TCPDF           |
| Auth         | PHP Sessions + bcrypt   |

---

## 👥 User Roles

| Role           | Permissions |
|----------------|-------------|
| Admin          | Full system access, user management, reports |
| Agency Manager | Campaigns, creators, financials, reports |
| Creator        | Own profile, analytics, campaigns, payments |
| Brand          | View campaign progress, ROI reports |

---

## 📦 9 Core Modules to Build

### Module 1 – User & Role Management
- Registration, Login, Logout
- Role-based access control (Admin / Agency / Creator / Brand)
- Password hashing with `password_hash()` (bcrypt)
- Session management with timeout
- Activity logging (user_id, action, timestamp, ip)

### Module 2 – Creator Profile Management
- CRUD for creator profiles
- Fields: handle, platform, followers, avg_views, niche, audience demographics, content frequency
- Search/filter by niche, platform, follower range

### Module 3 – Creator Analytics & Performance Engine
- Calculate and store: Engagement Rate, Growth Rate, Save/Share Ratio, Consistency Score, Authenticity Score
- **Performance Score Formula:**
  ```
  Performance Score = (0.35 × ER) + (0.25 × GR) + (0.20 × Audience Quality) + (0.20 × Consistency Score)
  ```
- Visualize with Chart.js bar and line charts

### Module 4 – Brand Deal Valuation Engine
- **Deal Price Formula:**
  ```
  Estimated Deal Price = (Base CPM × Avg Views / 1000) × Niche Multiplier × Engagement Modifier × Authenticity Score
  ```
- CPM range: $5–$50 | Niche Multiplier: 0.8x–2.5x | Engagement Modifier: 0.7x–1.5x | Authenticity: 0.5–1.0
- Show auto price suggestion + negotiation range

### Module 5 – Campaign Management
- Create/Edit/Delete campaigns with name, brand, budget, KPIs, start/end dates
- Assign creators to campaigns
- Status flow: Draft → Active → In Review → Completed → Archived
- Deliverable upload (URL-based)
- Deadline overdue detection

### Module 6 – Smart Fit Score Engine
- **Fit Score Formula:**
  ```
  Fit Score = (0.40 × Audience Match) + (0.30 × Engagement Strength) + (0.20 × Past ROI) + (0.10 × Niche Relevance)
  ```
- Output: Suitability %, Risk level (Low/Medium/High), ROI expectation
- Rank creators for a campaign

### Module 7 – ROI & Performance Tracking
- **ROI Formula:** `ROI = (Revenue - Cost) / Cost × 100`
- Also: Cost per Engagement, Cost per Conversion, Efficiency Ratio
- Chart.js visualization for ROI trends

### Module 8 – Financial & Commission Management
- Deal tracking with commission auto-calculation
- Payment status: Pending / Partial / Paid / Overdue
- Invoice generation (PDF)
- Monthly revenue dashboard

### Module 9 – Media Kit & Reporting
- Auto-generate creator performance summary
- Campaign analytics report
- Downloadable PDF (mPDF/TCPDF)
- Shareable public profile link

---

## 📁 Folder Structure

```
influenceos/
├── index.php                  # Landing / Login page
├── dashboard.php              # Role-based dashboard redirect
├── config/
│   └── db.php                 # DB connection
├── auth/
│   ├── login.php
│   ├── register.php
│   └── logout.php
├── modules/
│   ├── creators/              # Module 2
│   ├── analytics/             # Module 3
│   ├── deals/                 # Module 4
│   ├── campaigns/             # Module 5
│   ├── fitscore/              # Module 6
│   ├── roi/                   # Module 7
│   ├── finance/               # Module 8
│   └── mediakit/              # Module 9
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   ├── charts.js
│   │   └── main.js
│   └── img/
├── includes/
│   ├── header.php
│   ├── sidebar.php
│   └── footer.php
├── admin/
│   ├── users.php
│   └── logs.php
└── database/
    ├── schema.sql
    └── seed.sql
```

---

## 🗄️ Database

See `database/schema.sql` for the full MySQL schema.

**Core Tables:**
- `users` – all platform users with roles
- `creators` – creator profiles
- `campaigns` – campaign lifecycle
- `campaign_creators` – creator assignments per campaign
- `deals` – financial deals and commissions
- `analytics` – daily performance metrics per creator
- `invoices` – invoice records
- `activity_log` – audit trail

---

## ✅ Key Constraints & Rules

1. All passwords must be hashed with `password_hash()` — never plain text
2. Role checks on every page using `$_SESSION['role']`
3. All form inputs must be sanitized with `htmlspecialchars()` and `prepared statements`
4. Division-by-zero must be handled in all formula calculations
5. All monetary values stored as `DECIMAL(10,2)`
6. Dates validated: end_date must be >= start_date
7. Commission rate must be between 0 and 100
8. Use Chart.js CDN for all visualizations
9. Responsive design (mobile-friendly CSS)
10. Use `mPDF` or `TCPDF` for PDF generation

---

## 🚀 Agent Task Order

Build in this sequence for best results:

1. `tasks/01_database.md` – Create full MySQL schema + seed data
2. `tasks/02_auth.md` – Login, register, sessions, role access
3. `tasks/03_layout.md` – Header, sidebar, footer, CSS dashboard layout
4. `tasks/04_creators.md` – Creator profile CRUD
5. `tasks/05_analytics.md` – Performance engine + Chart.js
6. `tasks/06_deals.md` – Deal valuation engine
7. `tasks/07_campaigns.md` – Campaign management
8. `tasks/08_fitscore.md` – Fit score engine
9. `tasks/09_roi.md` – ROI tracking + charts
10. `tasks/10_finance.md` – Commission, payments, invoices
11. `tasks/11_mediakit.md` – PDF reports + public profiles
12. `tasks/12_admin.md` – Admin panel + activity logs

---

## 📋 Testing

See `docs/test_cases.md` for 87 test cases covering all 9 modules.

---

## 📚 References

- PHP Docs: https://www.php.net/docs.php
- MySQL Docs: https://dev.mysql.com/doc/
- Chart.js: https://www.chartjs.org/docs/
- mPDF: https://mpdf.github.io/
- YouTube API: https://developers.google.com/youtube/v3
