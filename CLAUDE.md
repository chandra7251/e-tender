# CLAUDE.md — E-PROCUREMENT (TENDER & BIDDING SYSTEM)
> Context file for AI-assisted development. Read this before writing any code, giving advice, or making architectural decisions for this project.

---

## 1. PROJECT OVERVIEW

This is a **Final Project**: a full-stack E-Procurement system that simulates a real company/institution procurement process. It is **NOT** a simple project listing app — it is a **Tender & Bidding System with end-to-end workflow, time-based logic, vendor verification, and winner selection**.

### Core Purpose
Solve the problems of manual procurement:
- Unverified vendors
- Non-transparent tender process
- Unstructured bidding with no deadlines
- Difficulty in objectively determining winners
- Poor documentation

### System Value
The system must demonstrate:
- Tender process runs end-to-end
- Vendors can genuinely participate in tenders
- Bidding actually runs with time constraints
- Winners are objectively selected
- System is usable like a real procurement system

---

## 2. TECHNOLOGY STACK (MANDATORY — DO NOT CHANGE)

| Layer | Technology |
|---|---|
| Mobile / Vendor App | **Ionic + Angular** (NgModules architecture, NOT standalone components) |
| Backend API | **Laravel** (REST API) |
| Web Admin Dashboard | **Laravel Blade** (server-side rendered web) |
| Database | **MySQL** (primary choice) |

### Critical Angular/Ionic Rules
- Every `@Component`, `@Pipe`, `@Directive` **MUST** explicitly declare `standalone: false`
- Use **NgModules** architecture with lazy-loaded feature modules
- Custom bottom navigation via `BottomNavComponent` with `routerLink` / `routerLinkActive`
- Separate service layer from page components (business logic in services, not pages)
- All API calls go through service classes, never directly in components

### Laravel Rules
- Separate: **Controller → Service → Business Logic**
- Time-based logic must be in dedicated service classes
- Bidding logic must be in dedicated service classes
- All business rules enforced at backend level — mobile is client-only

---

## 3. SYSTEM ARCHITECTURE

```
[Ionic + Angular Mobile App]  ←→  [Laravel REST API]  ←→  [MySQL Database]
                                         ↑
                              [Laravel Blade Web Admin]
```

- **Mobile App**: Vendor-facing only. Pure client. No business logic here.
- **Backend API**: Core of the system. All logic, validation, time control, workflow.
- **Web Admin**: Admin-facing only. Procurement team control panel.
- Roles must be strictly separated — vendor and admin must NEVER share the same interface without explicit role separation.

---

## 4. ROLES & PERMISSIONS

### 4.1 Vendor (Mobile App)
Vendors are tender participants. They access the system via the mobile app.

**Permissions:**
- Register and login
- Upload company documents
- View verification status
- View open tender list
- View tender detail
- Join tender (only if `approved`)
- Read aanwijzing (tender clarification) information
- Submit bid (only during active bidding window)
- Update bid (optional, recommended, only during active bidding window)
- View tender results (win/loss status)

**Restrictions:**
- Cannot join tender if status is `pending` or `rejected`
- Cannot submit bid outside the bidding time window
- Cannot see other vendors' bid amounts (optional but recommended)

### 4.2 Admin / Procurement Team (Web Dashboard)
Admins manage the entire procurement process. They access via the web dashboard.

**Permissions:**
- Login to web dashboard
- View and manage all vendors
- View vendor documents
- Approve or reject vendor verification
- Create and edit tenders
- Set tender specifications and timeline
- Change tender status
- Manage aanwijzing content
- Monitor all bids in real-time
- Close bidding
- Select winner from bid list
- Generate tender result / simple PO document
- View full tender history

---

## 5. MODULES & DETAILED REQUIREMENTS

### 5.1 MODULE: AUTHENTICATION

| Requirement | Role | Priority |
|---|---|---|
| Vendor registration | Vendor | MANDATORY |
| Login | Both | MANDATORY |
| Logout | Both | MANDATORY |
| Role differentiation (vendor vs admin) | System | MANDATORY |
| Update profile | Both | MANDATORY |
| Change password | Both | MANDATORY |

**Backend must:**
- Hash all passwords (bcrypt minimum)
- Return role-specific tokens / session
- Guard routes by role — vendor cannot access admin routes, admin cannot access vendor-only routes

---

### 5.2 MODULE: VENDOR MANAGEMENT

