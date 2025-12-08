<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Payment Successful | Lever A.I. Development</title>

  <!-- Favicon -->
  <link rel="apple-touch-icon" href="<?php echo base_url('assets/img/logo-mini.png'); ?>" type="image/x-icon" />
  <link rel="shortcut icon" href="<?php echo base_url('assets/img/logo-mini.png'); ?>" type="image/x-icon" /> 

  <link rel="stylesheet" href="<?php echo base_url('assets/css/main.css'); ?>">
  <link rel="stylesheet" href="<?php echo base_url('assets/css/tailwind.css'); ?>">

</head>
<body class="payment-status">
  <div class="payment-status-container">
    <div class="text-green-400 text-6xl my-3">✔</div>
    <h1 class="text-white ">Payment Successful!</h1>
    <div class="text-center">
      <p class="text-white ">Thank you for subscribing to Lever A.I. Development</p>
      <p class="text-white ">Your payment has been received successfully.</p>  
    </div>
    <div class="mb-4">
      <p class="text-white ">You can now start using your allocated hours.</p>
      <p class="text-white ">A confirmation email has been sent to your inbox.</p>
    </div>

    <a href="<?php echo base_url(); ?>" class="btn-primary">
      Go to Homepage
    </a>
  </div>

</body>
</html>
