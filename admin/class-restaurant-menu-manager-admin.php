<?php

/**
 * Manager Admin Class: Restaurant_Menu_Manager_Admin
 *
 * @since 1.2.0
 *
 *
 */
class Restaurant_Menu_Manager_Admin {
  private $version;

  /*
   *   Class Contructor - Sets This Class Version
   *
   */
  public function __construct( $version ) {
    $this->version = $version;
  }
  /*
   *   Enqueue Styles and Scripts
   *
   */
  public function enqueue_styles() {
    wp_enqueue_style('restaurant-menu-manager-admin', plugin_dir_url( __FILE__ ) . 'css/admin.css', array(),$this->version, FALSE);
    wp_enqueue_script ('jquery-ui-sortable');
  }

  /*
   *   C P T _ S U B _ M E N U
   *
   *   SETS UP THE SUB MENU FOR THE CUSTOM POST TYPES
   */
  public function cpt_sub_menu() {
    // add_submenu_page( parent_slug, page_title, menu_title, capability, menu_slug, function )
    add_submenu_page( 'edit.php?post_type=rs_menu_section', 'Menu Section', 'Menu Section', 'manage_options', 'rest-menu-sections', array( $this, 'menu_settings_page' ) );
  }
  /*
   *
   *   Register Custom Post Types
   *
   *   Tabs:                rs_menu_tab
   *     CPT Meta
   *        Tab Order            _menu_tab_order
   *        Tab Columns          _menu_tab_columns
   *
   *   Sections:            rs_menu_section
   *     CPT Meta
   *        Tab ID               _menu_tab_id
   *        Section Column       _menu_section_col
   *        Section Row          _menu_section_row
   *
   *
   *   Items:               rs_menu_item
   *     CPT Meta
   *        Section ID           _menu_section_id
   *        Item Order           _menu_item_order
   *        Item Title           _menu_item_title
   *        Item Price           _menu_item_price
   *
   */
  public function register_cpt() {

    register_post_type( 'rs_menu_tab', array(
      'label'               => __( 'Menu Tab', 'dhd_rs1' ),
      'description'         => __( 'Menu Tabs', 'dhd_rs1' ),
      'labels'              => array(
          'name'               => 'Menu Tabs',
          'singular_name'      => 'Menu Tab',
          'add_new'            => 'Add New',
          'add_new_item'       => 'Add New Menu Tab',
          'edit_item'          => 'Edit Menu Tab',
          'new_item'           => 'New Menu Tab',
          'all_items'          => 'Menu Tabs',
          'view_item'          => 'View Menu Tab',
          'search_items'       => 'Search Menu Tabs',
          'not_found'          => 'No Menu Tab found',
          'not_found_in_trash' => 'No Menu Tab found in Trash',
          'menu_name'          => 'Menu Tab' ),
      'supports'            => array( 'title', 'editor', 'thumbnail' ),
      'hierarchical'        => false,
      'public'              => true,
      'show_ui'             => true,
      'show_in_menu'        => true,
      'show_in_nav_menus'   => true,
      'show_in_admin_bar'   => true,
      'menu_position'       => 6,
      'can_export'          => true,
      'has_archive'         => true,
      'exclude_from_search' => false,
      'publicly_queryable'  => true,
      'capability_type'     => 'post',
      'menu_icon'           => 'dashicons-format-aside',
    ) );

    register_post_type( 'rs_menu_section', array(
      'label'               => __( 'Menu Section', 'dhd_rs1' ),
      'description'         => __( 'Menu Sections', 'dhd_rs1' ),
      'labels'              => array(
          'name'               => 'Menu Sections',
          'singular_name'      => 'Menu Section',
          'add_new'            => 'Add New',
          'add_new_item'       => 'Add New Menu Section',
          'edit_item'          => 'Edit Menu Section',
          'new_item'           => 'New Menu Section',
          'all_items'          => 'Menu Sections',
          'view_item'          => 'View Menu Section',
          'search_items'       => 'Search Menu Sections',
          'not_found'          => 'No Menu Section found',
          'not_found_in_trash' => 'No Menu Section found in Trash',
          'menu_name'          => 'Menu Section' ),
      'supports'            => array( 'title', 'editor', 'thumbnail' ),
      'hierarchical'        => false,
      'public'              => true,
      'show_ui'             => true,
      'show_in_menu'        => 'edit.php?post_type=rs_menu_tab',
      'show_in_nav_menus'   => true,
      'show_in_admin_bar'   => true,
      'can_export'          => true,
      'has_archive'         => true,
      'exclude_from_search' => false,
      'publicly_queryable'  => true,
      'capability_type'     => 'post',
      'menu_icon'           => 'dashicons-format-aside',
    ) );

    register_post_type( 'rs_menu_item', array(
      'label'               => __( 'Menu Item', 'dhd_rs1' ),
      'description'         => __( 'Menu Items', 'dhd_rs1' ),
      'labels'              => array(
          'name'               => 'Menu Items',
          'singular_name'      => 'Menu Item',
          'add_new'            => 'Add New',
          'add_new_item'       => 'Add New Menu Item',
          'edit_item'          => 'Edit Menu Item',
          'new_item'           => 'New Menu Item',
          'all_items'          => 'Menu Items',
          'view_item'          => 'View Menu Item',
          'search_items'       => 'Search Menu Items',
          'not_found'          => 'No Menu Item found',
          'not_found_in_trash' => 'No Menu Item found in Trash',
          'menu_name'          => 'Menu Item' ),
      'supports'            => array( 'title', 'editor', 'thumbnail' ),
      'hierarchical'        => false,
      'public'              => true,
      'show_ui'             => true,
      'show_in_menu'        => 'edit.php?post_type=rs_menu_tab',
      'show_in_nav_menus'   => true,
      'show_in_admin_bar'   => true,
      'can_export'          => true,
      'has_archive'         => true,
      'exclude_from_search' => false,
      'publicly_queryable'  => true,
      'capability_type'     => 'post',
      'menu_icon'           => 'dashicons-format-aside',
    ) );

  }
  /*
   *   Sanitizer
   */
  public function sanitize_taxonomy_order( $order ) {
    $order = sanitize_text_field( $order );
    $order = filter_var($order, FILTER_SANITIZE_NUMBER_INT);
    return $order;
  }

