-- Database Schema for Staff Appraisal System

-- Users table (unified for all roles)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL, -- hashed
    role ENUM('staff', 'hou', 'hod', 'hr') NOT NULL,
    staff_id VARCHAR(20) UNIQUE, -- for staff role
    department VARCHAR(100),
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Staff biodata table
CREATE TABLE biodata (
    id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id VARCHAR(20) NOT NULL,
    full_name VARCHAR(100),
    date_of_birth DATE,
    gender ENUM('Male', 'Female'),
    address TEXT,
    phone VARCHAR(20),
    email VARCHAR(100),
    qualifications TEXT,
    experience TEXT,
    passport_path VARCHAR(255),
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'approved_hou', 'rejected_hou', 'approved_hod', 'rejected_hod', 'approved_hr') DEFAULT 'pending',
    hou_comment TEXT,
    hod_comment TEXT,
    FOREIGN KEY (staff_id) REFERENCES users(staff_id)
);

-- Appraisal submissions table
CREATE TABLE appraisals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id VARCHAR(20) NOT NULL,
    appraisal_year YEAR NOT NULL,
    self_assessment TEXT,
    achievements TEXT,
    goals TEXT,
    training_needs TEXT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'approved_hou', 'rejected_hou', 'approved_hod', 'rejected_hod', 'approved_hr') DEFAULT 'pending',
    hou_comment TEXT,
    hod_comment TEXT,
    FOREIGN KEY (staff_id) REFERENCES users(staff_id)
);

-- Notifications table
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Departments table (for HOU/HOD assignment)
CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    hod_user_id INT,
    FOREIGN KEY (hod_user_id) REFERENCES users(id)
);

-- Update users table to include department
ALTER TABLE users ADD COLUMN department_id INT;
ALTER TABLE users ADD FOREIGN KEY (department_id) REFERENCES departments(id);