# LeverAI Consulting - Database Schema Documentation

This document describes the PostgreSQL database schema used by the LeverAI Consulting platform.

## Overview

The database uses PostgreSQL with UUID primary keys for better scalability and security. All tables follow a naming convention with the `_consulting` suffix.

---

## Tables

### users_consulting

Stores user account information.

| Column        | Type                     | Constraints      | Description                          |
|---------------|--------------------------|------------------|--------------------------------------|
| id            | UUID                     | PRIMARY KEY      | Unique user identifier               |
| name          | VARCHAR(255)             | NOT NULL         | User's full name                     |
| email         | VARCHAR(255)             | NOT NULL, UNIQUE | User's email address                 |
| password_hash | VARCHAR(255)             |                  | Bcrypt hashed password               |
| role          | VARCHAR(50)              | DEFAULT 'customer' | User role (customer, admin, developer) |
| created_at    | TIMESTAMP                | DEFAULT NOW()    | Account creation timestamp           |

**Indexes:**
- `idx_users_email` on `email`
- `idx_users_role` on `role`

---

### subscriptions_consulting

Stores user subscription information.

| Column          | Type                     | Constraints      | Description                          |
|-----------------|--------------------------|------------------|--------------------------------------|
| id              | SERIAL/UUID              | PRIMARY KEY      | Unique subscription identifier       |
| user_id         | UUID                     | FOREIGN KEY      | Reference to users_consulting.id     |
| plan_name       | VARCHAR(50)              | NOT NULL         | Plan type (basic, standard, pro, daily) |
| hours_allocated | DECIMAL(10,2)            | NOT NULL         | Total hours allocated for the period |
| hours_remaining | DECIMAL(10,2)            | NOT NULL         | Remaining hours                      |
| status          | VARCHAR(50)              | DEFAULT 'active' | Subscription status                  |
| start_date      | TIMESTAMP                | NOT NULL         | Subscription start date              |
| end_date        | TIMESTAMP                | NOT NULL         | Subscription end date                |

**Indexes:**
- `idx_subscriptions_user_id` on `user_id`
- `idx_subscriptions_status` on `status`

**Foreign Keys:**
- `user_id` → `users_consulting(id)` ON DELETE CASCADE

---

### billing_consulting

Stores billing and payment records.

| Column                   | Type                     | Constraints      | Description                          |
|--------------------------|--------------------------|------------------|--------------------------------------|
| id                       | SERIAL/UUID              | PRIMARY KEY      | Unique billing record identifier     |
| user_id                  | UUID                     | FOREIGN KEY      | Reference to users_consulting.id     |
| invoice_number           | VARCHAR(255)             | UNIQUE           | Invoice number or session ID         |
| stripe_session_id        | VARCHAR(255)             |                  | Stripe checkout session ID           |
| stripe_customer_id       | VARCHAR(255)             |                  | Stripe customer ID                   |
| stripe_subscription_id   | VARCHAR(255)             |                  | Stripe subscription ID               |
| stripe_invoice_id        | VARCHAR(255)             |                  | Stripe invoice ID                    |
| stripe_charge_id         | VARCHAR(255)             |                  | Stripe charge ID                     |
| stripe_payment_intent_id | VARCHAR(255)             |                  | Stripe payment intent ID             |
| amount                   | DECIMAL(10,2)            | NOT NULL         | Payment amount in USD                |
| status                   | VARCHAR(50)              | NOT NULL         | Payment status                       |
| billing_type             | VARCHAR(50)              | NOT NULL         | Type of billing                      |
| created_at               | TIMESTAMP                | DEFAULT NOW()    | Record creation timestamp            |
| paid_at                  | TIMESTAMP                |                  | Payment completion timestamp         |

**Status Values:**
- `pending` - Awaiting payment
- `paid` - Payment completed
- `failed` - Payment failed
- `cancelled` - Payment cancelled
- `active` - Subscription active
- `past_due` - Payment past due
- `trial` - Trial period

**Billing Type Values:**
- `new subscription` - Initial subscription payment
- `renewal subscription` - Monthly renewal payment
- `topup` - Additional hours purchase

**Indexes:**
- `idx_billing_user_id` on `user_id`
- `idx_billing_stripe_session_id` on `stripe_session_id`
- `idx_billing_stripe_customer_id` on `stripe_customer_id`
- `idx_billing_stripe_invoice_id` on `stripe_invoice_id`
- `idx_billing_status` on `status`

**Foreign Keys:**
- `user_id` → `users_consulting(id)` ON DELETE SET NULL

---

### tickets_consulting

Stores service request tickets.

