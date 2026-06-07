DROP DATABASE IF EXISTS campus_utility;
CREATE DATABASE campus_utility;
USE campus_utility;

-- 1. Whitelist for valid student ERP IDs
CREATE TABLE allowed_students (
    college_id VARCHAR(50) PRIMARY KEY,
    branch VARCHAR(100) NOT NULL,
    section VARCHAR(10) NOT NULL
);

-- Insert dummy whitelisted Student IDs for testing registration
INSERT INTO allowed_students (college_id, branch, section) VALUES 
('ERP2026CS01', 'Computer Science Engineering', 'A'),
('ERP2026CS02', 'Computer Science Engineering', 'B'),
('ERP2026EC01', 'Electronics & Comm. Engineering', 'A');

-- 2. Whitelist for authorized Faculty Codes
CREATE TABLE allowed_faculty (
    faculty_code VARCHAR(50) PRIMARY KEY,
    department VARCHAR(100) NOT NULL
);

-- Insert dummy whitelisted Faculty Codes for testing registration
INSERT INTO allowed_faculty (faculty_code, department) VALUES 
('FACULTY001', 'Department of Computer Science'),
('FACULTY002', 'Department of Information Technology');

-- 3. Core Users Table (Profiles for both Students and Faculty)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    college_id VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'faculty') NOT NULL DEFAULT 'student',
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    contact_no VARCHAR(20),
    branch VARCHAR(100),
    section VARCHAR(10),
    sex VARCHAR(20),
    age INT,
    parents_name VARCHAR(150),
    bio TEXT,
    skills TEXT,
    achievements TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 4. Complaints Table with relational mapping
CREATE TABLE complaints (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    status ENUM('Pending', 'In Progress', 'Resolved') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 5. Lost & Found Table with relational mapping
CREATE TABLE lost_found (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    item_name VARCHAR(255) NOT NULL,
    type ENUM('Lost', 'Found') NOT NULL,
    location VARCHAR(255) NOT NULL,
    contact VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 6. Open Forum Board with relational mapping
CREATE TABLE forum_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);