<?php
    defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Lever A.I. Consulting - Login</title>

       <!-- Favicon -->
        <link rel="apple-touch-icon" href="<?php echo base_url('assets/img/logo-mini.png'); ?>" type="image/x-icon" />
        <link rel="shortcut icon" href="<?php echo base_url('assets/img/logo-mini.png'); ?>" type="image/x-icon" />
        
        <!-- Add your CSS files here -->
        <link rel="stylesheet" href="<?php echo base_url('assets/css/main.css'); ?>">
        <link rel="stylesheet" href="<?php echo base_url('assets/css/tailwind.css'); ?>">

        <!-- Toastr CSS -->
        <link rel="stylesheet" href="<?php echo base_url('assets/devtools/toastr/toastr.min.css'); ?>">


        <!-- Datatable CSS -->
        <link rel="stylesheet" href="<?php echo base_url('assets/devtools/Datatables/jquery.dataTables.min.css'); ?>">
        <link rel="stylesheet" href="<?php echo base_url('assets/devtools/Datatables/buttons.dataTables.min.css'); ?>">

    </head>
    <body class="bg-extra" ng-app="leverai-dev" ng-controller="ng-variables">
       
        <!-- Header -->
        <?php $this->load->view('components/header', array('status' => 'login')); ?>
        
        <main class="login" ng-controller="ng-login">
            <section class="full-bleed" >
                <div class="flex flex-col items-center justify-center">
                    <div class="login-container">
                        <h2>Login</h2>
                        <div class="grid grid-cols-1 lg:grid-cols-1 gap-4 mt-4">
                            <div class="login-form">
                                <!-- Email -->
                                <div class="input-wrapper mb-2">
                                    <svg class="input-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#clip0_17_856)">
                                            <path d="M22 6C22 4.9 21.1 4 20 4H4C2.9 4 2 4.9 2 6V18C2 19.1 2.9 20 4 20H20C21.1 20 22 19.1 22 18V6ZM20 6L12 11L4 6H20ZM20 18H4V8L12 13L20 8V18Z" fill="white"/>
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_17_856">
                                                <rect width="24" height="24" fill="white"/>
                                            </clipPath>
                                        </defs>
                                    </svg>
                                    <input type="text" placeholder="Email*" ng-model="credentials.email" ng-keypress="$event.keyCode === 13 && login()" required>
                                </div>

                                <!-- Password -->
                                <div class="input-wrapper mb-2">
                                    <svg class="input-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#clip0_6_16196)">
                                            <path d="M18 8H17V6C17 3.24 14.76 1 12 1C9.24 1 7 3.24 7 6V8H6C4.9 8 4 8.9 4 10V20C4 21.1 4.9 22 6 22H18C19.1 22 20 21.1 20 20V10C20 8.9 19.1 8 18 8ZM9 6C9 4.34 10.34 3 12 3C13.66 3 15 4.34 15 6V8H9V6ZM18 20H6V10H18V20ZM12 17C13.1 17 14 16.1 14 15C14 13.9 13.1 13 12 13C10.9 13 10 13.9 10 15C10 16.1 10.9 17 12 17Z" fill="white"/>
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_6_16196">
                                                <rect width="24" height="24" fill="white"/>
                                            </clipPath>
                                        </defs>
                                    </svg>
                                    <input type="password" placeholder="Password*" ng-model="credentials.password" ng-keypress="$event.keyCode === 13 && login()" required>
                                </div>

                                <div class="submit-area">
                                    <input type="button" value="Login" class="btn-primary w-full max-w-80" ng-click="login()">
                                    <!-- <div class="submit-divider">
                                        <div class="divider"></div>
                                        <span>OR</span>
                                        <div class="divider"></div>
                                    </div> -->
                                </div>
                                <!-- <span class="text-white">Don't have an account? <a href="<?php echo base_url(); ?>subscribe" class="ml-2 primary">Subscribe Now</a></span> -->
                                <span class="text-white">Forgot Password? <a href="<?php echo base_url('forgot-password'); ?>" class="ml-2 primary">Reset Password</a></span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Modal -->
        <?php $this->load->view('components/modals/modal_developers_login'); ?>

        <!-- jQuery -->
        <script src="<?php echo base_url('assets/devtools/jquery/jquery-3.7.1.min.js'); ?>"></script>

        <!-- Toastr JS -->
        <script src="<?php echo base_url('assets/devtools/toastr/toastr.min.js'); ?>"></script>

        <!-- AngularJS -->
        <script src="<?php echo base_url('assets/devtools/angularjs/angular.min.js'); ?>"></script>

        <!-- Angular JS Scripts Bundle -->
        <script src="<?php echo base_url('assets/dist/bundle.min.js'); ?>"></script>

    </body>
</html> 
