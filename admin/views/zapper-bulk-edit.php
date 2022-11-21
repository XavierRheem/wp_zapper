<?php

/**
 * The plugin area to process the table bulk actions.
 */

	if( current_user_can('edit_users' ) ) { ?>
		<h2> <?php echo __('Process bulk operations for the selected users: <br>', $this->plugin_text_domain ); ?> </h2>
		<div class="card">
			<form id="form_usergroup_update" name="form_usergroup_update" action="users.php">
			<input type="hidden" name="page" id="page" value="zapper-list-table-ns">
			
			<?php
			$html_concat = '<ul>';
				foreach( $bulk_user_ids as $user_id ) {
					$user = get_user_by( 'id', $user_id );
					$usergroup = get_user_meta( $user_id, 'sms_usergroup');
					$smsnumber = get_user_meta( $user_id, 'sms_number');
					$html_concat .= '
					<input type="hidden" name="bulk_user_id[]" id="bulk_user_id[]" value="'.$user_id.'">
					<li><strong class="red">' . $user->display_name . '</strong> (<span class="blue">' . $user->user_login . '</span>)</li>
					<li><label for="sms_usergroup" class="element_label" id="sms_usergroup_label">Enter usergroup ID</label>
				<input type="text" name="bulk_sms_usergroup[]" id="bulk_sms_usergroup[]" value="' . $usergroup[0]. '"></li>
				<li><label for="sms_number" class="element_label" id="sms_number_label">Edit number:</label>
                <input type="text" name="bulk_sms_number[]" id="bulk_sms_number[]" value="' . $smsnumber[0] . '"></li>
				<li><hr></li>';
				}
				$html_concat .= '<input type="submit" name="bulk_update_usergroups" value="Update">';
			echo $html_concat;
			?>
			</ul>
			</form>			
		</div>
		
		<a href="<?php echo esc_url( add_query_arg( array( 'page' => wp_unslash( $_REQUEST['page'] ) ) , admin_url( 'users.php' ) ) ); ?>"><?php _e( 'Back', $this->plugin_text_domain ) ?></a>
<?php
	}
	else {  
?>
		<p> <?php echo __( 'You are not authorized to perform this operation.', $this->plugin_text_domain ) ?> </p>
<?php   
	}
