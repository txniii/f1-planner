# F1 Race Weekend Planner — Complete Setup Guide

> Follow these steps in order. Every step is required. Takes about 30–45 minutes total.

---

## PART 1 — Install Required Software

### Step 1: Install Git
1. Go to https://git-scm.com/download/win
2. Download and run the installer (click Next on everything)
3. Verify: Open **Command Prompt** and type `git --version` → should show a version number

### Step 2: Install PHP
1. Go to https://windows.php.net/download/
2. Download **PHP 8.2 Non Thread Safe** (x64 zip)
3. Extract the zip to `C:\php`
4. Copy `C:\php\php.ini-development` → rename the copy to `C:\php\php.ini`
5. Open `C:\php\php.ini` in Notepad and find these lines, remove the `;` at the start:
   ```
   ;extension=pdo_mysql    →   extension=pdo_mysql
   ;extension=openssl      →   extension=openssl
   ;extension=mbstring     →   extension=mbstring
   ```
6. Add PHP to your PATH:
   - Search Windows for "Environment Variables" → click it
   - Under "System Variables" click "Path" → Edit → New
   - Type `C:\php` → OK → OK → OK
7. Verify: Open a **new** Command Prompt and type `php --version`

### Step 3: Install MySQL (XAMPP is easiest)
1. Go to https://www.apachefriends.org/download.html
2. Download and install **XAMPP** (includes MySQL)
3. Open **XAMPP Control Panel** and click **Start** next to MySQL
4. MySQL is now running on localhost port 3306

---

## PART 2 — Set Up GitHub

### Step 4: Create a GitHub account
1. Go to https://github.com and sign up for a free account
2. Verify your email

### Step 5: Create a new repository
1. Click the **+** icon top right → **New repository**
2. Name it: `f1-planner`
3. Set it to **Public**
4. Do NOT check any "Initialize" boxes
5. Click **Create repository**
6. Copy the URL shown (looks like `https://github.com/YOURNAME/f1-planner.git`)

### Step 6: Put the project on GitHub
Open Command Prompt and run these commands one by one:
```
cd Desktop
git init f1-planner
cd f1-planner
```
Now copy ALL the project files I gave you into this `f1-planner` folder on your Desktop.
Then run:
```
git add .
git commit -m "Initial commit - F1 Race Weekend Planner"
git branch -M main
git remote add origin https://github.com/YOURNAME/f1-planner.git
git push -u origin main
```
(Replace YOURNAME with your actual GitHub username)

---

## PART 3 — Set Up the Database Locally

### Step 7: Create the database
1. Open your browser and go to: http://localhost/phpmyadmin
2. Click **SQL** tab at the top
3. Copy the entire contents of `database.sql` and paste it into the box
4. Click **Go** — this creates all the tables and adds all 24 F1 races

---

## PART 4 — Run the App Locally

### Step 8: Start the local server
Open Command Prompt:
```
cd Desktop\f1-planner
php -S localhost:8000
```
Open your browser and go to: **http://localhost:8000**

You should see the F1 Planner homepage with all 24 races!

**Test these things locally:**
- Register a new user account
- Browse races, click one to see the detail page
- Save a favorite (★ Fav button)
- Add a note on the race detail page
- Visit "My Planner" from the nav
- Log out, log back in as admin: `admin@f1planner.com` / `admin123`
- Visit the Admin panel, add/edit/delete a race

---

## PART 5 — Deploy to Render.com (Production)

> The assignment requires your demo to be from Render.com prod, not localhost.

### Step 9: Create Render account
1. Go to https://render.com and sign up with your GitHub account
2. Click **Authorize** to connect GitHub

### Step 10: Create the MySQL database on Render
> Note: Render's free tier uses PostgreSQL natively, but we'll use PlanetScale (free MySQL) for PHP compatibility.

**Option A — Use PlanetScale (Free MySQL, recommended for PHP):**
1. Go to https://planetscale.com and sign up free
2. Click **Create database** → name it `f1planner` → choose region closest to you → Create
3. Click **Connect** → choose **PHP (PDO)** → copy the credentials shown:
   - Host, Username, Password, Database name
4. In your project's `includes/db.php`, you'll set these as environment variables on Render

**Option B — Use Render's PostgreSQL (requires slight code changes):**
Skip this if you used Option A.

