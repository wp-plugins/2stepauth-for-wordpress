<?php
/*
Step 1 of 2StepAuth, used for getting Email + Phone Number information from the user.
*/

$url = plugin_dir_url(__FILE__) . 'gui1.php';

global $current_user;
$current_user = wp_get_current_user();
$id = $current_user->ID;


?>
<script language="Javascript"><!--

var countrybool = 0;

function checkfun() {


	if ( document.form1.select.value == "other" ) {
		document.getElementById('sgdiv1').style.display = 'inline';
		countrybool = 1;
		submitcheck();
	}

	else if ( document.form1.select.value == "india" ) {
		document.getElementById('sgdiv1').style.display = 'none';
		countrybool = 1;
		submitcheck();
	}
	
	else if ( document.form1.select.value == "default" ) {
		document.getElementById('sgdiv1').style.display = 'none';
		countrybool = 0;
		document.getElementById('submit').disabled = true;
	
	}
	
	
}

function emailcheck() {

	var emailid=document.getElementById("email").value;
	var emaillab=document.getElementById("emailcheck1");

	if ( emailid ) {
		if ( emailid.search('@') > 0 ) {

			emaillab.innerHTML = " ";
			emailbool = 1;
		
		}
		else {
			emaillab.innerHTML = " Please enter a valid email address";
			emailbool = 0;
		}
		}
	else {
		emaillab.innerHTML = "  Please enter email address.";
		emailbool = 0;
	}
	

}


function phonecheck() {

	var phone=document.getElementById("phone").value;
	var phonelab=document.getElementById("phonecheck1");

	if ( phone ) {
		phonelab.innerHTML = " ";
		phonebool = 1;

	}
	else {
		phonelab.innerHTML = " Please enter your Phone number";
		phonebool = 0;
	}
	submitcheck();
}

function submitcheck() {


	if ( phonebool == 1 && emailbool == 1 && countrybool == 1 ) {

		document.getElementById("submit").disabled = false;
	}
	else {
		document.getElementById("submit").disabled = true;
	}

}

function emailfocus() {

	var email1=document.getElementById("email").value;	
	var emaillab=document.getElementById("emailcheck1");

	emaillab.innerHTML = "Enter the Email where you want to receive the Email Verification code";

}

function phonefocus() {
	var phone=document.getElementById("phone").value;
	var phonelab=document.getElementById("phonecheck1");

	phonelab.innerHTML = "(eg. for India: enter 911234567890 where 91 is country code and 1234567890 is phone number)";


}

function checkgateway() {
	var lab = document.getElementById("gatelabel");
	var selectval = document.getElementById('select1').value;


	if ( selectval == "tm" ) {
	
		lab.innerHTML = "Please enter your TextMagic account information";

	}
	else if ( selectval == "sg" ) {
	
		lab.innerHTML = "Please enter your SMS Global account information";

	}

}


</script>

<form name='form1' action="" method="POST">

<p>Please enter your information in the form below.</p>
<table>
<tr><td width="160px">
<p><label for="email">Email</label></td><td><input type="text" name="email" id="email" onfocus="emailfocus()" onblur="emailcheck()" /></p></td><td><label name='emailcheck' id='emailcheck1' ></label></td><td><label name='emailcheck' id='emailcheck1' ></label></td></tr>
<tr><td><p>Phone Number<br />(with Country Code) </td><td><input type="text"  name="phone" id="phone" onblur="phonecheck()" onfocus="phonefocus()" /></p></td><td><label name='phonecheck' id='phonecheck1' ></label></td></tr>

<tr><td><p>Country </td><td> <select style="width:135px" name="select" onchange="checkfun()">
<option value="default">Select Country</option>
<option value="india">India</option>
<option value="other">Other</option>
</select></p></td></tr></table>

<div id="sgdiv1" style="DISPLAY:none"><table><tr><td width="133px">SMS Gateway </td>
<td class="padd"><select  style="width:135px;" name="select1" id="select1" onchange="checkgateway()">
<option value="tm">TextMagic</option>
<option value="sg">SMS Global</option>
</select></td></tr>

<tr><td colspan=2><p><label id="gatelabel" value="">Enter your TextMagic account information</label></p></td></tr>
<tr><td><label name="sguserlabel">Username </label></td><td class="padd"><input type="text" name="sguser" /></td></tr>
<tr><td><label name="sgpasslabel">Password </label></td><td class="padd"><input type="text" name="sgpass" /></td></tr>
</table></div>
<table><tr><td width="160px">
<p><label for="emailverifycheck"> Disable Email Authentication</label></td><td><input type="checkbox" name="emailver"  /></p></td></tr>
</table>

<p><input class='button-secondary' disabled type="submit" id="submit" value="Submit"  name="submit" />

<?php $url = admin_url('options-general.php?page=2stepauth&step=gui1'); ?>

<a style="DISPLAY:none; " id='nextstep' href="<?php echo $url; ?>" class='button-secondary'>Next Step</a></p>