| Column          | Type                     | Constraints      | Description                          |
|-----------------|--------------------------|------------------|--------------------------------------|
| id              | SERIAL                   | PRIMARY KEY      | Unique ticket database ID            |
| ticket_id       | VARCHAR(50)              | NOT NULL, UNIQUE | Human-readable ticket ID             |
| user_id         | UUID                     | FOREIGN KEY      | Reference to users_consulting.id     |
| title           | VARCHAR(255)             | NOT NULL         | Ticket title                         |
| details         | TEXT                     |                  | Detailed description                 |
| status          | VARCHAR(50)              | DEFAULT 'Pending'| Ticket status                        |
| request_priority| VARCHAR(50)              |                  | Priority level                       |
| dedicate_hours  | DECIMAL(10,2)            | DEFAULT 0        | Hours allocated to ticket            |
| created_at      | TIMESTAMP                | DEFAULT NOW()    | Ticket creation timestamp            |

**Ticket ID Format:** `TKT-YYYYMMDD-HHMMSS-XXXX`
- YYYYMMDD: Date
- HHMMSS: Time
- XXXX: Random 4-digit number

**Status Values:**
- `Pending` - Awaiting review
- `In Progress` - Being worked on
- `Completed` - Work completed
- `Rejected` - Request rejected (hours refunded)

**Priority Values:**
- `Low`
- `Medium`
- `High`
- `Urgent`

**Indexes:**
- `idx_tickets_user_id` on `user_id`
- `idx_tickets_status` on `status`
- `idx_tickets_ticket_id` on `ticket_id`

**Foreign Keys:**
- `user_id` → `users_consulting(id)` ON DELETE CASCADE

---

### ticket_comments_consulting

Stores comments on tickets.

| Column     | Type                     | Constraints      | Description                          |
|------------|--------------------------|------------------|--------------------------------------|
| id         | SERIAL                   | PRIMARY KEY      | Unique comment identifier            |
| ticket_id  | INTEGER                  | FOREIGN KEY      | Reference to tickets_consulting.id   |
| user_id    | UUID                     | FOREIGN KEY      | Reference to users_consulting.id     |
| usertype   | VARCHAR(50)              |                  | Type of user (customer, admin)       |
| message    | TEXT                     | NOT NULL         | Comment content                      |
| created_at | TIMESTAMP                | DEFAULT NOW()    | Comment creation timestamp           |

**Indexes:**
- `idx_comments_ticket_id` on `ticket_id`
- `idx_comments_user_id` on `user_id`

**Foreign Keys:**
- `ticket_id` → `tickets_consulting(id)` ON DELETE CASCADE
- `user_id` → `users_consulting(id)` ON DELETE SET NULL

---

### notifications_consulting

Stores user notifications.

| Column       | Type                     | Constraints      | Description                          |
|--------------|--------------------------|------------------|--------------------------------------|
| id           | SERIAL                   | PRIMARY KEY      | Unique notification identifier       |
| sender_id    | UUID                     |                  | User who triggered the notification  |
| recipient_id | UUID                     | FOREIGN KEY      | User receiving the notification      |
| ticket_id    | INTEGER                  |                  | Related ticket ID (if applicable)    |
| type         | VARCHAR(50)              | NOT NULL         | Notification type                    |
| message      | TEXT                     | NOT NULL         | Notification message                 |
| role         | VARCHAR(50)              |                  | Source role (customer, admin)        |
| is_read      | BOOLEAN                  | DEFAULT FALSE    | Read status                          |
| created_at   | TIMESTAMP                | DEFAULT NOW()    | Notification creation timestamp      |

**Type Values:**
- `request` - New request created
- `status` - Status changed
- `comment` - New comment added

**Indexes:**
- `idx_notifications_recipient_id` on `recipient_id`
- `idx_notifications_is_read` on `is_read`
- `idx_notifications_created_at` on `created_at`

**Foreign Keys:**
- `recipient_id` → `users_consulting(id)` ON DELETE CASCADE

---

### webhook_events

Tracks processed Stripe webhook events to prevent duplicate processing.

| Column       | Type                     | Constraints      | Description                          |
|--------------|--------------------------|------------------|--------------------------------------|
| id           | SERIAL                   | PRIMARY KEY      | Unique record identifier             |
| event_id     | VARCHAR(255)             | UNIQUE, NOT NULL | Stripe event ID                      |
| status       | VARCHAR(50)              | NOT NULL         | Processing status                    |
| created_at   | TIMESTAMP                | DEFAULT NOW()    | Record creation timestamp            |
| processed_at | TIMESTAMP                |                  | Processing completion timestamp      |
| updated_at   | TIMESTAMP                |                  | Last update timestamp                |

**Status Values:**
- `processing` - Currently being processed
- `processed` - Successfully processed

**Indexes:**
- `idx_webhook_events_event_id` on `event_id`
- `idx_webhook_events_status` on `status`

---

## Database Functions

### get_customers_consulting()

