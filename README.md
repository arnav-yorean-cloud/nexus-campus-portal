# nexus-campus-portal
A centralized full-stack web utility portal featuring a whitelist registration system, anonymous complaint handling, lost &amp; found tracking, and a student discussion forum.
# Nexus Campus Portal 🌐🏫

A unified, secure full-stack campus utility application designed to centralize student services, streamline communication, and eliminate administrative data silos.

## 🚀 Live Demo & Presentation
- **Live Website:** [nexus-campus-portal.great-site.net](http://nexus-campus-portal.great-site.net)

## ✨ Core Modules & Features
- **Identity Whitelist System:** Verifies student credentials against an institutional database during registration to prevent unauthorized access.
- **Anonymous Grievance System:** A secure dashboard allowing students to log complaints without exposing identity, fostering a transparent environment.
- **Lost & Found Bulletin:** A centralized, real-time feed for posting and claiming misplaced campus items.
- **Campus Discussion Forum:** An open message board for academic and peer-to-peer discussions.

## 🛠️ Technical Architecture (Tech Stack)
- **Frontend:** HTML5, CSS3, JavaScript (Responsive UI)
- **Backend:** PHP (Procedural/Object-Oriented via PDO)
- **Database:** MySQL (Cloud-hosted via InfinityFree relational architecture)
- **Security:** Parameterized SQL queries (SQLi prevention), secure session validation, and password hashing.

## 📸 Project Architecture
The project structure follows a clean directory separation:
- `/config` - Database connection profiles and cloud credentials (`db.php`).
- `/includes` - Global modular components (`header.php`, `footer.php`).
- `/` - Functional core web modules (Complaints, Forum, Profile, Auth).
