-- F1 Race Weekend Planner - Database Schema
-- Run this file once to set up your database

CREATE DATABASE IF NOT EXISTS f1_planner CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mb963;

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
-- Australia (id=1)
INSERT INTO sessions (race_id, session_name, session_datetime) VALUES
(1,'Practice 1','2025-03-14 01:30:00'),(1,'Practice 2','2025-03-14 05:00:00'),
(1,'Practice 3','2025-03-15 01:30:00'),(1,'Qualifying','2025-03-15 05:00:00'),
(1,'Race','2025-03-16 04:00:00');
-- China (id=2)
INSERT INTO sessions (race_id, session_name, session_datetime) VALUES
(2,'Practice 1','2025-03-21 03:30:00'),(2,'Practice 2','2025-03-21 07:00:00'),
(2,'Practice 3','2025-03-22 03:30:00'),(2,'Qualifying','2025-03-22 07:00:00'),
(2,'Race','2025-03-23 07:00:00');
-- Japan (id=3)
INSERT INTO sessions (race_id, session_name, session_datetime) VALUES
(3,'Practice 1','2025-04-04 02:30:00'),(3,'Practice 2','2025-04-04 06:00:00'),
(3,'Practice 3','2025-04-05 02:30:00'),(3,'Qualifying','2025-04-05 06:00:00'),
(3,'Race','2025-04-06 05:00:00');
-- Bahrain (id=4)
INSERT INTO sessions (race_id, session_name, session_datetime) VALUES
(4,'Practice 1','2025-04-11 11:30:00'),(4,'Practice 2','2025-04-11 15:00:00'),
(4,'Practice 3','2025-04-12 11:30:00'),(4,'Qualifying','2025-04-12 15:00:00'),
(4,'Race','2025-04-13 15:00:00');
-- Saudi Arabia (id=5)
INSERT INTO sessions (race_id, session_name, session_datetime) VALUES
(5,'Practice 1','2025-04-18 13:30:00'),(5,'Practice 2','2025-04-18 17:00:00'),
(5,'Practice 3','2025-04-19 13:30:00'),(5,'Qualifying','2025-04-19 17:00:00'),
(5,'Race','2025-04-20 17:00:00');
-- Miami (id=6)
INSERT INTO sessions (race_id, session_name, session_datetime) VALUES
(6,'Practice 1','2025-05-02 16:30:00'),(6,'Practice 2','2025-05-02 20:30:00'),
(6,'Practice 3','2025-05-03 15:30:00'),(6,'Qualifying','2025-05-03 19:00:00'),
(6,'Race','2025-05-04 16:00:00');
-- Emilia Romagna (id=7)
INSERT INTO sessions (race_id, session_name, session_datetime) VALUES
(7,'Practice 1','2025-05-16 11:30:00'),(7,'Practice 2','2025-05-16 15:00:00'),
(7,'Practice 3','2025-05-17 10:30:00'),(7,'Qualifying','2025-05-17 14:00:00'),
(7,'Race','2025-05-18 13:00:00');
-- Monaco (id=8)
INSERT INTO sessions (race_id, session_name, session_datetime) VALUES
(8,'Practice 1','2025-05-22 11:30:00'),(8,'Practice 2','2025-05-22 15:00:00'),
(8,'Practice 3','2025-05-24 10:30:00'),(8,'Qualifying','2025-05-24 14:00:00'),
(8,'Race','2025-05-25 13:00:00');
-- Spain (id=9)
INSERT INTO sessions (race_id, session_name, session_datetime) VALUES
(9,'Practice 1','2025-05-30 11:30:00'),(9,'Practice 2','2025-05-30 15:00:00'),
(9,'Practice 3','2025-05-31 10:30:00'),(9,'Qualifying','2025-05-31 14:00:00'),
(9,'Race','2025-06-01 13:00:00');
-- Canada (id=10)
INSERT INTO sessions (race_id, session_name, session_datetime) VALUES
(10,'Practice 1','2025-06-13 17:30:00'),(10,'Practice 2','2025-06-13 21:00:00'),
(10,'Practice 3','2025-06-14 16:30:00'),(10,'Qualifying','2025-06-14 20:00:00'),
(10,'Race','2025-06-15 18:00:00');
-- Austria (id=11)
INSERT INTO sessions (race_id, session_name, session_datetime) VALUES
(11,'Practice 1','2025-06-27 10:30:00'),(11,'Practice 2','2025-06-27 14:00:00'),
(11,'Practice 3','2025-06-28 09:30:00'),(11,'Qualifying','2025-06-28 13:00:00'),
(11,'Race','2025-06-29 13:00:00');
-- Britain (id=12)
INSERT INTO sessions (race_id, session_name, session_datetime) VALUES
(12,'Practice 1','2025-07-04 11:30:00'),(12,'Practice 2','2025-07-04 15:00:00'),
(12,'Practice 3','2025-07-05 10:30:00'),(12,'Qualifying','2025-07-05 14:00:00'),
(12,'Race','2025-07-06 14:00:00');
-- Belgium (id=13)
INSERT INTO sessions (race_id, session_name, session_datetime) VALUES
(13,'Practice 1','2025-07-25 11:30:00'),(13,'Practice 2','2025-07-25 15:00:00'),
(13,'Practice 3','2025-07-26 10:30:00'),(13,'Qualifying','2025-07-26 14:00:00'),
(13,'Race','2025-07-27 13:00:00');
-- Hungary (id=14)
INSERT INTO sessions (race_id, session_name, session_datetime) VALUES
(14,'Practice 1','2025-08-01 11:30:00'),(14,'Practice 2','2025-08-01 15:00:00'),
(14,'Practice 3','2025-08-02 10:30:00'),(14,'Qualifying','2025-08-02 14:00:00'),
(14,'Race','2025-08-03 13:00:00');
-- Netherlands (id=15)
INSERT INTO sessions (race_id, session_name, session_datetime) VALUES
(15,'Practice 1','2025-08-29 10:30:00'),(15,'Practice 2','2025-08-29 14:00:00'),
(15,'Practice 3','2025-08-30 09:30:00'),(15,'Qualifying','2025-08-30 13:00:00'),
(15,'Race','2025-08-31 13:00:00');
-- Italy (id=16)
INSERT INTO sessions (race_id, session_name, session_datetime) VALUES
(16,'Practice 1','2025-09-05 11:30:00'),(16,'Practice 2','2025-09-05 15:00:00'),
(16,'Practice 3','2025-09-06 10:30:00'),(16,'Qualifying','2025-09-06 14:00:00'),
(16,'Race','2025-09-07 13:00:00');
-- Azerbaijan (id=17)
INSERT INTO sessions (race_id, session_name, session_datetime) VALUES
(17,'Practice 1','2025-09-19 09:30:00'),(17,'Practice 2','2025-09-19 13:00:00'),
(17,'Practice 3','2025-09-20 08:30:00'),(17,'Qualifying','2025-09-20 12:00:00'),
(17,'Race','2025-09-21 11:00:00');
-- Singapore (id=18)
INSERT INTO sessions (race_id, session_name, session_datetime) VALUES
(18,'Practice 1','2025-10-03 09:30:00'),(18,'Practice 2','2025-10-03 13:00:00'),
(18,'Practice 3','2025-10-04 09:30:00'),(18,'Qualifying','2025-10-04 13:00:00'),
(18,'Race','2025-10-05 12:00:00');
-- USA COTA (id=19)
INSERT INTO sessions (race_id, session_name, session_datetime) VALUES
(19,'Practice 1','2025-10-17 17:30:00'),(19,'Practice 2','2025-10-17 21:00:00'),
(19,'Practice 3','2025-10-18 16:30:00'),(19,'Qualifying','2025-10-18 20:00:00'),
(19,'Race','2025-10-19 19:00:00');
-- Mexico (id=20)
INSERT INTO sessions (race_id, session_name, session_datetime) VALUES
(20,'Practice 1','2025-10-24 18:30:00'),(20,'Practice 2','2025-10-24 22:00:00'),
(20,'Practice 3','2025-10-25 17:30:00'),(20,'Qualifying','2025-10-25 21:00:00'),
(20,'Race','2025-10-26 20:00:00');
-- Brazil (id=21)
INSERT INTO sessions (race_id, session_name, session_datetime) VALUES
(21,'Practice 1','2025-11-07 14:30:00'),(21,'Practice 2','2025-11-07 18:00:00'),
(21,'Practice 3','2025-11-08 13:30:00'),(21,'Qualifying','2025-11-08 17:00:00'),
(21,'Race','2025-11-09 17:00:00');
-- Las Vegas (id=22)
INSERT INTO sessions (race_id, session_name, session_datetime) VALUES
(22,'Practice 1','2025-11-21 04:30:00'),(22,'Practice 2','2025-11-21 08:00:00'),
(22,'Practice 3','2025-11-22 04:00:00'),(22,'Qualifying','2025-11-22 08:00:00'),
(22,'Race','2025-11-23 06:00:00');
-- Qatar (id=23)
INSERT INTO sessions (race_id, session_name, session_datetime) VALUES
(23,'Practice 1','2025-11-28 13:30:00'),(23,'Practice 2','2025-11-28 17:00:00'),
(23,'Practice 3','2025-11-29 13:30:00'),(23,'Qualifying','2025-11-29 17:00:00'),
(23,'Race','2025-11-30 17:00:00');
-- Abu Dhabi (id=24)
INSERT INTO sessions (race_id, session_name, session_datetime) VALUES
(24,'Practice 1','2025-12-05 09:30:00'),(24,'Practice 2','2025-12-05 13:00:00'),
(24,'Practice 3','2025-12-06 09:30:00'),(24,'Qualifying','2025-12-06 13:00:00'),
(24,'Race','2025-12-07 13:00:00');


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