#### 5.2.1 Vendor Registration
Vendor must fill:
- Company name (`nama_perusahaan`)
- Email
- Address (`alamat`)
- Contact (`kontak`)

#### 5.2.2 Document Upload
Vendor must be able to upload company documents:
- Business legality documents
- Business license (`izin usaha`)
- Other supporting documents

Documents stored and accessible by admin for review.

#### 5.2.3 Vendor Verification
Admin must be able to:
- View vendor data list
- View uploaded documents per vendor
- Change vendor status:

```
pending → approved
pending → rejected
```

**Status definitions:**
| Status | Meaning |
|---|---|
| `pending` | Registered, awaiting admin review |
| `approved` | Verified, can participate in tenders |
| `rejected` | Not verified, cannot participate |

**Rule:** Only `approved` vendors can join any tender. Backend must enforce this.

---

### 5.3 MODULE: TENDER MANAGEMENT

#### 5.3.1 Create Tender
Admin must fill:
- Tender name (`nama_tender`)
- Description (`deskripsi`)
- Specifications (`spesifikasi`)
- Start date (`tanggal_mulai`)
- End date (`tanggal_selesai`)
- Bidding time window (`waktu_bidding_mulai`, `waktu_bidding_selesai`)

#### 5.3.2 Tender Status (State Machine)
Tender MUST follow this status lifecycle:

```
draft → open → aanwijzing → bidding → closed → finished
```

| Status | Description |
|---|---|
| `draft` | Created by admin, not yet visible to vendors |
| `open` | Visible to vendors, can join |
| `aanwijzing` | Clarification phase, admin posts additional info |
| `bidding` | Bidding window is active |
| `closed` | Bidding window has ended |
| `finished` | Winner selected, result published |

Admin must be able to manually change status. System may also auto-change status based on timeline (semi-automatic is acceptable).

#### 5.3.3 Tender Listing (Vendor View)
Vendor must be able to:
- See list of tenders (status: `open`, `aanwijzing`, `bidding`, `closed`, `finished`)
- View tender detail
- See current tender status clearly

---

### 5.4 MODULE: AANWIJZING (TENDER CLARIFICATION)

- Admin adds clarification information / updates for a tender
- Vendors can read this information
- Multiple updates can be added by admin
- Vendors can NOT post to aanwijzing — read only

---

### 5.5 MODULE: TENDER PARTICIPATION

- Approved vendors can register as participants of a tender
- System records vendor as participant (`tender_participants` table)
- Only `approved` vendors can register — backend must validate this
- Backend must prevent duplicate participation

---

### 5.6 MODULE: BIDDING SYSTEM

#### 5.6.1 Bidding Input (Vendor)
Vendor must be able to:
- Submit a price offer (`nilai_penawaran`)
- Optionally update their offer while bidding window is still open
- Know if the bidding window is currently active

#### 5.6.2 Time-Based Bidding Control (MANDATORY — CRITICAL)
This is the core of the system. All of the following are MANDATORY:

```
bidding_start_time ← system opens bidding
       ↓
  [accept bids]
       ↓
bidding_end_time ← system closes bidding
       ↓
  [reject all new bids]
```

Backend MUST:
- Check `NOW()` against `bidding_start_time` and `bidding_end_time` on every bid submission
- **Reject** any bid submitted before `bidding_start_time`
- **Reject** any bid submitted after `bidding_end_time`
- Return clear error message when bid is rejected due to time
- Auto-change or support semi-auto change of tender status when time window passes

Mobile app MUST:
- Display whether bidding is currently active or not
- Disable bid input form when outside bidding window
- Show countdown or clear time indicator

#### 5.6.3 Bidding Monitoring (Admin)
Admin must be able to:
- View all bid submissions for a tender
- See which vendors submitted bids
- See each vendor's bid amount
- Monitor bid activity in real time (or near real time)

---

### 5.7 MODULE: WINNER SELECTION

Admin must be able to:
- View all bids for a tender side by side
- Select a winner (default basis: lowest price; admin discretion also acceptable)
- Save the winner decision to the system

System must:
- Record the winning vendor (`vendor_id`)
- Record the winning bid amount
- Record the decision timestamp

---

### 5.8 MODULE: RESULT & PO OUTPUT

System must:
- Display tender result after winner is selected
- Show winner information
- Show winning bid details
- Generate a simple Purchase Order (PO) document