  /*
   *   Register Menu Meta Boxes
   *
   *   Tabs:                rs_menu_tab
   *     CPT Meta
   *        Tab Order            _menu_tab_order        Textbox
   *        Tab Columns          _menu_tab_columns      Textbox
   *
   *   Sections:            rs_menu_section
   *     CPT Meta
   *        Tab ID               _menu_tab_id           Select
   *        Section Column       _menu_section_col      Textbox
   *        Section Row          _menu_section_row      Textbox
   *
   *
   *   Items:               rs_menu_item
   *     CPT Meta
   *        Section ID           _menu_section_id       Select
   *        Item Order           _menu_item_order       Textbox
   *        Item Title           _menu_item_title       Textbox
   *        Item Price           _menu_item_price       Textbox
   */
  public function register_menu_meta_boxes() {
    add_meta_box( 'dhd_rs1_menu_tab_meta',     __( 'Menu Tab Options', 'dhd_restaurant1' ),     array( $this, 'menu_tab_meta_box' ),     'rs_menu_tab',     'normal', 'default' );
    add_meta_box( 'dhd_rs1_menu_section_meta', __( 'Menu Section Options', 'dhd_restaurant1' ), array( $this, 'menu_section_meta_box' ), 'rs_menu_section', 'normal', 'default' );
    add_meta_box( 'dhd_rs1_menu_item_meta',    __( 'Menu Item Options', 'dhd_restaurant1' ),    array( $this, 'menu_item_meta_box' ),    'rs_menu_item',    'normal', 'default' );
  }


