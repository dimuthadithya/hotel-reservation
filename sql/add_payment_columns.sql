-- Add payment related columns to bookings table
ALTER TABLE bookings 
ADD COLUMN payment_status ENUM('pending', 'paid', 'cancelled') DEFAULT 'pending',
ADD COLUMN payment_deadline DATETIME,
ADD COLUMN payment_date DATETIME;

-- Create payments table
CREATE TABLE IF NOT EXISTS payments (
    payment_id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    transaction_id VARCHAR(100),
    payment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    FOREIGN KEY (booking_id) REFERENCES bookings(booking_id)
);
