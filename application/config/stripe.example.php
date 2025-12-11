<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Stripe Configuration
|--------------------------------------------------------------------------
|
| Copy this file to stripe.php and fill in your Stripe credentials
| Get your keys from: https://dashboard.stripe.com/apikeys
|
*/

// Your Stripe API keys
$config['stripe_secret_key'] = 'sk_test_YOUR_SECRET_KEY';
$config['stripe_publishable_key'] = 'pk_test_YOUR_PUBLISHABLE_KEY';
$config['stripe_webhook_secret'] = 'whsec_YOUR_WEBHOOK_SECRET';

// Price IDs from your Stripe Dashboard
$config['stripe_prices'] = array(
  'basic' => 'price_YOUR_BASIC_PRICE_ID',
  'standard' => 'price_YOUR_STANDARD_PRICE_ID',
  'pro' => 'price_YOUR_PRO_PRICE_ID',
  'topup' => 'price_YOUR_TOPUP_PRICE_ID',
  'daily' => 'price_YOUR_DAILY_PRICE_ID'
);

