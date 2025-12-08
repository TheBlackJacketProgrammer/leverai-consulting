Lever A.I Development Web Application - CodeIgniter 3 Template with Stripe Integration
===============================================

A modern, production-ready CodeIgniter 3 template featuring Stripe payment integration, modern frontend tooling, and Docker support.

.. image:: https://img.shields.io/badge/CodeIgniter-3.x-orange.svg
    :target: https://codeigniter.com/
    :alt: CodeIgniter Version

.. image:: https://img.shields.io/badge/PHP-8.1+-blue.svg
    :target: https://php.net/
    :alt: PHP Version

.. image:: https://img.shields.io/badge/License-MIT-green.svg
    :target: https://opensource.org/licenses/MIT
    :alt: License

Features
--------

🚀 **Modern Development Stack**
   - CodeIgniter 3.x framework
   - PHP 8.1+ support
   - SCSS/SASS compilation with Gulp
   - Tailwind CSS integration
   - Modern JavaScript tooling

💳 **Stripe Payment Integration**
   - Complete subscription management
   - Webhook handling for payment events
   - Support for multiple pricing tiers
   - Production-ready configuration

🐳 **Docker Support**
   - Multi-stage Dockerfile
   - Docker Compose configuration
   - Production-ready container setup

📱 **Responsive Design**
   - Mobile-first approach
   - Modern UI components
   - Cross-browser compatibility

🔧 **Developer Tools**
   - Gulp build system
   - SCSS compilation
   - JavaScript bundling
   - Live reload support

Quick Start
-----------

Prerequisites
~~~~~~~~~~~~~

- PHP 8.1 or higher
- Composer
- Node.js and npm
- MySQL/PostgreSQL database
- Stripe account (for payment features)

Installation
~~~~~~~~~~~~

1. **Clone the repository**
   ::

      git clone https://github.com/yourusername/ci3_template.git
      cd ci3_template

2. **Install PHP dependencies**
   ::

      composer install

3. **Install Node.js dependencies**
   ::

      npm install

4. **Configure environment**
   
   Copy and configure the database settings:
   ::

      cp application/config/database.php.example application/config/database.php

   Update the database configuration with your credentials.

5. **Configure Stripe (Optional)**
   
   For payment features, update the Stripe configuration:
   ::

      # Edit application/config/stripe.php
      # Add your Stripe API keys and price IDs

6. **Build assets**
   ::

      npm run build

7. **Set up web server**
   
   Point your web server document root to the project directory.

Docker Setup
~~~~~~~~~~~~

For containerized deployment:

1. **Build the Docker image**
   ::

      docker build -t ci3_template .

2. **Run with Docker Compose**
   ::

      docker-compose up -d

3. **Access the application**
   
   Visit ``http://localhost:8080`` (or your configured port)

Development
-----------

Asset Compilation
~~~~~~~~~~~~~~~~~

The project uses modern frontend tooling for asset management:

**SCSS Compilation**
::

   # Watch for changes
   npm run watch:scss
   
   # Build once
   npm run build:scss

**Tailwind CSS**
::

   # Watch for changes
   npm run watch:tailwind
   
   # Build once
   npm run build:tailwind

**Development Mode**
::

   # Run both watchers simultaneously
   npm run dev

**Production Build**
::

   # Build all assets for production
   npm run build

Project Structure
~~~~~~~~~~~~~~~~~

::

   ci3_template/
   ├── application/
   │   ├── config/          # Configuration files
   │   ├── controllers/     # Application controllers
   │   ├── models/          # Data models
   │   ├── views/           # View templates
   │   ├── libraries/       # Custom libraries
   │   └── helpers/         # Helper functions
   ├── assets/
   │   ├── css/             # Compiled CSS
   │   ├── js/              # JavaScript files
   │   ├── scss/            # SCSS source files
   │   └── img/             # Images and assets
   ├── system/              # CodeIgniter core
   ├── vendor/              # Composer dependencies
   ├── node_modules/         # Node.js dependencies
   ├── Dockerfile           # Docker configuration
   ├── docker-compose.yml   # Docker Compose setup
   └── package.json         # Node.js dependencies

Configuration
-------------

Database Setup
~~~~~~~~~~~~~~~

Configure your database connection in ``application/config/database.php``:

.. code-block:: php

   $db['default'] = array(
       'dsn'      => '',
       'hostname' => 'localhost',
       'username' => 'your_username',
       'password' => 'your_password',
       'database' => 'your_database',
       'dbdriver' => 'mysqli', // or 'pdo', 'postgre', etc.
       // ... other settings
   );

Stripe Configuration
~~~~~~~~~~~~~~~~~~~~

