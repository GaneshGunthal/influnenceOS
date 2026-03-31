# 🤖 AGENT INSTRUCTIONS – InfluenceOS (CAIMS)

> This file is for the Antigravity AI Agent. Read this first before doing anything.

---

## What You Are Building

A full-stack PHP + MySQL web application called **InfluenceOS** — a Creator & Agency Intelligence Management System for managing influencer marketing operations.

**Read `README.md` completely before starting.**

---

## How to Execute This Project

Work through the tasks folder **in order**:

| Step | Task File                        | What to Build                          |
|------|----------------------------------|----------------------------------------|
| 1    | `tasks/01_database.md`           | MySQL schema + seed data + db.php      |
| 2    | `tasks/02_auth.md`               | Login, register, sessions, guard       |
| 3    | `tasks/03_layout.md`             | Header, sidebar, footer, CSS           |
| 4    | `tasks/04_to_12_modules.md`      | All 9 core modules (Tasks 4–12)        |

---

## Rules You Must Follow

1. **PDO Prepared Statements ONLY** — never raw string SQL queries
2. **password_hash() / password_verify()** — never store plain text passwords
3. **Sanitize all outputs** with `htmlspecialchars()` to prevent XSS
4. **Division by zero** — handle in every formula function (return 0.0)
5. **Role checks** — every protected page must include `auth/guard.php`
6. **CSRF tokens** — include on all forms
7. **Chart.js** — use CDN, do not download locally
8. **mPDF** — use composer for PDF generation (`composer require mpdf/mpdf`)
9. **Responsive design** — all pages must work on mobile (min 320px)
10. **Comments** — add PHPDoc comments on all functions

---

## Formulas Reference (Never hardcode — use functions)

```
Performance Score = (0.35 × ER) + (0.25 × GR) + (0.20 × AQ) + (0.20 × CS)
Deal Price        = (CPM × Views / 1000) × NicheMultiplier × EngagementModifier × AuthenticityScore
Fit Score         = (0.40 × AudienceMatch) + (0.30 × EngagementStrength) + (0.20 × PastROI) + (0.10 × NicheRelevance)
ROI               = (Revenue - Cost) / Cost × 100
CostPerEngagement = Cost / Engagements
EfficiencyRatio   = ActualKPI / TargetKPI × 100
```

---

## File Structure to Create

```
influenceos/
├── index.php
├── dashboard.php
├── config/db.php
├── auth/login.php, register.php, logout.php, guard.php
├── includes/header.php, sidebar.php, footer.php
├── assets/css/style.css
├── assets/js/main.js, charts.js
├── modules/
│   ├── creators/list.php, add.php, edit.php, view.php
│   ├── analytics/view.php, add.php, calculate.php
│   ├── deals/valuation.php, create.php, list.php
│   ├── campaigns/list.php, create.php, view.php, assign.php
│   ├── fitscore/calculate.php, rank.php
│   ├── roi/dashboard.php, campaign_roi.php, calculate.php
│   ├── finance/deals.php, create_deal.php, invoice.php, invoice_pdf.php, dashboard.php
│   └── mediakit/generate.php, public_profile.php, campaign_report.php
└── admin/dashboard.php, users.php, logs.php
```

---

## Test After Each Module

After building each module, verify against the test cases in `docs/test_cases.md`.
Run the specific test cases for that module before moving to the next.

---

## Database Credentials (Default XAMPP)
- Host: `localhost`
- Database: `influenceos`
- User: `root`
- Password: `` (empty)

Change in `config/db.php` if different.

---

## You're Ready. Start with Task 01.