  /*
   *   Meta Box For Menu Tab                                                              T  A  B      M  E  T  A      B  O  X
   *
   *   Tabs:                rs_menu_tab
   *     CPT Meta
   *        Tab Order            _menu_tab_order        Textbox
   *        Tab Columns          _menu_tab_columns      Textbox
   */
  public function menu_tab_meta_box( $post ) {
    // RETRIEVE OUR CUSTOM META BOX VALUES
    $menu_tab_order = get_post_meta( $post->ID, '_menu_tab_order', true );
    $menu_tab_columns = get_post_meta( $post->ID, '_menu_tab_columns', true );
    // NONCE FIELD FOR SECURITY
    wp_nonce_field( 'save_menu_tab_meta', 'dhd_restaurant1_menu_tab_meta_box' );

    // DISPLAY META BOX FORM
    echo '<div>';
    echo 'Tab Order: ';
    if ( $menu_tab_order ) {
      echo '<input type="text" name="menu_tab_order" id="menu_tab_order" value="'. $menu_tab_order .'" style="width:3rem;" />';
    } else {
      echo '<input type="text" name="menu_tab_order" id="menu_tab_order" value="0" style="width:3rem;" />';
    }
    echo ' Columns: ';
    if ( $menu_tab_columns ) {
      echo '<input type="text" name="menu_tab_columns" id="menu_tab_columns" value="'. $menu_tab_columns .'" style="width:3rem;" />';
    } else {
      echo '<input type="text" name="menu_tab_columns" id="menu_tab_columns" value="0"  style="width:3rem;" />';
    }
    echo '</div>';

  }
  /*
   *   Save Data For Menu Tab Meta Box                                                    S  A  V  E      T  A  B      M  E  T  A      B  O  X
   *
   *   Meta Box For Menu Tab
   *
   *   Tabs:                rs_menu_tab
   *     CPT Meta
   *        Tab Order            _menu_tab_order        Textbox
   *        Tab Columns          _menu_tab_columns      Textbox
   *
   */
  public function save_menu_tab_meta_box( $post_id ) {
    if ( get_post_type( $post_id ) == 'rs_menu_tab' ) {
      if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return;
      wp_verify_nonce( 'save_menu_tab_meta', 'dhd_restaurant1_menu_tab_meta_box' );

      if ( isset( $_POST['menu_tab_order'] ) ) {
        $menu_tab_order = $_POST['menu_tab_order'];
        update_post_meta( $post_id, '_menu_tab_order', $menu_tab_order );
      }
      if ( isset( $_POST['menu_tab_columns'] ) ) {
        $menu_tab_columns = $_POST['menu_tab_columns'];
        update_post_meta( $post_id, '_menu_tab_columns', $menu_tab_columns );
      }
    }
  }

  /*
   *   Meta Box For Menu Section                                                          S  E  C  T  I  O  N      M  E  T  A      B  O  X
   *
   *   Sections:            rs_menu_section
   *     CPT Meta
   *        Tab ID               _menu_tab_id           Select
   *        Section Column       _menu_section_col      Textbox
   *        Section Row          _menu_section_row      Textbox
   *
   */
  public function menu_section_meta_box( $post ) {
    // RETRIEVE OUR CUSTOM META BOX VALUES
    $menu_tab_id = get_post_meta( $post->ID, '_menu_tab_id', true );
    $menu_section_col = get_post_meta( $post->ID, '_menu_section_col', true );
    $menu_section_row = get_post_meta( $post->ID, '_menu_section_row', true );
    // NONCE FIELD FOR SECURITY
    wp_nonce_field( 'save_menu_section_meta', 'dhd_restaurant1_menu_section_meta_box' );

    // DISPLAY META BOX FORM
    echo '<div>Select Tab: ';
    echo $this->menu_tab_select( $menu_tab_id );
    echo ' Section Column: ';

    if ( $menu_section_col ) {
      echo '<input type="text" name="menu_section_col" id="menu_section_col" value="'. $menu_section_col .'" style="width:3rem;" />';
    } else {
      echo '<input type="text" name="menu_section_col" id="menu_section_col" value="0" style="width:3rem;" />';
    }
    echo ' Section Row: ';
    if ( $menu_section_row ) {
      echo '<input type="text" name="menu_section_row" id="menu_section_row" value="'. $menu_section_row .'" style="width:3rem;" />';
    } else {
      echo '<input type="text" name="menu_section_row" id="menu_section_row" value="0" style="width:3rem;" />';
    }
    echo '</div>';
  }

