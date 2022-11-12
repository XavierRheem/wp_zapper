<?php
$zapper_table = new Zapper_List_Table;

/** ------------------ BEGIN TESTING ---------- */

/*
add_filter( 'bulk_actions-zapper-table', 'register_my_bulk_actions' );
function register_my_bulk_actions() {
 
  $bulk_actions['page_bulkedit'] = __( 'Email to Eric', 'zapper');
  return $bulk_actions;
}
print_r(register_my_bulk_actions());

echo '<h1>register_my_bulk_actions("email")</h1><p>'. register_my_bulk_actions('email') .'</p>';

add_filter( 'handle_bulk_actions-zapper-table', 'my_bulk_action_handler', 10, 3 );
 
function my_bulk_action_handler( $redirect_to = 'admin.php?page=zapper-table', $doaction = 'page_bulkedit', $post_ids = $_REQUEST['users'] ? isset($_REQUEST['users']) : $post_ids = array(1,2,3,4)) {
  
  if ( $doaction !== 'page_bulkedit' ) {
    return $redirect_to;
  }
  foreach ( $post_ids as $post_id ) {
    echo 'post_id: '. $post_id .'<br>';
  }
  $redirect_to = add_query_arg( 'page_bulkedit', count( $post_ids ), $redirect_to );
  return $redirect_to;
}

echo '<h3>my_bulk_action_handler: </h3>';
// echo my_bulk_action_handler(); 

*/



/** ------------------ END TESTING ---------- */

$currentScreen = get_current_screen();
$currentScreenArray = (array) $currentScreen;
echo "<code>echo apply_filters( 'handle_bulk_actions-toplevel_page_zapper-table', 'admin.php?page=zapper-table', 'page_edit_usermeta',\$bulk_user_ids )</code>";
echo apply_filters( "handle_bulk_actions-toplevel_page_zapper-table", 'admin.php?page=zapper-table', 'page_edit_usermeta',$bulk_user_ids );

    echo "<br><strong>viewing:</strong> " . __FILE__;
    if(isset($_REQUEST['sms_usergroup'])){
        $zapper_table->usermeta_form_field_sms_usergroup_update($_GET['user_id']);
    }
if(isset($_REQUEST['action']) && ($_REQUEST['action'] == 'view_usermeta')){
$rmvd_query_args = remove_query_arg( '_wp_http_referer', $zapper_table->page_view_usermeta($_REQUEST['user_id']) );
echo $rmvd_query_args;
}
elseif(isset($_REQUEST['action']) && ($_REQUEST['action'] == 'edit_usermeta')){
    $rmvd_query_args = remove_query_arg( '_wp_http_referer', $zapper_table->page_edit_usermeta($_REQUEST['user_id']) );
echo $rmvd_query_args;
}
elseif(isset($_REQUEST['action']) && ($_REQUEST['action'] == 'view_all' || $_REQUEST['action'] == 'edit_all')) {
    $rmvd_query_args = remove_query_arg( '_wp_http_referer', $zapper_table->page_bulk_edit($_REQUEST['users']) );
    echo $rmvd_query_args;
}

// $this_plugin_url = WP_PLUGIN_DIR .'/zapper/includes/class-zapper-list-table.php';
// require $this_plugin_url;
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php  ?>

<h1>Viewing <?php echo $currentScreenArray['id']; ?></h1>
    <div class="wrap">
        <div id="poststuff">
            <div id="post-body" class="metabox-holder">
                <div id="post-body-content">
                    <div class="meta-box-sortables ui-sortable">
                        <?php $zapper_table->prepare_items();?>
                        <form id="display_sms_users" method="get">
                            <?php $zapper_table->display(); ?>
                        </form>
                    </div>
                </div>
            </div>
            <br class="clear">
        </div>
    </div>

    <?php
echo '<pre>';

// echo 'var_dump(get_defined_vars()): ' . var_dump(get_defined_vars());
echo 'var_dump(_REQUEST)' . var_dump($_REQUEST);
echo 'var_dump(_POST)' . var_dump($_POST);
echo 'var_dump(_GET)' . var_dump($_GET);
echo 'print_r(rmvd_query_args)' . var_dump($rmvd_query_args);

echo '</pre>';
?>