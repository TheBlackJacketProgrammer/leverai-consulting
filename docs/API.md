# LeverAI Consulting - API Documentation

This document provides detailed information about the REST API endpoints available in the LeverAI Consulting platform.

## Base URL

- **Development**: `http://localhost/leverai-consulting/`
- **Production**: `https://leverai.consulting/`

## Authentication

Most API endpoints require an authenticated session. Authentication is managed via PHP sessions.

### Login

**POST** `/authenticate`

Authenticates a user and creates a session.

**Request Body:**
```json
{
  "email": "user@example.com",
  "password": "your_password"
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Login successful!"
}
```

**Error Responses:**
```json
{
  "success": false,
  "message": "Account not found."
}
```

```json
{
  "success": false,
  "message": "Incorrect password."
}
```

```json
{
  "success": false,
  "message": "Subscription is pending. Please complete payment.",
  "checkout_url": "https://checkout.stripe.com/...",
  "requires_payment": true
}
```

### Logout

**GET** `/logout`

Destroys the current session and redirects to the login page.

---

## User Management

### Get User Profile

**GET** `/api/get_user_profile`

Returns the current user's profile information.

**Response:**
```json
{
  "user_profile": {
    "id": "uuid-string",
    "name": "John Doe",
    "email": "john@example.com",
    "role": "customer",
    "created_at": "2025-01-15 10:30:00"
  }
}
```

### Update User Profile

**POST** `/api/update_user_profile`

Updates the current user's profile information.

**Request Body:**
```json
{
  "fullname": "John Smith",
  "new_password": "optional_new_password"
}
```

**Response:**
```json
{
  "success": true,
  "message": "User profile updated successfully"
}
```

### Get Remaining Hours

**GET** `/api/remaining_hours`

Returns the user's remaining consulting hours.

**Response:**
```json
{
  "hours_remaining": 45.5
}
```

---

## Subscription & Registration

### Register and Checkout

**POST** `/api/register_and_checkout`

