<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Payment Cancelled | Lever A.I. Development</title>

  <!-- Favicon -->
  <link rel="apple-touch-icon" href="<?php echo base_url('assets/img/logo-mini.png'); ?>" type="image/x-icon" />
  <link rel="shortcut icon" href="<?php echo base_url('assets/img/logo-mini.png'); ?>" type="image/x-icon" /> 

  <link rel="stylesheet" href="<?php echo base_url('assets/css/main.css'); ?>">
  <link rel="stylesheet" href="<?php echo base_url('assets/css/tailwind.css'); ?>">
</head>
<body class="payment-status">

  <div class="payment-status-container">
    <div class="text-red-500 text-6xl mb-4">✖</div>
    <h1 class="text-white ">Payment Cancelled!</h1>
    <div class="text-center">
      <p class="text-white ">Your payment was not completed. You can retry or choose a different subscription plan.</p>
    </div>

    <a href="<?php echo base_url(); ?>subscribe" class="btn-primary">
      Try Again
    </a>
  </div>

</body>
</html>
