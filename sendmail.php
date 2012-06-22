<?php
/*
Sends email to the user at registered Email Address containing the random number. GUI included.
*/

$path = dirname ( dirname ( dirname ( dirname (__FILE__) ) ) );
require( $path. '/wp-load.php' );
require_once plugin_dir_path(__FILE__) . 'random.php';

$userid = $current_user->ID;

global $current_user;
$current_user = wp_get_current_user();
$uid = $current_user->ID;


global $wpdb;

$table = $wpdb->prefix."cookies";

/* check for cookies */
$namecheck = "2stepauth_rem_".$current_user->ID;

// If cookie corresponding to $namecheck is present, proceed.
if(isset($_COOKIE[$namecheck])){
 
	// Get the value of user browser cookie in $id
	$id = $_COOKIE[$namecheck];
	$date1 = date('y-m-d');

	// Get all valid cookie values from DB
	$cookieArr = $wpdb->get_results('Select cookie from '.$table.' where userid='.$userid.' AND date>'.$date1);
	
	if ( $cookieArr ) {
	
		foreach ( $cookieArr as $ck ) {
		 
			$cookieVal[] = $ck->cookie;
		 
		}
	 
		foreach ( $cookieVal as $c ) {

			// Compare browser cookie value with values in DB
       			if ( $c == $id ) {
				//echo "Cookie matched";
				update_option( '2stepauth_verificationdone_'.$uid, 1 );
				wp_redirect( admin_url() );

			}	
		}	
	} 
 
}
/*end check for cookies */



	if ( $_POST['submit'] ) {                         // Start if-post submit

		$code = get_option( '2stepauth_emailcode_'.$uid );

		$input = $_POST['code'];
		$input = intval($input);
		
		// if code matches
		if ( $code == $input ) {

			// If Remember Me is checked
			if ( $_POST['remember'] ) {

				$randomval = send111();
				$cookie = $userid.$randomval;

				$data['userid'] = $current_user->ID;
				$data['cookie'] = $cookie;
				$ts = strtotime( '+2 weeks' );
				$data['date'] = date( 'y-m-d', $ts );
				
				global $wpdb;
				$table1 = $wpdb->prefix."cookies";
				$wpdb->insert($table1,$data);
				$name = "2stepauth_rem_".$current_user->ID;

				setcookie($name, $cookie, time()+(60*60*24*14),"/");

			}

			update_option( '2stepauth_verificationdone_'.$uid, 1 ); 
			wp_redirect( admin_url() );

		}

		// code does not match, show the form and error msg
		else {

			
			?>
			<!DOCTYPE html>
			<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">  
			<link rel='stylesheet' id='wp-admin-css'  href='<?php echo admin_url('css/wp-admin.css?ver=20111208'); ?>' type='text/css' media='all' />
			<link rel='stylesheet' id='colors-fresh-css'  href='<?php echo admin_url('css/colors-fresh.css?ver=20111206');  ?>' type='text/css' media='all' />
			<head><title>2stepAuth: Email Authentication</title></head>
			<body class="login" >
			<div id='login'>
			<img src="2stepauth.png" style="display: block; overflow: hidden; padding-bottom: 15px; padding-left:30px; align:center;" />
			<div class='updated fade'><label id='custom_msg' value=''><p><strong>Incorrect Code. Please try again.</strong></p></label></div><br />
			<form id="login_form" name="loginform" action="" method="post">

			<label for="user_login1">Enter Code</label><input class="input" style="width:270px;" type="text" name="code" />
			<p><label for="rememberme"><input type="checkbox" name="remember" > Remember Me</label></p>
			<p><input type="submit" name="submit" id="wp-submit" class="button-primary" value="<?php esc_attr_e('Log In'); ?>" tabindex="100" /></p>
			<br /><br />
			<p><label for="other_options">
			Other Options:</p></label>
			<?php $phone = get_option( '2stepauth_gotphone_'.$uid ); 
			if ( $phone == 1 ) { ?>
				<label for="sms_ver"><a href="<?php echo plugin_dir_url(__FILE__) . 'auth.php'; ?>">Use SMS Verification</a></label><br />
			<?php } ?>
			<label for="backup_codes"><a href="<?php echo plugin_dir_url(__FILE__) . 'backupcodes.php'; ?>">Use Backup Codes</a></label>
			
			</form><br />
			<label for='cancel'><a href='<?php echo site_url('wp-login.php'); ?>'>Cancel</a></label>
			</div></body>
			<?php

		}

	}	// End if-post submit

	// If !post submit
	else {			
		
		// If Step 3 is completed
		if ( get_option( '2stepauth_completed_'.$uid ) == 1 ) {
	
			$enabled = get_option( '2stepauth_emailverify_'.$uid );
			
			// If Email verification is enabled
			if ( $enabled == 1 ) {
			
				$path = dirname ( dirname ( dirname ( dirname (__FILE__) ) ) );
				require( $path. '/wp-load.php' );
				require_once plugin_dir_path(__FILE__) . 'random.php';
				
				$email1 = get_option( '2stepauth_email_'.$uid );
				
				$pos = strpos( $email1, "@" );
				$pos = $pos - 3;
				$len = strlen( $email1 );
				$email = substr( $email1, $pos, $len - $pos );
				
				?>
				<!DOCTYPE html>
				<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">  
				<link rel='stylesheet' id='wp-admin-css'  href='<?php echo admin_url('css/wp-admin.css?ver=20111208'); ?>' type='text/css' media='all' />
				<link rel='stylesheet' id='colors-fresh-css'  href='<?php echo admin_url('css/colors-fresh.css?ver=20111206');  ?>' type='text/css' media='all' />
				<head><title>2stepAuth: Email Authentication</title></head>
				<body class="login" > 
				<div id='login'>
				<img src="2stepauth.png" style="display: block; overflow: hidden; padding-bottom: 15px; padding-left:30px; align:center;" />
				<?php echo "<div class='updated fade' style=''><p><strong>". __( "Validation code is sent to your email *****".$email.". Please enter it below.", '2step_auth' ) . "</strong></p></div>"; ?><br />
				<form id="login_form" name="loginform" action="" method="post">

				<label for="user_login1">Enter Code</label><input class="input" style="width:270px;" type="text" name="code" />
				<p><label for="rememberme"><input type="checkbox" name="remember" > Remember Me</label></p>
				<p><input type="submit" name="submit" id="wp-submit" class="button-primary" value="<?php esc_attr_e('Log In'); ?>" tabindex="100" /></p>
				<br /><br />
				<p><label for="other_options">
				Other Options:</p></label>
				<?php $phone = get_option( '2stepauth_gotphone_'.$uid ); 
				if ( $phone == 1 ) { ?>
					<label for="sms_ver"><a href="<?php echo plugin_dir_url(__FILE__) . 'auth.php'; ?>">Use SMS Verification</a></label><br />
				<?php } ?>
				<label for="backup_codes"><a href="<?php echo plugin_dir_url(__FILE__) . 'backupcodes.php'; ?>">Use Backup Codes</a></label>
				
				</form><br /> 
				<label for='cancel'><a href='<?php echo site_url('wp-login.php'); ?>'>Cancel</a></label>
				</div></body>

				<?php
			
				// Sending Mail with Verification Code
				$to = get_option( '2stepauth_email_'.$uid );
				$code = send111();
				update_option( '2stepauth_emailcode_'.$uid, $code );
				$subject = "2StepAuth Verification Code";
				$message = "Your 2StepAuth Verification Code is ".$code.". Please use this code to get access to your blog.";
				$return = wp_mail( $to, $subject, $message ); 

				
			}
			else {
				update_option( '2stepauth_verificationdone_'.$uid, 1 );
				$url = plugin_dir_url(__FILE__) . 'backupcodes.php';
				wp_redirect( $url );
		
			}	
			
		}
		else {
			update_option( '2stepauth_verificationdone_'.$uid, 1 );
			wp_redirect( admin_url() );
		
		}
	}

?>