<style type="text/css">
td.padd{
padding-left:10%;

}
</style>

<?php

//If 2stepAuth is requested to be disabled
if ( $_POST['submit1'] ) {

	update_option( '2stepauth_completed_'.$id, 0 );
	delete_option( '2stepauth_completed_'.$id );
	update_option( '2stepauth_gotphone_'.$id, 0 );
	update_option( '2stepauth_newphone_'.$id, 1 );
	delete_option( '2stepauth_newphone_number_'.$id );
	
}


if ( $_POST['submit'] ) {

	// allow only if user is administrator	
	if ( current_user_can ( 'administrator' ) ) {

		if ( $_POST['email'] ) {
			$users = count_users();
			
			foreach ( $users['avail_roles'] as $role => $count ) {
				
				if ( $role == "administrator" ) {
					
					$id = $count;
				
				}

			}
				
			global $current_user;
			$current_user = wp_get_current_user();
			$id = $current_user->ID;
			
			
			//insert uid in users table
			global $wpdb;
			$table = $wpdb->prefix."2stepauth_users";
			$data['userid'] = $id;
			$wpdb->insert( $table,$data );
			
			update_option( '2stepauth_email_'.$id, $_POST['email'] );
			update_option( '2stepauth_newphone_number_'.$id, $_POST['phone'] );
			update_option( '2stepauth_newuser_'.$id, $_POST['sguser'] );
			update_option( '2stepauth_newpass_'.$id, $_POST['sgpass'] );
			update_option( '2stepauth_newphone_'.$id, 1 );

			// Disable Email Authentication???
			if ( $_POST['emailver'] != "on" ) {

				update_option( '2stepauth_emailverify_'.$id, 1 );

			}
			
			else{
			
				update_option( '2stepauth_emailverify_'.$id, 0 );
				
			}
			
			// Get gateway+country information
			if ( $_POST['select1'] == "tm" ) {

				update_option( '2stepauth_newgateway_'.$id, 'tm' );
			}

			if ( $_POST['select1'] == "sg" ) {
				update_option( '2stepauth_newgateway_'.$id, 'sg' );
			}

			if ( $_POST['select'] == "india" ) {
				update_option( '2stepauth_newcountry_'.$id, 'india' );
			}

			if ( $_POST['select'] == "other" ) {
				update_option( '2stepauth_newcountry_'.$id, 'other' );
			}
			
			
			//user roles
			if ( $_POST['editorcheck'] == "on" ) {
			
				update_option('2stepauth_editor_'.$id,1);
			}
			else{
			
				update_option('2stepauth_editor_'.$id,0);
			
			}
			
			if($_POST['authorcheck']=="on"){
			
				update_option('2stepauth_author_'.$id,1);
			
			}
			else{
			
				update_option('2stepauth_author_'.$id,0);
			
			}
			
			
			if($_POST['contricheck']=="on"){
			
				update_option('2stepauth_contri_'.$id,1);
			
			}
			else{
			
				update_option('2stepauth_contri_'.$id,0);
			
			}
			
			
			if($_POST['subcheck']=="on"){
			
				update_option('2stepauth_sub_'.$id,1);
			
			}
			else{
			
				update_option('2stepauth_sub_'.$id,0);
			
			}
			
			
			
			$url1  = admin_url( 'options-general.php?page=2stepauth&step=gui1' );
			echo "<div class='updated fade' style='width:400px;'><p><strong>". __( "Email updated. Please continue to <a href=".$url1.">Step 2</a> to validate Phone number.", '2step_auth' ) . "</strong></p></div>";
			?>
			<script language='Javascript'><!--

			document.getElementById('nextstep').style.display='inline';

			</script>
			<?php
		}
		else {
		
			if( !$_POST['email'] ){
			
				echo "<div class='updated fade' style='width:400px;'><p><strong>". __( "Please enter a valid Email Address", '2step_auth' ) . "</strong></p></div>";
			
			}
				
		}
	}
	else {
	
		echo "<div class='updated fade' style='width:400px;'><p><strong>". __( "You do not have sufficient privilege to access this resource", '2step_auth' ) . "</strong></p></div>";
	}
}

?>
<table><tr><td width="160px">
<p><label for="disablecheck"> Disable 2StepAuth for my account</label></td><td><input <?php if(get_option('2stepauth_completed_'.$id)==0){ ?> disabled <?php } ?> type="checkbox" name="disablecheck"  /></p></td></tr>
</table>
<p><input class='button-secondary' type="submit" id="submit1" name="submit1" value="Submit" <?php if(get_option('2stepauth_completed_'.$id)==0){ ?> disabled <?php } ?>  name="submit1" /></p>

</form>
<?php
if ( $_POST['submit1'] ) {
	
	// allow only if user is administrator
	if ( current_user_can ( 'administrator' ) ) {

		echo "<div class='updated fade' style='width:430px;'><p><strong>". __( "2StepAuth Disabled for your Account. To re-enable, complete the 3 steps", '2step_auth' ) . "</strong></p></div>";
	
	}
}
?>