<?php

/**
 * Order Admin Class: Restaurant_Menu_Order_Admin
 *
 */

class Restaurant_Menu_Order_Admin {
  private $version;

  /*
   *   C O N S T R U C T O R
   *
   *   CLASS CONSTRUCTOR
   */
  public function __construct( $version ) {
    $this->version = $version;
  }

  /*
   *   O R D E R I N G _ S U B _ M E N U
   *
   *   SETS UP THE SUB MENU FOR ORDERING AND ADDS JAVASCRIPT TO HANDLE AJAX
   */
  public function ordering_sub_menu() {
    $my_settings_page = add_submenu_page( 'edit.php?post_type=rs_menu_tab', 'Menu Order', 'Menu Order', 'manage_options', 'rest-menu-order-settings', array( $this, 'menu_settings_page' ) );

    add_action( "admin_head-{$my_settings_page}", array( $this, 'menu_order_head_script' ) );  // ADDS JAVASCRIPT FUNCTIONS TO HEADER OF THIS ADMIN PAGE
  }

  /*
   *   M E N U _ S E T T I N G S _ P A G E
   *
   *   THE LANDING PAGE FOR THE SUB MENU FOR ORDERING
   */
  public function menu_settings_page() {
    $restmenu_options_arr = get_option( 'restaurant_menu_options' );  // LOAD THE PLUGIN OPTIONS ARRAY
    $show_cents = ( ! empty( $restmenu_options_arr['show_cents'] ) ) ? $restmenu_options_arr['show_cents'] : false;
    $dot_lead = ( ! empty( $restmenu_options_arr['dot_lead'] ) ) ? $restmenu_options_arr['dot_lead'] : false;
    $currency_sign = ( ! empty( $restmenu_options_arr['currency_sign'] ) ) ? $restmenu_options_arr['currency_sign'] : '';
    ?>
    <div class="wrap">
    <form method="post" action="options.php">
      <?php settings_fields( 'restaurant_menu-settings-group' ); ?>
      <input type="hidden" id="hid_menu_tab_columns" />
      <div id="panel1">
        <h2><?php _e( 'Restaurant Menu Options', 'restaurant_menu-plugin' ) ?></h2>
        <?php _e( 'Show Cents ', 'restaurant_menu-plugin' ) ?><input type="checkbox" name="restaurant_menu_options[show_cents]" <?php echo checked( $show_cents, 1 ); ?> />
        <?php _e( 'Show Dot Leader ', 'restaurant_menu-plugin' ) ?><input type="checkbox" name="restaurant_menu_options[dot_lead]" <?php echo checked( $dot_lead, 1 ); ?> />
        <?php _e( 'Currency Sign ', 'restaurant_menu-plugin' ) ?><input type="text" name="restaurant_menu_options[currency_sign]" value="<?php echo esc_attr( $currency_sign ); ?>" size="1" maxlength="1" />
        <input type="submit" class="button-primary" value="<?php _e( 'Save Options', 'restaurant_menu-plugin' ); ?>" /><br/>
        <h2><?php _e( 'Menu Tab Sorting', 'restaurant_menu-plugin' ) ?></h2>
        <p>Drag the tab into the position you want and then click on the <em>Save Tab Order</em> button to save the changes.</p>
        <p>To edit the Tab Section ordering, click on the <em>Edit Section Order</em> button.</p>
        <div class="ctrl-wrap bg1">
          <div class="ctrl-left">
          <?php
            $menu_tabs = new WP_Query( array(
                'posts_per_page'  => -1,
                'post_type'       => 'rs_menu_tab',
                'orderby'         => 'meta_value_num',
                'order'           =>  'ASC',
                'meta_key'        => '_menu_tab_order',
            ) );
            if ( $menu_tabs->have_posts() ) :
              $menu_tab_list = '<ul id="menu_tab_list">';
              while ( $menu_tabs->have_posts() ) :  // TAB LOOP
                $menu_tabs->the_post();
                $menu_tab_columns = get_post_meta( get_the_ID(), '_menu_tab_columns', true );  // THE NUMBER OF COLUMNS IN THE TAB

                // <input type="button" onclick="location.href='http://google.com';" value="Go to Google" />

                $menu_tab_list .= '<li value="'. get_the_ID() .'">'. get_the_title() .' <input type="button" class="btn1" onclick="location.href=\'post.php?post='. get_the_ID() .'&action=edit\';" value="Edit" /> <input type="button" class="btn1" value="Sort Sections" onclick="javascript:show_section('. get_the_ID() .','. $menu_tab_columns .')" /></li>';
              endwhile;
              $menu_tab_list .= '</ul>';
              echo $menu_tab_list;
            else :
              ?><h1>Found Nothing</h1><?php
            endif;
          ?>
          </div>
            <div class="ctrl-right">
              <input id="save_tab_order" type="button" class="button-primary" value="<?php _e( 'Save Tab Order', 'restaurant_menu-plugin' ); ?>" />
            </div>
          </div>
        </div>
        <div id="panel2">
          <h2><?php _e( 'Menu Section Sorting', 'restaurant_menu-plugin' ) ?></h2>
          <p>Drag the section into the position you want and then click on the <em>Save Section Order</em> button to save the changes.</p>
          <p>To edit the Section Item ordering, click on the <em>Edit Item Order</em> button.</p>
          <div class="ctrl-wrap bg2">
            <div class="ctrl-left">
              <div id="menu_wrap"></div>
            </div>
            <div class="ctrl-right">
              <input id="save_section_order" type="button" class="button-primary" value="<?php _e( 'Save Section Order', 'restaurant_menu-plugin' ); ?>" />
            </div>
          </div>
        </div>
        <div id="panel3">
          <h2><?php _e( 'Section Item Sorting', 'restaurant_menu-plugin' ) ?></h2>
          <p>Drag the item into the position you want and then click on the <em>Save Item Order</em> button to save the changes.</p>
          <div class="ctrl-wrap bg1">
            <div class="ctrl-left">
              <ul id="menu_item_list"></ul>
            </div>
            <div class="ctrl-right">
              <input id="save_item_order" type="button" class="button-primary" value="<?php _e( 'Save Item Order', 'restaurant_menu-plugin' ); ?>" />
            </div>
          </div>
        </div>
      </form>
      </div>
    <?php
  }
  /*
   *   G E T _ I T E M _ L I S T
   *
   *   RETURNS AN ARRAY OF MENU SECTION ITEMS
   *
   */
  public function get_item_list( $section_id ) {
    $item_ar = array();
    /*   GET MENU ITEMS   */
    $menu_items = new WP_Query( array(
        'posts_per_page'  => -1,
        'post_type'       => 'rs_menu_item',
        'meta_query'      => array(
          'get_clause' => array( 'key' => '_menu_section_id', 'value' => $section_id ),
          'row_clause' => array( 'key' => '_menu_item_order', 'compare' => 'EXISTS', 'type' => 'NUMERIC', ),
        ),
        'orderby'  => array(
          'row_clause' => 'ASC',
        ),
    ) );
    if ( $menu_items->have_posts() ) :
      while ( $menu_items->have_posts() ) :  // ITEM LOOP
        $menu_items->the_post();
        $item_ar[] = get_the_title();
      endwhile;
      wp_reset_postdata();
    endif;
    return $item_ar;
  }