  /*
   *   Selection Box For Menu Tabs                                                        D  D  L      M  E  N  U      T  A  B  S
   */
  public function menu_tab_select( $menu_tab_id ) {
    $htm_ddl = '';
    $mnMenuTabs = new WP_Query( array(
        'posts_per_page'  => -1,
        'post_type'       => 'rs_menu_tab',
    ) );
    if ( $mnMenuTabs->have_posts() ) :
      $htm_ddl = '<select id="menu_tab_id" name="menu_tab_id" class="">';
      while ( $mnMenuTabs->have_posts() ) :
        $mnMenuTabs->the_post();
        if (get_the_ID() == $menu_tab_id) :
          $htm_ddl .= '<option value="'. get_the_ID() .'" selected>'. get_the_title() .'</option>';
        else :
          $htm_ddl .= '<option value="'. get_the_ID() .'">'. get_the_title() .'</option>';
        endif;
      endwhile;
      $htm_ddl .= '</select>';
    else :
      $htm_ddl = '<select id="menu_tab_id" class=""><option value="">No Tabs To Choose</option></select>';
    endif;
    wp_reset_postdata(); // RESET POST DATA
    return $htm_ddl;
  }

  /*
   *   Save Data For Menu Section Meta Box                                                S  A  V  E      S  E  C  T  I  O  N      M  E  T  A      B  O  X
   *
   *   Sections:            rs_menu_section
   *     CPT Meta
   *        Tab ID               _menu_tab_id           Select
   *        Section Column       _menu_section_col      Textbox
   *        Section Row          _menu_section_row      Textbox
   *
   */
  public function save_menu_section_meta_box( $post_id ) {
    if ( get_post_type( $post_id ) == 'rs_menu_section' ) {
      if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return;
      wp_verify_nonce( 'save_menu_section_meta', 'dhd_restaurant1_menu_section_meta_box' );

      if ( isset( $_POST['menu_tab_id'] ) ) {
        $this->write_out_log("Tab ID Set\n");
        $menu_tab_id = $_POST['menu_tab_id'];
        update_post_meta( $post_id, '_menu_tab_id', $menu_tab_id );
      } else {
        $this->write_out_log("Tab ID Not Set\n");
      }
      if ( isset( $_POST['menu_section_col'] ) ) {
        $menu_section_col = $_POST['menu_section_col'];
        update_post_meta( $post_id, '_menu_section_col', $menu_section_col );
      }
      if ( isset( $_POST['menu_section_row'] ) ) {
        $menu_section_row = $_POST['menu_section_row'];
        update_post_meta( $post_id, '_menu_section_row', $menu_section_row );
      }
    }
  }



  /*
   *   Meta Box For Menu Item                                                             I  T  E  M      M  E  T  A      B  O  X
   *
   *   Items:               rs_menu_item
   *     CPT Meta
   *        Section ID           _menu_section_id       Select
   *        Item Order           _menu_item_order       Textbox
   *        Item Title           _menu_item_title       Textbox
   *        Item Price           _menu_item_price       Textbox
   *
   */
  public function menu_item_meta_box( $post ) {
    // RETRIEVE OUR CUSTOM META BOX VALUES
    $menu_section_id = get_post_meta( $post->ID, '_menu_section_id', true );
    $menu_item_order = get_post_meta( $post->ID, '_menu_item_order', true );
    $menu_item_title = get_post_meta( $post->ID, '_menu_item_title', true );
    $menu_item_price = get_post_meta( $post->ID, '_menu_item_price', true );
    // NONCE FIELD FOR SECURITY
    wp_nonce_field( 'save_menu_item_meta', 'dhd_restaurant1_menu_item_meta_box' );

    // DISPLAY META BOX FORM
    echo '<div>Select Section: ';
    echo $this->menu_section_select( $menu_section_id );
    echo ' Item Order: ';
    if ( $menu_item_order ) {
      echo '<input type="text" name="menu_item_order" id="menu_item_order" value="'. $menu_item_order .'" style="width:3rem;" />';
    } else {
      echo '<input type="text" name="menu_item_order" id="menu_item_order" value="0" style="width:3rem;" />';
    }
    echo ' Title: ';
    if ( $menu_item_title ) {
      echo '<input type="text" name="menu_item_title" id="menu_item_title" value="'. $menu_item_title .'" style="width:30rem;" />';
    } else {
      echo '<input type="text" name="menu_item_title" id="menu_item_title" value=""  style="width:30rem;" />';
    }
    echo ' Price: ';
    if ( $menu_item_price ) {
      echo '<input type="text" name="menu_item_price" id="menu_item_price" value="'. $menu_item_price .'" style="width:30rem;" />';
    } else {
      echo '<input type="text" name="menu_item_price" id="menu_item_price" value="0.00" style="width:30rem;" />';
    }
    echo '</div>';
  }


