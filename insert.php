<?php

/**
 * The most important one. Checks whether the user is validated for each page load. 
 * If validated, returns true otherwise, redirect user to either auth, backup or Email verification page depending on user input.
 * Called on every page load. Attached to auth_redirect 
 */
function is_user_2stepauth_validated() {

	global $current_user;
	$current_user = wp_get_current_user();
	$uid = $current_user->ID;  
	
	// Check if 2StepAuth verification is done. If yes, return true or redirect visitor to one of 2StepAuth pages.
	if ( get_option( '2stepauth_verificationdone_'.$uid ) == 1 ) {

		return true;

	}
	else {
		//return false;
		$phone = get_option( '2stepauth_gotphone_'.$uid );
		
		if ( $phone == 1 ) {
		
			$auth_url = plugin_dir_url(__FILE__) . 'auth.php';
			wp_redirect( $auth_url );
			
		}
		
		else {
		
			$auth_url = plugin_dir_url(__FILE__) . 'backupcodes.php';
			wp_redirect( $auth_url );
		
		}
	}


}


/**
 * Called on each Logout. 
 * If 2StepAuth is activated, it un-sets Verificationdone option
 * Helps in redirecting user to 2StepAuth page on login
 */
function stepauth_logoutfunction() {

	global $current_user;
	$current_user = wp_get_current_user();
	$uid = $current_user->ID;  

	if ( get_option( '2stepauth_validated_'.$uid ) == 1 ) {
		update_option( '2stepauth_verificationdone_'.$uid, 0 );
	}

}

?>