Returns all users with role 'customer'.

```sql
CREATE OR REPLACE FUNCTION get_customers_consulting()
RETURNS TABLE (
    id UUID,
    name VARCHAR,
    email VARCHAR,
    created_at TIMESTAMP
) AS $$
BEGIN
    RETURN QUERY
    SELECT u.id, u.name, u.email, u.created_at
    FROM users_consulting u
    WHERE u.role = 'customer'
    ORDER BY u.created_at DESC;
END;
$$ LANGUAGE plpgsql;
```

### get_tickets_consulting()

Returns all tickets with user information.

```sql
CREATE OR REPLACE FUNCTION get_tickets_consulting()
RETURNS TABLE (
    id INTEGER,
    ticket_id VARCHAR,
    user_id UUID,
    user_name VARCHAR,
    title VARCHAR,
    details TEXT,
    status VARCHAR,
    request_priority VARCHAR,
    dedicate_hours DECIMAL,
    created_at TIMESTAMP
) AS $$
BEGIN
    RETURN QUERY
    SELECT 
        t.id, t.ticket_id, t.user_id, 
        u.name as user_name,
        t.title, t.details, t.status, 
        t.request_priority, t.dedicate_hours, t.created_at
    FROM tickets_consulting t
    LEFT JOIN users_consulting u ON t.user_id = u.id
    ORDER BY t.created_at DESC;
END;
$$ LANGUAGE plpgsql;
```

### get_ticket_details_consulting(ticket_id)

Returns detailed ticket information.

```sql
CREATE OR REPLACE FUNCTION get_ticket_details_consulting(p_ticket_id INTEGER)
RETURNS TABLE (
    id INTEGER,
    ticket_id VARCHAR,
    user_id UUID,
    user_name VARCHAR,
    user_email VARCHAR,
    title VARCHAR,
    details TEXT,
    status VARCHAR,
    request_priority VARCHAR,
    dedicate_hours DECIMAL,
    created_at TIMESTAMP
) AS $$
BEGIN
    RETURN QUERY
    SELECT 
        t.id, t.ticket_id, t.user_id,
        u.name as user_name, u.email as user_email,
        t.title, t.details, t.status,
        t.request_priority, t.dedicate_hours, t.created_at
    FROM tickets_consulting t
    LEFT JOIN users_consulting u ON t.user_id = u.id
    WHERE t.id = p_ticket_id;
END;
$$ LANGUAGE plpgsql;
```

### get_user_subscriptions_consulting(user_id)

Returns subscription information for a user or all users.

```sql
CREATE OR REPLACE FUNCTION get_user_subscriptions_consulting(p_user_id UUID DEFAULT NULL)
RETURNS TABLE (
    id UUID,
    name VARCHAR,
    email VARCHAR,
    plan_name VARCHAR,
    hours_allocated DECIMAL,
    hours_remaining DECIMAL,
    status VARCHAR,
    start_date TIMESTAMP,
    end_date TIMESTAMP
) AS $$
BEGIN
    RETURN QUERY
    SELECT 
        u.id, u.name, u.email,
        s.plan_name, s.hours_allocated, s.hours_remaining,
        s.status, s.start_date, s.end_date
    FROM users_consulting u
    LEFT JOIN subscriptions_consulting s ON u.id = s.user_id
    WHERE u.role = 'customer'
      AND (p_user_id IS NULL OR u.id = p_user_id)
    ORDER BY u.created_at DESC;
END;
$$ LANGUAGE plpgsql;
```

### get_user_billing_consulting()

Returns billing records with user information.

```sql
CREATE OR REPLACE FUNCTION get_user_billing_consulting()
RETURNS TABLE (
    id INTEGER,
    user_id UUID,
    user_name VARCHAR,
    invoice_number VARCHAR,
    amount DECIMAL,
    status VARCHAR,
    billing_type VARCHAR,
    created_at TIMESTAMP,
    paid_at TIMESTAMP
) AS $$
BEGIN
    RETURN QUERY
    SELECT 
        b.id, b.user_id,
        u.name as user_name,
        b.invoice_number, b.amount, b.status,
        b.billing_type, b.created_at, b.paid_at
    FROM billing_consulting b
    LEFT JOIN users_consulting u ON b.user_id = u.id
    ORDER BY b.created_at DESC;
END;
$$ LANGUAGE plpgsql;
```

### get_billing_totals_prev_curr_consulting()

Returns revenue totals for current and previous month.