  /*
   *   S A V E _ T A B _ O R D E R
   *
   *   SAVE THE SORT ORDER OF THE MENU TABS SET ON THE ADMIN PAGE
   *
   *   META KEY: _menu_tab_order
   *
   */
  public function save_tab_order() {
    global $wpdb; // THIS IS HOW YOU GET ACCESS TO THE DATABASE

    $tab_list = $_POST['tab_list'];
    $tab_ar = explode(',', $tab_list);
    $result_str = '';
    $sort_num = 0;
    $term_id = 0;

    foreach ($tab_ar as $i => $value) {
      $sort_num++;
      update_post_meta( $value, '_menu_tab_order', $sort_num );
      $result_str .=  ' - '. $value;
    }
    echo 'Order Updated '. $result_str;
    wp_die();
  }

  /*
   *   S A V E _ S E C T I O N _ O R D E R
   *
   *   SAVE THE SORT ORDER OF THE MENU TAB SECTION SET ON THE ADMIN PAGE
   *
   *   META KEYS: _menu_section_col, _menu_section_row
   *
   */
  public function save_section_order() {
    global $wpdb; // THIS IS HOW YOU GET ACCESS TO THE DATABASE
    $section_list = $_POST['section_list'];                       // LIST FORMAT: ID_COL_ROW, ID_COL_ROW, ID_COL_ROW, ...
    $section_ar = explode(',', $section_list);                    // BREAK IT UP BY SECTION
    $result_str = '';
    $section_id = 0;

    foreach ($section_ar as $i => $value) {
      $item_ar = explode('_', $section_ar[$i]); // $item_ar[0]=ID, $item_ar[1]=COL, $item_ar[2]=ROW

      update_post_meta( $item_ar[0], '_menu_section_col', $item_ar[1] );
      update_post_meta( $item_ar[0], '_menu_section_row', $item_ar[2] );
      $result_str .=  ' - '. $section_ar[$i];
      //$result_str .=  ' - ID:'. $item_ar[0] .' COL:'. $item_ar[1] .' ROW:'. $item_ar[2] .'\n';
    }
    echo 'Order Updated '. $result_str;
    wp_die(); // THIS IS REQUIRED TO TERMINATE IMMEDIATELY AND RETURN A PROPER RESPONSE
  }

