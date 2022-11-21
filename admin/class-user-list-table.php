<?php

namespace Zapper_NS\Admin;

// maybe the following seems weird but was a hack to pull in the Deafult WP List Table? According to w3schools, this is not the correct use of the "use" keyword.
use Zapper_NS\Includes\Libraries;

class Zapper_List_Table_NS extends Libraries\WP_List_Table  {

    /**
     * The text domain of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_text_domain    The text domain of this plugin.
     */
    protected $plugin_text_domain;
    
    /*
     * Call the parent constructor to override the defaults $args
     * 
     * @param string $plugin_text_domain    Text domain of the plugin.    
     * 
     * @since 1.0.0
     */
    public function __construct( $plugin_text_domain ) {
        
        $this->plugin_text_domain = $plugin_text_domain;
        
        parent::__construct( array( 
                'plural'    =>    'users',    // Plural value used for labels and the objects being listed.
                'singular'    =>    'user',        // Singular label for an object being listed, e.g. 'post'.
                'ajax'        =>    false,        // If true, the parent class will call the _js_vars() method in the footer        
            ) );
    }    
    
    /**
     * Prepares the list of items for displaying.
     * 
     * Query, filter data, handle sorting, and pagination, and any other data-manipulation required prior to rendering
     * 
     * @since   1.0.0
     */
    public function prepare_items() {
        
        // check if a search was performed.
        $user_search_key = isset( $_REQUEST['s'] ) ? wp_unslash( trim( $_REQUEST['s'] ) ) : '';
        
        $this->_column_headers = $this->get_column_info();
        
        // check and process any actions such as bulk actions.
        $this->handle_table_actions();
        
        // fetch table data
        $table_data = $this->fetch_table_data();
        // filter the data in case of a search.
        if( $user_search_key ) {
            $table_data = $this->filter_table_data( $table_data, $user_search_key );
        }        
        
        // required for pagination
        $users_per_page = $this->get_items_per_page( 'users_per_page' );
        $table_page = $this->get_pagenum();        
        
        // provide the ordered data to the List Table.
        // we need to manually slice the data based on the current pagination.
        $this->items = array_slice( $table_data, ( ( $table_page - 1 ) * $users_per_page ), $users_per_page );

        // set the pagination arguments        
        $total_users = count( $table_data );
        $this->set_pagination_args( array (
                    'total_items' => $total_users,
                    'per_page'    => $users_per_page,
                    'total_pages' => ceil( $total_users/$users_per_page )
                ) );
    }
    
    /**
     * Get a list of columns. The format is:
     * 'internal-name' => 'Title'
     *
     * @since 1.0.0
     * 
     * @return array
     */    
    public function get_columns() {
        
        $table_columns = array(
            'cb'                => '<input type="checkbox" />', // to display the checkbox.             
            'user_login'        =>    __( 'User Login', $this->plugin_text_domain ),
            'wpmmv'        =>    __( 'sms_number', $this->plugin_text_domain ),            
            'wpm2mv'    => __( 'sms_usergroup', 'column name', $this->plugin_text_domain ),
            'display_name' => __('Name', $this->plugin_text_domain)
        );
        
        return $table_columns;
           
    }
    
    /**
     * Get a list of sortable columns. The format is:
     * 'internal-name' => 'orderby'
     * or
     * 'internal-name' => array( 'orderby', true )
     *
     * The second format will make the initial sorting order be descending
     *
     * @since 1.1.0
     * 
     * @return array
     */
    protected function get_sortable_columns() {
        
        /*
         * actual sorting still needs to be done by prepare_items.
         * specify which columns should have the sort icon.
         * 
         * key => value
         * column name_in_list_table => columnname in the db
         */
        $sortable_columns = array (
                /* 'ID' => array( 'ID', true ), */
                'user_login'=>'user_login',            
                'wpmmv'=>'wpmmv',
                'wpm2mv'=>'wpm2mv',
                'display_name'=>'display_name'
                
            );
        
        return $sortable_columns;
    }    
    
    /** 
     * Text displayed when no user data is available 
     * 
     * @since   1.0.0
     * 
     * @return void
     */
    public function no_items() {
        _e( 'No users avaliable.', $this->plugin_text_domain );
    }    
    
    /*
     * Fetch table data from the WordPress database.
     * 
     * @since 1.0.0
     * 
     * @return    Array
     */
    
