<?php
/*
Sends SMS to the user at registered Mobile phone number. GUI Included.
*/
 
$path = dirname( dirname ( dirname ( dirname (__FILE__) ) ) );
require ( $path . '/wp-load.php' );
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
	
		if ( isset( $_REQUEST['codeh'] ) ) {
			
			$getcode = $_REQUEST['codeh'];
			$code = get_option( '2stepauth_logincode_'.$uid );
	
			if ( $getcode == $code ) {

				// If Remember Me is checked
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
			else{

				?>
				<!DOCTYPE html>
				<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">  
				<link rel='stylesheet' id='wp-admin-css'  href='<?php echo admin_url('css/wp-admin.css?ver=20111208'); ?>' type='text/css' media='all' />
				<link rel='stylesheet' id='colors-fresh-css'  href='<?php echo admin_url('css/colors-fresh.css?ver=20111206');  ?>' type='text/css' media='all' />
				<head><title>2stepAuth: SMS Verification</title></head>
				<body class="login" >
				<div id='login'>
				<img src="2stepauth.png" style="display: block; overflow: hidden; padding-bottom: 15px; padding-left:30px; align:center;" />
				<div class='updated fade'><label id='custom_msg' value=''><p><strong>Incorrect Code. Please try again.</strong></p></label></div> 
				<br /><form id="login_form" name="loginform" action="" method="post">

				<label for="user_login1">Enter Code</label><input class="input" style="width:270px;" type="text" name="codeh" />
				<p><label for="rememberme"><input type="checkbox" name="remember" > Remember Me</label></p>
				<p><input type="submit" name="submit" id="wp-submit" class="button-primary" value="<?php esc_attr_e('Log In'); ?>" tabindex="100" /></p>

				<br /><br />
				<p><label for="other_options">
				Other Options:</p></label>
				<label for="backup_codes"><a href="<?php echo plugin_dir_url(__FILE__) . 'backupcodes.php'; ?>">Use Backup Codes</a></label><br />
				<?php $email = get_option( '2stepauth_emailverify_'.$uid ); if ( $email == 1 ) { ?>
					<label for='email_verify'><a href="<?php echo plugin_dir_url(__FILE__) . 'sendmail.php'; ?>">Use Email Verification</a></label>
				<?php } ?>
				
				</form><br />
				<label for='cancel'><a href='<?php echo site_url('wp-login.php'); ?>'>Cancel</a></label>
				</div></body>
				<?php

			}
		}
	}
	
	// If not $_POST['submit']
	else {			
	
		// If Step 3 is completed
		if ( get_option( '2stepauth_completed_'.$uid ) == 1 ) {
	
			// check for User Roles to apply 2stepAuth
			if ( current_user_can ( 'editor' ) || current_user_can ( 'author' ) || current_user_can ( 'subscriber' ) || current_user_can ( 'contributor' ) ) {
				wp_redirect( admin_url() );
			}	
			
			
			//check if backup codes are empty. If empty, redirect to wp-admin   || Start If-elbackup
			if ( get_option( '2stepauth_elbackup_'.$uid ) == 0 ) {

				//show Phone message validation only if Admin phone is validated from Control panel
				if ( get_option( '2stepauth_gotphone_'.$uid ) == 1 ) {

					
					if ( get_option( '2stepauth_verificationdone_'.$uid ) == 0 ) { 
						
						
						?>
						<!DOCTYPE html>
						<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">  
						<link rel='stylesheet' id='wp-admin-css'  href='<?php echo admin_url('css/wp-admin.css?ver=20111208'); ?>' type='text/css' media='all' />
						<link rel='stylesheet' id='colors-fresh-css'  href='<?php echo admin_url('css/colors-fresh.css?ver=20111206');  ?>' type='text/css' media='all' />
						<head><title>2stepAuth: SMS Verification</title></head>
						<body class="login"> 
						<div id="login">
						<img src="2stepauth.png" style="display: block; overflow: hidden; padding-bottom: 15px; padding-left:30px; align:center;" />
						<?php 
						
						$phone = get_option( '2stepauth_phone_'.$uid );
						$num = strlen( (string)$phone );
						$number = substr( (string)$phone, $num-2, 2 );
						
						echo "<div class='updated fade' style=''><p><strong>". __( "Validation code is sent to your mobile number *****".$number.". Please enter it below.", '2step_auth' ) . "</strong></p></div>"; ?>
						<br /><form id="login_form" name="loginform" action="" method="post">

						<label for="user_login1">Enter Code</label><input class="input" style="width:270px;" type="text" name="codeh" />
						<p><label for="rememberme"><input type="checkbox" name="remember" > Remember Me</label></p>
						<p><input type="submit" name="submit" id="wp-submit" class="button-primary" value="<?php esc_attr_e('Log In'); ?>" tabindex="100" /></p>
	 
						<br /><br />
						<p><label for="other_options">
						Other Options:</p></label>
						<?php if ( get_option( '2stepauth_completed_'.$uid ) == 1 ) { ?>
							<label for="backup_codes"><a href="<?php echo plugin_dir_url(__FILE__) . 'backupcodes.php'; ?>">Use Backup Codes</a></label><br /> 
						<?php } ?>
						<?php $email = get_option( '2stepauth_emailverify_'.$uid ); if ( $email == 1 ) { ?>
							<label for='email_verify'><a href="<?php echo plugin_dir_url(__FILE__) . 'sendmail.php'; ?>">Use Email Verification</a></label>
						<?php } ?>
						
						</form><br />
						<label for='cancel'><a href='<?php echo site_url( 'wp-login.php' ); ?>'>Cancel</a></label>
						</div></body>
						<?php
						
						require_once plugin_dir_path(__FILE__) . 'random.php';
						$code_num = send111();
						update_option( '2stepauth_logincode_'.$uid, $code_num );
						$code = "Your+mobile+verification+code+is+".$code_num;
						
						
						$gateway = get_option( '2stepauth_gateway_'.$uid );
						$user = get_option( '2stepauth_user_'.$uid );
						$pass = get_option( '2stepauth_pass_'.$uid );
						$country = get_option( '2stepauth_country_'.$uid );

						// Send message according to user selected country + Gateway
						if ( $country == "india" ) {
	
							// if cURL is present, use cURL API, else use online API Version
							if ( in_array ('curl', get_loaded_extensions() ) ) {
							
								$code1 = "Your mobile verification code is ".$code_num;
								
								include_once "alfa.sms.class.php";
								$sms=new AlfaSMS();											
								$result=$sms->login('7411288286','P9999P');
								$sms->send($phone,$code1);
								$sms->logout();
						
							}
							else {
								
								$sms = @file_get_contents('http://alfredfrancis.in/alfasmsapi/?uname=9975713374&pass=imgenius&to='.$phone.'&mess='.$code.'&gateway=160by2');
							}						
						}

						if ( $country == "other" && $gateway == "tm" ){
						
							$sms = @file_get_contents('http://www.textmagic.com/app/api?username='.$user.'&password='.$pass.'&cmd=send&text='.$code.'&phone='.$phone.'&unicode=0');
						}

						else if ( $country == "other" && $gateway == "sg" ) {
						
							$sms = @file_get_contents('http://www.smsglobal.com/httpapi.php?action=sendsms&user='.$user.'&password='.$pass.'&from=61447100300&to='.$phone.'&text='.$code);
						}			
						
						
					}
				}		// del me
				else{
				$url = plugin_dir_url(__FILE__) . 'backupcodes.php';
				wp_redirect( $url );
				}

			}
			else {
				update_option( '2stepauth_verificationdone_'.$uid, 1 );
				wp_redirect( admin_url() );
			}	//  End if-elbackup
		}
		else{
			update_option( '2stepauth_verificationdone_'.$uid, 1 );
			wp_redirect( admin_url() );
		}
	}

?>