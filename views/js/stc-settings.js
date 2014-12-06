jQuery(document).ready(function($) {
  $('.stc-section-heading').click(function(){ 
	$(this).siblings('.stc-section-content').toggle('slow');
  });
  $('.no-redirect').click(function(){
	return false;
  });
  $('.stc-add-new-link').click(function(){
	var parent = $(this).parents('.stc-div-container');
	$(parent).siblings('.stc-table').toggle('slow');		
  });
  $('#reddit-link').click(function(){
   var status = $(this);
   var loading = $(this).siblings('.stc-check');
   var isok = $(this).siblings('.stc-ok');
   var notok = $(this).siblings('.stc-error');
   $(loading).show();
   $(notok).hide();
   $(isok).hide();
   var reddditusername = $("#reddit-username").val();
   var redditpassword = $("#reddit-password").val();
   var data = {
     action: 'stc_reddit',
     username: reddditusername,
     password: redditpassword
    };
    jQuery.post(ajaxurl, data, function(response) {
	if(response['success'] == 'OK'){
          $(loading).hide();
          $(notok).hide();
          $(isok).show();
	}
	else{
          $(loading).hide();
          $(notok).show();
          $(isok).hide();
	}
    });
  });

  $('#gplus-link').click(function(){
   var status = $(this);
   var loading = $(this).siblings('.stc-check');
   var isok = $(this).siblings('.stc-ok');
   var notok = $(this).siblings('.stc-error');
   $(loading).show();
   $(notok).hide();
   $(isok).hide();
   var gplususername = $("#gplus-username").val();
   var gpluspassword = $("#gplus-password").val();
   var data = {
     action: 'stc_gplus',
     username: gplususername,
     password: gpluspassword
    };
    jQuery.post(ajaxurl, data, function(response) {
	if(response['success'] == 'OK'){
          $(loading).hide();
          $(notok).hide();
          $(isok).show();
	}
	else{
          $(loading).hide();
          $(notok).show();
          $(isok).hide();
	}
    });
  });

  $('#stumbleupon-link').click(function(){
   var status = $(this);
   var loading = $(this).siblings('.stc-check');
   var isok = $(this).siblings('.stc-ok');
   var notok = $(this).siblings('.stc-error');
   $(loading).show();
   $(notok).hide();
   $(isok).hide();
   var stumbleuponusername = $("#stumbleupon-username").val();
   var stumbleuponpassword = $("#stumbleupon-password").val();
   var data = {
     action: 'stc_stumbleupon',
     username: stumbleuponusername,
     password: stumbleuponpassword
    };
    jQuery.post(ajaxurl, data, function(response) {
	if(response['success'] == 'OK'){
          $(loading).hide();
          $(notok).hide();
          $(isok).show();
	}
	else{
          $(loading).hide();
          $(notok).show();
          $(isok).hide();
	}
    });
  });

  $('#friendfeed-link').click(function(){
   var status = $(this);
   var loading = $(this).siblings('.stc-check');
   var isok = $(this).siblings('.stc-ok');
   var notok = $(this).siblings('.stc-error');
   $(loading).show();
   $(notok).hide();
   $(isok).hide();
   var friendfeedusername = $("#friendfeed-username").val();
   var friendfeedpassword = $("#friendfeed-password").val();
   var data = {
     action: 'stc_friendfeed',
     username: friendfeedusername,
     password: friendfeedpassword
    };
    jQuery.post(ajaxurl, data, function(response) {
	if(response['success'] == 'OK'){
          $(loading).hide();
          $(notok).hide();
          $(isok).show();
	}
	else{
          $(loading).hide();
          $(notok).show();
          $(isok).hide();
	}
    });
  });

  $('#blogger-link').click(function(){
   var status = $(this);
   var loading = $(this).siblings('.stc-check');
   var isok = $(this).siblings('.stc-ok');
   var notok = $(this).siblings('.stc-error');
   $(loading).show();
   $(notok).hide();
   $(isok).hide();
   var bloggerusername = $("#blogger-username").val();
   var bloggerpassword = $("#blogger-password").val();
   var data = {
     action: 'stc_blogger',
     username: bloggerusername,
     password: bloggerpassword
    };
    jQuery.post(ajaxurl, data, function(response) {
	if(response['success'] == 'OK'){
          $(loading).hide();
          $(notok).hide();
          $(isok).show();
	}
	else{
          $(loading).hide();
          $(notok).show();
          $(isok).hide();
	}
    });
  });

  $('#diigo-link').click(function(){
   var status = $(this);
   var loading = $(this).siblings('.stc-check');
   var isok = $(this).siblings('.stc-ok');
   var notok = $(this).siblings('.stc-error');
   $(loading).show();
   $(notok).hide();
   $(isok).hide();
   var diigousername = $("#diigo-username").val();
   var diigopassword = $("#diigo-password").val();
   var data = {
     action: 'stc_diigo',
     username: diigousername,
     password: diigopassword
    };
    jQuery.post(ajaxurl, data, function(response) {
	if(response['success'] == 'OK'){
          $(loading).hide();
          $(notok).hide();
          $(isok).show();
	}
	else{
          $(loading).hide();
          $(notok).show();
          $(isok).hide();
	}
    });
  });

  $('#lj-link').click(function(){
   var status = $(this);
   var loading = $(this).siblings('.stc-check');
   var isok = $(this).siblings('.stc-ok');
   var notok = $(this).siblings('.stc-error');
   $(loading).show();
   $(notok).hide();
   $(isok).hide();
   var ljusername = $("#lj-username").val();
   var ljpassword = $("#lj-password").val();
   var data = {
     action: 'stc_lj',
     username: ljusername,
     password: ljpassword
    };
    jQuery.post(ajaxurl, data, function(response) {
	if(response['success'] == 'OK'){
          $(loading).hide();
          $(notok).hide();
          $(isok).show();
	}
	else{
          $(loading).hide();
          $(notok).show();
          $(isok).hide();
	}
    });
  });

  $('#scribd-link').click(function(){
   var status = $(this);
   var loading = $(this).siblings('.stc-check');
   var isok = $(this).siblings('.stc-ok');
   var notok = $(this).siblings('.stc-error');
   $(loading).show();
   $(notok).hide();
   $(isok).hide();
   var key = $("#scribd-api-key").val();
   var secret = $("#scribd-access-secret").val();
   var data = {
     action: 'stc_scribd',
     key: key,
     secret: secret
    };
    jQuery.post(ajaxurl, data, function(response) {
	if(response['success'] == 'OK'){
          $(loading).hide();
          $(notok).hide();
          $(isok).show();
	}
	else{
          $(loading).hide();
          $(notok).show();
          $(isok).hide();
	}
    });
  });

  $('#delicious-link').click(function(){
   var status = $(this);
   var loading = $(this).siblings('.stc-check');
   var isok = $(this).siblings('.stc-ok');
   var notok = $(this).siblings('.stc-error');
   $(loading).show();
   $(notok).hide();
   $(isok).hide();
   var delicioususername = $("#delicious-username").val();
   var deliciouspassword = $("#delicious-password").val();
   var data = {
     action: 'stc_delicious',
     username: delicioususername,
     password: deliciouspassword
    };
    jQuery.post(ajaxurl, data, function(response) {
	if(response['success'] == 'OK'){
          $(loading).hide();
          $(notok).hide();
          $(isok).show();
	}
	else{
          $(loading).hide();
          $(notok).show();
          $(isok).hide();
	}
    });
  });

  $('#pinterest-link').click(function(){
   var status = $(this);
   var loading = $(this).siblings('.stc-check');
   var isok = $(this).siblings('.stc-ok');

   var notok = $(this).siblings('.stc-error');
   $(loading).show();
   $(notok).hide();
   $(isok).hide();
   var pinterestusername = $("#pinterest-username").val();
   var pinterestpassword = $("#pinterest-password").val();
   var data = {
     action: 'stc_pinterest',
     username: pinterestusername,
     password: pinterestpassword
    };
    jQuery.post(ajaxurl, data, function(response) {
	if(response['success'] == 'OK'){
          $(loading).hide();
          $(notok).hide();
          $(isok).show();
	}
	else{
          $(loading).hide();
          $(notok).show();
          $(isok).hide();
	}
    });
  });
  $('.stc-remove').click(function(){
   var varid = $(this).attr("varid");
   var current = $(this);
   var loading = $(this).siblings('.stc-check');
   var isok = $(this).siblings('.stc-ok');
   var notok = $(this).siblings('.stc-error');
   $(loading).show();
   $(notok).hide();
   $(isok).hide();
   var data = {
     action: 'stc_del_user_data',
     varid: varid
    };
    jQuery.post(ajaxurl, data, function(response) {
	if(response['success'] == 'OK'){
          $(loading).hide();
          $(notok).hide();
          $(isok).show();
          $(current).html('Deleted')
	  $(current).closest('tr').remove();
 	}
	else{
          $(loading).hide();
          $(notok).show();
          $(isok).hide();
          $(current).html('Not deleted! try again');
	}
    });
  });
});