    public function fetch_table_data() {

        global $wpdb;
        
        $wpdb_table = $wpdb->prefix . 'users';        
        $orderby = ( isset( $_GET['orderby'] ) ) ? esc_sql( $_GET['orderby'] ) : 'sms_usergroup';
        $order = ( isset( $_GET['order'] ) ) ? esc_sql( $_GET['order'] ) : 'ASC';
        
        $user_query = 'SELECT DISTINCT wpu.ID, wpu.display_name, wpu.user_login, wpm2.meta_value wpm2mv, wpm.meta_value wpmmv FROM wp_users wpu LEFT JOIN wp_usermeta wpm ON wpm.user_id = wpu.ID LEFT JOIN wp_usermeta wpm2 ON wpm2.user_id = wpu.ID WHERE wpm.meta_key LIKE "sms_usergroup" AND wpm2.meta_key LIKE "sms_number"';

        // query output_type will be an associative array with ARRAY_A.
        $query_results = $wpdb->get_results( $user_query, ARRAY_A  );
        
        // return result array to prepare_items.
        return $query_results;        
    }
    
    /*
     * Filter the table data based on the user search key
     * 
     * @since 1.0.0
     * 
     * @param array $table_data
     * @param string $search_key
     * @returns array
     */
    public function filter_table_data( $table_data, $search_key ) {
        $filtered_table_data = array_values( array_filter( $table_data, function( $row ) use( $search_key ) {
            foreach( $row as $row_val ) {
                if( stripos( $row_val, $search_key ) !== false ) {
                    return true;
                }                
            }            
        } ) );
        
        return $filtered_table_data;
        
    }
        
