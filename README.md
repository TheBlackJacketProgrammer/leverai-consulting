# LeverAI Consulting

A web-based consulting services management platform built with CodeIgniter 3.x, featuring subscription-based payments, ticket management, and client dashboards.

## Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Technology Stack](#technology-stack)
- [Project Structure](#project-structure)
- [Installation](#installation)
- [Configuration](#configuration)
- [Database Setup](#database-setup)
- [Running the Application](#running-the-application)
- [API Reference](#api-reference)
- [Deployment](#deployment)
- [Development](#development)

---

## Overview

LeverAI Consulting is a SaaS platform that enables consulting businesses to manage client subscriptions, service requests (tickets), and billing. Customers subscribe to plans that provide a set number of consulting hours, which they can use to submit service requests.

### Core Concepts

- **Subscription Plans**: Customers purchase plans (Basic, Standard, Pro, Daily) that allocate consulting hours
- **Hours-Based Billing**: Service requests consume allocated hours from the customer's subscription
- **Ticket System**: Customers submit requests that are tracked, prioritized, and commented on
- **Role-Based Access**: Separate dashboards for Customers, Admins, and Developers

---

## Features

### User Management
- User registration with email verification
- Secure authentication with password hashing (bcrypt)
- Role-based access control (Customer, Admin, Developer)
- User profile management

### Subscription & Billing
- **Stripe Integration** for secure payment processing
- Multiple subscription tiers:
  | Plan     | Hours/Month | Price   |
  |----------|-------------|---------|
  | Basic    | 1 hour      | $50     |
  | Standard | 10 hours    | $450    |
  | Pro      | 100 hours   | $4,000  |
  | Daily    | 1 hour/day  | $50     |
- Top-up hours purchasing
- Automatic subscription renewal
- Invoice generation and PDF download
- Billing history tracking

### Ticket/Request Management
- Create service requests with title, details, and priority
- Allocate hours to each request
- Status tracking (Pending, In Progress, Completed, Rejected)
- Comment system for client-admin communication
- Automatic ticket ID generation (TKT-YYYYMMDD-HHMMSS-XXXX)

### Notifications
- Real-time notification system
- Mark as read functionality
- Role-based notification delivery

### Admin Dashboard
- Customer management
- View all tickets across customers
- Update ticket status
- Billing overview
- Revenue analytics (current vs previous month)
- Active plan counts
- Ticket status distribution

### Customer Dashboard
- View remaining hours
- Create new service requests
- Track request status
- View billing history
- Profile management

---

## Technology Stack

### Backend
- **PHP 5.3.7+** (Compatible with PHP 7.x and 8.x)
- **CodeIgniter 3.x** - MVC Framework
- **PostgreSQL** - Database (with UUID support)

### Frontend
- **HTML5/CSS3**
- **Tailwind CSS 3.4** - Utility-first CSS framework
- **SCSS/SASS** - CSS preprocessor
- **JavaScript (ES6+)**
- **Chart.js** - Data visualization

### Third-Party Libraries
- **Stripe PHP SDK** - Payment processing
- **DomPDF** - PDF generation
- **PHPMailer** (via CodeIgniter Email) - Email sending

### DevOps
- **Docker** - Containerization
- **Docker Compose** - Multi-container orchestration
- **GitHub Actions** - CI/CD pipeline
- **Caddy** - Web server (production)

---

## Project Structure

```
leverai-consulting/
├── application/                 # CodeIgniter application directory
│   ├── config/                  # Configuration files
│   │   ├── autoload.php         # Auto-loaded resources
│   │   ├── config.php           # Main configuration
│   │   ├── database.php         # Database credentials (gitignored)
│   │   ├── database.example.php # Database template
│   │   ├── email.php            # Email configuration
│   │   └── routes.php           # URL routing
│   │
│   ├── controllers/             # Application controllers
│   │   ├── Ctrl_Admin.php       # Admin module loader
│   │   ├── Ctrl_Api.php         # Main API endpoints
│   │   ├── Ctrl_Dev.php         # Developer testing endpoints
│   │   ├── Ctrl_Main.php        # Main page controller
│   │   └── Ctrl_Stripe_Api.php  # Stripe payment integration
│   │
│   ├── helpers/                 # Custom helper functions
│   │   ├── dompdf_helper.php    # PDF generation helper
│   │   └── file_upload_helper.php
│   │
│   ├── hooks/                   # CodeIgniter hooks
│   │   └── Api_json_hook.php    # JSON response formatting
│   │
│   ├── libraries/               # Custom libraries
│   │   ├── Dompdf_lib.php       # DomPDF wrapper
│   │   └── Emailer.php          # Email sending library
│   │
│   ├── models/                  # Database models
│   │   ├── Model_Api.php        # API data operations
│   │   └── Model_Main.php       # Main data operations
│   │
│   ├── third_party/             # Third-party libraries
│   │   ├── dompdf/              # PDF library
│   │   └── stripe/              # Stripe SDK
│   │
│   └── views/                   # View templates
│       ├── admin-modules/       # Admin dashboard modules
│       ├── components/          # Reusable UI components
│       ├── emails/              # Email templates
│       ├── errors/              # Error pages
│       ├── pages/               # Main pages
│       └── pdf/                 # PDF templates
│
├── assets/                      # Frontend assets
│   ├── css/                     # Compiled CSS
│   ├── dist/                    # Bundled JavaScript
│   ├── fonts/                   # Custom fonts (Roboto)
│   ├── img/                     # Images and icons
│   ├── js/                      # JavaScript source files
│   └── scss/                    # SCSS source files
│
├── system/                      # CodeIgniter core (do not modify)
│
├── .github/workflows/           # GitHub Actions
│   └── deploy.yml               # Deployment workflow
│
├── Dockerfile                   # Docker build configuration
├── docker-compose.yml           # Docker services definition
├── Caddyfile                    # Caddy server configuration
├── composer.json                # PHP dependencies
├── package.json                 # Node.js dependencies
├── tailwind.config.js           # Tailwind CSS configuration
└── index.php                    # Application entry point
```

---

## Installation

### Prerequisites

- PHP 7.4+ (recommended) or PHP 5.3.7+
- PostgreSQL 12+
- Composer
- Node.js 18+ and npm
- Docker & Docker Compose (for containerized deployment)

### Local Development Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-org/leverai-consulting.git
   cd leverai-consulting
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Build frontend assets**
   ```bash
   npm run build
   ```

5. **Configure the application** (see [Configuration](#configuration))

6. **Set up the database** (see [Database Setup](#database-setup))

7. **Start the development server**
   - Using XAMPP: Place the project in `htdocs/leverai-consulting`
   - Using PHP built-in server:
     ```bash
     php -S localhost:8080
     ```

---

## Configuration

### Database Configuration

1. Copy the example database configuration:
   ```bash
   cp application/config/database.example.php application/config/database.php
   ```

2. Edit `application/config/database.php`:
   ```php
   $db['default'] = array(
       'hostname' => 'YOUR_HOST',
       'port'     => 'YOUR_PORT',
       'username' => 'YOUR_USERNAME',
       'password' => 'YOUR_PASSWORD',
       'database' => 'YOUR_DATABASE',
       'dbdriver' => 'postgre',
       // ... other settings
   );
   ```

### Stripe Configuration

Add Stripe API keys to `application/config/config.php`:

```php
// Stripe API Configuration
$config['stripe_secret_key'] = 'sk_live_xxxxx';  // or sk_test_xxxxx for testing
$config['stripe_publishable_key'] = 'pk_live_xxxxx';
$config['stripe_webhook_secret'] = 'whsec_xxxxx';

// Stripe Price IDs for each plan
$config['stripe_prices'] = [
    'basic'    => 'price_xxxxx',
    'standard' => 'price_xxxxx',
    'pro'      => 'price_xxxxx',
    'daily'    => 'price_xxxxx'
];
```

### Email Configuration

Configure SMTP settings in `application/config/email.php` or via environment variables:

```php
$config['protocol'] = 'smtp';
$config['smtp_host'] = 'smtp.gmail.com';
$config['smtp_port'] = 587;
$config['smtp_user'] = 'your-email@gmail.com';
$config['smtp_pass'] = 'your-app-password';
$config['smtp_crypto'] = 'tls';
$config['mailtype'] = 'html';
```

### Environment Variables (Docker)

When using Docker, configure via `docker-compose.yml`:

```yaml
environment:
  SMTP_HOST: smtp.gmail.com
  SMTP_USER: your-email@gmail.com
  SMTP_PASS: your-app-password
  SMTP_PORT: 587
  SMTP_CRYPTO: tls
  FROM_EMAIL: noreply@yourdomain.com
  FROM_NAME: LeverAI
```

---

## Database Setup

### Required Tables

The application uses PostgreSQL with the following main tables:

| Table                       | Description                           |
|-----------------------------|---------------------------------------|
| `users_consulting`          | User accounts and credentials         |
| `subscriptions_consulting`  | User subscription plans               |
| `billing_consulting`        | Payment and invoice records           |
| `tickets_consulting`        | Service requests/tickets              |
| `ticket_comments_consulting`| Comments on tickets                   |
| `notifications_consulting`  | User notifications                    |
| `webhook_events`            | Stripe webhook event tracking         |

### Database Functions

The application uses PostgreSQL stored functions:

- `get_customers_consulting()` - Get all customers
- `get_tickets_consulting()` - Get all tickets with user info
- `get_ticket_details_consulting(ticket_id)` - Get ticket details
- `get_user_subscriptions_consulting(user_id)` - Get subscriptions
- `get_user_billing_consulting()` - Get billing records
- `get_billing_totals_prev_curr_consulting()` - Revenue analytics
- `get_active_plan_counts_consulting()` - Plan distribution
- `get_ticket_counts_by_status_consulting()` - Ticket statistics

---

## Running the Application

### Development Mode

```bash
# Watch for SCSS/Tailwind changes
npm run dev

# Or build once
npm run build
```

### Docker Deployment

```bash
# Build and start containers
docker-compose up -d --build

# View logs
docker-compose logs -f app

# Stop containers
docker-compose down
```

The application will be available at `http://localhost:35688`

### Production URLs

- Main site: `https://leverai.consulting` or `https://www.leverai.dev`

---

## API Reference

### Authentication

| Endpoint          | Method | Description              |
|-------------------|--------|--------------------------|
| `/authenticate`   | POST   | User login               |
| `/logout`         | GET    | User logout              |
| `/api/login`      | POST   | REST API login           |

### User API

| Endpoint                    | Method | Description                    |
|-----------------------------|--------|--------------------------------|
| `/api/get_user_profile`     | GET    | Get current user profile       |
| `/api/update_user_profile`  | POST   | Update user profile            |
| `/api/remaining_hours`      | GET    | Get remaining subscription hours|

### Ticket/Request API

| Endpoint                        | Method | Description                    |
|---------------------------------|--------|--------------------------------|
| `/api/create_request`           | POST   | Create new service request     |
| `/api/get_all_tickets`          | GET    | Get all tickets (admin)        |
| `/api/get_all_tickets_by_user`  | GET    | Get user's tickets             |
| `/api/get_ticket_with_comments` | POST   | Get ticket details + comments  |
| `/api/update_status`            | POST   | Update ticket status           |
| `/api/send_comment`             | POST   | Add comment to ticket          |
| `/api/update_dedicate_hours`    | POST   | Update hours for ticket        |

### Notification API

| Endpoint                  | Method | Description                    |
|---------------------------|--------|--------------------------------|
| `/api/get_notifications`  | GET    | Get user notifications         |
| `/api/mark_as_read`       | POST   | Mark notification as read      |
| `/api/mark_all_as_read`   | POST   | Mark all notifications as read |

### Billing API

| Endpoint                            | Method | Description                      |
|-------------------------------------|--------|----------------------------------|
| `/api/get_all_billing`              | GET    | Get all billing records          |
| `/api/get_billing_totals_prev_curr` | GET    | Get revenue comparison           |
| `/api/download_invoice_pdf`         | GET    | Download invoice PDF             |

### Stripe API

| Endpoint                       | Method | Description                      |
|--------------------------------|--------|----------------------------------|
| `/api/register_and_checkout`   | POST   | Register user and start checkout |
| `/api/stripe_webhook`          | POST   | Stripe webhook handler           |
| `/api/top_up`                  | POST   | Purchase additional hours        |
| `/api/sync_payment_status`     | POST   | Sync payment status              |
| `/api/billing/:session_id`     | GET    | Get billing by session ID        |

### Admin API

| Endpoint                          | Method | Description                  |
|-----------------------------------|--------|------------------------------|
| `/load_module`                    | POST   | Load admin dashboard module  |
| `/api/get_all_customers`          | GET    | Get all customers            |
| `/api/get_active_plan_counts`     | GET    | Get plan distribution        |
| `/api/get_ticket_counts_by_status`| GET    | Get ticket statistics        |

---

## Deployment

### GitHub Actions CI/CD

The project uses GitHub Actions for automated deployment. On push to `main`:

1. Checkout code
2. Verify Docker availability
3. Run `deploy.sh` script

### Manual Deployment

```bash
# SSH into server
ssh user@your-server

# Navigate to project
cd /path/to/leverai-consulting

# Pull latest changes
git pull origin main

# Rebuild and restart containers
docker-compose up -d --build
```

### Stripe Webhook Setup

Configure your Stripe webhook endpoint:
- URL: `https://yourdomain.com/api/stripe_webhook`
- Events to listen for:
  - `checkout.session.completed`
  - `checkout.session.expired`
  - `customer.created`
  - `customer.updated`
  - `customer.subscription.created`
  - `customer.subscription.updated`
  - `customer.subscription.deleted`
  - `invoice.created`
  - `invoice.finalized`
  - `invoice.paid`
  - `invoice.payment_succeeded`
  - `invoice.payment_failed`
  - `payment_intent.created`
  - `payment_intent.succeeded`
  - `charge.succeeded`
  - `charge.updated`

---

## Development

### Frontend Development

```bash
# Watch SCSS and Tailwind for changes
npm run dev

# Build for production
npm run build

# Build specific assets
npm run build:scss
npm run build:tailwind
npm run build:js
```

### JavaScript Files

- `ng-*.js` - Angular-style module components
- `app.js` - Main application initialization
- `custom-script.js` - Custom utility functions

### SCSS Structure

```
scss/
├── base/           # Variables, mixins, general styles
├── components/     # Reusable component styles
├── pages/          # Page-specific styles
├── main.scss       # Main entry point
└── tailwind.scss   # Tailwind CSS entry
```

### Testing

```bash
# Run PHP unit tests
composer test:coverage
```

---

## Support

For support or questions, please contact:
- Website: [leverai.consulting](https://leverai.consulting)
- Email: support@leverai.dev

---

## License

This project is licensed under the MIT License - see the [license.txt](license.txt) file for details.
