<?php  /***   AJAX JS FOR THE ORDER INTERFACE   ***/ ?>
<script type="text/javascript">

  jQuery(document).ready( function($) {
    jQuery( "#tier1" ).sortable();
    jQuery( "#tier1" ).disableSelection();
    jQuery( "#tier2" ).sortable();
    jQuery( "#tier2" ).disableSelection();
    jQuery( "#tier3" ).sortable();
    jQuery( "#tier3" ).disableSelection();

    jQuery( document ).tooltip({
      track: true
    });

    jQuery( "#save_tab_order" ).click(function() {      /***   SAVE TAB ORDER   ***/
      var list = document.getElementById("tier1");
      var items = list.getElementsByTagName("li");
      var IdStr = "";

      for (var j = 0; j < items.length; ++j) {
        if (j == 0) { IdStr += items[j].value; }
        else { IdStr += ","+ items[j].value; }
      }
      var data = { 'action': 'save_tab_order', 'tab_list': IdStr };
      jQuery.post(ajaxurl, data, function(response) {
        alert('Response From Server:\n' + response);
      });
    });

    jQuery( "#save_section_order" ).click(function() {  /***   SAVE SECTION ORDER   ***/
      var list = document.getElementById("tier2");
      var items = list.getElementsByTagName("li");
      var IdStr = "";

      for (var j = 0; j < items.length; ++j) {
        if (j == 0) { IdStr += items[j].value; }
        else { IdStr += ","+ items[j].value; }
      }
      var data = { 'action': 'save_section_order', 'section_list': IdStr };
      jQuery.post(ajaxurl, data, function(response) {
        alert('Response From Server:\n' + response);
      });
    });

    jQuery( "#save_item_order" ).click(function() {     /***   SAVE ITEM ORDER   ***/
      var list = document.getElementById("tier3");
      var items = list.getElementsByTagName("li");
      var IdStr = "";

      for (var j = 0; j < items.length; ++j) {
        if (j == 0) { IdStr += items[j].value; }
        else { IdStr += ","+ items[j].value; }
      }
      var data = { 'action': 'save_item_order', 'item_list': IdStr };
      jQuery.post(ajaxurl, data, function(response) {
        alert('Response From Server:\n' + response);
      });
    });
  });
  /***********************************************************************************/
  function show_section( tab_id ) {
    var data = { 'action': 'load_sections', 'tab_id': tab_id };
    jQuery.post(ajaxurl, data, function(response) {
      load_sections(response);
    });
  }
  /***********************************************************************************/
  var show_item = function( item_id ) {
    // NEED TO SEND ID, NAME AND SLUG AS STRING id,slug,name
    var sectionSlug = document.getElementById("sectionSlug"+item_id);
    var sectionName = document.getElementById("sectionName"+item_id);
    var dataStr = ""+ item_id +","+ sectionSlug.value +","+ sectionName.value;
    var data = { 'action': 'load_items', 'data_str': dataStr };
    jQuery.post(ajaxurl, data, function(response) {
      load_items(response);
    });
  };

  /***********************************************************************************/
  function load_sections( response_json ) {
    var ulList = document.getElementById("tier2");
    var lItem, lText, lButton, lHidName, lHidSlug;
    var itemId = 0;
    var jsObj;

    if (jQuery("#panel1").css("display") != "none") {
      jQuery("#panel1").fadeOut("slow");
    }
    jQuery("#panel2").delay(800).fadeIn("slow");
    jQuery("#tier2").empty();
    jQuery("#tier3").empty();

    jsObj = JSON.parse(response_json);  // CONVERT TEXT TO A JSON OBJECT
    for(var i = 0; i < jsObj.sections.length; i++) {
      itemId = parseInt(jsObj.sections[i].sectionId);
      lText = document.createTextNode(htmlDecode(jsObj.sections[i].sectionName));
      lHidName = document.createElement("input");
      lHidName.type = "hidden";
      lHidName.value = jsObj.sections[i].sectionName;
      lHidName.id = "sectionName"+ itemId;
      lHidSlug = document.createElement("input");
      lHidSlug.type = "hidden";
      lHidSlug.value = jsObj.sections[i].sectionSlug;
      lHidSlug.id = "sectionSlug"+ itemId;
      lButton = document.createElement("input");
      lButton.type = "button";
      lButton.value = "Edit Item Order";
      lButton.id = itemId;
      lButton.onclick = function() { show_item( this.id ); };
      lItem = document.createElement("li");
      lItem.value = jsObj.sections[i].sectionId;
      lItem.appendChild(lText);
      lItem.appendChild(lButton);
      lItem.appendChild(lHidSlug);
      lItem.appendChild(lHidName);
      ulList.appendChild(lItem);
    }
  }
  /***********************************************************************************/
  function clearList(sList) {
    var liList = document.getElementById(sList).getElementsByTagName("li");;

    for (var i=0; i<liList.length; i++) {
      liList[i].parentNode.removeChild(liList[i]);
    }
  }
  /***********************************************************************************/
  function htmlDecode(input){
    var e = document.createElement('div');
    e.innerHTML = input;
    return e.childNodes.length === 0 ? "" : e.childNodes[0].nodeValue;
  }
  /***********************************************************************************/
  function load_items( response_json ) {
    var ulList = document.getElementById("tier3");
    var lItem;
    var lText;
    var itemId = 0;
    var jsObj;

    if (jQuery("#panel2").css("display") != "none") {
      jQuery("#panel2").fadeOut("slow");
    }
    jQuery("#panel3").delay(800).fadeIn("slow");


    jQuery("#tier3").empty();
    jsObj = JSON.parse(response_json);  // CONVERT TEXT TO A JSON OBJECT
    for(var i = 0; i < jsObj.items.length; i++) {
      itemId = parseInt(jsObj.items[i].itemId);
      lText = document.createTextNode(htmlDecode(jsObj.items[i].itemName));
      lItem = document.createElement("li");
      lItem.value = jsObj.items[i].itemId;
      lItem.appendChild(lText);
      ulList.appendChild(lItem);
    }
  }
</script>

<?php
