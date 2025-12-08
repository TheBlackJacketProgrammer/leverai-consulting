-- Create webhook_events table for Stripe webhook deduplication
CREATE TABLE IF NOT EXISTS webhook_events (
    id SERIAL PRIMARY KEY,
    event_id VARCHAR(255) UNIQUE NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'processing',
    processed_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_webhook_events_event_id ON webhook_events(event_id);
CREATE INDEX IF NOT EXISTS idx_webhook_events_status ON webhook_events(status);
CREATE INDEX IF NOT EXISTS idx_webhook_events_processed_at ON webhook_events(processed_at);

-- Add additional columns to billing table if they don't exist
-- These columns are needed for the enhanced webhook handling
ALTER TABLE billing ADD COLUMN IF NOT EXISTS stripe_charge_id VARCHAR(255);
ALTER TABLE billing ADD COLUMN IF NOT EXISTS stripe_payment_intent_id VARCHAR(255);

-- Create indexes for the new billing columns
CREATE INDEX IF NOT EXISTS idx_billing_stripe_charge_id ON billing(stripe_charge_id);
CREATE INDEX IF NOT EXISTS idx_billing_stripe_payment_intent_id ON billing(stripe_payment_intent_id);

-- Optional: Add a trigger to automatically update the updated_at timestamp
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ language 'plpgsql';

-- Apply the trigger to webhook_events table
DROP TRIGGER IF EXISTS update_webhook_events_updated_at ON webhook_events;
CREATE TRIGGER update_webhook_events_updated_at
    BEFORE UPDATE ON webhook_events
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();
