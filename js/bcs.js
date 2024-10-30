/**
 * Plugin Name: Better Category Selector for WooCommerce
 * Version: 1.0.4
 * Description: Better Category Selector for WooCommerce provides an alternative, easier to use GUI for putting products in categories.
 * Author: NO.BrainerAPPs / HisDesigns LLC
 * Author URI: http://no.brainerapps.com
 * Plugin URI: http://no.brainerapps.com
 * Text Domain: better-category-selector-for-woocommerce
 *
 * Copyright: (c) 2022, HisDesigns LLC
 *
 */

jQuery( function ( $ ) {

  $('.post-php.js #taxonomy-product_cat').addClass('hide_old_meta_box');
  $('#bcs_show_selector_link').removeClass('hide_old_meta_box');
  $('#bcs_hide_old_meta_box_link').addClass('hide_old_meta_box');
  $('#bcs_show_old_meta_box_link').addClass('show_old_meta_box');
  $('#bcs_show_old_meta_box_link').removeClass('hide_old_meta_box');

  $('#bcs_show_old_meta_box_link').on('click', function(e){
    e.preventDefault();
    $('.post-php.js #taxonomy-product_cat').toggleClass('hide_old_meta_box').toggleClass('show_old_meta_box');
    $('#bcs_hide_old_meta_box_link').toggleClass('hide_old_meta_box').toggleClass('show_old_meta_box');
    $('#bcs_show_old_meta_box_link').toggleClass('hide_old_meta_box').toggleClass('show_old_meta_box');
  });

  $('.js #bcs_hide_old_meta_box_link').on('click', function(e){
    e.preventDefault();
    $('.post-php.js #taxonomy-product_cat').toggleClass('hide_old_meta_box').toggleClass('show_old_meta_box');
    $('#bcs_hide_old_meta_box_link').toggleClass('hide_old_meta_box').toggleClass('show_old_meta_box');
    $('#bcs_show_old_meta_box_link').toggleClass('hide_old_meta_box').toggleClass('show_old_meta_box');
  });

  // Catch changes to the category meta box.
  $('#product_catdiv .tabs-panel input[type=checkbox]').change(function() {
    if(this.checked) {
      bcsUpdateCatChangeTracker($(this).attr('id'),true);
    }
    else{
      bcsUpdateCatChangeTracker($(this).attr('id'),false);
    }
  });

  // Adjust height of BCS TB Frame
  $('.bcs_categorydiv div.tabs-panel').height($(window.parent.document).find("#TB_iframeContent").height()-180);
  $('.hd-bcs-category-selector-free .bcs_categorydiv div.tabs-panel').height($(window.parent.document).find("#TB_iframeContent").height()-350);

  // Plugin Navigation Tabs
  $('.category-tabs a').on('click', function(){
    $('.bcs_categorydiv .tabs-panel').css('display','none');
    $(this).closest('.category-tabs').find('li').removeClass('tabs');
    $(this).parent().addClass('tabs');
    $($(this).attr('href')).css('display','block');
  });

  // Update category meta box so that changes 
  // will be mirrored between BCS TB Frame 
  // and category meta box.
  $('.admin_page_bcs-category-selector .bcs_categorydiv .categorychecklist li input[type=checkbox]').on('click', function() {
    if(this.checked) {
      $(this).closest('li').addClass('bcs-is-selected');
      //$(window.parent.document).find("#" + $(this).attr('id')).prop('checked', true);
      // Have to use click because wordpress
      // syncs popular to all and appears to
      // to save from all only.
      $(window.parent.document).find("#" + $(this).attr('id')).click();

      // Sync up popular and all because wordpress
      // sucks.
      catId = getCatIdFromDomId($(this).attr('id'));
      if(catId && ($(this).attr('id').indexOf('in-product_cat') != -1)){
        $('#in-popular-product_cat-' + catId).prop('checked', true);
      }
      else if(catId && ($(this).attr('id').indexOf('in-popular-product_cat') != -1)){
        $('#in-product_cat-' + catId).prop('checked', true);
        $(window.parent.document).find('#in-product_cat-' + catId).prop('checked', true);
        $(window.parent.document).find('#in-popular-product_cat-' + catId).prop('checked', true);
      }

      // If the default category was not just
      // selected then we need to make sure the
      // default is unchecked.
      if($('#product_cat-all input[type=checkbox]').first().attr('id') != $(this).attr('id')){
        $(window.parent.document).find('#product_cat-all input[type=checkbox]').first().prop('checked', false);
        //$(window.parent.document).find('#product_catchecklist-pop input[type=checkbox]').first().prop('checked', false);
        $('#product_cat-all input[type=checkbox]').first().prop('checked', false);
        $('#in-popular-product_cat-all input[type=checkbox]').first().prop('checked', false);
      }
      bcsUpdateCatChangeTracker($(this).attr('id'),true);
    }
    else{
      $(this).closest('li').removeClass('bcs-is-selected');
      //$(window.parent.document).find("#" + $(this).attr('id')).prop('checked', false);
//alert("#" + $(this).attr('id') + " " + $(window.parent.document).find("#" + $(this).attr('id')).length);
      $(window.parent.document).find("#" + $(this).attr('id')).click();

      catId = getCatIdFromDomId($(this).attr('id'));
      if(catId && ($(this).attr('id').indexOf('in-product_cat') != -1)){
        $('#in-popular-product_cat-' + catId).prop('checked', false);
        $(window.parent.document).find('#in-popular-product_cat-' + catId).prop('checked', false);
      }
      else if(catId && ($(this).attr('id').indexOf('in-popular-product_cat') != -1)){
        $('#in-product_cat-' + catId).prop('checked', false);
        $(window.parent.document).find('#in-product_cat-' + catId).prop('checked', false);
        $(window.parent.document).find('#in-popular-product_cat-' + catId).prop('checked', false);
      }

      if($('#product_cat-all input[type=checkbox]').first().attr('id') != $(this).attr('id') && !$('#product_cat-all input[type=checkbox]:checked').length){
        $(window.parent.document).find('#product_catchecklist-pop input[type=checkbox]').first().prop('checked', true);
        //$(window.parent.document).find('#product_cat-all input[type=checkbox]').first().prop('checked', true);
        $('#product_cat-all input[type=checkbox]').first().prop('checked', true);
        $('#in-popular-product_cat-all input[type=checkbox]').first().prop('checked', true);
      }

      bcsUpdateCatChangeTracker($(this).attr('id'),false);
    }
  });

  // Closes BCS TB Frame
  $('#BCS_closeWindowButton').on('click', function(){
    self.parent.tb_remove();
  });

  // Closes BCS TB Frame and saves product with
  // category selection changes.
  $('#BCS_closeWindowSaveButton').on('click', function(){
    self.parent.tb_remove();
    $(window.parent.document).find("#post").submit();
  });

  // Get any changes made in the category meta
  // box before BCS TB Frame is opened.
  if($(window.parent.document).find('#bcs_track_cat_meta_box_changes').length){
    metaBoxChanges = $(window.parent.document).find('#bcs_track_cat_meta_box_changes').val();
    if((typeof(metaBoxChanges) != "undefined") && (metaBoxChanges != "")){
      bcsUpdateCatsFromChangeTracker(metaBoxChanges);
    }
  }

  // Update BCS TB Frame categories from
  // change tracker.
  function bcsUpdateCatsFromChangeTracker(changes){
    catChanges = false;
    if((typeof(changes) != "undefined") && (changes != "")){

      if (typeof JSON.parse !== "undefined") {
        try {
          catChanges = JSON.parse(changes);
        } catch(e) {
        }
      } // if (typeof JSON.parse
      else if (typeof $.parseJSON !== "undefined") {
        try {
          catChanges = $.parseJSON(changes);
        } catch(e) {
        }
      } // if (typeof $.parseJSON
      if(catChanges){
        if(Array.isArray(catChanges.add)){
          for(i = 0; i < catChanges.add.length; i++){
            $('.admin_page_bcs-category-selector .bcs_categorydiv #' + catChanges.add[i]).prop('checked', true);
          }
        }
        // Should not be an else because both of these
        // need to run everytime.
        if(Array.isArray(catChanges.remove)){
          for(i = 0; i < catChanges.remove.length; i++){
            $('.admin_page_bcs-category-selector .bcs_categorydiv #' + catChanges.remove[i]).prop('checked', false);
          }
        }
      } // if(catChanges)

    } // if((typeof(changes)
  } // function bcsUpdateCatsFromChangeTracker

  // Update change tracker with categories
  // selected in BCS TB Frame.
  function bcsUpdateCatChangeTracker(id,add = false,scope = false){

    if(!scope && $('#bcs_track_cat_meta_box_changes').length){
      scope = $('#bcs_track_cat_meta_box_changes');
    }
    if(!scope && $(window.parent.document).find('#bcs_track_cat_meta_box_changes').length){
      scope = $(window.parent.document).find('#bcs_track_cat_meta_box_changes');
    }

    metaBoxChanges = false;

    if(typeof(id) != "undefined"){

      // See if input already has a value and then
      // convert to obj.
      if(scope.val() != ""){
        if (typeof JSON.parse !== "undefined") {
          try {
            metaBoxChanges = JSON.parse(scope.val());
          } catch(e) {
            metaBoxChanges = {add:[],remove:[]};
          }
        } // if (typeof JSON.parse
        else if (typeof $.parseJSON !== "undefined") {
          try {
            metaBoxChanges = $.parseJSON(scope.val());
          } catch(e) {
            metaBoxChanges = {add:[],remove:[]};
          }
        } // if (typeof $.parseJSON
      } // if($('#bcs_track_cat_meta_box_changes')
      else {
        metaBoxChanges = {add:[],remove:[]};
      }

      if(add){

        // If adding to add list we need to
        // remove from remove list.
        idIndex = metaBoxChanges.remove.indexOf(id);
        if(idIndex != -1){
          metaBoxChanges.remove.splice(idIndex, 1);
        }

        // We need to make sure id is not already in
        // the add list.
        idIndex = metaBoxChanges.add.indexOf(id);
        if((Array.isArray(metaBoxChanges.add)) && (metaBoxChanges.add.length > 0) && (idIndex == -1)){
          metaBoxChanges.add[metaBoxChanges.add.length] = id;
        }
        else{
          metaBoxChanges.add = [id];
        }
      } // if(add){
      else{
        idIndex = metaBoxChanges.add.indexOf(id);
        if(idIndex != -1){
          metaBoxChanges.add.splice(idIndex, 1);
        }

        // If we are removing we need to remove from
        // the add list.  And then we need to add to
        // the remove list incase popup is closed
        // and then reopened.
        //
        // We need to make sure id is not already in
        // the remove list.
        idIndex = metaBoxChanges.remove.indexOf(id);
        if((Array.isArray(metaBoxChanges.remove)) && (metaBoxChanges.remove.length > 0) && (idIndex == -1)){
          metaBoxChanges.remove[metaBoxChanges.remove.length] = id;
        }
        else{
          metaBoxChanges.remove = [id];
        }

      } // if(add){ else

      metaBoxChangesStr = JSON.stringify(metaBoxChanges);
      scope.val(metaBoxChangesStr);

    } // if(typeof(id)
  } // function bcsUpdateCatChangeTracker

  function getCatIdFromDomId(domId){
    key = "cat-";
    pos = domId.indexOf(key);
    ret = false;
    if(pos != -1){
      ret = domId.substring(pos + key.length);
    }
    if(ret != ""){
      return ret;
    }
    else{
      return false;
    }
  }

} );