Creates a new user account and initiates Stripe checkout for subscription.

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "secure_password",
  "plan": "standard"
}
```

**Available Plans:**
- `basic` - 1 hour/month ($50)
- `standard` - 10 hours/month ($450)
- `pro` - 100 hours/month ($4,000)
- `daily` - 1 hour/day ($50)

**Success Response:**
```json
{
  "url": "https://checkout.stripe.com/c/pay/..."
}
```

**Error Responses:**
```json
{
  "error": "Invalid plan selected"
}
```

```json
{
  "error": "User already has an active subscription"
}
```

### Top Up Hours

**POST** `/api/top_up`

Purchase additional consulting hours.

**Request Body:**
```json
{
  "hours": 5,
  "total": 250
}
```

**Success Response:**
```json
{
  "success": true,
  "checkout_url": "https://checkout.stripe.com/c/pay/...",
  "message": "Redirecting to payment..."
}
```

---

## Ticket/Request Management

### Create Request

**POST** `/api/create_request`

Creates a new service request/ticket.

**Request Body:**
```json
{
  "title": "Website Bug Fix",
  "details": "There is a bug on the login page that prevents users from logging in.",
  "request_priority": "High",
  "dedicate_hours": 2
}
```

**Success Response:**
```json
{
  "success": true,
  "message": "Request created successfully",
  "ticket_id": "TKT-20250131-143052-1234"
}
```

**Note:** Creating a request deducts the specified hours from the user's remaining balance.

### Get All Tickets (Admin)

**GET** `/api/get_all_tickets`

Returns all tickets across all users (admin only).

**Response:**
```json
{
  "tickets": [
    {
      "id": 1,
      "ticket_id": "TKT-20250131-143052-1234",
      "user_id": "uuid-string",
      "user_name": "John Doe",
      "title": "Website Bug Fix",
      "details": "Description...",
      "status": "Pending",
      "request_priority": "High",
      "dedicate_hours": 2,
      "created_at": "2025-01-31 14:30:52"
    }
  ]
}
```

### Get User's Tickets

**GET** `/api/get_all_tickets_by_user`

Returns tickets for the currently logged-in user.

**Response:**
```json
{
  "tickets": [
    {
      "id": 1,
      "ticket_id": "TKT-20250131-143052-1234",
      "title": "Website Bug Fix",
      "status": "In Progress",
      "dedicate_hours": 2,
      "created_at": "2025-01-31 14:30:52"
    }
  ]
}
```

### Get Ticket Details with Comments

**POST** `/api/get_ticket_with_comments`

Returns detailed ticket information including all comments.

**Request Body:**
```json
{
  "ticket_id": 1
}
```

**Response:**
```json
{
  "ticket_details": {
    "id": 1,
    "ticket_id": "TKT-20250131-143052-1234",
    "title": "Website Bug Fix",
    "details": "Full description...",
    "status": "In Progress",
    "request_priority": "High",
    "dedicate_hours": 2,
    "user_name": "John Doe",
    "user_email": "john@example.com"
  },
  "ticket_comments": [
    {
      "id": 1,
      "message": "Working on this now",
      "user_name": "Admin",
      "usertype": "admin",
      "created_at": "2025-01-31 15:00:00"
    }
  ]
}
```

### Update Ticket Status

**POST** `/api/update_status`

Updates the status of a ticket (admin only).

**Request Body:**
```json
{
  "ticket_id": 1,
  "status": "In Progress",
  "dedicate_hours": 2
}
```

**Available Statuses:**
- `Pending`
- `In Progress`
- `Completed`
- `Rejected`

**Response:**
```json
{
  "success": true,
  "message": "Status updated successfully"
}
```

**Note:** If status is set to "Rejected", the dedicated hours are refunded to the user.

### Send Comment

**POST** `/api/send_comment`

Adds a comment to a ticket.

**Request Body:**
```json
{
  "ticket_id": 1,
  "message": "Your comment here",
  "title": "Website Bug Fix",
  "user_id": "uuid-of-ticket-owner"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Comment sent successfully"
}
```

### Update Dedicated Hours

**POST** `/api/update_dedicate_hours`

Updates the hours allocated to a ticket.

**Request Body:**
```json
{
  "ticket_id": 1,
  "dedicate_hours": 5,
  "hours_remaining": 40
}
```

**Response:**
```json
{
  "success": true,
  "message": "Dedicate hours updated successfully",
  "hours_remaining": 40
}
```

---

## Notifications

### Get Notifications

**GET** `/api/get_notifications`

Returns notifications for the current user.

**Query Parameters:**
- `since` (optional): ISO datetime - Get notifications after this time
- `before` (optional): ISO datetime - Get notifications before this time
- `limit` (optional): Number - Maximum notifications to return

**Response:**
```json
{
  "notifications": [
    {
      "id": 1,
      "message": "Admin updated the status of your ticket to Completed",
      "type": "status",
      "ticket_id": 1,
      "is_read": false,
      "created_at": "2025-01-31 16:00:00"
    }
  ]
}
```

### Mark Notification as Read

**POST** `/api/mark_as_read`

Marks a single notification as read.

**Request Body:**
```json
{
  "id": 1
}
```

**Response:**
```json
{
  "success": true,
  "message": "Notification marked as read"
}
```

### Mark All Notifications as Read

**POST** `/api/mark_all_as_read`

Marks all notifications for the current user as read.

**Response:**
```json
{
  "success": true,
  "message": "All notifications marked as read",
  "affected_rows": 5
}
```

---

## Billing & Admin

### Get All Customers (Admin)

**GET** `/api/get_all_customers`

Returns all customers with their subscription information.

**Response:**
```json
{
  "customers": [
    {
      "id": "uuid-string",
      "name": "John Doe",
      "email": "john@example.com",
      "plan_name": "standard",
      "hours_allocated": 10,
      "hours_remaining": 5,
      "status": "active",
      "start_date": "2025-01-01",
      "end_date": "2025-01-31"
    }
  ]
}
```

### Get All Billing (Admin)

**GET** `/api/get_all_billing`

Returns all billing records.

**Response:**
```json
{
  "billing": [
    {
      "id": 1,
      "user_id": "uuid-string",
      "user_name": "John Doe",
      "invoice_number": "INV-12345",
      "amount": 450.00,
      "status": "paid",
      "billing_type": "new subscription",
      "created_at": "2025-01-01 10:00:00",
      "paid_at": "2025-01-01 10:05:00"
    }
  ]
}
```

### Get Billing Totals

**GET** `/api/get_billing_totals_prev_curr`

Returns revenue comparison between current and previous month.

**Response:**
```json
{
  "billing_totals": [
    {
      "period": "current_month",
      "total": 4500.00
    },
    {
      "period": "previous_month",
      "total": 3200.00
    }
  ]
}
```

### Get Active Plan Counts

**GET** `/api/get_active_plan_counts`

Returns count of active subscriptions by plan type.

**Response:**
```json
{
  "active_plan_counts": [
    { "plan_name": "basic", "count": 10 },
    { "plan_name": "standard", "count": 25 },
    { "plan_name": "pro", "count": 5 }
  ]
}
```

### Get Ticket Counts by Status

**GET** `/api/get_ticket_counts_by_status`

Returns count of tickets grouped by status.

**Response:**
```json
{
  "ticket_counts": [
    { "status": "Pending", "count": 15 },
    { "status": "In Progress", "count": 8 },
    { "status": "Completed", "count": 42 },
    { "status": "Rejected", "count": 3 }
  ]
}
```

### Download Invoice PDF

**GET** `/api/download_invoice_pdf`

Downloads an invoice PDF from Stripe.

**Query Parameters:**
- `invoice_id` (required): Stripe invoice ID

**Response:** PDF file download

---

## Stripe Webhooks

### Webhook Handler

**POST** `/api/stripe_webhook`

Handles incoming Stripe webhook events. This endpoint should be configured in your Stripe dashboard.

**Handled Events:**
- `customer.created`
- `customer.updated`
- `payment_method.attached`
- `checkout.session.completed`
- `checkout.session.expired`
- `customer.subscription.created`
- `customer.subscription.updated`
- `customer.subscription.deleted`
- `invoice.created`
- `invoice.finalized`
- `invoice.updated`
- `invoice.paid`
- `invoice.payment_succeeded`
- `invoice.payment_failed`
- `payment_intent.created`
- `payment_intent.succeeded`
- `charge.succeeded`
- `charge.updated`

---

## Admin Module Loading

### Load Admin Module

**POST** `/load_module`

Loads an admin dashboard module dynamically.

**Request Body:**
```json
{
  "module": "dashboard"
}
```

**Available Modules:**
- `dashboard` - Main admin dashboard
- `customers` - Customer management
- `request` - Ticket management
- `billing` - Billing records

**Response:**
```json
{
  "section": "<html content of the module>"
}
```

---

## Error Handling

All API endpoints return JSON responses. Errors are indicated by:

**Authentication Errors:**
```json
{
  "error": "Unauthorized"
}
```

**Validation Errors:**
```json
{
  "success": false,
  "error": "Invalid JSON data."
}
```

**Server Errors:**
```json
{
  "error": "Server error: <message>"
}
```

---

## Rate Limiting

Currently, there is no rate limiting implemented. For production use, consider implementing rate limiting at the server level (Caddy, nginx) or application level.

---

## Security Considerations

1. **HTTPS**: All production requests should be over HTTPS
2. **CSRF**: The application uses CodeIgniter's built-in CSRF protection
3. **SQL Injection**: All database queries use parameterized queries
4. **XSS**: Output is escaped in views
5. **Password Security**: Passwords are hashed using bcrypt (PASSWORD_BCRYPT)
