<?php
/*
Init.php Contains functions for initialization, uninstall actions and notifications regarding Backup Codes.
*/

require_once plugin_dir_path(__FILE__) . 'insert.php';


/**
 * Initializes the plugin content. 
 * Creates 3 necessary DB tables and adds new options. 
 * Called only once while activating the plugin.
 */
function stepauth_init() {
	
	global $current_user;
	$current_user = wp_get_current_user();
	$uid = $current_user->ID;
	
	// tells whether user is verified 
	update_option( '2stepauth_verificationdone_'.$uid, 1 );
	
	// Reserved
	update_option( '2stepauth_inserted', 0 );
	
	// whether mobile number is validated + Later added confirmation for Backup Codes too.
	update_option( '2stepauth_validated_'.$uid, 0 );
	
	// Reserved
	update_option( '2stepauth_editor', 0 );
	update_option( '2stepauth_author', 0 );
	update_option( '2stepauth_contributor', 0 );
	update_option( '2stepauth_subscriber', 0 );
	
	
	//create table wp_cookies
	global $wpdb;

	$table = $wpdb->prefix."cookies";

	$sql = "CREATE TABLE $table (
	    id int NOT NULL AUTO_INCREMENT,
	    userid int,
	    date Date,
	    cookie text,
	    PRIMARY KEY (id)           
	);";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	if (function_exists('dbDelta')) {
			dbDelta($sql);
			
	}
	
	
	//create table 2stepauth_users
	$table = $wpdb->prefix."2stepauth_users";

	$sql = "CREATE TABLE $table (
	    id int NOT NULL AUTO_INCREMENT,
	    userid int,
	    PRIMARY KEY (id)           
	);";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	if (function_exists('dbDelta')) {
			dbDelta($sql);
			
	}
		
	
	//create table wp_backup
	
	global $wpdb;

	$table = $wpdb->prefix."backup";

	$sql = "CREATE TABLE $table (
	    id int NOT NULL AUTO_INCREMENT,
	    backup text,
	    userid int,
	    PRIMARY KEY (id)           
	);";

	//require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	if (function_exists('dbDelta')) {
		dbDelta($sql);
		
	}
				

}


/**
 * Erases all the changes made by the plugin.
 * Called while uninstalling the plugin. 
 */
function stepauth_uninstall(){

	global $current_user;
	$current_user = wp_get_current_user();
	$uid = $current_user->ID;
	
	$users = count_users();

	global $wpdb;
	$table = $wpdb->prefix."2stepauth_users";
	$return = $wpdb->get_results('Select DISTINCT userid from '.$table);
	foreach ( $return as $r ) {
	$admin[] = $r->userid;
	
	}
	
	// delete options
	if ( $admin[0] ) {
		foreach ( $admin as $uid ) {
		
			update_option( '2stepauth_verificationdone_'.$uid, 1 );
			update_option( '2stepauth_validated_'.$uid, 0 );
			delete_option( '2stepauth_email_'.$uid );
			delete_option( '2stepauth_phone_'.$uid );
			delete_option( '2stepauth_country_'.$uid );
			delete_option( '2stepauth_generate_'.$uid );
			delete_option( '2stepauth_user_'.$uid );
			delete_option( '2stepauth_pass_'.$uid );
			delete_option( '2stepauth_gateway_'.$uid );
			delete_option( '2stepauth_gotphone_'.$uid );
			delete_option( '2stepauth_emailverify_'.$uid );
			delete_option( '2stepauth_completed_'.$uid );
		
		}
	}
	
	// remove actions
	remove_action( 'wp_logout', 'logoutfunction' );
	remove_action( 'auth_redirect', 'is_user_2stepauth_validated' );

	global $wpdb;
	
	$table1 = $wpdb->prefix . "cookies";
	$table2 = $wpdb->prefix . "backup";
	$table3 = $wpdb->prefix . "2stepauth_users";
	
	// remove tables
	$wpdb->query('Drop table '.$table1);
	$wpdb->query('Drop table '.$table2);
	$wpdb->query('Drop table '.$table3);

}


/**
 * This function is used to display notifications to Administrator related to 2StepAuth
 * 2 notifications are shown: 1) Regarding Validating Mobile Number and 2) Regarding Low Backup Count
 */
function stepauth_notify() {
	
	global $current_user;
	$current_user = wp_get_current_user();
	$uid = $current_user->ID;

	$valid = get_option( '2stepauth_validated_'.$uid );
	
	//notify admin about mobile number validation
	if ( $valid == 0 ) {
		if ( current_user_can( 'administrator' ) ) {
			$url = admin_url('options-general.php?page=2stepauth');
			echo "<div class='updated fade' style='background-color:#FFCCCC;'><p><strong>". __( "Your mobile number is not validated. For proper working of 2step Auth, please validate it <a href=".$url.">here.</a>", '2step_auth' ) . "</strong></p></div>";
		}
	}

	//notify admin about low backup codes
	if ( get_option( '2stepauth_lowbackup_'.$uid ) == 1 ) {
		if ( current_user_can( 'administrator' ) ){
			$url1 = admin_url('options-general.php?page=2stepauth&step=gui2');
			echo "<div class='updated fade' style='background-color:#FFCCCC;'><p><strong>". __( "You are low on Backup Codes. Please generate new Backup Codes from <a href=".$url1.">here.</a>", '2step_auth' ) . "</strong></p></div>";
		}
	}

}

?>