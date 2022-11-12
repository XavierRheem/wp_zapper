<?php

/**
 * The plugin area to process the table bulk actions.
 */
/** ---------- TESTING ----------------- */
add_filter( 'handle_bulk_actions-zapper-table', 'my_bulk_action_handler', 10, 3 );
 
function my_bulk_action_handler( $this->page_bulkedit, 'email_to_eric', $bulk_user_ids ) {
  if ( $doaction !== 'email_to_eric' ) {
    return $redirect_to;
  }
  foreach ( $bulk_user_ids as $post_id ) {
    // Perform action for each post.
  }
  $redirect_to = add_query_arg( 'bulk_emailed_posts', count( $post_ids ), $redirect_to );
  return $redirect_to;
}

/** ----------------- END TEST ----------------- */

	if( current_user_can('edit_users' ) ) { ?>
		<h2> <?php echo __('Process bulk operations for the selected users: <br>', $this->plugin_text_domain ); ?> </h2>
		<h4>
			<ul>
			<?php
				foreach( $bulk_user_ids as $user_id ) {
					$user = get_user_by( 'id', $user_id );
					echo '<li>' . $user->display_name . ' (' . $user->user_login . ')' . '</li>';
				}
			?>
			</ul>
		</h4>
		<div class="card">
			<h4> HTML Form for bulk operations. </h4>
		</div>
		<br>
		<a href="<?php echo esc_url(
			add_query_arg( 
				array( 'page' => wp_unslash( 
					$_REQUEST['page'] 
					) 
				) , 
				admin_url( 'admin.php' ) 
				) 
			); ?>"><?php _e( 'Back', $this->plugin_text_domain ) ?></a>
<?php
	}
	else {  
?>
		<p> <?php echo __( 'Not authorized.', $this->plugin_text_domain ) ?> </p>
<?php   
	}