  /*
   *   Save Data For Menu Item Meta Box                                                   S  A  V  E      I  T  E  M      M  E  T  A      B  O  X
   *
   *   Items:               rs_menu_item
   *     CPT Meta
   *        Section ID           _menu_section_id       Select
   *        Item Order           _menu_item_order       Textbox
   *        Item Title           _menu_item_title       Textbox
   *        Item Price           _menu_item_price       Textbox
   *
   */
  public function save_menu_item_meta_box( $post_id ) {
    if ( get_post_type( $post_id ) == 'rs_menu_item' ) {
      if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return;
      wp_verify_nonce( 'save_menu_item_meta', 'dhd_restaurant1_menu_item_meta_box' );

      if ( isset( $_POST['menu_section_id'] ) ) {
        $menu_section_id = $_POST['menu_section_id'];
        update_post_meta( $post_id, '_menu_section_id', $menu_section_id );
      }
      if ( isset( $_POST['menu_item_order'] ) ) {
        $menu_item_order = $_POST['menu_item_order'];
        update_post_meta( $post_id, '_menu_item_order', $menu_item_order );
      }
      if ( isset( $_POST['menu_item_title'] ) ) {
        $menu_item_title = $_POST['menu_item_title'];
        update_post_meta( $post_id, '_menu_item_title', $menu_item_title );
      }
      if ( isset( $_POST['menu_item_price'] ) ) {
        $menu_item_price = $_POST['menu_item_price'];
        update_post_meta( $post_id, '_menu_item_price', $menu_item_price );
      }
    }
  }

  /*
   *   Selection Box For Menu Sections
   *
   */
  public function menu_section_select( $menu_sect_id ) {
    $htm_ddl = '';
    $mnMenuSections = new WP_Query( array(
        'posts_per_page'  => -1,
        'post_type'       => 'rs_menu_section',
    ) );
    if ( $mnMenuSections->have_posts() ) :
      $htm_ddl = '<select id="menu_section_id" name="menu_section_id" class="">';
      while ( $mnMenuSections->have_posts() ) :
        $mnMenuSections->the_post();
        if (get_the_ID() == $menu_sect_id) :
          $htm_ddl .= '<option value="'. get_the_ID() .'" selected>'. get_the_title() .'</option>';
        else :
          $htm_ddl .= '<option value="'. get_the_ID() .'">'. get_the_title() .'</option>';
        endif;
      endwhile;
      $htm_ddl .= '</select>';
    else :
      $htm_ddl = '<select id="menu_section_id" class=""><option value="">No Sections To Choose</option></select>';
    endif;
    wp_reset_postdata(); // RESET POST DATA
    return $htm_ddl;
  }


  /*
   *   Register Menu Settings
   *
   */
  public function register_menu_settings() {
    register_setting( 'restaurant_menu-settings-group', 'restaurant_menu_options', 'menu_sanitize_options' );
  }

  /*
   *   Sanitizer For Menu Settings
   *
   */
  public function menu_sanitize_options( $options ) {
    $options['show_cents']     = ( ! empty( $options['show_cents'] ) ) ? sanitize_text_field( $options['show_cents'] ) : '';
    $options['dot_lead']       = ( ! empty( $options['dot_lead'] ) ) ? sanitize_text_field( $options['dot_lead'] ) : '';
    $options['currency_sign']  = ( ! empty( $options['currency_sign'] ) ) ? sanitize_text_field( $options['currency_sign'] ) : '';
    return $options;
  }

  /*
   *   Add Meta Box
   *
   */
  public function add_meta_box() {
    add_meta_box('restaurant-menu-manager-admin', 'Restaurant Menu Manager', array( $this, 'render_meta_box' ), 'post', 'normal', 'core');
  }

  /*
   *   Render Meta Box
   *
   */
  public function render_meta_box() {
    require_once plugin_dir_path( __FILE__ ) . 'partials/restaurant-menu-manager.php';
  }

  /*
   *   Write Out Log (Used For Debugging)
   *
   */
  public function write_out_log( $log_str ) {

    $dirfile = plugin_dir_path( dirname(__FILE__) ) .'admin/logFile.txt';

    $myfile = fopen( $dirfile, 'w') or die('Unable to open file!');
    fwrite($myfile, $log_str);
    fclose($myfile);
  }




}
