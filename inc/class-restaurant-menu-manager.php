<?php

/**
 * Class Restaurant_Menu_Manager
 *
 *
 */
class Restaurant_Menu_Manager {
  protected $loader;              // ATTRIBUTE - LOADER CLASS OBJECT
  protected $plugin_slug;         // ATTRIBUTE - SLUG
  protected $version;             // ATTRIBUTE - VERSION

  public function __construct() {
    $this->plugin_slug = 'restaurant-menu-manager-slug';  // SET THE SLUG
    $this->version = '1.1.0';                             // SET THE VERSION
    $this->load_dependencies();                           // LOAD DEPENDENCIES
    $this->define_admin_hooks();                          // SET ALL ADMIN HOOKS
  }
  private function load_dependencies() {

                                                     // admin/class-restaurant-menu-manager-admin.php
    require_once plugin_dir_path( dirname(__FILE__) ) .'admin/class-restaurant-menu-manager-admin.php'; // INCLUDE MANAGER ADMIN CODE
    require_once plugin_dir_path( dirname(__FILE__) ) .'admin/class-restaurant-menu-order-admin.php';   // INCLUDE ORDERING ADMIN CODE
    require_once plugin_dir_path( dirname(__FILE__) ) .'inc/class-restaurant-menu-shortcode.php';       // INCLUDE SHORTCODE CLASS CODE
    require_once plugin_dir_path( dirname(__FILE__) ) .'inc/class-restaurant-menu-manager-loader.php';  // INCLUDE MANAGER LOADER CODE
    $this->loader = new Restaurant_Menu_Manager_Loader();                                               // CREATE NEW LOADER CLASS OBJECT
  }
  private function define_admin_hooks() {


    $admin = new Restaurant_Menu_Manager_Admin( $this->get_version() );                                 // CREATE A NEW MANAGER - ADMIN CLASS
    $this->loader->add_action( 'init', $admin, 'register_cpt' );                                        // REGISTER CUSTOM POST TYPES
    $this->loader->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_styles' );                     // ADD ADMIN STYLES AND SCRIPT REQUIREMENTS
    $this->loader->add_action( 'add_meta_boxes', $admin, 'register_menu_meta_boxes' );                  // REGISTER MENU META BOXES
    $this->loader->add_action( 'save_post', $admin, 'save_menu_tab_meta_box' );                         // SAVE MENU META BOX DATA FOR TAB
    $this->loader->add_action( 'save_post', $admin, 'save_menu_section_meta_box' );                     // SAVE MENU META BOX DATA FOR SECTION
    $this->loader->add_action( 'save_post', $admin, 'save_menu_item_meta_box' );                        // SAVE MENU META BOX DATA FOR ITEM
    $this->loader->add_action( 'admin_init', $admin, 'register_menu_settings' );                        // REGISTER PLUGIN SETTINGS ARRAY
    $this->loader->add_action( 'admin_menu', $admin, 'cpt_sub_menu' );                                  // CREATE A SUBMENU TO HANDLE MENU EDITING **

    $order_admin = new Restaurant_Menu_Order_Admin( $this->get_version() );                             // CREATE A NEW MENU ORDER ADMIN CLASS
    $this->loader->add_action( 'admin_menu', $order_admin, 'ordering_sub_menu' );                       // CREATE A SUBMENU TO HANDLE ORDERING
    $this->loader->add_action( 'wp_ajax_load_items', $order_admin, 'load_items' );                      //
    $this->loader->add_action( 'wp_ajax_load_sections', $order_admin, 'load_sections' );                //
    $this->loader->add_action( 'wp_ajax_save_tab_order', $order_admin, 'save_tab_order' );              //
    $this->loader->add_action( 'wp_ajax_save_section_order', $order_admin, 'save_section_order' );      //
    $this->loader->add_action( 'wp_ajax_save_item_order', $order_admin, 'save_item_order' );            //
    $shorty = new Restaurant_Menu_Shortcode( $this->get_version() );                                    // CREATE A NEW SHORTCODE - CLASS
  }
  public function run() {
    $this->loader->run();
  }
  public function get_version() {
    return $this->version;
  }
}