Vendor must be able to:
- View the result of a tender they participated in
- Know if they won or lost
- View detail of the result

Admin must be able to:
- View full result for all tenders
- Access/download the simple PO document
- View tender history

---

### 5.9 MODULE: ADMIN DASHBOARD

Admin dashboard homepage MUST display:
- Total number of vendors
- Number of approved vendors
- Number of active tenders (status: `open`, `aanwijzing`, `bidding`)
- Number of finished tenders
- Number of tender participants
- Bidding statistics (total bids submitted, etc.)

---

## 6. DATA REQUIREMENTS

All of the following data MUST be stored. Database design is flexible but these entities and fields are mandatory.

### 6.1 Users / Auth
```
users:
  - id
  - name
  - email
  - password (hashed)
  - role (vendor | admin)
  - created_at
  - updated_at
```

### 6.2 Vendor Profile
```
vendors:
  - id
  - user_id (FK → users)
  - nama_perusahaan
  - alamat
  - kontak
  - status (pending | approved | rejected)
  - created_at
  - updated_at
```

### 6.3 Vendor Documents
```
vendor_documents:
  - id
  - vendor_id (FK → vendors)
  - document_type (legalitas | izin_usaha | lainnya)
  - file_path
  - uploaded_at
```

### 6.4 Tenders
```
tenders:
  - id
  - nama_tender
  - deskripsi
  - spesifikasi
  - status (draft | open | aanwijzing | bidding | closed | finished)
  - created_by (FK → users / admin)
  - created_at
  - updated_at
```

### 6.5 Tender Timeline
```
tender_timelines:
  - id
  - tender_id (FK → tenders)
  - tanggal_mulai
  - tanggal_selesai
  - aanwijzing_start
  - aanwijzing_end
  - bidding_start       ← CRITICAL for time-based logic
  - bidding_end         ← CRITICAL for time-based logic
```

### 6.6 Aanwijzing
```
aanwijzings:
  - id
  - tender_id (FK → tenders)
  - content
  - posted_by (FK → users / admin)
  - created_at
```

### 6.7 Tender Participants
```
tender_participants:
  - id
  - tender_id (FK → tenders)
  - vendor_id (FK → vendors)
  - joined_at
  - UNIQUE(tender_id, vendor_id)
```

### 6.8 Bids
```
bids:
  - id
  - tender_id (FK → tenders)
  - vendor_id (FK → vendors)
  - nilai_penawaran (decimal)
  - submitted_at       ← timestamp of submission
  - updated_at
```

### 6.9 Tender Results / Winner
```
tender_results:
  - id
  - tender_id (FK → tenders)
  - winner_vendor_id (FK → vendors)
  - winning_bid_id (FK → bids)
  - winning_amount (decimal)
  - decided_by (FK → users / admin)
  - decided_at
  - notes (optional)
```

### 6.10 PO (Purchase Order) — Simple
```
purchase_orders:
  - id
  - tender_result_id (FK → tender_results)
  - po_number (auto-generated)
  - issued_at
  - content / details (JSON or text)
```

---

## 7. SYSTEM FLOWS

### 7.1 Vendor Registration Flow
```
Vendor registers → fills company data → uploads documents
→ status = pending
→ Admin reviews documents
→ Admin approves → status = approved  (can join tenders)
→ Admin rejects  → status = rejected  (cannot join tenders)
```

### 7.2 Tender Creation Flow
```
Admin creates tender → status = draft
→ Admin publishes → status = open
→ Vendors can see and join the tender
→ Admin starts aanwijzing phase → status = aanwijzing
→ Admin posts aanwijzing info → Vendors read
→ Admin opens bidding → status = bidding
→ Bidding window active (NOW >= bidding_start AND NOW <= bidding_end)
→ Vendors submit bids
→ Bidding window ends (NOW > bidding_end) → bids rejected
→ Status changes to → closed
→ Admin reviews all bids
→ Admin selects winner
→ Status changes to → finished
→ PO generated, result published
```

### 7.3 Bidding Flow (Time-Based Logic)
```
Request: POST /api/bids

Backend checks:
1. Is vendor authenticated? → 401 if not
2. Is vendor status = approved? → 403 if not
3. Is vendor a participant of this tender? → 403 if not
4. Is tender status = 'bidding'? → 422 if not
5. Is NOW() >= bidding_start? → 422 if not (too early)
6. Is NOW() <= bidding_end? → 422 if not (too late)
7. All checks pass → save bid → 201 success
```

