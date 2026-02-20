CREATE DATABASE IF NOT EXISTS mamafua CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mamafua;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('client', 'mamafua', 'admin') NOT NULL,
    status ENUM('active', 'suspended') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_users_role_status (role, status)
);

CREATE TABLE mamafuas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL UNIQUE,
    skills VARCHAR(255) NOT NULL,
    location VARCHAR(120) NOT NULL,
    availability VARCHAR(255) NOT NULL,
    verification_status ENUM('pending', 'verified', 'rejected', 'suspended') NOT NULL DEFAULT 'pending',
    rating_average DECIMAL(3,2) DEFAULT 0.00,
    completed_jobs INT UNSIGNED DEFAULT 0,
    earnings_total DECIMAL(10,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_mamafua_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_mamafua_verification_location (verification_status, location)
);

CREATE TABLE mamafua_documents (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mamafua_id INT UNSIGNED NOT NULL,
    document_type ENUM('id_document', 'photo', 'police_clearance') NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    status ENUM('submitted', 'approved', 'rejected') DEFAULT 'submitted',
    reviewed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_doc_mamafua FOREIGN KEY (mamafua_id) REFERENCES mamafuas(id) ON DELETE CASCADE,
    UNIQUE KEY uniq_mamafua_doc (mamafua_id, document_type)
);

CREATE TABLE bookings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    client_id INT UNSIGNED NOT NULL,
    mamafua_id INT UNSIGNED NOT NULL,
    service_type ENUM('cleaning', 'laundry', 'childcare', 'cooking') NOT NULL,
    pricing_plan ENUM('hourly', 'daily', 'weekly') NOT NULL,
    booking_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    location VARCHAR(255) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending_payment', 'confirmed', 'in_progress', 'completed', 'cancelled', 'rescheduled', 'disputed') DEFAULT 'pending_payment',
    payment_status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_booking_client FOREIGN KEY (client_id) REFERENCES users(id),
    CONSTRAINT fk_booking_mamafua FOREIGN KEY (mamafua_id) REFERENCES mamafuas(id),
    INDEX idx_booking_slot (mamafua_id, booking_date, start_time, end_time),
    INDEX idx_booking_client (client_id, booking_date)
);

CREATE TABLE payments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_id INT UNSIGNED NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    checkout_request_id VARCHAR(120) NOT NULL UNIQUE,
    merchant_request_id VARCHAR(120) NOT NULL,
    mpesa_receipt_number VARCHAR(120) NULL,
    status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
    raw_response JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_payment_booking FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    INDEX idx_payment_status_created (status, created_at)
);

CREATE TABLE reviews (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_id INT UNSIGNED NOT NULL,
    client_id INT UNSIGNED NOT NULL,
    mamafua_id INT UNSIGNED NOT NULL,
    rating TINYINT UNSIGNED NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    moderation_status ENUM('pending', 'approved', 'flagged') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_review_booking FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    CONSTRAINT fk_review_client FOREIGN KEY (client_id) REFERENCES users(id),
    CONSTRAINT fk_review_mamafua FOREIGN KEY (mamafua_id) REFERENCES mamafuas(id),
    INDEX idx_review_moderation (moderation_status, created_at)
);

CREATE TABLE admin_logs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admin_id INT UNSIGNED NOT NULL,
    action VARCHAR(120) NOT NULL,
    target_type VARCHAR(80) NOT NULL,
    target_id INT UNSIGNED NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_admin_logs_admin FOREIGN KEY (admin_id) REFERENCES users(id),
    INDEX idx_admin_logs_admin_date (admin_id, created_at)
);

INSERT INTO users (name, email, phone, password_hash, role, status) VALUES
('Mamafua Admin', 'admin@mamafua.co.ke', '254700000001', '$2y$12$IjoIlwy/fZwW.kPURBJL2.JDedKDP8tsbhHRmkMqhqc38n5T5TmRK', 'admin', 'active'),
('Grace Wanjiku', 'grace.mamafua@example.com', '254700000002', '$2y$12$IjoIlwy/fZwW.kPURBJL2.JDedKDP8tsbhHRmkMqhqc38n5T5TmRK', 'mamafua', 'active'),
('Brian Otieno', 'brian.client@example.com', '254700000003', '$2y$12$IjoIlwy/fZwW.kPURBJL2.JDedKDP8tsbhHRmkMqhqc38n5T5TmRK', 'client', 'active');

INSERT INTO mamafuas (user_id, skills, location, availability, verification_status, rating_average, completed_jobs, earnings_total)
VALUES (2, 'cleaning,laundry,cooking', 'Nairobi', 'Weekdays 8am-5pm', 'verified', 4.80, 29, 85000.00);

INSERT INTO bookings (client_id, mamafua_id, service_type, pricing_plan, booking_date, start_time, end_time, location, amount, status, payment_status)
VALUES (3, 1, 'cleaning', 'daily', CURDATE(), '09:00:00', '17:00:00', 'Kilimani, Nairobi', 2500.00, 'confirmed', 'paid');

INSERT INTO payments (booking_id, phone_number, amount, checkout_request_id, merchant_request_id, mpesa_receipt_number, status, raw_response)
VALUES (1, '254700000003', 2500.00, 'ws_CO_DM_SAMPLE_001', '29115-34620561-1', 'QWE123RTY', 'paid', JSON_OBJECT('sample', TRUE));