    /**
     * Render a column when no column specific method exists.
     *
     * @param array $item
     * @param string $column_name
     *
     * @return mixed
     */
    public function column_default( $item, $column_name ) {
        
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
    
    /**
     * Get value for checkbox column.
     *
     * The special 'cb' column
     *
     * @param object $item A row's data
     * @return string Text to be placed inside the column <td>.
     */
    protected function column_cb( $item ) {
        return sprintf(        
                '<label class="screen-reader-text" for="user_' . $item['ID'] . '">' . sprintf( __( 'Select %s' ), $item['user_login'] ) . '</label>'
                . "<input type='checkbox' name='users[]' id='user_{$item['ID']}' value='{$item['ID']}' />"                    
            );
    }
    
    
    /*
     * Method for rendering the user_login column.
     * 
     * Adds row action links to the user_login column.
     * 
     * @param object $item A singular item (one full row's worth of data).
     * @return string Text to be placed inside the column <td>.
     * 
     */
    protected function column_user_login( $item ) {

        /*
         *  Build usermeta row actions.
         * 
         * e.g. /users.php?page=zapper-list-table-ns&action=usergroup_view&user=18&_wpnonce=1984253e5e
         */
        
        $admin_page_url =  admin_url( 'users.php' );
        
        // row actions to view usermeta.
        $query_args_usergroup_view = array(
            'page'        =>  wp_unslash( $_REQUEST['page'] ),
            'action'    => 'usergroup_view',
            'user_id'        => absint( $item['ID']),
            '_wpnonce'    => wp_create_nonce( 'usergroup_view_nonce' ),
        );
        $usergroup_view_link = esc_url( add_query_arg( $query_args_usergroup_view, $admin_page_url ) );        
        $actions['usergroup_view'] = '<a href="' . $usergroup_view_link . '">' . __( 'View Meta', $this->plugin_text_domain ) . '</a>';        
                
        // row actions to add usermeta.
        $query_args_usergroup_edit = array(
            'page'        =>  wp_unslash( $_REQUEST['page'] ),
            'action'    => 'usergroup_edit',
            'user_id'        => absint( $item['ID']),
            '_wpnonce'    => wp_create_nonce( 'usergroup_edit_nonce' ),
        );
        $usergroup_edit_link = esc_url( add_query_arg( $query_args_usergroup_edit, $admin_page_url ) );        
        $actions['usergroup_edit'] = '<a href="' . $usergroup_edit_link . '">' . __( 'Add  Meta', $this->plugin_text_domain ) . '</a>';            
        
        
        $row_value = '<strong>' . $item['user_login'] . '</strong>';
        return $row_value . $this->row_actions( $actions );
    }
    
    /**
     * Returns an associative array containing the bulk action
     *
     * @since    1.0.0
     * 
     * @return array
     */
    public function get_bulk_actions() {

        /*
         * on hitting apply in bulk actions the url paramas are set as
         * ?action=bulk-edit&paged=1&action2=-1
         * 
         * action and action2 are set based on the triggers above or below the table
         *             
         */
         $actions = array(
             'bulk-edit' => 'Download Usermeta'
         );

         return $actions;
    }
    
    /**
     * Process actions triggered by the user
     *
     * @since    1.0.0
     * 
     */    
    public function handle_table_actions() {
        
        /*
         * Note: Table bulk_actions can be identified by checking $_REQUEST['action'] and $_REQUEST['action2']
         * 
         * action - is set if checkbox from top-most select-all is set, otherwise returns -1
         * action2 - is set if checkbox the bottom-most select-all checkbox is set, otherwise returns -1
         */
        
        // check for individual row actions
        $the_table_action = $this->current_action();
        
        if ( 'usergroup_view' === $the_table_action ) {
            $nonce = wp_unslash( $_REQUEST['_wpnonce'] );
            // verify the nonce.
            if ( ! wp_verify_nonce( $nonce, 'usergroup_view_nonce' ) ) {
                $this->invalid_nonce_redirect();
            }
            else {                    
                $this->page_usergroup_view( absint( $_REQUEST['user_id']) );
                $this->graceful_exit();
            }
        }
        
        if ( 'usergroup_edit' === $the_table_action ) {
            $nonce = wp_unslash( $_REQUEST['_wpnonce'] );
            // verify the nonce.
            if ( ! wp_verify_nonce( $nonce, 'usergroup_edit_nonce' ) ) {
                $this->invalid_nonce_redirect();
            }
            else {                    
                $this->page_usergroup_edit( absint( $_REQUEST['user_id']) );
                $this->graceful_exit();
            }
        }
        
        // check for table bulk actions
        if ( ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] === 'bulk-edit' ) || ( isset( $_REQUEST['action2'] ) && $_REQUEST['action2'] === 'bulk-edit' ) ) {
            
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
        }
        
    }
    
    /**
     * View a user's meta information.
     *
     * @since   1.0.0
     * 
     * @param int $user_id  user's ID     
     */
    public function page_usergroup_view( $user_id ) {
        
        $user = get_user_by( 'id', $user_id );        
        include_once( 'views/zapper-view-usermeta.php' );
    }
    
    /**
     * Add a meta information for a user.
     *
     * @since   1.0.0
     * 
     * @param int $user_id  user's ID     
     */    
    
    public function page_usergroup_edit( $user_id ) {
        
        $user = get_user_by( 'id', $user_id );        
        include_once( 'views/zapper-add-usermeta.php' );
    }
    
    /**
     * Bulk process users.
     *
     * @since   1.0.0
     * 
     * @param array $bulk_user_ids
     */        
    public function page_bulk_edit( $bulk_user_ids ) {
                
        include_once( 'views/zapper-bulk-edit.php' );
    }            
    
    /**
     * Stop execution and exit
     *
     * @since    1.0.0
     * 
     * @return void
     */    
     public function graceful_exit() {
         exit;
     }
     
    /**
     * Die when the nonce check fails.
     *
     * @since    1.0.0
     * 
     * @return void
     */         
     public function invalid_nonce_redirect() {
        wp_die( __( 'Invalid Nonce', $this->plugin_text_domain ),
                __( 'Error', $this->plugin_text_domain ),
                array( 
                        'response'     => 403, 
                        'back_link' =>  esc_url( add_query_arg( array( 'page' => wp_unslash( $_REQUEST['page'] ) ) , admin_url( 'users.php' ) ) ),
                    )
        );
     }

     public function usergroup_update($user_id)
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

     public function bulk_usergroup_update($user_id,$usergroup,$smsnumber)
     {
         // check that the current user have the capability to edit the $user_id
         if (!current_user_can('edit_user', $user_id)) {
             return false;
         }
     
         // create/update user meta for the $user_id
         update_user_meta(
             $user_id,
             'sms_usergroup',
             $usergroup
         );
         update_user_meta(
            $user_id,
            'sms_number',
            $smsnumber

         );
     }
}
