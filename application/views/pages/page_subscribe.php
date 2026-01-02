<?php
    defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Lever A.I Development - Subscribe</title>

        <!-- Favicon -->
        <link rel="apple-touch-icon" href="<?php echo base_url('assets/img/logo-mini.png'); ?>" type="image/x-icon" />
        <link rel="shortcut icon" href="<?php echo base_url('assets/img/logo-mini.png'); ?>" type="image/x-icon" /> 
        
        <!-- Add your CSS files here -->
        <link rel="stylesheet" href="<?php echo base_url('assets/css/main.css'); ?>">
        <link rel="stylesheet" href="<?php echo base_url('assets/css/tailwind.css'); ?>">

        <!-- Toastr CSS -->
        <link rel="stylesheet" href="<?php echo base_url('assets/devtools/toastr/toastr.min.css'); ?>">



    </head>
    <body class="bg-extra" ng-app="leverai-dev" ng-controller="ng-variables">
        <!-- Header -->
        <?php $this->load->view('components/header', array('status' => 'subscribe')); ?>
        
        <!-- Main Content Area -->
        <main class="subscribe">
            <section class="full-bleed" ng-controller="ng-subscribe">
                <div class="flex flex-col items-center justify-center">
                    <div class="subscribe-form-container">
                        <h1>Subscribe</h1>
                        <p>Please fill up the form and select your subscription plan</p>
                        <div class="grid-container mt-4">
                            <div class="subscribe-form">
                                <!-- Fullname -->
                                <div class="input-wrapper mb-2">
                                    <svg class="input-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#clip0_6_15991)">
                                            <path d="M9 12C10.93 12 12.5 10.43 12.5 8.5C12.5 6.57 10.93 5 9 5C7.07 5 5.5 6.57 5.5 8.5C5.5 10.43 7.07 12 9 12ZM9 7C9.83 7 10.5 7.67 10.5 8.5C10.5 9.33 9.83 10 9 10C8.17 10 7.5 9.33 7.5 8.5C7.5 7.67 8.17 7 9 7ZM9.05 17H4.77C5.76 16.5 7.47 16 9 16C9.11 16 9.23 16.01 9.34 16.01C9.68 15.28 10.27 14.68 10.98 14.2C10.25 14.07 9.56 14 9 14C6.66 14 2 15.17 2 17.5V19H9V17.5C9 17.33 9.02 17.16 9.05 17ZM16.5 14.5C14.66 14.5 11 15.51 11 17.5V19H22V17.5C22 15.51 18.34 14.5 16.5 14.5ZM17.71 12.68C18.47 12.25 19 11.44 19 10.5C19 9.12 17.88 8 16.5 8C15.12 8 14 9.12 14 10.5C14 11.44 14.53 12.25 15.29 12.68C15.65 12.88 16.06 13 16.5 13C16.94 13 17.35 12.88 17.71 12.68Z" fill="white"/>
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_6_15991">
                                                <rect width="24" height="24" fill="white"/>
                                            </clipPath>
                                        </defs>
                                    </svg>
                                    <input type="text" placeholder="Fullname*" ng-model="credentials.fullname" required>
                                </div>
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

                                    <input type="text" placeholder="Email*" ng-model="credentials.email" required>
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

                                    <input type="password" placeholder="Password*" ng-model="credentials.password" required>
                                </div>
                                <!-- Confirm Password -->
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
                                    <input type="password" placeholder="Confirm Password*" ng-model="credentials.confirmPassword" required>
                                </div>
                                <!-- Secret Question -->
                                <div class="input-wrapper mb-2">
                                    <svg class="input-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM13 19H11V17H13V19ZM15.07 11.25L14.17 12.17C13.45 12.9 13 13.5 13 15H11V14.5C11 13.4 11.45 12.4 12.17 11.67L13.41 10.41C13.78 10.05 14 9.55 14 9C14 7.9 13.1 7 12 7C10.9 7 10 7.9 10 9H8C8 6.79 9.79 5 12 5C14.21 5 16 6.79 16 9C16 9.88 15.64 10.68 15.07 11.25Z" fill="white"/>
                                    </svg>
                                    <select ng-model="credentials.secret_question" required>
                                        <option value="" disabled selected>Select Secret Question*</option>
                                        <option value="pet">What is the name of your first pet?</option>
                                        <option value="school">What was the name of your first school?</option>
                                        <option value="city">In what city were you born?</option>
                                        <option value="mother">What is your mother's maiden name?</option>
                                        <option value="friend">What is the name of your childhood best friend?</option>
                                        <option value="car">What was the make of your first car?</option>
                                    </select>
                                </div>
                                <!-- Secret Answer -->
                                <div class="input-wrapper mb-2">
                                    <svg class="input-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M18 8H17V6C17 3.24 14.76 1 12 1C9.24 1 7 3.24 7 6V8H6C4.9 8 4 8.9 4 10V20C4 21.1 4.9 22 6 22H18C19.1 22 20 21.1 20 20V10C20 8.9 19.1 8 18 8ZM9 6C9 4.34 10.34 3 12 3C13.66 3 15 4.34 15 6V8H9V6ZM18 20H6V10H18V20ZM12 17C13.1 17 14 16.1 14 15C14 13.9 13.1 13 12 13C10.9 13 10 13.9 10 15C10 16.1 10.9 17 12 17Z" fill="white"/>
                                    </svg>
                                    <input type="text" placeholder="Secret Answer*" ng-model="credentials.secret_answer" required>
                                </div>
                            </div>
                            <div class="subscribe-plan">
                                <div class="card">
                                    <p class="card-tag">Choose your subscription plan</p>
                                    <div class="mt-4">
                                        <label class="flex items-center my-2">
                                            <input type="radio" name="plan" value="basic" ng-model="credentials.subscriptionPlan" class="primary">
                                            <span class="text-white">1 hour per month - $50.00</span>
                                        </label>

                                        <label class="flex items-center my-2">
                                            <input type="radio" name="plan" value="standard" ng-model="credentials.subscriptionPlan" class="primary">
                                            <span class="text-white">10 hours per month - $450.00</span>
                                        </label>

                                        <label class="flex items-center my-2">
                                            <input type="radio" name="plan" value="pro" ng-model="credentials.subscriptionPlan" class="primary">
                                            <span class="text-white">100 hours per month - $4000.00</span>
                                        </label>

                                        <!-- Daily - TESTING ONLY -->
                                        <label class="flex items-center my-2">
                                            <input type="radio" name="plan" value="daily" ng-model="credentials.subscriptionPlan" class="primary">
                                            <span class="text-white">Daily - $50.00</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="submit-area">
                            <input type="button" value="Proceed for Payment" class="btn-primary mt-4" ng-click="proceedForPayment()">
                            <div class="submit-divider">
                                <div class="divider"></div>
                                <span>OR</span>
                                <div class="divider"></div>
                            </div>
                            <span class="text-white">Have already an account <a href="<?php echo base_url('login'); ?>" class="ml-2 primary">Login instead</a></span>
                        </div>
                    </div>
                </div>
            </section>
        </main>
        

        
        <!-- jQuery -->
        <script src="<?php echo base_url('assets/devtools/jquery/jquery-3.7.1.min.js'); ?>"></script>

        <!-- Custom Script -->
        <script src="<?php echo base_url('assets/js/custom-script.js'); ?>"></script>

        <!-- Toastr JS -->
        <script src="<?php echo base_url('assets/devtools/toastr/toastr.min.js'); ?>"></script>

        <!-- AngularJS -->
        <script src="<?php echo base_url('assets/devtools/angularjs/angular.min.js'); ?>"></script>

        <!-- Angular JS Scripts Bundle -->
        <script src="<?php echo base_url('assets/dist/bundle.min.js'); ?>"></script>

    </body>
</html> 