### 7.4 Winner Selection Flow
```
Admin views all bids for a tender
→ Sees vendor name + bid amount for each
→ Admin selects winner (may auto-sort by lowest price)
→ Admin confirms decision
→ System saves to tender_results
→ Tender status → finished
→ PO generated
→ All vendor participants can see their win/loss status
```

---

## 8. API ENDPOINT STRUCTURE

All endpoints prefixed with `/api/v1/`

### Authentication
| Method | Endpoint | Description | Auth |
|---|---|---|---|
| POST | `/auth/register` | Vendor register | Public |
| POST | `/auth/login` | Login (vendor + admin) | Public |
| POST | `/auth/logout` | Logout | Required |
| GET | `/auth/me` | Get current user profile | Required |
| PUT | `/auth/profile` | Update profile | Required |
| PUT | `/auth/password` | Change password | Required |

### Vendors (Admin)
| Method | Endpoint | Description | Auth |
|---|---|---|---|
| GET | `/vendors` | List all vendors | Admin |
| GET | `/vendors/{id}` | Vendor detail | Admin |
| PUT | `/vendors/{id}/approve` | Approve vendor | Admin |
| PUT | `/vendors/{id}/reject` | Reject vendor | Admin |
| GET | `/vendors/{id}/documents` | View vendor documents | Admin |

### Vendor Profile (Vendor)
| Method | Endpoint | Description | Auth |
|---|---|---|---|
| GET | `/vendor/profile` | Get own profile | Vendor |
| PUT | `/vendor/profile` | Update own profile | Vendor |
| POST | `/vendor/documents` | Upload document | Vendor |
| GET | `/vendor/documents` | List own documents | Vendor |
| GET | `/vendor/status` | Get verification status | Vendor |

### Tenders
| Method | Endpoint | Description | Auth |
|---|---|---|---|
| GET | `/tenders` | List tenders | Vendor (open+) |
| GET | `/tenders/{id}` | Tender detail | Vendor |
| POST | `/tenders` | Create tender | Admin |
| PUT | `/tenders/{id}` | Update tender | Admin |
| PUT | `/tenders/{id}/status` | Change tender status | Admin |
| GET | `/admin/tenders` | All tenders (admin view) | Admin |

### Tender Timeline
| Method | Endpoint | Description | Auth |
|---|---|---|---|
| POST | `/tenders/{id}/timeline` | Set timeline | Admin |
| PUT | `/tenders/{id}/timeline` | Update timeline | Admin |
| GET | `/tenders/{id}/timeline` | Get timeline | Both |

### Aanwijzing
| Method | Endpoint | Description | Auth |
|---|---|---|---|
| GET | `/tenders/{id}/aanwijzing` | Get aanwijzing list | Both |
| POST | `/tenders/{id}/aanwijzing` | Post aanwijzing | Admin |
| PUT | `/tenders/{id}/aanwijzing/{aId}` | Update aanwijzing | Admin |

### Participation
| Method | Endpoint | Description | Auth |
|---|---|---|---|
| POST | `/tenders/{id}/join` | Join tender | Vendor (approved only) |
| GET | `/tenders/{id}/participants` | List participants | Admin |
| GET | `/vendor/tenders` | My tenders | Vendor |

### Bidding
| Method | Endpoint | Description | Auth |
|---|---|---|---|
| POST | `/tenders/{id}/bids` | Submit bid | Vendor (time-checked) |
| PUT | `/tenders/{id}/bids` | Update bid | Vendor (time-checked) |
| GET | `/tenders/{id}/bids` | All bids | Admin |
| GET | `/tenders/{id}/my-bid` | My bid for tender | Vendor |

### Winner & Results
| Method | Endpoint | Description | Auth |
|---|---|---|---|
| POST | `/tenders/{id}/winner` | Select winner | Admin |
| GET | `/tenders/{id}/result` | Get result | Both |
| GET | `/tenders/{id}/po` | Get PO document | Admin |
| GET | `/vendor/results` | My tender results | Vendor |

### Dashboard
| Method | Endpoint | Description | Auth |
|---|---|---|---|
| GET | `/dashboard/stats` | Dashboard statistics | Admin |

---

## 9. MOBILE APP (IONIC + ANGULAR) — PAGE STRUCTURE

