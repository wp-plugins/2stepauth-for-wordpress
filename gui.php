<?php
/*
Plugin Name: 2StepAuth
Description: 2StepAuth <strong>increases security</strong> of Wordpress blogs by adding <strong>2nd level of Authentication.</strong> After entering correct login credentials, the user has to validate himself using one of 3 ways: SMS Verification, Backup Codes or Email Verification to gain access to his/her blog.
Version: 1.0
Author: Rajesh Chaukwale
Author URI: http://www.techozens.com
License: GPLv2 or later

*/

require_once plugin_dir_path(__FILE__) . 'insert.php';
require_once plugin_dir_path(__FILE__) . 'init.php';

add_action( 'admin_menu' , 'send12' );

function send12() {
add_submenu_page( 'options-general.php','2StepAuth','2StepAuth','manage_options','2stepauth',stepauth_gui );

}

function stepauth_gui() {

?>
<br />
<div class="wrap">
<div id="navigation">
<a class="button-primary" href="?page=2stepauth">Help</a>
<a class="button-primary" href="?page=2stepauth&step=gui0">Step 1</a>
<a class="button-primary" href="?page=2stepauth&step=gui1">Step 2</a>
<a class="button-primary" href="?page=2stepauth&step=gui2">Step 3</a>

</div>
<?php

switch ( $_GET['step'] ) {
				
        case '':
                require_once 'help.php';
                break;
	case 'gui0':
                require_once 'gui0.php';
                break;
	case 'gui1':
                require_once 'gui1.php';
                break;
	case 'gui2':
                require_once 'gui2.php';
                break;
		
	
}
}

// Activation and Deactivation
register_activation_hook(__FILE__, 'stepauth_init');
register_deactivation_hook(__FILE__, 'stepauth_uninstall');

//actions
add_action( 'admin_notices', 'stepauth_notify' );
add_action( 'wp_logout', 'stepauth_logoutfunction' );
add_action( 'wp_login', 'stepauth_logoutfunction' );   
add_action( 'auth_redirect', 'is_user_2stepauth_validated' );


?>