  /*
   *   S A V E _ I T E M _ O R D E R
   *
   *   META KEYS: _menu_item_order
   *
   */
  public function save_item_order() {
    global $wpdb; // THIS IS HOW YOU GET ACCESS TO THE DATABASE
    $item_list = $_POST['item_list'];
    $item_ar = explode(',', $item_list);
    $result_str = '';
    $sort_num = 0;
    $item_id = 0;

    foreach ($item_ar as $i => $value) {
      $sort_num++;
      $item_id = intval($item_ar[$i]);
      update_post_meta( $value, '_menu_item_order', $sort_num );
      $result_str .=  ' - '. $value;
    }

    echo 'Order Updated '. $result_str;
    wp_die(); // THIS IS REQUIRED TO TERMINATE IMMEDIATELY AND RETURN A PROPER RESPONSE
  }

  /*
   *   W R I T E _ O U T _ L O G
   *
   */
  public function write_out_log( $log_str ) {

    $dirfile = plugin_dir_path( dirname(__FILE__) ) .'admin/logFile2.txt';

    $myfile = fopen( $dirfile, 'w') or die('Unable to open file!');
    fwrite($myfile, $log_str);
    fclose($myfile);
  }
  /*
   *   P R I N T _ O U T _ L O G
   *
   */
  public function print_out_log( $file_str, $log_str ) {

    $dirfile = plugin_dir_path( dirname(__FILE__) ) .'admin/'. $file_str;

    $myfile = fopen( $dirfile, 'w') or die('Unable to open file!');
    fwrite($myfile, $log_str);
    fclose($myfile);
  }

  /*
   *   L O A D _ I T E M S
   *
   *   LOAD ITEMS: JSON Response
   *   CALLED IN RESPONSE TO A AJAX CALL FROM THE JS FUNCTION "show_item(ItemID)".
   *   RETURNS DATA AS JSON TO BE PARSED BY JAVASCRIPT ON THE CLIENT
   */
  public function load_items() {
    $section_id = $_POST['section_id'];  // CONTAINS:
    $item_ar = array();

    /*   GET MENU ITEMS   */
    $menu_items = new WP_Query( array(
        'posts_per_page'  => -1,
        'post_type'       => 'rs_menu_item',
        'meta_query'      => array(
          'get_clause' => array( 'key' => '_menu_section_id', 'value' => $section_id ),
          'row_clause' => array( 'key' => '_menu_item_order', 'compare' => 'EXISTS', 'type' => 'NUMERIC', ),
        ),
        'orderby'  => array(
          'row_clause' => 'ASC',
        ),
    ) );
    if ( $menu_items->have_posts() ) :
      while ( $menu_items->have_posts() ) :  // ITEM LOOP
        $menu_items->the_post();
        $item_ar[] = array( 'items' => array( 'itemId' => get_the_ID(), 'itemName' => get_the_title() ) );
      endwhile;
      wp_reset_postdata();  // RESET POST DATA
      echo json_encode($item_ar);
    else :
      $this->write_out_log('Failed Have Posts With SectionID:'. $section_id);
      echo '';
    endif;
    wp_die();
  }

