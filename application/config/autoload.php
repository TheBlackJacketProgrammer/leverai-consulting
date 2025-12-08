<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$autoload['packages'] = array(APPPATH.'third_party/stripe/');

// $autoload['libraries'] = array('database');

// $autoload['libraries'] = array('email','session','parser','upload','dompdf_lib');

$autoload['libraries'] = array('database','email','emailer','session','parser','upload','dompdf_lib');

$autoload['drivers'] = array();

$autoload['helper'] = array('html','date','url','file','form','download','file_upload','dompdf',);

$autoload['config'] = array('stripe','email');

$autoload['language'] = array();

$autoload['model'] = array('Model_Api', 'Model_Main');

// $autoload['model'] = array('Model_Main', 'Model_Api');