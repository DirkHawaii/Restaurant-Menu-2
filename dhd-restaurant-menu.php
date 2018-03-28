<?php
/*
Plugin Name: DHD Restaurant Menu
Plugin URI: https://github.com/dirkharriman/RestMenu
Description: A plugin for building and maintaining a restaurant menu
Version: 1.1.0
Author: Dirk Harriman
Author URI: http://www.dirkharriman.com
License: GPLv2

Copyright 2017  Dirk Harriman  (email : dirkharriman@yahoo.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 *
 *   R O O T   F I L E
 *
 */
if ( ! defined( 'WPINC' ) ) {
  die;
}
register_activation_hook( __FILE__, 'restaurant_menu_install' );  // CALL FUNCTION WHEN PLUGIN IS ACTIVATED
/**
 *   I N S T A L L   P L U G I N
 */
function restaurant_menu_install() {

  /**   SET UP DEFAULT PLUGIN OPTIONS:
   *      CURRENCY SIGN: currency_sign
   *      SHOW CENTS:    show_cents
   *      DOT LEADER:    dot_leader
   */
  update_option( 'restaurant_menu_options', array( 'currency_sign' => '$', 'show_cents' => true, 'dot_lead' => true ) );  // SAVE OUR DEFAULT OPTION VALUES
}
require_once plugin_dir_path( __FILE__ ) . 'inc/class-restaurant-menu-manager.php';


function run_restaurant_menu_manager() {
  $rmm = new Restaurant_Menu_Manager();
  $rmm->run();
}

run_restaurant_menu_manager();



