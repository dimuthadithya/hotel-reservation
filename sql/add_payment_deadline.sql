-- Add payment_deadline to payments table if not exists
ALTER TABLE payments 
ADD COLUMN IF NOT EXISTS payment_deadline DATETIME AFTER payment_date;

-- Update payment status enum if needed
ALTER TABLE payments 
MODIFY COLUMN status ENUM('pending', 'completed', 'failed', 'expired') DEFAULT 'pending';

-- Update payment_method enum if needed
ALTER TABLE payments 
MODIFY COLUMN payment_method ENUM('bank_transfer', 'cash') NOT NULL;