  /*
   *   L O A D _ S E C T I O N S
   *
   *   LOAD SECTIONS: show_section( tab_id, tab_cols )
   *   CALLED IN RESPONSE TO A CLIENT CLICKING ON THE "EDIT SECTION ORDER" BUTTON ON A MENU TAB.
   *   RETURNS DATA AS JSON TO BE PARSED BY JAVASCRIPT ON THE CLIENT
   *
   */
  public function load_sections() {
    $menu_tab_id = $_POST['tab_id'];
    $sections_ar = array();
    $menu_sections = new WP_Query( array(
        'posts_per_page'  => -1,
        'post_type'       => 'rs_menu_section',
        'meta_query'      => array(
          'get_clause' => array( 'key' => '_menu_tab_id', 'value' => $menu_tab_id ),
          'col_clause' => array( 'key' => '_menu_section_col', 'compare' => 'EXISTS', 'type' => 'NUMERIC', ),
          'row_clause' => array( 'key' => '_menu_section_row', 'compare' => 'EXISTS', 'type' => 'NUMERIC', ),
        ),
        'orderby'  => array(
          'col_clause' => 'ASC',
          'row_clause' => 'ASC',
        ),
    ) );
    if ( $menu_sections->have_posts() ) :
      $current_column = 1;
      $section_text = '';
      $section_id = 0;
      $section_name = '';
      $section_text = '';
      while ( $menu_sections->have_posts() ) :  // SECTION LOOP
        $menu_sections->the_post();
        $section_id = get_the_ID();
        $section_name = get_the_title();
        $section_text = get_the_content();

        $section_column = get_post_meta( get_the_ID(), '_menu_section_col', true );  // COLUMN
        $section_row = get_post_meta( get_the_ID(), '_menu_section_row', true );     // ROW
        $item_ar = $this->get_item_list(get_the_ID()); // A JSON ARRAY
        $sections_ar[] = array('sections' => array( 'sectionId' => $section_id, 'sectionName' => $section_name, 'sectionCol' => $section_column, 'sectionTxt' => $section_text, 'itemList' => $item_ar ));
      endwhile;
      wp_reset_postdata();
      echo json_encode($sections_ar);
    else :
      echo '';
    endif;
    wp_die();
  }
  /*
   *   M E N U _ O R D E R _ H E A D _ S C R I P T
   *
   *   INCLUDES JAVASCRIPT FOR HANDLING ORDERING                       J  A  V  A  S  C  R  I  P  T
   */
  public function menu_order_head_script() {

    /***   AJAX JS FOR THE ORDER INTERFACE   ***/
    ?>
    <script type="text/javascript">
      /***********************************************************************************************/
      jQuery(document).ready( function($) {
        jQuery( "#menu_tab_list" ).sortable();
        jQuery( "#menu_tab_list" ).disableSelection();
        jQuery( "#menu_item_list" ).sortable();
        jQuery( "#menu_item_list" ).disableSelection();
        //jQuery( document ).tooltip({
        //  track: true
        //});

        /*********************************************************************************************/
        jQuery( "#save_tab_order" ).click(function() {                     /***   SAVE TAB ORDER   ***/
          var list = document.getElementById("menu_tab_list");
          var items = list.getElementsByTagName("li");
          var idStr = "";

          for (var j = 0; j < items.length; ++j) {
            if (j == 0) { idStr += items[j].value; }
            else { idStr += ","+ items[j].value; }
          }
          var data = { 'action': 'save_tab_order', 'tab_list': idStr };
          jQuery.post(ajaxurl, data, function(response) {
            alert('Response From Server:\n' + response);
          });
        });
        /*********************************************************************************************/
        jQuery( "#save_section_order" ).click(function() {             /***   SAVE SECTION ORDER   ***/

          var listCols = document.getElementById("menu_wrap").getElementsByClassName("col-md-6");
          var menu_sections;
          var idStr = "";

          for (var i = 0; i < listCols.length; ++i) {
            for (var j = 0; j < listCols[i].children.length; ++j) {
              if ((i == 0 ) && (j == 0)) {
                idStr += listCols[i].children[j].getAttribute("id") +"_"+ eval(i+1) +"_"+ eval(j+1);
              } else {
                idStr += ","+ listCols[i].children[j].getAttribute("id") +"_"+ eval(i+1) +"_"+ eval(j+1) +" ";
              }
            }
          }
          var data = { 'action': 'save_section_order', 'section_list': idStr };
          jQuery.post(ajaxurl, data, function(response) {
            alert('Response From Server:\n' + response);
          });
        });

        /*********************************************************************************************/
        jQuery( "#save_item_order" ).click(function() {                   /***   SAVE ITEM ORDER   ***/
          var list = document.getElementById("menu_item_list");
          var items = list.getElementsByTagName("li");
          var idStr = "";

          for (var j = 0; j < items.length; ++j) {
            if (j == 0) { idStr += items[j].value; }
            else { idStr += ","+ items[j].value; }
          }
          var data = { 'action': 'save_item_order', 'item_list': idStr };
          jQuery.post(ajaxurl, data, function(response) {
            alert('Response From Server:\n' + response);
          });
        });
      });
      /***********************************************************************************************/
      function show_section( tab_id, tab_cols ) {                            /***   SHOW SECTION   ***/
        var data = { 'action': 'load_sections', 'tab_id': tab_id };
        jQuery.post(ajaxurl, data, function(response) {
          load_sections(response, tab_cols);
        });
      }
      /***********************************************************************************************/
      var show_items = function( section_id ) {                                /***   SHOW ITEMS   ***/
        var data = { 'action': 'load_items', 'section_id': section_id };
        jQuery.post(ajaxurl, data, function(response) {
          //alert("Retuned");
          load_items(response);
        });
      };
      /***********************************************************************************************/
      function load_items( response_json ) {                                   /***   LOAD ITEMS   ***/
        var ulList = document.getElementById("menu_item_list");
        var lItem, lText;
        var itemId = 0;
        var jsObj;

        if (jQuery("#panel2").css("display") != "none") {
          jQuery("#panel2").fadeOut("slow");
        }
        jQuery("#panel3").delay(800).fadeIn("slow");
        jQuery("#menu_item_list").empty();
        /*
          JSON EXAMPLE:
          [
            {"items":{"itemId":66,"itemName":"Traditional Bruschetta"}},
            {"items":{"itemId":67,"itemName":"Avocado Bruschetta"}},
            {"items":{"itemId":68,"itemName":"Goat Cheese Bruschetta"}}
          ]
        */
        jsObj = JSON.parse(response_json);  // CONVERT TEXT TO A JSON OBJECT
        for(var i = 0; i < jsObj.length; i++) {
          itemId = parseInt(jsObj[i].items.itemId);
          lText = document.createTextNode(htmlDecode(jsObj[i].items.itemName));

          btnItemEdit = document.createElement("input");
          btnItemEdit.type = "button";
          btnItemEdit.value = "Edit";
          btnItemEdit.setAttribute("data-id", itemId);
          btnItemEdit.onclick = function() {
            location.href = "post.php?post="+ this.dataset.id +"&action=edit";
          };
          lItem = document.createElement("li");
          lItem.value = jsObj[i].items.itemId;
          lItem.appendChild(lText);
          lItem.appendChild(btnItemEdit);
          ulList.appendChild(lItem);
        }
      }
      /***********************************************************************************************/
      /***                                                                                         ***/
      /***   JSON:  sectionId, sectionName, sectionCol, sectionTxt, itemList                       ***/
      /***                                                                                         ***/
      /***      #menu-wrap (Exists)                                                                ***/
      /***        .menu-column                      divColumn                                      ***/
      /***          .menu-section                     divSection                                   ***/
      /***            .menu-section-header              divSectionHead                             ***/
      /***            .menu-section-content             divSectionContent                          ***/
      /***                                                                                         ***/
      /***   divMenuWrap, divColumn, divSection, divSectionHead, divSectionContent, lButton,       ***/
      /***   lHidName, lHidCol, sectionId, sectionName, sectionCol, col_class, jsObj               ***/
      /***                                                                                         ***/
      /***********************************************************************************************/
      function load_sections( response_json, tab_cols ) {                   /***   LOAD SECTIONS   ***/
        var divMenuWrap = document.getElementById("menu_wrap");
        var divColumn, divSection, divSectionHead, divSectionContent, btnSectionHead;
        var lButton;
        var itemListUl, itemListLi, itemListLiStr;
        var sectionId, sectionName, sectionCol;
        var col_class = "";
        var section_count = 0;
        var current_column = 1;
        var jsObj;

        switch (tab_cols) {
          case 1 :
            col_class = "col-md-12";
            break;
          case 2 :
            col_class = "col-md-6";
            break;
          case 3 :
            col_class = "col-md-4";
            break;
          case 4 :
            col_class = "col-md-3";
            break;
        }
        if (jQuery("#panel1").css("display") != "none") {
          jQuery("#panel1").fadeOut("slow");
        }
        jQuery("#panel2").delay(800).fadeIn("slow");
        jQuery("#menu_item_list").empty();
        jsObj = JSON.parse(response_json);      // CONVERT TEXT TO A JSON OBJECT
        for(var i = 0; i < jsObj.length; i++) { // LOOP THROUGH SECTIONS  J S O N   D A T A:  sectionId, sectionName, sectionCol, sectionTxt, itemList
          section_count++;                                                                      // INCREMENT SECTION COUNTER
          sectionId = parseInt(jsObj[i].sections.sectionId);                                    // sectionId   - INTEGER
          sectionName = document.createTextNode(htmlDecode(jsObj[i].sections.sectionName));     // sectionName - TEXT NODE
          sectionText = document.createTextNode(htmlDecode(jsObj[i].sections.sectionTxt));      // sectionText - TEXT NODE
          sectionCol = parseInt(jsObj[i].sections.sectionCol);                                  // sectionCol  - INTEGER
          itemListUl = document.createElement("ul");                                            // CREATE AN UNORDERED LIST TO STORE LIST OF SECTION ITEMS
          for (j = 0; j < jsObj[i].sections.itemList.length; j++) {                             // LOOP THROUGH THE ITEMS IN THE ARRAY
            itemListLi = document.createElement("li");                                            // CREATE A LIST ITEM FOR UNORDERED LIST
            itemListLiStr = document.createTextNode(htmlDecode(jsObj[i].sections.itemList[j]));   // CREATE A TEXT NODE OF THE MENU ITEM
            itemListLi.appendChild(itemListLiStr);                                                // ADD TEXT NODE TO LIST ITEM
            itemListUl.appendChild(itemListLi);                                                   // ADD LIST ITEM TO UNORDERED LIST
          }
          if ( section_count == 1 ) {                                                           // TEST FOR FIRST SECTION
            current_column = sectionCol;                                                          // SET THE CURRENT COLUMN
            divColumn = document.createElement("div");                                            // CREATE FIRST COLUMN NODE
            divColumn.className = "menu-column "+ col_class;                                      // SET THE COLUMN NODE CSS CLASSES
          } else {                                                                              // NOT THE FIRST SECTION
            if (sectionCol != current_column) {                                                   // TEST FOR COLUMN CHANGE
              current_column = sectionCol;                                                          // SET NEW CURRENT COLUMN NUMBER
              divMenuWrap.appendChild(divColumn);                                                   // ADD CURRENT COLUMN TO EXISTING WRAP DIV
              divColumn = document.createElement("div");                                            // CREATE NEW COLUMN NODE
              divColumn.className = "menu-column "+ col_class;                                      // SET THE COLUMN NODE CSS CLASSES
            }
          }
          divSection = document.createElement("div");                                           // CREATE A NEW SECTION NODE
          divSection.className = "menu-section";                                                // SET SECTION NODE CSS CLASS
          divSection.setAttribute("id", sectionId);                                             // ADD AND SET SECTION NODE ID ATTRIBUTE
          divSectionHead = document.createElement("div");                                       // CREATE A SECTION HEADER NODE
          divSectionHead.className = "menu-section-header";                                     // SET SECTION HEADER NODE CSS CLASS

          btnSectionHead = document.createElement("input");
          btnSectionHead.type = "button";
          btnSectionHead.value = "Sort Items";
          btnSectionHead.setAttribute("data-id", sectionId);
          btnSectionHead.onclick = function() {
            show_items(this.dataset.id);
          };
          btnSectionEdit = document.createElement("input");
          btnSectionEdit.type = "button";
          btnSectionEdit.value = "Edit";
          btnSectionEdit.setAttribute("data-id", sectionId);
          btnSectionEdit.onclick = function() {
            location.href = "post.php?post="+ this.dataset.id +"&action=edit";
          };
          divSectionHead.appendChild(sectionName);                                              // ADD SECTION NAME TO SECTION HEAD **
          divSectionHead.appendChild(btnSectionEdit);                                           // ADD EDIT ITEM BUTTON TO SECTION HEAD **
          divSectionHead.appendChild(btnSectionHead);                                           // ADD SORT ITEM BUTTON TO SECTION HEAD **
          divSectionContent = document.createElement("div");                                    // CREATE A NEW SECTION CONTENT NODE
          divSectionContent.className = "menu-section-content";                                 // SET SECTION CONTENT NODE CSS CLASS
          divSectionContent.appendChild(itemListUl);                                            // ADD UNORDERED LIST OF SECTION ITEMS TO SECTION CONTENT
          divSection.appendChild(divSectionHead);                                               // ADD SECTION-HEAD TO SECTION
          divSection.appendChild(divSectionContent);                                            // ADD SECTION-CONTENT TO SECTION
          divColumn.appendChild(divSection);                                                    // ADD SECTION TO COLUMN
        }
        divMenuWrap.appendChild(divColumn);                                                   // ADD COLUMN TO WRAP
        makeSectionSortable();                                                                // CALL FUNCTION TO ENABLE SORTING
      }
      /***********************************************************************************************/
      function html_Decode(value) {                                            /***   HTML DECODE   ***/
        return $("<textarea/>").html(value).text();
      }
      /***********************************************************************************************/
      function makeSectionSortable() {                              /***   MAKE SECTION SORTABLE   ***/

        jQuery( ".menu-column" ).sortable({
          connectWith: ".menu-column",
          handle: ".menu-section-header",
          cancel: ".menu-section-toggle",
          placeholder: "menu-section-placeholder ui-corner-all"
        });

        jQuery( ".menu-section" )
          .addClass( "ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" )
          .find( ".menu-section-header" )
            .addClass( "ui-widget-header ui-corner-all" )
            .prepend( "<span class='ui-icon ui-icon-minusthick menu-section-toggle'></span>");


        jQuery( ".menu-section-toggle" ).on( "click", function() {
          var icon = $( this );
          icon.toggleClass( "ui-icon-minusthick ui-icon-plusthick" );
          icon.closest( ".menu-section" ).find( ".menu-section-content" ).toggle();
        });

      }
      /***********************************************************************************************/
      function clearList(sList) {                                              /***   CLEAR LIST   ***/
        var liList = document.getElementById(sList).getElementsByTagName("li");;

        for (var i=0; i<liList.length; i++) {
          liList[i].parentNode.removeChild(liList[i]);
        }
      }
      /***********************************************************************************************/
      function htmlDecode(input){                                            /***   HTML DECODE   ***/
        var e = document.createElement('div');
        e.innerHTML = input;
        return e.childNodes.length === 0 ? "" : e.childNodes[0].nodeValue;
      }
    </script>
  <?php
  } // END OF menu_order_head_script()
} // END OF CLASS