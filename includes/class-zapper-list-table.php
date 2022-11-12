<?php

if(!defined('PLUGIN_TEXT_DOMAIN')){
    define('PLUGIN_TEXT_DOMAIN', 'zapper');
}

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/screen.php' );
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Zapper_List_Table extends WP_List_Table
{
    // define $table_data property
    private $table_data;
    
    public function __construct()
    {
        $this->screen = get_current_screen();
        $this->plugin_text_domain = PLUGIN_TEXT_DOMAIN;
    
        parent::__construct( array( 
                'plural'    =>    'users',    // Plural value used for labels and the objects being listed.
                'singular'    =>    'user',        // Singular label for an object being listed, e.g. 'post'.
                'ajax'        =>    false,        // If true, the parent class will call the _js_vars() method in the footer        
            ) );
    }
    // Here we will add our code

    // Get table data
    private function get_table_data($search = '')
    {
        global $wpdb;

        $table = $wpdb->prefix . 'users';

        if (!empty($search)) {
            return $wpdb->get_results(
                "SELECT * from {$table} WHERE name Like '%{$search}%' OR description Like '%{$search}%' OR status Like '%{$search}%'",
                ARRAY_A
            );
        } else {
            return $wpdb->get_results('SELECT DISTINCT wpu.ID, wpu.display_name, wpu.user_login, wpm2.meta_value wpm2mv, wpm.meta_value wpmmv FROM wp_users wpu LEFT JOIN wp_usermeta wpm ON wpm.user_id = wpu.ID LEFT JOIN wp_usermeta wpm2 ON wpm2.user_id = wpu.ID WHERE wpm.meta_key LIKE "sms_usergroup" AND wpm2.meta_key LIKE "sms_number" ORDER BY wpu.ID DESC; ', ARRAY_A);
        }
    }

    // Define table columns
    function get_columns()
    {
        $columns = array(
            'cb'            => '<input type="checkbox" />',
            'user_login'    => __('User Login:', 'PLUGIN_TEXT_DOMAIN'),
            'wpmmv'         => __('User Group:', 'PLUGIN_TEXT_DOMAIN'),
            'wpm2mv'        => __('SMS Number:', 'PLUGIN_TEXT_DOMAIN'),
            'display_name'  => __('Display Name:', 'PLUGIN_TEXT_DOMAIN')
        );
        return $columns;
    }
 
    // Bind table with columns, data and all
    public function prepare_items()
    {
        //data
        if (isset($_POST['s'])) {
            $this->table_data = $this->get_table_data($_POST['s']);
        } else {
            $this->table_data = $this->get_table_data();
        }

        $columns = $this->get_columns();
        $hidden = (is_array(get_user_meta(get_current_user_id(), 'sms_usergroup', true))) ? get_user_meta(get_current_user_id(), 'sms_usergroup', true) : array();
        $sortable = $this->get_sortable_columns();
        $primary  = 'name';
        $this->_column_headers = array($columns, $hidden, $sortable, $primary);

        usort($this->table_data, array(&$this, 'usort_reorder'));

        /* pagination */
        $per_page = $this->get_items_per_page('elements_per_page', 10);
        $current_page = $this->get_pagenum();
        $total_items = count($this->table_data);

        $this->table_data = array_slice($this->table_data, (($current_page - 1) * $per_page), $per_page);

        $this->set_pagination_args(array(
            'total_items' => $total_items, // total number of items
            'per_page'    => $per_page, // items to show on a page
            'total_pages' => ceil($total_items / $per_page) // use ceil to round up
        ));

        $this->items = $this->table_data;
    }

    // set value for each column
    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'ID':
            case 'user_login':
            case 'wpmmv':
            case 'wpm2mv':
            case 'display_name':
                return $item[$column_name];
            default:
                return "no value";
        }
    }

    // Add a checkbox in the first column
