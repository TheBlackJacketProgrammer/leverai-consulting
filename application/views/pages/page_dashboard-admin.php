<?php
    defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>LEVER AI Consulting</title>

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
    <body class="bg-dashboard" ng-app="leverai-dev" ng-controller="ng-dashboard-admin">
        <!-- Header -->
        <?php // $this->load->view('components/header', array('status' => 'dashboard')); ?>
        <?php $this->load->view('components/headers/header-dashboard'); ?>

        <!-- Main Menu Bar -->
        <?php $this->load->view('components/headers/main-menu-bar'); ?>
        
        <!-- Main Content Area -->
        <main class="dashboard-admin">
            <div class="admin-module-container" compile="section"></div>  
        </main>

        <!-- Footer -->
        <?php $this->load->view('components/footer'); ?>

        <!-- Modals -->
        <?php $this->load->view('components/modals/modal_view_request'); ?>
        <?php $this->load->view('components/modals/modal_user_profile'); ?>
        
        <!-- Add your JavaScript files here -->

        <!-- jQuery -->
        <script src="<?php echo base_url('assets/devtools/jquery/jquery-3.7.1.min.js'); ?>"></script>

        <!-- Custom Script -->
        <script src="<?php echo base_url('assets/js/custom-script.js'); ?>"></script>

        <!-- Toastr JS -->
        <script src="<?php echo base_url('assets/devtools/toastr/toastr.min.js'); ?>"></script>

        <!-- Datatable JS -->
        <script src="<?php echo base_url('assets/devtools/Datatables/jquery.dataTables.min.js'); ?>"></script>
        <script src="<?php echo base_url('assets/devtools/Datatables/dataTables.buttons.min.js'); ?>"></script>

        <!-- Excel Export -->
        <script src="<?php echo base_url('assets/devtools/Datatables/jszip.min.js'); ?>"></script>
        <script src="<?php echo base_url('assets/devtools/Datatables/buttons.html5.min.js'); ?>"></script>

        <!-- PDF Export -->
        <script src="<?php echo base_url('assets/devtools/Datatables/pdfmake.min.js'); ?>"></script>
        <script src="<?php echo base_url('assets/devtools/Datatables/vfs_fonts.js'); ?>"></script>

        <!-- Chart JS -->
        <script src="<?php echo base_url('assets/devtools/Chartjs/chart.js'); ?>"></script>

        <!-- AngularJS -->
        <script src="<?php echo base_url('assets/devtools/angularjs/angular.min.js'); ?>"></script>

        <!-- Angular JS Datatable -->
        <script src="<?php echo base_url('assets/devtools/angularjs/angular-datatables.min.js'); ?>"></script>

        <!-- Angular JS Scripts Bundle -->
        <script src="<?php echo base_url('assets/dist/bundle.min.js'); ?>"></script>

    </body>
</html> 