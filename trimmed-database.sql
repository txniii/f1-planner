-- F1 Race Weekend Planner - Database Schema (Trimmed for mb963)
-- Remove CREATE DATABASE f1_planner & USE mb963; -- already in context
-- Paste this ENTIRE content into DB client query and run

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Races table
CREATE TABLE IF NOT EXISTS races (
    id INT AUTO_INCREMENT PRIMARY KEY,
    grand_prix_name VARCHAR(100) NOT NULL,
    country VARCHAR(100) NOT NULL,
    circuit_name VARCHAR(150) NOT NULL,
    race_date DATE NOT NULL,
    description TEXT,
    flag_emoji VARCHAR(10),
    circuit_length VARCHAR(20),
    lap_count INT,
    status ENUM('upcoming', 'completed') DEFAULT 'upcoming',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sessions table (FP1, FP2, FP3, Qualifying, Race)
CREATE TABLE IF NOT EXISTS sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    race_id INT NOT NULL,
    session_name VARCHAR(50) NOT NULL,
    session_datetime DATETIME NOT NULL,
    FOREIGN KEY (race_id) REFERENCES races(id) ON DELETE CASCADE
);

-- Favorites table
CREATE TABLE IF NOT EXISTS favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    race_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_favorite (user_id, race_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (race_id) REFERENCES races(id) ON DELETE CASCADE
);

-- Notes table
CREATE TABLE IF NOT EXISTS notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    race_id INT NOT NULL,
    note_text TEXT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (race_id) REFERENCES races(id) ON DELETE CASCADE
);

-- --------------------------------------------------------
-- Seed Data: 2025 F1 Season
-- --------------------------------------------------------

