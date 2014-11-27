jQuery(function(){

  // color picker init
  jQuery('.pr-color-picker').wpColorPicker();
  if(jQuery(".chosen-select").length)
    jQuery(".chosen-select").chosen();

  // calendar init
  jQuery('#_pr_date').datepicker({
     dateFormat : 'yy-mm-dd'
  });

  //repeater fields
  var prefix = jQuery('#_pr_prefix_bundle_id');
  var subscription = jQuery('#_pr_subscription_prefix');

  jQuery( '#_pr_prefix_bundle_id').keyup(function() {
    jQuery( '#_pr_single_edition_prefix, #_pr_subscription_prefix, .pr_repeater input[type="text"]' ).trigger( "keyup" );
  });

  jQuery('#_pr_single_edition_prefix, #_pr_subscription_prefix').keyup(function() {
      var autocompleted = jQuery(this).next();
      autocompleted.html(prefix.val() + '.' + jQuery(this).val() );
      jQuery( '.pr_repeater input[type="text"]' ).trigger( "keyup" );
  });

  jQuery( ".form-table" ).delegate('.pr_repeater input[type="text"]','keyup',function() {
      var autocompleted = jQuery(this).parent().find('.repeater-completer');
      autocompleted.html(prefix.val() + '.' + subscription.val() + '.' + jQuery(this).val() );
  });

  jQuery( "#_pr_prefix_bundle_id" ).trigger( "keyup" );



  //add cloned field
  jQuery( "#add-field" ).click(function(e) {
    e.preventDefault();


    var clone = jQuery( "#pr_repeater" ).clone();
    var minus = '<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAARBAMAAAA1VnEDAAAAA3NCSVQICAjb4U/gAAAACXBIWXMAAAF3AAABdwE7iaVvAAAAGXRFWHRTb2Z0d2FyZQB3d3cuaW5rc2NhcGUub3Jnm+48GgAAAA9QTFRF////AAAAAAAAAAAAAAAAUTtq8AAAAAR0Uk5TADVCUDgXPZIAAAAaSURBVAhbY2CgKVA2BgIjCJvRBQwEMGVoBQCxXAPsAZwyyQAAAABJRU5ErkJggg60a8c977b5851eb7a101a51c617fd8ad"/>';
    var last_index = jQuery( ".pr_repeater" ).last().data('index');

    clone.find('#add-field').attr('class','remove-field');
    clone.find('#add-field').attr('id','remove-field');
    clone.find('#remove-field').html(minus);

    var parent = jQuery(".pr_repeater" ).parent();
    clone.data('index',parseInt( last_index ) + 1 );

    clone.find('input[type="text"]').val('');

    var name = clone.find('input[type="text"]').attr('name');
    var radioname = clone.find('input[type="radio"]').attr('name');

    clone.find('input[type="text"]').attr('name',name.replace('[0]', '[' + (parseInt(last_index ) +1)  + ']'));
    clone.find('input[type="radio"]').attr('name',radioname.replace('[0]', '[' + (parseInt(last_index ) +1)  + ']'));
    clone.find('.repeater-completer').html( prefix.val() + '.' + subscription.val() );

    clone.appendTo( parent );

  });

  jQuery( ".form-table" ).delegate( ".remove-field", "click", function(e) {
    e.preventDefault();
    jQuery(this).parent().remove();
  });

  jQuery('#pressroom_metabox').removeClass('postbox');
  jQuery('.tabbed').css('display','none');
  jQuery('.hpub_metabox').css('display','table-row');
  jQuery('.basic_metabox').css('display','table-row');
  jQuery('.flatplan').css('display','table-row');


  jQuery('.nav-tab').click(function(e) {
    e.preventDefault();
    jQuery('.nav-tab').each(function(){
      jQuery(this).removeClass('nav-tab-active');
    })
    var tab = jQuery(this).data('tab');
    jQuery(this).addClass('nav-tab-active');
    jQuery('.tabbed').css('display','none');
    jQuery('.'+ tab).css('display','table-row');

  });

  //remove upload image
  jQuery('.remove-file').click(function(e) {
    e.preventDefault();
    if(confirm("Do you really want to delete this file?") ) {

      var field = jQuery(this).data('field');
      var term_id = jQuery(this).data('term');
      var attach_id = jQuery(this).data('attachment');
      var current = jQuery(this);

      var data = {
          'field'      : field,
          'term_id'    : term_id,
          'attach_id'  : attach_id,
          'action'     : 'remove_upload_file'
      };

      jQuery.post(ajaxurl, data, function(response) {
        if( response ) {
            current.parent().find('img').css('display', 'none');
            current.css('display','none');
        }
        else {
            alert('Error. Please retry');
        }
      });
    }

  });

  jQuery('#test-connection').click(function(e) {
    e.preventDefault();
    jQuery('#connection-result').html('<div class="spinner"></div>');
    jQuery("#connection-result .spinner").css('display','inline-block').css('float','none');

    var server    = jQuery('input[name="_pr_ftp_server[0]"]').val();
    var port      = jQuery('input[name="_pr_ftp_server[1]"]').val();
    var base      = jQuery('input[name="_pr_ftp_destination_path"]').val();
    var user      = jQuery('input[name="_pr_ftp_user"]').val();
    var password  = jQuery('input[name="_pr_ftp_password"]').val();
    var protocol  = jQuery('input[name="_pr_ftp_protocol"]:checked').val();

    var data = {
      'server'      : server,
      'port'        : port,
      'base'        : base,
      'user'        : user,
      'password'    : password,
      'protocol'    : protocol,
      'action'      : 'test_ftp_connection'
    };

    jQuery.post(ajaxurl, data, function(response) {
      if( response ) {
        jQuery('#connection-result').html(response.data.message);
        jQuery('#connection-result').removeClass( 'connection-result-success connection-result-failure' );
        jQuery('#connection-result').addClass('connection-result connection-result-'+response.data.class);
      }
    })
  });
});
