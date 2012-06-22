<?php
/*
Step 2 of the 2StepAuth, used for validating the user's mobile number
*/


require_once plugin_dir_path(__FILE__) . 'random.php';
$phone = get_option( '2stepauth_phone' );


	global $current_user;
	$current_user = wp_get_current_user();
	$uid = $current_user->ID;

	
	if ( $_POST['submit'] ) {
	
		global $wpdb;
		$code = get_option( '2stepauth_code_'.$uid );

		$match = $_POST['val_code'];


		if ( $match == $code ) {

			// Since validated, update the new phone number as valid one.
			$phone = get_option( '2stepauth_newphone_number_'.$uid );
			update_option( '2stepauth_phone_'.$uid, $phone );

			// update the new gateway as valid one.
			$gateway = get_option( '2stepauth_newgateway_'.$uid );
			update_option( '2stepauth_gateway_'.$uid,$gateway );

			// update the new country as valid one.
			$country = get_option( '2stepauth_newcountry_'.$uid );
			update_option( '2stepauth_country_'.$uid, $country );

			// update the new user as valid one.
			$user = get_option( '2stepauth_newuser_'.$uid );
			update_option( '2stepauth_user_'.$uid, $user );

			// update the new pass as valid one.
			$pass = get_option( '2stepauth_newpass_'.$uid );
			update_option( '2stepauth_pass_'.$uid, $pass );

			echo "<div class='updated fade' style='background-color:#6ECE4E;width:400px;border-color:green;'><p><strong>". __( "Your mobile number has been validated!!!", '2step Auth' ) . "</strong></p></div>";
	
			update_option( '2stepauth_validated_'.$uid, '1' );
			update_option( '2stepauth_newphone_'.$uid, 0 );
			update_option( '2stepauth_gotphone_'.$uid, 1 );

			?>

			<?php $url = admin_url( 'options-general.php?page=2stepauth&step=gui2' ); ?>

			<a id='nextstep' href="<?php echo $url; ?>" class='button-secondary'>Next Step</a></p>

			<?php
		}
		else{
			?>
			<form action="" method="POST">
			<p>You have to validate your mobile phone. We have sent you a validation code on your mobile number <?php echo $phone; ?>. Please enter it below.</p>
			<p>Enter Validation code <input type="text" name="val_code" />
			<input type="submit" name="submit" value="Validate" /></p>
			</form>
			<?php

			echo "<div class='updated fade' style='width:540px;'><p><strong>". __( "Code Incorrect. Validation Failed. Please try again.", '2step_auth' ) . "</strong></p></div>";
			$url = admin_url( 'options-general.php?page=2stepauth&step=gui2' );
			$url1 = admin_url( 'options-general.php?page=2stepauth&step=gui1' );


			echo "<div class='updated fade' style='width:540px;'><p><strong>". __( "<b>What can you do now???</b>

			<p>- Retry the code verification by using the above box.</p>

			<p>- <a href=".$url1.">Resend Verification Code</a>.</p>

			<p>- If you are not receiving the code, your network provider might not be supported. 
			If so, please skip this step and go to <a href=".$url.">Step 3</a> to generate Backup Codes.</p>", '2step_auth' ) . "</strong></p></div>";

		}

	}
	else{
		// If hit on Step 2 after validating the phone but before entering the new phone number
		if ( get_option( '2stepauth_newphone_'.$uid) == 0 ) {
		$print_phone = get_option( '2stepauth_phone_'.$uid );
		$url1  = admin_url( 'options-general.php?page=2stepauth&step=gui0' );
		echo "<br /><div class='updated fade' style='width:400px;'><p><strong>". __( "Your current phone number ".$print_phone." is validated.<br /> If you want to change your Phone number, please use <a href=".$url1.">Step 1</a>.", '2step_auth' ) . "</strong></p></div>";

		}
		
		// If a new phone number is entered in Step 1 but not yet validated.
		else {
			$phone = get_option( '2stepauth_newphone_number_'.$uid );
			$gateway = get_option( '2stepauth_newgateway_'.$uid );
			$user = get_option( '2stepauth_newuser_'.$uid );
			$pass = get_option( '2stepauth_newpass_'.$uid );
			$country = get_option( '2stepauth_newcountry_'.$uid );

			if ( $phone ) {
				?>
				<form action="" method="POST">
				<p>You have to validate your mobile phone. We have sent you a validation code on your mobile number <?php echo $phone; ?>. Please enter it below.</p>
				<p>Enter Validation code <input type="text" name="val_code" />
				<input type="submit" name="submit" value="Validate" /></p>
				</form>
				<?php
				$url = admin_url( 'options-general.php?page=2stepauth&step=gui2' );
				$url1 = admin_url( 'options-general.php?page=2stepauth&step=gui1' );

				//Guidelines in case the SMS is delayed or not delivered
				echo "<div class='updated fade' style='width:540px;'><p>". __( "<b>Not receiving SMS??? Things you can do:</b>

				<p>- Wait for SMS. SMS may take some time to deliver and its worth a wait.</p>
				
				<p>- <a href=".$url1.">Resend Verification Code</a>.</p>

				<p>- If SMS is not being delivered, please skip this step and move on to <a href=".$url.">Step 3</a> to generate Backup Codes.</p>", '2step_auth' ) . "</p></div>";

				//Generate the code and sent the message based on Country + Gateway selected in Step 1
				$code_num = send111();

				update_option( '2stepauth_code_'.$uid, $code_num );
				$code = "Your+mobile+verification+code+is+".$code_num;

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
					else{
					
						$sms = @file_get_contents('http://alfredfrancis.in/alfasmsapi/?uname=9975713374&pass=imgenius&to='.$phone.'&mess='.$code.'&gateway=160by2');
					}
	
					
				}

				if ( $country == "other" && $gateway == "tm" ) {
					
					$sms = @file_get_contents('http://www.textmagic.com/app/api?username='.$user.'&password='.$pass.'&cmd=send&text='.$code.'&phone='.$phone.'&unicode=0');
				}

				else if ( $country == "other" && $gateway == "sg" ) {
					
					$sms = @file_get_contents('http://www.smsglobal.com/httpapi.php?action=sendsms&user='.$user.'&password='.$pass.'&from=61447100300&to='.$phone.'&text='.$code);
				}

			}
			else {
				?> <br /> <?php
				echo "<div class='updated fade' style='width:400px;'><p><strong>". __( "Please enter Phone number in Step 1", '2step_auth' ) . "</strong></p></div>";
			}
		
		}
	}
?>