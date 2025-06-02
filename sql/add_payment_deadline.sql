
-- Modify the payments table structure
ALTER TABLE payments 
ADD COLUMN IF NOT EXISTS payment_deadline DATETIME AFTER payment_date,
ADD COLUMN IF NOT EXISTS verified_by INT AFTER notes,
ADD COLUMN IF NOT EXISTS verified_at DATETIME AFTER verified_by,
ADD COLUMN IF NOT EXISTS bank_name VARCHAR(100) AFTER payment_method,
ADD COLUMN IF NOT EXISTS bank_reference VARCHAR(50) AFTER bank_name,
ADD COLUMN IF NOT EXISTS transfer_date DATE AFTER bank_reference,
ADD COLUMN IF NOT EXISTS bank_slip VARCHAR(255) AFTER transfer_date;

-- Update payment status enum
ALTER TABLE payments 
MODIFY COLUMN status ENUM('pending', 'completed', 'failed', 'expired') DEFAULT 'pending';

-- Update payment_method enum
ALTER TABLE payments 
MODIFY COLUMN payment_method ENUM('bank_transfer', 'cash') NOT NULL;

-- Add foreign key for verified_by if not exists
ALTER TABLE payments
ADD CONSTRAINT IF NOT EXISTS fk_payment_verifier
FOREIGN KEY (verified_by) REFERENCES users(user_id)
ON DELETE SET NULL ON UPDATE CASCADE;

-- Add indexes for better performance
CREATE INDEX IF NOT EXISTS idx_payment_status ON payments(status);
CREATE INDEX IF NOT EXISTS idx_payment_method ON payments(payment_method);
CREATE INDEX IF NOT EXISTS idx_payment_date ON payments(payment_date);
CREATE INDEX IF NOT EXISTS idx_payment_deadline ON payments(payment_deadline);
