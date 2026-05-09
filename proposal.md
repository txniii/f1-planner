# F1 Race Weekend Planner

## Project Overview
A web application that helps Formula 1 fans browse the 2025 F1 season, open a specific Grand Prix weekend, and view the full event schedule, circuit information, and manage their saved favorites and personal notes.

## Creator
- Marco Bautista

## Tech Stack
- **Frontend:** PHP (EJS-style templating), HTML5, CSS3, JavaScript
- **Backend:** PHP 8.2 with PDO
- **Database:** MySQL (PlanetScale on production)
- **Hosting:** Render.com
- **Version Control:** GitHub

---

## Milestone 1 — Core Features

| Feature | Status | Notes |
|---|---|---|
| User Registration | ✅ Complete | Password hashed with bcrypt via password_hash() |
| User Login | ✅ Complete | PHP sessions, password_verify() |
| User Logout | ✅ Complete | session_destroy() |
| Browse Race Calendar | ✅ Complete | All 24 races of 2025 F1 season |
| Race Detail Page | ✅ Complete | Circuit info, laps, description |
| Session Schedule | ✅ Complete | FP1, FP2, FP3, Qualifying, Race |
| Search / Filter Races | ✅ Complete | Search by name/country, filter by status/country |
| Responsive Design | ✅ Complete | Mobile-friendly CSS grid layout |

---

## Milestone 2 — User Features

| Feature | Status | Notes |
|---|---|---|
| Save Favorites | ✅ Complete | AJAX toggle via fetch() API, JSON response |
| My Planner Page | ✅ Complete | Shows all saved races and notes |
| Personal Notes | ✅ Complete | Upsert notes per race per user |
| Admin Role | ✅ Complete | Role stored in users table, checked via PHP session |

---

## Milestone 3 — Admin CRUD

| Feature | Status | Notes |
|---|---|---|
| Admin Dashboard | ✅ Complete | Stats: total races, users, favorites, notes |
| Add Race | ✅ Complete | Form creates race + auto-generates 5 sessions |
| Edit Race | ✅ Complete | Pre-populated form, UPDATE query |
| Delete Race | ✅ Complete | Confirm dialog, CASCADE deletes sessions/favorites/notes |
| Admin-only Access | ✅ Complete | requireAdmin() redirects non-admins |

---

## Demo

**Video Link:** (https://youtu.be/yPdN47baGyk)

**Production URL:** (https://f1-planner.onrender.com/index.php)

**Admin credentials for demo:**
- Email: admin@f1planner.com
- Password: admin123

---

## Database Schema

### users
| Column | Type | Notes |
|---|---|---|
| id | INT PK AUTO_INCREMENT | |
| username | VARCHAR(50) UNIQUE | |
| email | VARCHAR(100) UNIQUE | |
| password_hash | VARCHAR(255) | bcrypt |
| role | ENUM('user','admin') | Default: user |
| created_at | TIMESTAMP | |

### races
| Column | Type | Notes |
|---|---|---|
| id | INT PK AUTO_INCREMENT | |
| grand_prix_name | VARCHAR(100) | |
| country | VARCHAR(100) | |
| circuit_name | VARCHAR(150) | |
| race_date | DATE | |
| description | TEXT | |
| flag_emoji | VARCHAR(10) | |
| circuit_length | VARCHAR(20) | |
| lap_count | INT | |
| status | ENUM('upcoming','completed') | |

### sessions
| Column | Type | Notes |
|---|---|---|
| id | INT PK AUTO_INCREMENT | |
| race_id | INT FK → races.id | CASCADE DELETE |
| session_name | VARCHAR(50) | FP1/FP2/FP3/Qualifying/Race |
| session_datetime | DATETIME | |

### favorites
| Column | Type | Notes |
|---|---|---|
| id | INT PK AUTO_INCREMENT | |
| user_id | INT FK → users.id | CASCADE DELETE |
| race_id | INT FK → races.id | CASCADE DELETE |
| created_at | TIMESTAMP | UNIQUE (user_id, race_id) |

### notes
| Column | Type | Notes |
|---|---|---|
| id | INT PK AUTO_INCREMENT | |
| user_id | INT FK → users.id | CASCADE DELETE |
| race_id | INT FK → races.id | CASCADE DELETE |
| note_text | TEXT | |
| updated_at | TIMESTAMP | ON UPDATE CURRENT_TIMESTAMP |

---

## Architecture Notes

- **Authentication:** PHP native sessions + bcrypt password hashing. `requireLogin()` and `requireAdmin()` guard protected pages.
- **Database Access:** PDO with prepared statements throughout — no raw string interpolation in queries, preventing SQL injection.
- **AJAX Favorites:** The favorite toggle sends a JSON POST to `/api/toggle_favorite.php`, which returns `{status: "added"}` or `{status: "removed"}`. JavaScript updates the button state without reloading the page.
- **Admin CRUD:** Adding a race automatically generates 5 sessions (FP1, FP2, FP3, Qualifying, Race) based on the race date. Deleting a race cascades to sessions, favorites, and notes.
- **Deployment:** GitHub → Render.com with auto-deploy on push to `main` branch.