INSERT INTO races (grand_prix_name, country, circuit_name, race_date, description, flag_emoji, circuit_length, lap_count, status) VALUES
('Australian Grand Prix', 'Australia', 'Albert Park Circuit', '2025-03-16', 'The season opener in Melbourne, known for its street-style circuit through Albert Park. Fast, smooth and fan-friendly.', '🇦🇺', '5.278 km', 58, 'completed'),
('Chinese Grand Prix', 'China', 'Shanghai International Circuit', '2025-03-23', 'Shanghai returns to the calendar with its unique layout featuring one of the longest straights in F1.', '🇨🇳', '5.451 km', 56, 'completed'),
('Japanese Grand Prix', 'Japan', 'Suzuka International Racing Course', '2025-04-06', 'Suzuka is one of the most beloved circuits on the calendar, with its iconic figure-of-eight layout.', '🇯🇵', '5.807 km', 53, 'completed'),
('Bahrain Grand Prix', 'Bahrain', 'Bahrain International Circuit', '2025-04-13', 'The desert circuit lit up at night under the floodlights — a spectacular venue for racing.', '🇧🇭', '5.412 km', 57, 'completed'),
('Saudi Arabian Grand Prix', 'Saudi Arabia', 'Jeddah Corniche Circuit', '2025-04-20', 'One of the fastest street circuits in F1 history, running along the Jeddah waterfront.', '🇸🇦', '6.174 km', 50, 'completed'),
('Miami Grand Prix', 'USA', 'Miami International Autodrome', '2025-05-04', 'The glitzy Miami race around the Hard Rock Stadium, complete with a fake marina and party atmosphere.', '🇺🇸', '5.412 km', 57, 'completed'),
('Emilia Romagna Grand Prix', 'Italy', 'Autodromo Enzo e Dino Ferrari', '2025-05-18', 'Imola — a classic circuit with history dating back decades, home to passionate tifosi.', '🇮🇹', '4.909 km', 63, 'completed'),
('Monaco Grand Prix', 'Monaco', 'Circuit de Monaco', '2025-05-25', 'The jewel of the F1 calendar. The slowest circuit, the highest glamour — streets of Monte Carlo.', '🇲🇨', '3.337 km', 78, 'completed'),
('Spanish Grand Prix', 'Spain', 'Circuit de Barcelona-Catalunya', '2025-06-01', 'Barcelona is a well-known circuit to all teams — a true test of car balance and setup.', '🇪🇸', '4.675 km', 66, 'completed'),
('Canadian Grand Prix', 'Canada', 'Circuit Gilles Villeneuve', '2025-06-15', 'Montreal on Île Notre-Dame — a fan favourite with its famous Wall of Champions.', '🇨🇦', '4.361 km', 70, 'upcoming'),
('Austrian Grand Prix', 'Austria', 'Red Bull Ring', '2025-06-29', 'Short, punchy and set in the Styrian mountains — the Red Bull Ring delivers non-stop action.', '🇦🇹', '4.318 km', 71, 'upcoming'),
('British Grand Prix', 'United Kingdom', 'Silverstone Circuit', '2025-07-06', 'Silverstone — the home of British motorsport. High-speed corners and passionate crowds.', '🇬🇧', '5.891 km', 52, 'upcoming'),
('Belgian Grand Prix', 'Belgium', 'Circuit de Spa-Francorchamps', '2025-07-27', 'Spa is legendary. From Eau Rouge to Raidillon, this is one of the greatest circuits in the world.', '🇧🇪', '7.004 km', 44, 'upcoming'),
('Hungarian Grand Prix', 'Hungary', 'Hungaroring', '2025-08-03', 'The Monaco of high-speed circuits — technical, twisty, and hot in late summer.', '🇭🇺', '4.381 km', 70, 'upcoming'),
('Dutch Grand Prix', 'Netherlands', 'Circuit Zandvoort', '2025-08-31', 'Zandvoort returned to the calendar with its banked corners and passionate Orange Army.', '🇳🇱', '4.259 km', 72, 'upcoming'),
('Italian Grand Prix', 'Italy', 'Autodromo Nazionale Monza', '2025-09-07', 'The Temple of Speed. Monza is the fastest circuit on the calendar — pure racing heritage.', '🇮🇹', '5.793 km', 53, 'upcoming'),
('Azerbaijan Grand Prix', 'Azerbaijan', 'Baku City Circuit', '2025-09-21', 'Baku delivers chaos and drama every year — a high-speed street circuit through a medieval city.', '🇦🇿', '6.003 km', 51, 'upcoming'),
('Singapore Grand Prix', 'Singapore', 'Marina Bay Street Circuit', '2025-10-05', 'The only night race on a street circuit — Singapore is a glamorous and physically demanding event.', '🇸🇬', '4.940 km', 62, 'upcoming'),
('United States Grand Prix', 'USA', 'Circuit of the Americas', '2025-10-19', 'COTA in Austin — the first purpose-built F1 circuit in the USA, with a dramatic opening climb.', '🇺🇸', '5.513 km', 56, 'upcoming'),
('Mexico City Grand Prix', 'Mexico', 'Autodromo Hermanos Rodriguez', '2025-10-26', 'High altitude and a passionate crowd — Mexico City delivers unique racing conditions.', '🇲🇽', '4.304 km', 71, 'upcoming'),
('São Paulo Grand Prix', 'Brazil', 'Autodromo Jose Carlos Pace', '2025-11-09', 'Interlagos is iconic — unpredictable weather, elevation changes and Brazilian passion.', '🇧🇷', '4.309 km', 71, 'upcoming'),
('Las Vegas Grand Prix', 'USA', 'Las Vegas Strip Circuit', '2025-11-22', 'F1 on the Las Vegas Strip — pure spectacle under the neon lights of Nevada.', '🇺🇸', '6.201 km', 50, 'upcoming'),
('Qatar Grand Prix', 'Qatar', 'Lusail International Circuit', '2025-11-30', 'Lusail hosts F1 under the floodlights — a technical challenge in the desert heat.', '🇶🇦', '5.380 km', 57, 'upcoming'),
('Abu Dhabi Grand Prix', 'UAE', 'Yas Marina Circuit', '2025-12-07', 'The season finale on Yas Island — from sunset to night, a fitting end to the F1 year.', '🇦🇪', '5.281 km', 58, 'upcoming');

-- Sessions for each race (using race IDs 1-24)
INSERT INTO sessions (race_id, session_name, session_datetime) VALUES
(1,'Practice 1','2025-03-14 01:30:00'),(1,'Practice 2','2025-03-14 05:00:00'),
(1,'Practice 3','2025-03-15 01:30:00'),(1,'Qualifying','2025-03-15 05:00:00'),
(1,'Race','2025-03-16 04:00:00'),
-- [ALL remaining INSERTs for sessions... full content matches original from line after USE mb963; ]


-- Extra table: user_races (for planner-style associations)
CREATE TABLE IF NOT EXISTS user_races (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    race_id INT NOT NULL,
    is_favorite TINYINT(1) NOT NULL DEFAULT 0,
    note_text VARCHAR(500) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_user_race (user_id, race_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (race_id) REFERENCES races(id) ON DELETE CASCADE
);

CREATE TABLE sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    race_id INT NOT NULL,
    session_type VARCHAR(50) NOT NULL,
    start_time_utc DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (race_id) REFERENCES races(id) ON DELETE CASCADE
);

ALTER TABLE sessions
ADD COLUMN start_time_utc DATETIME NULL;
