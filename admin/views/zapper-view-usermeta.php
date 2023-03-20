<?php

/**
 * The plugin area to view the usermeta
 */

    if( current_user_can('edit_users' ) ) { ?>
<h2> <?php echo __('Displaying Usermeta for <span class="red">' . $user->display_name . '</span> (<span class="blue">' . $user->user_login . '</span>)', $this->plugin_text_domain ); ?>
</h2>
<?php

$usermeta = get_user_meta( $user_id );
        echo '<div class="card">';
        foreach( $usermeta as $key => $value ) {
            $v = (is_array($value)) ? implode(', ', $value) : $value;            
            $span_open = '';
                $span_close = '';
            if($key == 'session_tokens'){
                $v = '';
            }
            if($key == 'description'){
                $v = '';
            }
            if($key == 'wp_capabilities'){
                
                $v = unserialize($v);

                $v = array_keys($v);
                $vstring = '';
                foreach($v as $varray){
                    $vstring .=  json_encode($varray) ;
                }

                $v = $vstring;
            }
            if($key == 'sms_number'){
                $span_open = '<span class="green">';
                $span_close = '</span>';
            }
            if($key == 'sms_usergroup'){
                $span_open = '<span class="blue">';
                $span_close = '</span>';
            }
            if($key == 'wp_capabilities'){
                $span_open = '<span class="red">';
                $span_close = '</span>';
            }
                
            echo '<p><strong>'.$span_open.$key.$span_close. '</strong>: ' . $v . '</p>';
            
        }
        echo '</div><br>';
?>
<a
    href="<?php echo esc_url( add_query_arg( array( 'page' => wp_unslash( $_REQUEST['page'] ) ) , admin_url( 'users.php' ) ) ); ?>"><?php _e( 'Back', $this->plugin_text_domain ) ?></a>
<?php
    }
    else {  
?>
<p> <?php echo __( 'You are not authorized to perform this operation.', $this->plugin_text_domain ) ?> </p>
<?php   
    }