### Step 11: Deploy the web service on Render
1. Go to https://dashboard.render.com
2. Click **New +** → **Web Service**
3. Choose **Connect a repository** → select your `f1-planner` repo
4. Fill in:
   - **Name:** `f1-planner`
   - **Runtime:** `PHP`  (or `Docker` if PHP isn't listed — see note below)
   - **Build Command:** `echo "ready"`
   - **Start Command:** `php -S 0.0.0.0:$PORT router.php`
5. Under **Environment Variables**, add:
   ```
   DB_HOST     = (your PlanetScale host)
   DB_USER     = (your PlanetScale username)
   DB_PASS     = (your PlanetScale password)
   DB_NAME     = f1planner
   DB_PORT     = 3306
   ```
6. Click **Create Web Service**
7. Wait 2–3 minutes for it to deploy
8. Click the URL Render gives you — your app is live!

### Step 12: Set up the database on production
1. Go to PlanetScale → your database → **Console** tab
2. Paste in the contents of `database.sql` (skip the `CREATE DATABASE` and `USE` lines at the top — PlanetScale already made the database)
3. Click Run

### Step 13: Enable auto-deploy
By default Render auto-deploys every time you push to GitHub. To verify:
1. In Render dashboard → your service → **Settings** → scroll to **Auto-Deploy**
2. Make sure it says **Yes**

Now every time you do `git push`, your site updates automatically!

---

## PART 6 — After Any Code Change

Run these commands to push updates:
```
git add .
git commit -m "Describe what you changed"
git push
```
Render will automatically redeploy in about 2 minutes.

---

## Demo Script for Your Video Recording

**Introduction (say this at the start):**
> "My project is an F1 Race Weekend Planner. It helps Formula 1 fans keep track of each Grand Prix weekend by viewing the full race calendar, session schedules, and circuit details in one place. Users can create an account, browse races, save their favorites, and add personal notes."

**Walkthrough order:**
1. Start on the Register page → create a new account
2. Show the Home/Calendar page — explain the stats, filters, race cards
3. Filter by "upcoming" races — show the filter working
4. Click a race (e.g., Monaco) — show the detail page
5. Point out: circuit info, lap count, description
6. Show the weekend schedule (FP1, FP2, FP3, Qualifying, Race)
7. Click the ★ Fav button — show toast notification
8. Add a note — click Save Note
9. Navigate to My Planner — show saved race and note
10. Log out → Log in as admin (admin@f1planner.com / admin123)
11. Visit Admin panel — show the stats table
12. Add a new race using the form
13. Edit an existing race
14. Delete the race you just added

**Technical explanation (say this during/after the demo):**
> "The frontend is built with PHP, HTML, and CSS. When a user clicks a race, the browser sends a GET request to race.php with the race ID. The PHP backend queries the MySQL database using PDO — a secure way to run prepared statements that prevents SQL injection. It joins the races and sessions tables to get all the weekend data, then renders the HTML. When saving a favorite, JavaScript sends a POST request to our API endpoint, which toggles the favorite in the database and responds with JSON. The admin panel uses the same database but requires the admin role, checked via PHP session variables."

---

## File Structure Reference
```
f1-planner/
├── index.php           ← Home page (race calendar)
├── login.php           ← Login page
├── register.php        ← Registration page
├── logout.php          ← Logs user out
├── router.php          ← URL routing for Render.com
├── database.sql        ← Database schema + all race data
├── render.yaml         ← Render.com deployment config
├── includes/
│   ├── db.php          ← Database connection (PDO)
│   ├── auth.php        ← Login, register, session helpers
│   ├── header.php      ← Shared navbar HTML
│   └── footer.php      ← Shared footer HTML
├── pages/
│   ├── race.php        ← Race detail page
│   └── planner.php     ← My favorites & notes page
├── admin/
│   ├── index.php       ← Admin dashboard
│   └── race_form.php   ← Add/Edit race form
├── api/
│   └── toggle_favorite.php  ← AJAX endpoint for favorites
└── public/
    ├── css/style.css   ← All styling (F1 dark theme)
    └── js/app.js       ← Frontend JavaScript
```

---

## Common Issues & Fixes

**"php is not recognized" error:**
→ PHP is not in your PATH. Redo Step 2, point 6, and open a NEW Command Prompt window.

**Database connection error locally:**
→ Make sure XAMPP MySQL is running (green light in XAMPP Control Panel)
→ Check `includes/db.php` — DB_USER should be `root`, DB_PASS should be `''` (empty)

**Page not found errors locally:**
→ Make sure you're running `php -S localhost:8000` from inside the f1-planner folder

**Render deploy fails:**
→ Check the Render logs — click your service → **Logs** tab
→ Most likely a missing environment variable