### Module Structure (NgModules — Lazy Loaded)

```
AppModule
├── AuthModule (lazy)
│   ├── LoginPage
│   └── RegisterPage
├── VendorModule (lazy)
│   ├── ProfilePage
│   ├── DocumentsPage
│   └── VerificationStatusPage
├── TenderModule (lazy)
│   ├── TenderListPage
│   ├── TenderDetailPage
│   └── JoinTenderPage
├── AanwijzingModule (lazy)
│   └── AanwijzingViewPage
├── BiddingModule (lazy)
│   ├── BiddingPage        ← time-based bidding UI
│   └── MyBidPage
└── ResultModule (lazy)
    ├── TenderResultPage
    └── MyResultsPage
```

### Services Required
```
services/
├── auth.service.ts         — login, register, token management
├── vendor.service.ts       — profile, documents, status
├── tender.service.ts       — tender list, detail, join
├── aanwijzing.service.ts   — read aanwijzing info
├── bidding.service.ts      — submit bid, check time window
├── result.service.ts       — view results
└── api.service.ts          — base HTTP client wrapper
```

### Bidding Page Critical Logic
```typescript
// BiddingPage must:
// 1. Load bidding_start and bidding_end from API
// 2. Check current time against window
// 3. Disable input form if outside window
// 4. Show clear status: "Bidding Active" / "Bidding Closed" / "Bidding Not Yet Open"
// 5. Show time remaining (countdown) if active

isBiddingActive(): boolean {
  const now = new Date();
  return now >= this.biddingStart && now <= this.biddingEnd;
}
```

---

## 10. WEB ADMIN DASHBOARD (LARAVEL BLADE) — PAGE STRUCTURE

```
Admin Web Pages:
├── /admin/dashboard              — Overview stats
├── /admin/vendors                — Vendor list
├── /admin/vendors/{id}           — Vendor detail + documents
├── /admin/vendors/{id}/verify    — Approve/reject vendor
├── /admin/tenders                — Tender list
├── /admin/tenders/create         — Create new tender
├── /admin/tenders/{id}/edit      — Edit tender
├── /admin/tenders/{id}/timeline  — Set timeline
├── /admin/tenders/{id}/aanwijzing — Manage aanwijzing
├── /admin/tenders/{id}/participants — View participants
├── /admin/tenders/{id}/bids      — Monitor bids
├── /admin/tenders/{id}/winner    — Select winner
└── /admin/tenders/{id}/result    — View result + PO
```

---

## 11. BACKEND LARAVEL — SERVICE LAYER STRUCTURE

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   │   └── AuthController.php
│   │   ├── Vendor/
│   │   │   ├── VendorProfileController.php
│   │   │   └── VendorDocumentController.php
│   │   ├── Admin/
│   │   │   ├── VendorManagementController.php
│   │   │   ├── TenderController.php
│   │   │   ├── AanwijzingController.php
│   │   │   ├── BiddingMonitorController.php
│   │   │   ├── WinnerController.php
│   │   │   └── DashboardController.php
│   │   └── Api/
│   │       ├── TenderApiController.php
│   │       ├── ParticipationApiController.php
│   │       ├── BiddingApiController.php
│   │       └── ResultApiController.php
│   └── Middleware/
│       ├── EnsureVendorApproved.php
│       └── EnsureAdmin.php
├── Services/
│   ├── AuthService.php
│   ├── VendorService.php
│   ├── TenderService.php
│   ├── TimeControlService.php      ← CRITICAL: bidding time logic
│   ├── BiddingService.php          ← CRITICAL: bid validation & storage
│   ├── WinnerSelectionService.php
│   ├── ResultService.php
│   └── DashboardService.php
└── Models/
    ├── User.php
    ├── Vendor.php
    ├── VendorDocument.php
    ├── Tender.php
    ├── TenderTimeline.php
    ├── Aanwijzing.php
    ├── TenderParticipant.php
    ├── Bid.php
    ├── TenderResult.php
    └── PurchaseOrder.php
```

### TimeControlService — Core Logic
```php
class TimeControlService {
    public function isBiddingOpen(Tender $tender): bool {
        $now = now();
        $timeline = $tender->timeline;
        return $now->gte($timeline->bidding_start) 
            && $now->lte($timeline->bidding_end);
    }

