-- Pearl Stay Hotel Reservation System Database
-- Created for HND IT Project - Sri Lanka Tourism Platform

-- Create Database
CREATE DATABASE IF NOT EXISTS pearl_stay_db;
USE pearl_stay_db;

-- 1. Users Table (Both customers and admins)
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    date_of_birth DATE,
    nationality VARCHAR(50),
    address TEXT,
    city VARCHAR(50),
    country VARCHAR(50),
    profile_image VARCHAR(255),
    -- Role-based system
    user_role ENUM('customer', 'admin', 'super_admin', 'moderator', 'hotel_manager') DEFAULT 'customer',
    permissions JSON,
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

-- 3. Locations/Destinations Table
CREATE TABLE locations (
    location_id INT PRIMARY KEY AUTO_INCREMENT,
    location_name VARCHAR(100) NOT NULL,
    district VARCHAR(50),
    province VARCHAR(50),
    country VARCHAR(50) DEFAULT 'Sri Lanka',
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    description TEXT,
    is_popular BOOLEAN DEFAULT FALSE,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 4. Hotels Table
CREATE TABLE hotels (
    hotel_id INT PRIMARY KEY AUTO_INCREMENT,
    hotel_name VARCHAR(150) NOT NULL,
    description TEXT,
    address TEXT NOT NULL,
    location_id INT,
    star_rating ENUM('1', '2', '3', '4', '5'),
    contact_phone VARCHAR(20),
    contact_email VARCHAR(100),
    website_url VARCHAR(255),
    check_in_time TIME DEFAULT '14:00:00',
    check_out_time TIME DEFAULT '12:00:00',
    total_rooms INT DEFAULT 0,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    is_eco_friendly BOOLEAN DEFAULT FALSE,
    property_type ENUM('hotel', 'resort', 'villa', 'homestay', 'guesthouse', 'boutique') DEFAULT 'hotel',
    status ENUM('active', 'inactive', 'pending') DEFAULT 'pending',
    featured BOOLEAN DEFAULT FALSE,
    average_rating DECIMAL(3, 2) DEFAULT 0.00,
    total_reviews INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (location_id) REFERENCES locations(location_id)
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
    payment_status ENUM('pending', 'paid', 'partially_paid', 'refunded', 'failed') DEFAULT 'pending',
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
    payment_reference VARCHAR(50) UNIQUE,
    payment_method ENUM('credit_card', 'debit_card', 'paypal', 'bank_transfer', 'cash') NOT NULL,
    payment_gateway VARCHAR(50),
    transaction_id VARCHAR(100),
    amount DECIMAL(10, 2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'LKR',
    payment_status ENUM('pending', 'completed', 'failed', 'refunded', 'cancelled') DEFAULT 'pending',
    payment_date TIMESTAMP,
    gateway_response JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(booking_id)
);

-- 12. Reviews and Ratings Table
CREATE TABLE reviews (
    review_id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id INT NOT NULL,
    user_id INT NOT NULL,
    hotel_id INT NOT NULL,
    overall_rating ENUM('1', '2', '3', '4', '5') NOT NULL,
    cleanliness_rating ENUM('1', '2', '3', '4', '5'),
    service_rating ENUM('1', '2', '3', '4', '5'),
    location_rating ENUM('1', '2', '3', '4', '5'),
    value_rating ENUM('1', '2', '3', '4', '5'),
    review_title VARCHAR(200),
    review_text TEXT,
    pros TEXT,
    cons TEXT,
    review_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    is_verified BOOLEAN DEFAULT FALSE,
    helpful_votes INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(booking_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (hotel_id) REFERENCES hotels(hotel_id),
    UNIQUE KEY unique_review (booking_id, user_id)
);

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

-- Insert Sample Data

-- Insert sample admin user
INSERT INTO users (username, first_name, last_name, email, password, user_role, permissions, account_status) VALUES
('admin', 'System', 'Administrator', 'admin@pearlstay.lk', '$2y$10$example_hashed_password', 'super_admin', 
'{"hotels": "full", "bookings": "full", "users": "full", "reviews": "full", "settings": "full"}', 'active'),
('moderator', 'Review', 'Moderator', 'moderator@pearlstay.lk', '$2y$10$example_hashed_password', 'moderator', 
'{"reviews": "moderate", "hotels": "view", "bookings": "view"}', 'active');

-- Insert sample customer
INSERT INTO users (first_name, last_name, email, password, phone, nationality, user_role) VALUES
('John', 'Doe', 'john.doe@email.com', '$2y$10$example_hashed_password', '+1234567890', 'American', 'customer'),
('Jane', 'Smith', 'jane.smith@email.com', '$2y$10$example_hashed_password', '+9876543210', 'British', 'customer');

-- Insert sample locations
INSERT INTO locations (location_name, district, province, is_popular) VALUES
('Colombo', 'Colombo', 'Western Province', TRUE),
('Kandy', 'Kandy', 'Central Province', TRUE),
('Galle', 'Galle', 'Southern Province', TRUE),
('Ella', 'Badulla', 'Uva Province', TRUE),
('Nuwara Eliya', 'Nuwara Eliya', 'Central Province', TRUE),
('Sigiriya', 'Matale', 'Central Province', TRUE),
('Anuradhapura', 'Anuradhapura', 'North Central Province', TRUE),
('Bentota', 'Galle', 'Southern Province', TRUE),
('Mirissa', 'Matara', 'Southern Province', TRUE),
('Trincomalee', 'Trincomalee', 'Eastern Province', TRUE);

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
CREATE INDEX idx_hotels_location ON hotels(location_id);
CREATE INDEX idx_hotels_rating ON hotels(average_rating);
CREATE INDEX idx_reviews_hotel ON reviews(hotel_id);
CREATE INDEX idx_reviews_status ON reviews(review_status);
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_role ON users(user_role);
CREATE INDEX idx_users_status ON users(account_status);

-- Show tables created
SHOW TABLES;