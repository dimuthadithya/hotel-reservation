-- Add payment_logs table
CREATE TABLE IF NOT EXISTS payment_logs (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    payment_id INT NOT NULL,
    booking_id INT NOT NULL,
    action VARCHAR(50) NOT NULL,
    notes TEXT,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (payment_id) REFERENCES payments(payment_id),
    FOREIGN KEY (booking_id) REFERENCES bookings(booking_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);