For payment integration, configure Stripe in ``application/config/stripe.php``:

**Development Mode**
::

   # Uses test keys and test price IDs
   # Safe for development and testing

**Production Mode**
::

   # Requires live keys and live price IDs
   # See STRIPE_PRODUCTION_CHECKLIST.md for detailed setup

Environment Configuration
~~~~~~~~~~~~~~~~~~~~~~~~~

Set the environment in ``index.php``:

.. code-block:: php

   // For development
   define('ENVIRONMENT', 'development');
   
   // For production
   define('ENVIRONMENT', 'production');

Features Overview
-----------------

Payment Integration
~~~~~~~~~~~~~~~~~~~

The template includes a complete Stripe integration:

- **Subscription Management**: Handle recurring payments
- **Multiple Pricing Tiers**: Basic ($50), Standard ($400), Pro ($3000)
- **Webhook Handling**: Process payment events securely
- **Success/Cancel Pages**: Handle payment outcomes

**API Endpoints**
::

   POST /api/stripe_checkout     # Create checkout session
   POST /api/stripe_webhook      # Handle webhook events
   GET  /payment/success         # Payment success page
   GET  /payment/cancel          # Payment cancel page

Frontend Features
~~~~~~~~~~~~~~~~~

- **Responsive Design**: Mobile-first approach
- **Modern CSS**: SCSS with Tailwind CSS
- **Interactive Components**: JavaScript-powered UI
- **Asset Optimization**: Minified and compressed assets

Security Features
~~~~~~~~~~~~~~~~~

- **CSRF Protection**: Built-in CSRF token validation
- **XSS Filtering**: Input sanitization
- **Secure Sessions**: Configurable session handling
- **Environment-based Configuration**: Separate dev/prod settings

Deployment
----------

Production Checklist
~~~~~~~~~~~~~~~~~~~~

Before deploying to production:

1. **Update Configuration**
   - Set ``ENVIRONMENT`` to ``'production'``
   - Configure production database
   - Update Stripe keys to live keys
   - Set proper error reporting levels

2. **Security Settings**
   - Enable CSRF protection
   - Set secure session configuration
   - Configure proper file permissions

3. **Performance Optimization**
   - Enable output compression
   - Configure caching
   - Optimize database queries

4. **Stripe Production Setup**
   - See ``STRIPE_PRODUCTION_CHECKLIST.md`` for detailed instructions

Docker Deployment
~~~~~~~~~~~~~~~~~

For containerized deployment:

1. **Build production image**
   ::

      docker build -t ci3_template:production .

2. **Deploy with Docker Compose**
   ::

      docker-compose -f docker-compose.prod.yml up -d

3. **Configure reverse proxy** (nginx/Apache)
4. **Set up SSL certificates**
5. **Configure domain and DNS**

Troubleshooting
---------------

Common Issues
~~~~~~~~~~~~~

**Asset Compilation Errors**
::

   # Clear node_modules and reinstall
   rm -rf node_modules package-lock.json
   npm install

**Database Connection Issues**
::

   # Check database credentials
   # Verify database server is running
   # Check firewall settings

**Stripe Integration Problems**
::

   # Verify API keys are correct
   # Check webhook endpoint accessibility
   # Review webhook logs in Stripe Dashboard

**Docker Issues**
::

   # Rebuild image
   docker build --no-cache -t ci3_template .
   
   # Check container logs
   docker logs <container_name>

Support
-------

Documentation
~~~~~~~~~~~~~

- `CodeIgniter 3 User Guide <https://codeigniter.com/userguide3/>`_
- `Stripe Documentation <https://stripe.com/docs>`_
- `Docker Documentation <https://docs.docker.com/>`_

Issues and Contributions
~~~~~~~~~~~~~~~~~~~~~~~~

- Report issues on the `GitHub Issues <https://github.com/yourusername/ci3_template/issues>`_ page
- Submit pull requests for bug fixes and new features
- Follow the existing code style and conventions

License
-------

This project is licensed under the MIT License - see the `LICENSE <LICENSE>`_ file for details.

Credits
-------

- `CodeIgniter <https://codeigniter.com/>`_ - PHP Framework
- `Stripe <https://stripe.com/>`_ - Payment Processing
- `Tailwind CSS <https://tailwindcss.com/>`_ - CSS Framework
- `Docker <https://docker.com/>`_ - Containerization Platform

Changelog
---------

Version 1.0.0
~~~~~~~~~~~~~

- Initial release
- CodeIgniter 3.x integration
- Stripe payment system
- Modern frontend tooling
- Docker support
- Responsive design
- Production-ready configuration