/*     function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="element[]" value="%s" />',
            $item['ID']
        );
    } 
*/

    protected function column_cb( $item ) {
    // note that this didn't change func of zapper
        return sprintf(        
                '<label class="screen-reader-text" for="user_' . $item['ID'] . '">' . sprintf( __( 'Select %s' ), $item['user_login'] ) . '</label>'
                . "<input type='checkbox' name='users[]' id='user_{$item['ID']}' value='{$item['ID']}' />"                    
            );
    }
	protected function column_user_login( $item ) 
    {

		/*
		 *  Build meta row actions.
		 * 
		 */
		
		$admin_page_url =  admin_url( 'admin.php' );
		
		// row actions to view meta.
		$query_args_view_usermeta = array(
			'page'		=>  wp_unslash( $_REQUEST['page'] ),
			'action'	=> 'view_usermeta',
			'user_id'		=> absint( $item['ID']),
			'_wpnonce'	=> wp_create_nonce( 'view_usermeta_nonce' ),
		);
		$view_usermeta_link = esc_url( add_query_arg( $query_args_view_usermeta, $admin_page_url ) );		
		$actions['view_usermeta'] = '<a href="' . $view_usermeta_link . '">' . __( 'View Meta', $this->plugin_text_domain ) . '</a>';		
				
		// row actions to add meta.
		$query_args_edit_usermeta = array(
			'page'		=>  wp_unslash( $_REQUEST['page'] ),
			'action'	=> 'edit_usermeta',
			'user_id'		=> absint( $item['ID']),
			'_wpnonce'	=> wp_create_nonce( 'edit_usermeta_nonce' ), 
		);
		$edit_usermeta_link = esc_url( add_query_arg( $query_args_edit_usermeta, $admin_page_url ) );		
		$actions['edit_usermeta'] = '<a href="' . $edit_usermeta_link . '">' . __( 'Edit Meta', $this->plugin_text_domain ) . '</a>';			
		
		
		$row_value = '<strong>' . $item['user_login'] . '</strong>';
		return $row_value . $this->row_actions( $actions );
	}
    // Define sortable column
    protected function get_sortable_columns()
    {
        $sortable_columns = array(
            'user_login'  => array('user_login', false),
            'wpm2mv' => array('wpm2mv', false),
            'wpmmv'   => array('wpmmv', true),
            'display_name'   => array('display_name', false),
        );
        return $sortable_columns;
    }

    // Sorting function
    function usort_reorder($a, $b)
    {
        // If no sort, default to user_login
        $orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'user_login';

        // If no order, default to asc
        $order = (!empty($_GET['order'])) ? $_GET['order'] : 'asc';

        // Determine sort order
        $result = strcmp($a[$orderby], $b[$orderby]);

        // Send final sort direction to usort
        return ($order === 'asc') ? $result : -$result;
    }

    // To show bulk action dropdown
    function get_bulk_actions()
    {
        $actions = array(
            'view_all'    => __('View Multi', 'PLUGIN_TEXT_DOMAIN'),
            'edit_all' => __('Edit Multi', 'PLUGIN_TEXT_DOMAIN')
        );
        return $actions;
    }

    public function handle_table_actions() 
    {
        
        /*
         * Note: Table bulk_actions can be identified by checking $_REQUEST['action'] and $_REQUEST['action2']
         * 
         * action - is set if checkbox from top-most select-all is set, otherwise returns -1
         * action2 - is set if checkbox the bottom-most select-all checkbox is set, otherwise returns -1
         */
        
        // check for individual row actions
        $the_table_action = $this->current_action();
        
        if ( 'view_usermeta' === $the_table_action ) {
            $nonce = wp_unslash( $_REQUEST['_wpnonce'] );
            // verify the nonce.
            if ( ! wp_verify_nonce( $nonce, 'view_usermeta_nonce' ) ) {
                $this->invalid_nonce_redirect();
            }
            else {                    
                $this->page_view_usermeta( absint( $_REQUEST['user_id']) );
                $this->graceful_exit();
            }
        }
        
        if ( 'edit_usermeta' === $the_table_action ) {
            $nonce = wp_unslash( $_REQUEST['_wpnonce'] );
            // verify the nonce.
            if ( ! wp_verify_nonce( $nonce, 'edit_usermeta_nonce' ) ) {
                $this->invalid_nonce_redirect();
            }
            else {                    
                $this->page_edit_usermeta( absint( $_REQUEST['user_id']) );
                $this->graceful_exit();
            }
        }
        
        // check for table bulk actions
        if ( ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] === 'bulk-edit' ) 
          || ( isset( $_REQUEST['action2'] ) && $_REQUEST['action2'] === 'bulk-edit' ) ) {
            
            $nonce = wp_unslash( $_REQUEST['_wpnonce'] );
            // verify the nonce.
            /*
             * Note: the nonce field is set by the parent class
             * wp_nonce_field( 'bulk-' . $this->_args['plural'] );
             * 
             */
           if ( ! wp_verify_nonce( $nonce, 'bulk-users' ) ) {
                $this->invalid_nonce_redirect();
            }
            else {
                $this->page_bulk_edit( $_REQUEST['users']);
                $this->graceful_exit();
            }
            $this->page_bulk_edit( $_REQUEST['users']);
        }
        
    }

    /**
     * View a user's meta information.
     *
     * @since   1.0.0
     * 
     * @param int $user_id  user's ID     
     */
    public function page_view_usermeta( $user_id ) 
    {
        // wp_die();
        
        $user = get_user_by( 'id', $user_id );        
        include_once( WP_PLUGIN_DIR .'/zapper/admin/partials/zapper-view-meta.php' );
        
    }
    
    /**
     * Add a meta information for a user.
     *
     * @since   1.0.0
     * 
     * @param int $user_id  user's ID     
     */    
    
    public function page_edit_usermeta( $user_id ) 
    {
        
        $user = get_user_by( 'id', $user_id );        
        include_once( WP_PLUGIN_DIR .'/zapper/admin/partials/zapper-edit-meta.php' );
    }
    
    /**
     * Bulk process users.
     *
     * @since   1.0.0
     * 
     * @param array $bulk_user_ids
     */        
 public function page_bulkedit( $bulk_user_ids  ) {
	include_once( WP_PLUGIN_DIR .'/zapper/admin/partials/zapper-bulk-edit.php' );
} 
    
    public function no_items() {
        _e( 'No users avaliable.', $this->plugin_text_domain );
    }   

    public function graceful_exit() {
        // exit;
        wp_die();
    }

/*     public function invalid_nonce_redirect() {
        wp_die( __( 'Invalid Nonce', $this->plugin_text_domain ),
                __( 'Error', $this->plugin_text_domain ),
                array( 
                        'response'     => 403, 
                        'back_link' =>  esc_url( add_query_arg( array( 'page' => wp_unslash( $_REQUEST['page'] ) ) , admin_url( 'admin.php' ) ) ),
                    )
        );
    } */

    function usermeta_form_field_sms_usergroup_update($user_id)
    {
        // check that the current user have the capability to edit the $user_id
        if (!current_user_can('edit_user', $user_id)) {
            return false;
        }
    
        // create/update user meta for the $user_id
        return update_user_meta(
            $user_id,
            'sms_usergroup',
            $_REQUEST['sms_usergroup']
        );
    }

	/**
	 * @return mixed
	 */
	public function getTable_data() {
		return $this->table_data;
	}
	
	/**
	 * @param mixed $table_data 
	 * @return self
	 */
	public function setTable_data($table_data): self {
		$this->table_data = $table_data;
		return $this;
	}

 
 


}


