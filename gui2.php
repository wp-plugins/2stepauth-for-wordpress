<?php
/*
Step 3 of 2StepAuth, generates, stores and assigns Backup Codes to the user.
*/

$table = $wpdb->prefix."backup";

global $current_user;
$current_user = wp_get_current_user();
$uid = $current_user->ID;

?>
<form action="" method="POST" name="form3">

<p></p><p>If mobile phone is not with you or say you have lost your mobile phone, use these Backup Codes to gain access to your Wordpress.<br />
Make sure you print or save these Backup codes at a safe location.</p><br />
Backup Codes:<br />
<input class="button-secondary" type="submit" value="Generate New" name="generate" />
<input class="button-secondary" type="submit" disabled value="View Current Codes" name="view" />

<script language='Javascript'><!--
<?php if ( get_option( '2stepauth_generate_'.$uid ) == 1 ) {?>
document.form3.view.disabled = false;
<?php } ?>
</script>
<?php

	if ( $_POST['view'] ) {
		// allow only if user is administrator
		if ( current_user_can ( 'administrator' ) ) {
		
			global $wpdb;
			$table = $wpdb->prefix."backup";
			$return = $wpdb->get_results("Select backup from ".$table." where userid=".$uid);
		 
			foreach ( $return as $r ) {
			 
				$codes[] = $r->backup;
			 
			}
			?>
			<table style="padding-left:12%;" border="0">
			<tr>

			<td><?php echo $codes[5]; ?></td>
			<td><?php echo $codes[0]; ?></td>
			</tr>
			<tr>

			<td><?php echo $codes[1]; ?></td>
			<td><?php echo $codes[2]; ?></td>

			</tr>
			<tr>

			<td><?php echo $codes[3]; ?></td>
			<td><?php echo $codes[4]; ?></td>

			</tr>

			</table>
			<?php 
		}
		else {
	
			echo "<div class='updated fade' style='width:400px;'><p><strong>". __( "You do not have sufficient privilege to access this resource", '2step_auth' ) . "</strong></p></div>";
		}
		
	}

	if ( $_POST['generate'] ) {
	
		// allow only if user is administrator
		if ( current_user_can ( 'administrator' ) ) {
		
			update_option( '2stepauth_generate_'.$uid, 1 );
			require_once plugin_dir_path(__FILE__) . 'random.php';
			
			//insert uid in users table
			global $wpdb;
			$table = $wpdb->prefix . "2stepauth_users";
			$data['userid'] = $uid;
			$wpdb->insert($table,$data);

			update_option( '2stepauth_completed_'.$uid, 1 );
			update_option( '2stepauth_lowbackup_'.$uid, 0 );
			update_option( '2stepauth_validated_'.$uid, 1 );
			update_option( '2stepauth_elbackup_'.$uid, 0 );

			
			for ( $i = 0; $i < 7 ; $i++ ) {

				if ( $i = 0 ) {
					$code = send111();
					$codes[0] = $code*11;

				}
				
				if ( $i = 1 ) {
					$code = send111();
					$codes[1] = $code*13;
				}
				
				if ( $i = 2 ) {
					$code = send111();
					$codes[2] = $code*17;
				}
				
				if ( $i = 3 ) {
					$code = send111();
					$codes[3] = $code*19;
				}
				
				if ( $i = 4 ) {
					$code = send111();
					$codes[4] = $code*15;
				}
				
				if ( $i = 5 ) {
					$code = send111();
					$codes[5] = $code*18;
				}

				if ( $i = 6 ) {
					$code = send111();
					$codes[6] = $code*21;

				}
				
				global $wpdb;

				$table = $wpdb->prefix."backup";
				$wpdb->query("Delete from $table where userid=".$uid);
				
				for ( $i = 1 ; $i < 7 ; $i++) {
					$data['userid'] = $uid;
					$data['backup'] = $codes[$i];
					$wpdb->insert($table,$data);
				}
				
			}

			?>
			<br />
			<table border="0">
			<tr>

			<td><?php echo $codes[6]; ?></td>
			<td><?php echo $codes[1]; ?></td>
			</tr>
			<tr>

			<td><?php echo $codes[2]; ?></td>
			<td><?php echo $codes[3]; ?></td>

			</tr>
			<tr>

			<td><?php echo $codes[4]; ?></td>
			<td><?php echo $codes[5]; ?></td>

			</tr>

			</table>
			<br />
			<?php 
			echo "<div class='updated fade' style='border-color:#56a83c; background-color:#6ECE4E;width:400px;'><p><strong>". __( "Backup Codes saved. 2stepAuth is now ready!!!", '2step_auth' ) . "</strong></p></div>";
		}
		else {
	
			echo "<div class='updated fade' style='width:400px;'><p><strong>". __( "You do not have sufficient privilege to access this resource", '2step_auth' ) . "</strong></p></div>";
		}		
	}

?>

</form>