-- Pearl Stay Hotel Reservation System Database
-- Created for HND IT Project - Sri Lanka Tourism Platform

-- Create Database
CREATE DATABASE IF NOT EXISTS pearl_stay_db;
USE pearl_stay_db;

-- 1. Users Table (Both customers and admins)
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    profile_image VARCHAR(255),    -- Role-based system
    role ENUM('admin', 'user') DEFAULT 'user',
    -- Account status and verification
    email_verified BOOLEAN DEFAULT FALSE,
    account_status ENUM('active', 'inactive', 'suspended', 'pending') DEFAULT 'active',
    -- Admin specific fields
    last_login TIMESTAMP,
    login_attempts INT DEFAULT 0,
    locked_until TIMESTAMP NULL,
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 3. Hotels Table
CREATE TABLE hotels (
    hotel_id INT PRIMARY KEY AUTO_INCREMENT,    hotel_name VARCHAR(150) NOT NULL,
    description TEXT,
    address TEXT NOT NULL,
    district VARCHAR(50),
    province VARCHAR(50),
    star_rating ENUM('1', '2', '3', '4', '5'),    contact_phone VARCHAR(20),
    contact_email VARCHAR(100),
    website_url VARCHAR(255),
    total_rooms INT DEFAULT 0,    property_type ENUM('hotel', 'resort', 'villa', 'homestay', 'guesthouse', 'boutique') DEFAULT 'hotel',
    status ENUM('active', 'inactive', 'pending') DEFAULT 'pending',    main_image VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 5. Hotel Amenities Table
CREATE TABLE amenities (
    amenity_id INT PRIMARY KEY AUTO_INCREMENT,
    amenity_name VARCHAR(100) NOT NULL,
    icon_class VARCHAR(50),
    category ENUM('basic', 'comfort', 'business', 'recreation', 'accessibility') DEFAULT 'basic',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 6. Hotel-Amenities Junction Table
CREATE TABLE hotel_amenities (
    hotel_id INT,
    amenity_id INT,
    PRIMARY KEY (hotel_id, amenity_id),
    FOREIGN KEY (hotel_id) REFERENCES hotels(hotel_id) ON DELETE CASCADE,
    FOREIGN KEY (amenity_id) REFERENCES amenities(amenity_id) ON DELETE CASCADE
);

-- 7. Room Types Table
CREATE TABLE room_types (
    room_type_id INT PRIMARY KEY AUTO_INCREMENT,
    hotel_id INT NOT NULL,
    type_name VARCHAR(100) NOT NULL,
    description TEXT,
    max_occupancy INT NOT NULL,
    base_price DECIMAL(10, 2) NOT NULL,
    room_size VARCHAR(50),
    bed_type VARCHAR(50),
    total_rooms INT NOT NULL,
    room_amenities JSON,
    images JSON,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (hotel_id) REFERENCES hotels(hotel_id) ON DELETE CASCADE
);

-- 8. Room Inventory Table (Individual rooms)
CREATE TABLE rooms (
    room_id INT PRIMARY KEY AUTO_INCREMENT,
    hotel_id INT NOT NULL,
    room_type_id INT NOT NULL,
    room_number VARCHAR(20) NOT NULL,
    floor_number INT,
    status ENUM('available', 'occupied', 'maintenance', 'out_of_order') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (hotel_id) REFERENCES hotels(hotel_id) ON DELETE CASCADE,
    FOREIGN KEY (room_type_id) REFERENCES room_types(room_type_id) ON DELETE CASCADE,
    UNIQUE KEY unique_room (hotel_id, room_number)
);

-- 9. Bookings Table
CREATE TABLE bookings (
    booking_id INT PRIMARY KEY AUTO_INCREMENT,
    booking_reference VARCHAR(20) UNIQUE NOT NULL,
    user_id INT NOT NULL,
    hotel_id INT NOT NULL,
    room_type_id INT NOT NULL,
    check_in_date DATE NOT NULL,
    check_out_date DATE NOT NULL,
    adults INT NOT NULL DEFAULT 1,
    children INT DEFAULT 0,
    total_nights INT NOT NULL,
    rooms_booked INT DEFAULT 1,
    room_rate DECIMAL(10, 2) NOT NULL,
    taxes DECIMAL(10, 2) DEFAULT 0.00,
    service_charges DECIMAL(10, 2) DEFAULT 0.00,
    total_amount DECIMAL(10, 2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'LKR',
    booking_status ENUM('pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled', 'no_show') DEFAULT 'pending',
    payment_status ENUM('pending', 'paid', 'cancelled') DEFAULT 'pending',
    payment_deadline DATETIME,
    payment_date DATETIME,
    special_requests TEXT,
    guest_name VARCHAR(100) NOT NULL,
    guest_email VARCHAR(100) NOT NULL,
    guest_phone VARCHAR(20),
    booking_source ENUM('website', 'mobile_app', 'phone', 'walk_in') DEFAULT 'website',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (hotel_id) REFERENCES hotels(hotel_id),
    FOREIGN KEY (room_type_id) REFERENCES room_types(room_type_id)
);

-- 10. Room Bookings (Junction table for specific room assignments)
CREATE TABLE room_bookings (
    booking_id INT,
    room_id INT,
    PRIMARY KEY (booking_id, room_id),
    FOREIGN KEY (booking_id) REFERENCES bookings(booking_id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(room_id)
);

-- 11. Payments Table
CREATE TABLE payments (
    payment_id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('bank_transfer', 'cash') NOT NULL,
    transaction_id VARCHAR(100),
    payment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    payment_deadline DATETIME,             -- Deadline for payment completion
    status ENUM('pending', 'completed', 'failed', 'expired') DEFAULT 'pending',
    bank_slip VARCHAR(255) NULL,           -- Path to uploaded bank slip
    bank_reference VARCHAR(100) NULL,      -- Bank reference number for bank transfers
    transfer_date DATE NULL,               -- Date of bank transfer
    bank_name VARCHAR(100) NULL,           -- Name of the bank used for transfer
    notes TEXT NULL,                       -- Any additional notes
    verified_by INT NULL,                  -- Admin who verified the payment
    verified_at DATETIME NULL,             -- When payment was verified
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(booking_id) ON DELETE RESTRICT,
    FOREIGN KEY (verified_by) REFERENCES users(user_id) ON DELETE SET NULL,
    UNIQUE INDEX idx_transaction (transaction_id),
    INDEX idx_payment_booking (booking_id),
    INDEX idx_payment_status (status),
    INDEX idx_payment_method (payment_method),
    INDEX idx_payment_date (payment_date),
    INDEX idx_payment_deadline (payment_deadline)
);

-- 11b. Payment Logs Table
CREATE TABLE payment_logs (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    payment_id INT NOT NULL,
    booking_id INT NOT NULL,
    action VARCHAR(50) NOT NULL,           -- e.g., 'payment_initiated', 'payment_verified', 'payment_failed'
    notes TEXT NULL,                       -- Details about the action
    user_id INT NOT NULL,                  -- Who performed the action
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (payment_id) REFERENCES payments(payment_id) ON DELETE CASCADE,
    FOREIGN KEY (booking_id) REFERENCES bookings(booking_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE RESTRICT,
    INDEX idx_payment_log (payment_id),
    INDEX idx_booking_log (booking_id),
    INDEX idx_user_log (user_id)
);

-- Reviews table removed

-- 13. Hotel Images Table
CREATE TABLE hotel_images (
    image_id INT PRIMARY KEY AUTO_INCREMENT,
    hotel_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    image_title VARCHAR(150),
    image_type ENUM('main', 'gallery', 'room', 'amenity', 'exterior', 'interior') DEFAULT 'gallery',
    alt_text VARCHAR(255),
    is_primary BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (hotel_id) REFERENCES hotels(hotel_id) ON DELETE CASCADE
);


-- Insert sample amenities
INSERT INTO amenities (amenity_name, category, icon_class) VALUES
('Free WiFi', 'basic', 'fa-wifi'),
('Swimming Pool', 'recreation', 'fa-swimming-pool'),
('Restaurant', 'basic', 'fa-utensils'),
('Air Conditioning', 'comfort', 'fa-snowflake'),
('Parking', 'basic', 'fa-parking'),
('Spa & Wellness', 'recreation', 'fa-spa'),
('Fitness Center', 'recreation', 'fa-dumbbell'),
('Room Service', 'comfort', 'fa-concierge-bell'),
('Business Center', 'business', 'fa-briefcase'),
('Pet Friendly', 'comfort', 'fa-paw'),
('Airport Shuttle', 'basic', 'fa-shuttle-van'),
('Laundry Service', 'comfort', 'fa-tshirt'),
('24/7 Reception', 'basic', 'fa-clock'),
('Bar/Lounge', 'recreation', 'fa-cocktail'),
('Beach Access', 'recreation', 'fa-umbrella-beach');

-- Create indexesfor better performance
CREATE INDEX idx_bookings_dates ON bookings(check_in_date, check_out_date);
CREATE INDEX idx_bookings_status ON bookings(booking_status);
CREATE INDEX idx_bookings_user ON bookings(user_id);
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_users_status ON users(account_status);

-- Payment related indexes
CREATE INDEX idx_payments_status ON payments(status);
CREATE INDEX idx_payments_deadline ON payments(payment_deadline);
CREATE INDEX idx_payments_booking ON payments(booking_id);
CREATE INDEX idx_payments_method ON payments(payment_method);

-- Show tables created
SHOW TABLES;