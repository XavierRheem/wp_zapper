<?php
$zapper_table = $this->Zapper_List_Table_NS;
$current_zapper_screen = get_current_screen();
$zapper_screen_array = (array) $current_zapper_screen; 

    if(isset($_REQUEST['sms_usergroup'])){
        $zapper_table->usergroup_update($_GET['user_id']);
    }
	elseif(isset($_REQUEST['bulk_user_id'])){
		foreach($_REQUEST['bulk_user_id'] as $bulk_ukey => $bulk_uval){
			$zapper_table->bulk_usergroup_update($bulk_uval,$_REQUEST['bulk_sms_usergroup'][$bulk_ukey],$_REQUEST['bulk_sms_number'][$bulk_ukey]);
		}

	}
if(isset($_REQUEST['action']) && ($_REQUEST['action'] == 'usergroup_view')){
$rmvd_query_args = remove_query_arg( '_wp_http_referer', $zapper_table->page_usergroup_view($_REQUEST['user_id']) );
echo $rmvd_query_args;
}
elseif(isset($_REQUEST['action']) && ($_REQUEST['action'] == 'usergroup_edit')){
    $rmvd_query_args = remove_query_arg( '_wp_http_referer', $zapper_table->page_edit_usermeta($_REQUEST['user_id']) );
echo $rmvd_query_args;
}
elseif(isset($_REQUEST['action']) && ($_REQUEST['action'] == 'selected_bulk_edit' || $_REQUEST['action2'] == 'selected_bulk_edit')) {
    $rmvd_query_args = remove_query_arg( '_wp_http_referer', $zapper_table->page_bulk_edit($_REQUEST['users']) );
    echo $rmvd_query_args;
}


?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php  ?>

<div class="wrap">    
    <h2><?php _e( 'Zapper: Your SMS Notifier', $this->plugin_text_domain); ?></h2>
        <div id="zapper-list-table-ns">			
            <div id="post-body">		
				<form id="user-list-form" method="get">
					<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
					<?php 
						$this->Zapper_List_Table_NS->search_box( __( 'Find', $this->plugin_text_domain ), 'nds-user-find');
						$this->Zapper_List_Table_NS->display(); 
					?>					
				</form>
            </div>			
        </div>
</div>
<?php

/* echo '<pre>';
//echo 'var_dump(get_defined_vars()): ' . var_dump(get_defined_vars());
echo 'var_dump(_REQUEST)' . var_dump($_REQUEST);
echo 'var_dump(_POST)' . var_dump($_POST);
echo 'var_dump(_GET)' . var_dump($_GET); 
echo '</pre>'; */

?>