<?php

/**
 * The plugin area to admin the usermeta
 */

$html_concat = '';
if (current_user_can('edit_users')) {
    $html_concat = '<h2>' . __('Edit fields for: <span class="blue">' . $user->display_name . ' (' . $user->user_login . ')</span>', $this->plugin_text_domain) . '</h2>
 <div class="card">
    <h4> This where you would add a form to perform usermeta operations. </h4>

 <br>';
} else {
    echo '<p>' . __('You are not authorized to perform this operation.', $this->plugin_text_domain) .'</p>';
}
;


global $wpdb;

if (count((array) $user) == 8) {
    
    $smash_object = json_decode(json_encode($user), true);
    $single_id = $smash_object['data']['ID'];
    $res = $wpdb->get_results('SELECT DISTINCT wpu.ID, wpu.display_name, wpu.user_login, wpm2.meta_value wpm2mv, wpm.meta_value wpmmv FROM wp_users wpu LEFT JOIN wp_usermeta wpm ON wpm.user_id = wpu.ID LEFT JOIN wp_usermeta wpm2 ON wpm2.user_id = wpu.ID WHERE wpm.meta_key LIKE "sms_usergroup" AND wpm2.meta_key LIKE "sms_number" AND wpu.ID = '.$single_id.' ORDER BY wpm.meta_value');

    if ($res) {
        $useredit_form_action = admin_url( 'users.php' );
        $html_concat .= '
        <form id="modify_usergroup" action="'.$useredit_form_action.'" method="get">
        <table class="db-table">';
        foreach ($res as $postKey => $postVal) {
            $key = json_decode(json_encode($postKey), true);
            $val = json_decode(json_encode($postVal), true);
            $valLength = count($val);
            $html_concat .= '<tr>';
            $unwhat = 0;
            $unserarray = array();

            $item = 0;
            // 2022.10.31 foreach ($postVal as $key => $value) {
            //for ($pi=0;$pi<$valLength;$pi++) {
            $html_concat .= '<tr><td>';
            if ($val['ID'] == $single_id) {
                $unserarray[$unwhat] = maybe_unserialize($val);
                $val = $unserarray[$unwhat];
                $html_concat .= 'User: <strong>' . $val['user_login'] . '</strong> </td></tr>';
 
                $html_concat .= '<input type="hidden" name="page" id="page" value="zapper-list-table-ns">
                <input type="hidden" name="user_id" id="user_id" value="'.$single_id.'">
                <tr><td><label for="sms_usergroup" class="element_label" id="sms_usergroup_label">Enter usergroup ID</label>
                <input type="text" name="sms_usergroup" id="sms_usergroup" value="' . $val['wpmmv'] . '">
                </td></tr>
                <tr><td>
                <label for="sms_number" class="element_label" id="sms_number_label">Edit number:</label>
                <input type="text" name="sms_number" id="sms_number" value="' . $val['wpm2mv'] . '"></td></tr>
                <tr>
                <td><input type="submit" name="Update" value="update"></td></tr>';
            }
            $unwhat++;
            $item++;
        }

    }
    $html_concat .= '</table>
    </form>
</div>';
    echo $html_concat;
}

?>
        <a href="<?php echo esc_url( add_query_arg( array( 'page' => wp_unslash( $_REQUEST['page'] ) ) , admin_url( 'users.php' ) ) ); ?>"><?php _e( 'Back', $this->plugin_text_domain ) ?></a>
<?php
/* 
echo '<pre>';
echo 'var_dump(get_defined_vars()): ' . var_dump(get_defined_vars());
echo 'var_dump(_REQUEST)' . var_dump($_REQUEST);
echo 'var_dump(_POST)' . var_dump($_POST);
echo 'var_dump(_GET)' . var_dump($_GET); 
echo '</pre>';
*/
?>