    public function rejectIfOutsideBiddingWindow(Tender $tender): void {
        if (!$this->isBiddingOpen($tender)) {
            abort(422, 'Bidding is not currently active for this tender.');
        }
    }
}
```

---

## 12. SEEDING DATA (MANDATORY)

The following seed data MUST be provided so the system can be tested during demo:

```
Seeders required:
├── AdminSeeder          — 1 admin account
├── VendorSeeder         — min. 3 vendors:
│   ├── 1 vendor: status = pending
│   ├── 1 vendor: status = approved
│   └── 1 vendor: status = approved (with bids)
├── TenderSeeder         — min. 2 tenders:
│   ├── 1 tender: status = bidding (active window)
│   └── 1 tender: status = finished (with winner)
├── AanwijzingSeeder     — sample aanwijzing for each tender
├── ParticipantSeeder    — approved vendors joined tenders
├── BidSeeder            — sample bids from vendors
└── ResultSeeder         — 1 winner selected, PO generated
```

---

## 13. NON-FUNCTIONAL REQUIREMENTS

| Requirement | Standard |
|---|---|
| API response time | < 3 seconds |
| Password storage | Hashed (bcrypt) |
| Input validation | MANDATORY on all endpoints |
| Vendor bid privacy | Vendors MUST NOT see other vendors' bid amounts |
| System stability | Must not crash during demo |
| Bidding time enforcement | Must work correctly |
| UI clarity | Tender status must be clearly visible |
| Timeline readability | Bidding open/close time must be easy to understand |

---

## 14. DEPLOYMENT REQUIREMENTS (MANDATORY)

| Component | Requirement |
|---|---|
| Backend API | Must be deployed online (accessible via public URL) |
| Web Admin Dashboard | Must be deployed online |
| Mobile App | APK/AAB must be buildable and installable |
| Demo | End-to-end live demo required |

### Demo Checklist (All must work)
- [ ] Vendor registers
- [ ] Vendor uploads documents
- [ ] Admin verifies vendor
- [ ] Admin creates tender with timeline
- [ ] Vendor joins tender
- [ ] Aanwijzing information posted and readable
- [ ] Bidding window opens
- [ ] Vendor submits bid within time window
- [ ] System rejects bid outside time window
- [ ] Admin monitors bids
- [ ] Bidding window closes
- [ ] Admin selects winner
- [ ] Tender result published
- [ ] Vendor views win/loss result

---

## 15. FAILURE CONDITIONS

The project is considered **FAILED** if any of the following are true:

- No time-based bidding (bids accepted at any time without enforcement)
- No vendor verification process
- Bidding does not actually run (no real workflow)
- No winner selection mechanism
- No complete end-to-end flow
- System is only CRUD for tenders
- Not deployed online
- Admin and vendor share the same interface without role separation

---

## 16. ACCEPTANCE CRITERIA

The project is considered **SUCCESSFUL** if ALL of the following pass:

- [ ] Vendor can register and be verified by admin
- [ ] Only approved vendors can join tenders
- [ ] Vendor can submit bids during active bidding window
- [ ] System rejects bids submitted outside bidding window
- [ ] Admin can view all bids
- [ ] Admin can select a winner
- [ ] Tender result is displayed to both admin and vendor
- [ ] System runs end-to-end without crashing
- [ ] UI is clean and status information is clear
- [ ] System is deployed and accessible online

---

## 17. WHAT THIS PROJECT IS AND IS NOT

| ❌ This project is NOT | ✅ This project IS |
|---|---|
| A project listing app | A tender management system with real workflow |
| Simple CRUD for tenders | A procurement system with business process enforcement |
| A price input form | A time-bound bidding system with validation |
| A static dashboard | A live monitoring and decision system |

---

## 18. TEAM CODING STANDARDS

- All naming conventions must be consistent across all 5 team members
- Database column names: `snake_case`
- Laravel route names: `kebab-case`
- Angular component selectors: `app-component-name`
- API responses must follow consistent JSON structure:
```json
{
  "status": "success" | "error",
  "message": "...",
  "data": { ... }
}
```
- All API endpoints must be versioned: `/api/v1/...`
- Migrations must be committed — never share raw SQL
- All team members use the same `.env` structure (use `.env.example`)

---

*This CLAUDE.md was generated from: BRD, Technical Blueprint (TB), and SRS documents for E-Procurement Final Project.*
*Last updated based on source documents. Do not modify without updating source docs.*
