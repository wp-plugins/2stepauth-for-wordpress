<?php
 
$path = dirname ( dirname ( dirname ( dirname (__FILE__) ) ) );
require($path. '/wp-load.php');
require_once plugin_dir_path(__FILE__) . 'random.php';

	global $current_user;
	$current_user = wp_get_current_user();
	$uid = $current_user->ID; 

 
$email = get_option( '2stepauth_emailverify_'.$uid ); 
$userid = $current_user->ID;
global $wpdb;

$table = $wpdb->prefix."cookies";

/* check for cookies */
$namecheck = "2stepauth_rem_".$current_user->ID;

// If cookie corresponding to $namecheck is present, proceed.
if ( isset ( $_COOKIE[$namecheck] ) ) {

	// Get the value of user browser cookie in $id
  	$id = $_COOKIE[$namecheck];
	$date1 = date( 'y-m-d' );
	
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
 
	if ( $_POST['submit'] ) {
	
		// Get Backup codes corresponding to current user from DB
		global $wpdb;
		$table = $wpdb->prefix."backup";
		$return = $wpdb->get_results("Select backup from ".$table." where userid=".$uid);
	
		foreach ( $return as $r ) {
		 
			$backup[] = $r->backup;
		 
		}

		// Get user entered Backup code in $getcode
		$getcode = $_REQUEST['codeh'];

		// Compare
		foreach ( $backup as $b ) {

			if ( $getcode == $b ) {
				global $wpdb;
				$table = $wpdb->prefix."backup";
				$wpdb->query('Delete from '.$table.' where backup="'.$getcode.'"');
				$backup1 = $wpdb->get_results("Select backup from ".$table." where userid=".$uid);
				
				$sizebackup = sizeof($backup1);
				
				// Options for Notifications related to Low backup count
				if ( $sizebackup < 3 ) {
					update_option( '2stepauth_lowbackup_'.$uid, 1 );
				}
				if ( $sizebackup > 2 ) {
					update_option( '2stepauth_lowbackup_'.$uid, 0 );
				}
				if ( $sizebackup == 0 ) {
					update_option( '2stepauth_elbackup_'.$uid, 1 );
				}
			
				// Remember Me is checked
				if ( $_POST['remember'] ) {

					$randomval = send111();
					$cookie = $userid.$randomval;

					$data['userid'] = $current_user->ID;
					$data['cookie'] = $cookie;
					$ts = strtotime('+2 weeks');
					$data['date'] = date('y-m-d',$ts);
					global $wpdb;
					$table1 = $wpdb->prefix."cookies";
					$wpdb->insert($table1,$data);
					$name = "2stepauth_rem_".$current_user->ID;

					setcookie($name, $cookie, time()+(60*60*24*14),"/");

				}
				update_option( '2stepauth_verificationdone_'.$uid, 1 );
				wp_redirect( admin_url() );

			}
		}
		
		
		// If code does not match, show incorrect code error message
		if ( get_option( '2stepauth_verificationdone_'.$uid ) == 0 ) {

			?>
			<!DOCTYPE html>
			<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">  
			<link rel='stylesheet' id='wp-admin-css'  href='<?php echo admin_url('css/wp-admin.css?ver=20111208'); ?>' type='text/css' media='all' />
			<link rel='stylesheet' id='colors-fresh-css'  href='<?php echo admin_url('css/colors-fresh.css?ver=20111206');  ?>' type='text/css' media='all' />
			<head><title>2stepAuth: Backup Codes</title></head>
			<body class="login">
			<div id='login'>
			<img src="2stepauth.png" style="display: block; overflow: hidden; padding-bottom: 15px; padding-left:30px; align:center;" />


			<div class='updated fade'><label id='custom_msg' value=''><p><strong>Incorrect Code. Please try again.</strong></p></label></div> 
			<br /><form id="login_form" name="loginform"  action="" method="post">

			<label for="user_login">Enter Code here<br /><input class="input" type="text" name="codeh" />
			<p><label for="rememberme"><input type="checkbox" name="remember" > Remember Me</label></p>
			<p class="submit"><input type="submit" name="submit" tabindex="100" id="wp-submit" class="button-primary" value="Log In" tabindex="100" /></p>
			<br /><br />
			<p><label for="other_options">
			Other Options:</label></p>
			<?php if ( get_option( '2stepauth_gotphone_'.$uid ) == 1 ) { ?>

				<label for="sms_verify"><a href="<?php echo plugin_dir_url(__FILE__) . 'auth.php'; ?>">Use SMS Verification</a></label><br />

			<?php  }
		 
			if ( $email == 1 ) { ?>

				<label for='email_auth'><a href="<?php echo plugin_dir_url(__FILE__) . 'sendmail.php'; ?>">Use Email Verification</a></label>

			<?php }
			
			?>

			</form><br />
			<label for='cancel'><a href='<?php echo site_url('wp-login.php'); ?>'>Cancel</a></label>
			</div>
			</body>
			<?php

		}
	}

	// If not $_POST['submit']
	else {			
	
		// If Step 3 is completed
		if ( get_option( '2stepauth_completed_'.$uid ) == 1 ) {
			
			// If Backup Codes are not empty, proceed, otherwise redirect to admin_url()
			if ( get_option( '2stepauth_elbackup_'.$uid ) == 0 ) {
				?>
				<!DOCTYPE html>
				<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">  
				<link rel='stylesheet' id='wp-admin-css'  href='<?php echo admin_url('css/wp-admin.css?ver=20111208'); ?>' type='text/css' media='all' />
				<link rel='stylesheet' id='colors-fresh-css'  href='<?php echo admin_url('css/colors-fresh.css?ver=20111206');  ?>' type='text/css' media='all' />
				<head><title>2stepAuth: Backup Codes</title></head>
				<body class="login" >
				<div id='login'>
				<img src="2stepauth.png" style="display: block; overflow: hidden; padding-bottom: 15px; padding-left:30px; align:center;" />


				<div class='updated fade'><label id='custom_msg' value=''><p><strong>Please enter one of your Backup Codes.</strong></p></label></div> 
				<br /><form id="login_form" name="loginform"  action="" method="post">

				<label for="user_login">Enter Code here<br /><input class="input" type="text" name="codeh" />
				<p><label for="rememberme"><input type="checkbox" name="remember" > Remember Me</label></p>
				<p class="submit"><input type="submit" name="submit" tabindex="100" id="wp-submit" class="button-primary" value="Log In" tabindex="100" /></p>
				<br /><br />
				<p><label for="other_options"> 
				Other Options:</label></p>
				<?php if ( get_option( '2stepauth_gotphone_'.$uid ) == 1 ) { ?>

					<label for="sms_verify"><a href="<?php echo plugin_dir_url(__FILE__) . 'auth.php'; ?>">Use SMS Verification</a></label><br />

				<?php  }
				
				if ( $email == 1 ) { ?>

					<label for='email_auth'><a href="<?php echo plugin_dir_url(__FILE__) . 'sendmail.php'; ?>">Use Email Verification</a></label>

				<?php } 
				?>
				
				</form><br />
				<label for='cancel'><a href='<?php echo site_url('wp-login.php'); ?>'>Cancel</a></label>
				</div>
				
				</body>
				<?php
			
			}
			else {
			
				update_option( '2stepauth_verificationdone_'.$uid, 1 );
				wp_redirect( admin_url() );
			
			}
		}
		
		else {
		
			update_option( '2stepauth_verificationdone_'.$uid, 1 );
			wp_redirect( admin_url() );
		
		}
	}
?>