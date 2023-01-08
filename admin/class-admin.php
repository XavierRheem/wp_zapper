<?php

namespace Zapper_NS\Admin;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @link       https://www.WordPressCenter.net
 * @since      1.0.0
 *
 * @author    Jeff Sabarese
 */
class Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;
	
	/**
	 * The text domain of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_text_domain    The text domain of this plugin.
	 */
	private $plugin_text_domain;
	
	/**
	 * WP_List_Table object
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      Zapper_List_Table_NS    $Zapper_List_Table_NS
	 */
	private $Zapper_List_Table_NS;	

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string $plugin_name	The name of this plugin.
	 * @param    string $version	The version of this plugin.
	 * @param	 string $plugin_text_domain	The text domain of this plugin
	 */
	public function __construct( $plugin_name, $version, $plugin_text_domain ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->plugin_text_domain = $plugin_text_domain;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __DIR__ ) . 'assets/css/zapper_wp-list-table.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		
		$params = array ( 'ajaxurl' => admin_url( 'admin-ajax.php' ) );
		wp_enqueue_script( 'nds_ajax_handle', plugin_dir_url( __DIR__ ) . 'assets/js/zapper_wp-list-table.js', array( 'jquery' ), $this->version, false );				
		wp_localize_script( 'nds_ajax_handle', 'params', $params );		

	}
	
	/**
	 * Callback for the user sub-menu in define_admin_hooks() for class Init.
	 * 
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {
				
		$page_hook = add_menu_page( 
						__( 'Zapper Admin', $this->plugin_text_domain ), //page title
						__( 'Zapper', $this->plugin_text_domain ), //menu title
						'manage_options', //capability
						$this->plugin_name, //menu_slug,
						array( $this, 'load_Zapper_List_Table_NS' ),
						'dashicons-superhero',
						99
					);
		
		/*
		 * The $page_hook_suffix can be combined with the load-($page_hook) action hook
		 * https://codex.wordpress.org/Plugin_API/Action_Reference/load-(page) 
		 * 
		 * The callback below will be called when the respective page is loaded
		 * 		 
		 */				
		add_action( 'load-'.$page_hook, array( $this, 'load_Zapper_List_Table_NS_screen_options' ) );
		
	}
	
	/**
	* Screen options for the List Table
	*
	* Callback for the load-($page_hook_suffix)
	* Called when the plugin page is loaded
	* 
	* @since    1.0.0
	*/
	public function load_Zapper_List_Table_NS_screen_options() {
				
		$arguments	=	array(
						'label'		=>	__( 'Users Per Page', $this->plugin_text_domain ),
						'default'	=>	25,
						'option'	=>	'users_per_page'
					);
		
		add_screen_option( 'per_page', $arguments );
		
		// instantiate the User List Table
		$this->Zapper_List_Table_NS = new Zapper_List_Table_NS( $this->plugin_text_domain );		
		
	}
	
	/*
	 * Display the User List Table
	 * 
	 * Callback for the add_users_page() in the add_plugin_admin_menu() method of this class.
	 * 
	 * @since	1.0.0
	 */
	public function load_Zapper_List_Table_NS(){
		
		// query, filter, and sort the data
		$this->Zapper_List_Table_NS->prepare_items();
		
		// render the List Table
		include_once( 'views/zapper-display.php' );
	}

}