```sql
CREATE OR REPLACE FUNCTION get_billing_totals_prev_curr_consulting()
RETURNS TABLE (
    period VARCHAR,
    total DECIMAL
) AS $$
BEGIN
    RETURN QUERY
    SELECT 'current_month'::VARCHAR as period, 
           COALESCE(SUM(amount), 0) as total
    FROM billing_consulting
    WHERE status = 'paid'
      AND DATE_TRUNC('month', paid_at) = DATE_TRUNC('month', CURRENT_DATE)
    UNION ALL
    SELECT 'previous_month'::VARCHAR as period,
           COALESCE(SUM(amount), 0) as total
    FROM billing_consulting
    WHERE status = 'paid'
      AND DATE_TRUNC('month', paid_at) = DATE_TRUNC('month', CURRENT_DATE - INTERVAL '1 month');
END;
$$ LANGUAGE plpgsql;
```

### get_active_plan_counts_consulting()

Returns count of active subscriptions by plan.

```sql
CREATE OR REPLACE FUNCTION get_active_plan_counts_consulting()
RETURNS TABLE (
    plan_name VARCHAR,
    count BIGINT
) AS $$
BEGIN
    RETURN QUERY
    SELECT s.plan_name, COUNT(*) as count
    FROM subscriptions_consulting s
    WHERE s.status = 'active'
    GROUP BY s.plan_name
    ORDER BY count DESC;
END;
$$ LANGUAGE plpgsql;
```

### get_ticket_counts_by_status_consulting()

Returns count of tickets by status.

```sql
CREATE OR REPLACE FUNCTION get_ticket_counts_by_status_consulting()
RETURNS TABLE (
    status VARCHAR,
    count BIGINT
) AS $$
BEGIN
    RETURN QUERY
    SELECT t.status, COUNT(*) as count
    FROM tickets_consulting t
    GROUP BY t.status
    ORDER BY count DESC;
END;
$$ LANGUAGE plpgsql;
```

---

## Entity Relationship Diagram

```
┌─────────────────────┐
│  users_consulting   │
├─────────────────────┤
│ id (PK)             │
│ name                │
│ email               │
│ password_hash       │
│ role                │
│ created_at          │
└─────────┬───────────┘
          │
          │ 1:1
          ▼
┌─────────────────────────┐
│ subscriptions_consulting│
├─────────────────────────┤
│ id (PK)                 │
│ user_id (FK)            │
│ plan_name               │
│ hours_allocated         │
│ hours_remaining         │
│ status                  │
│ start_date              │
│ end_date                │
└─────────────────────────┘

┌─────────────────────┐         ┌──────────────────────────┐
│  users_consulting   │         │    billing_consulting    │
├─────────────────────┤ 1:N     ├──────────────────────────┤
│ id (PK)             │◄────────│ user_id (FK)             │
└─────────────────────┘         │ id (PK)                  │
                                │ invoice_number           │
                                │ stripe_* (various)       │
                                │ amount                   │
                                │ status                   │
                                │ billing_type             │
                                └──────────────────────────┘

┌─────────────────────┐         ┌─────────────────────────┐
│  users_consulting   │         │   tickets_consulting    │
├─────────────────────┤ 1:N     ├─────────────────────────┤
│ id (PK)             │◄────────│ user_id (FK)            │
└─────────────────────┘         │ id (PK)                 │
                                │ ticket_id               │
                                │ title                   │
                                │ details                 │
                                │ status                  │
                                │ dedicate_hours          │
                                └───────────┬─────────────┘
                                            │
                                            │ 1:N
                                            ▼
                                ┌─────────────────────────────┐
                                │ ticket_comments_consulting  │
                                ├─────────────────────────────┤
                                │ id (PK)                     │
                                │ ticket_id (FK)              │
                                │ user_id (FK)                │
                                │ message                     │
                                │ usertype                    │
                                └─────────────────────────────┘

┌─────────────────────┐         ┌────────────────────────────┐
│  users_consulting   │         │ notifications_consulting   │
├─────────────────────┤ 1:N     ├────────────────────────────┤
│ id (PK)             │◄────────│ recipient_id (FK)          │
└─────────────────────┘         │ id (PK)                    │
                                │ sender_id                  │
                                │ ticket_id                  │
                                │ type                       │
                                │ message                    │
                                │ is_read                    │
                                └────────────────────────────┘
```

---

## Migration Notes

### Creating UUID Extension

```sql
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";
```

### Default UUID Generation

```sql
ALTER TABLE users_consulting 
ALTER COLUMN id SET DEFAULT uuid_generate_v4();
```

---

## Backup and Restore

### Backup

```bash
pg_dump -h hostname -U username -d database_name > backup.sql
```

### Restore

```bash
psql -h hostname -U username -d database_name < backup.sql
```

---

## Performance Considerations

1. **UUID vs Serial**: UUIDs are used for distributed systems compatibility but have slightly larger storage overhead
2. **Indexing**: Critical columns are indexed for query performance
3. **Foreign Keys**: Cascade deletes prevent orphaned records
4. **Functions**: Stored functions reduce network overhead for complex queries
