<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/userguide3/general/hooks.html
|
*/

$hook['post_controller_constructor'] = array(
    'class'    => 'Api_json_hook',
    'function' => 'set_json_content_type',
    'filename' => 'Api_json_hook.php',
    'filepath' => 'hooks'
);
