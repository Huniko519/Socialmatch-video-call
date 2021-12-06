jQuery(document).ready(function ($) {
	var pid = 0;
	var purl;
	
	$('[data-aurl='+aurl+']').addClass('active');
	
	$('[data-review]').click(function(){								  
		pid = $(this).attr('data-review');
		purl = $(this).attr('data-psrc');		
		$('[data-review]').removeClass('selected');
		$(this).addClass('selected');		
	});
	
	$('[data-testsmtp]').click(function(e){
		e.preventDefault();
		$.ajax({ 
			type: "POST", 
			url:  request_source() + "/admin.php",
			data:{
				action: 'testsmtp',
			},
			success: function(response){
				if ( response.indexOf('Error') > -1 ) {
					swal({   title: "Error",   text: response,   type: "error" }, function(){ });	
				} else {
					swal({   title: "Email sent",   text: response,   type: "success" }, function(){ });
				}
			}
		});								
	});
	
	$('[data-lang-switch]').click(function(e){
	 	var val;
		var id = $(this).attr('data-lang-id');		
		if($('#myonoffswitch'+id).is(':checked')) { val = 1} else { val = 0};
		$.ajax({ 
			type: "POST", 
			url:  request_source() + "/admin.php",
			data:{
				action: 'lang_visible',
				id: id,
				val: val
			},
			success: function(response){
				
			}
		});								
	});	
	
	if(aurl == 'photos' ){
		$('body').keypress(function( event ) {
		  if(pid == 0){
			alert('Please select a photo first');
			return false;
		  }
		  if ( event.which == 49 ) { // approve
			event.preventDefault();
			$.ajax({ 
				type: "POST", 
				url: request_source() + "/admin.php",
				data: {
					action: 'photo',
					method: 1,
					photoid: pid
				},
				success: function(response){
					$('[data-review='+pid+']').fadeOut();				
				}
			});				 
			 
		  }		
		  if ( event.which == 50 ) { // discard
			 event.preventDefault();
			$.ajax({ 
				type: "POST", 
				url: request_source() + "/admin.php",
				data: {
					action: 'photo',
					method: 2,
					photoid: pid
				},
				success: function(response){
					$('[data-review='+pid+']').fadeOut();
				}
			});				 
			 
		  }		
		  if ( event.which == 51 ) { // private
			 event.preventDefault();
			$.ajax({ 
				type: "POST", 
				url: request_source() + "/admin.php",
				data: {
					action: 'photo',
					method: 3,
					photoid: pid
				},
				success: function(response){
					$('[data-review='+pid+']').fadeOut();			
				}
			});				 
			 
		  }		
		  if ( event.which == 119 || event.which == 87 ) { // view
			 event.preventDefault();
			 window.open(purl);
		  }			  
		});
	}
	
	//FORMS
	
	$('#website-settings').submit(function(e) {
		e.preventDefault();		
		$.ajax({ 
			type: "POST", 
			url: request_source() + "/admin.php",
			data: $(this).serialize(),
			success: function(response){
				swal({   title: "Site updated",   text: "Site updated successfully",   type: "success" }, function(){ });	
			}
		});	
	});
	
	$('[data-theme]').click(function() {
		var col = $(this).attr('data-type');
		var folder = $(this).attr('data-theme');
		$.ajax({ 
			type: "POST", 
			url: request_source() + "/admin.php",
			data: {
				action: 'change_theme',
				col: col,
				folder: folder
			},
			success: function(response){
				swal({   title: "Theme updated",   text: "Theme updated successfully",   type: "success" }, function(){ window.location.reload(); });	
			}
		});	
	});		
	
	$('#smtp-settings').submit(function(e) {
		e.preventDefault();		
		$.ajax({ 
			type: "POST", 
			url: request_source() + "/admin.php",
			data: $(this).serialize(),
			success: function(response){
				swal({   title: "SMTP updated",   text: "SMTP updated successfully",   type: "success" }, function(){ });	
			}
		});	
	});		
	
	$('#vserver-settings').submit(function(e) {
		e.preventDefault();		
		$.ajax({ 
			type: "POST", 
			url: request_source() + "/admin.php",
			data: $(this).serialize(),
			success: function(response){
				swal({   title: "Server updated",   text: "Server updated successfully",   type: "success" }, function(){ });	
			}
		});	
	});	
	
	$('#social-connect').submit(function(e) {
		e.preventDefault();		
		$.ajax({ 
			type: "POST", 
			url: request_source() + "/admin.php",
			data: $(this).serialize(),
			success: function(response){
				swal({   title: "Social connect updated",   text: "Social connect updated successfully",   type: "success" }, function(){ });	
			}
		});	
	});
	
	$('#s3-settings').submit(function(e) {
		e.preventDefault();		
		$.ajax({ 
			type: "POST", 
			url: request_source() + "/admin.php",
			data: $(this).serialize(),
			success: function(response){
				swal({   title: "Amazon S3 updated",   text: "Amazon S3 updated successfully",   type: "success" }, function(){ });	
			}
		});	
	});	
	
	$('#geokey-settings').submit(function(e) {
		e.preventDefault();		
		$.ajax({ 
			type: "POST", 
			url: request_source() + "/admin.php",
			data: $(this).serialize(),
			success: function(response){
				swal({   title: "Google Map updated",   text: "Google Map updated successfully",   type: "success" }, function(){ });	
			}
		});	
	});		
	
	$('#paypal-settings').submit(function(e) {
		e.preventDefault();		
		$.ajax({ 
			type: "POST", 
			url: request_source() + "/admin.php",
			data: $(this).serialize(),
			success: function(response){
				swal({   title: "Paypal updated",   text: "Paypal updated successfully",   type: "success" }, function(){ });	
			}
		});	
	});	
	
	$('#paygol-settings').submit(function(e) {
		e.preventDefault();		
		$.ajax({ 
			type: "POST", 
			url: request_source() + "/admin.php",
			data: $(this).serialize(),
			success: function(response){
				swal({   title: "Paygol updated",   text: "Paygol updated successfully",   type: "success" }, function(){ });	
			}
		});	
	});
	
	$('#stripe-settings').submit(function(e) {
		e.preventDefault();		
		$.ajax({ 
			type: "POST", 
			url: request_source() + "/admin.php",
			data: $(this).serialize(),
			success: function(response){
				swal({   title: "Stripe updated",   text: "Stripe updated successfully",   type: "success" }, function(){ });	
			}
		});	
	});	
	
	$('#fortumo-settings').submit(function(e) {
		e.preventDefault();		
		$.ajax({ 
			type: "POST", 
			url: request_source() + "/admin.php",
			data: $(this).serialize(),
			success: function(response){
				swal({   title: "Fortumo updated",   text: "Fortumo updated successfully",   type: "success" }, function(){ });	
			}
		});	
	});	
	
	$('#currency-settings').submit(function(e) {
		e.preventDefault();		
		$.ajax({ 
			type: "POST", 
			url: request_source() + "/admin.php",
			data: $(this).serialize(),
			success: function(response){
				swal({   title: "Currency updated",   text: "Currency updated successfully",   type: "success" }, function(){ });	
			}
		});	
	});
	
	$('#price-settings').submit(function(e) {
		e.preventDefault();		
		$.ajax({ 
			type: "POST", 
			url: request_source() + "/admin.php",
			data: $(this).serialize(),
			success: function(response){
				swal({   title: "Prices updated",   text: "Prices updated successfully",   type: "success" }, function(){ });	
			}
		});	
	});	
	
	$('#credits-settings').submit(function(e) {
		e.preventDefault();		
		$.ajax({ 
			type: "POST", 
			url: request_source() + "/admin.php",
			data: $(this).serialize(),
			success: function(response){
				swal({   title: "Credits prices updated",   text: "Credits prices updated successfully",   type: "success" }, function(){ });	
			}
		});	
	});	
	
	$('#premium-settings').submit(function(e) {
		e.preventDefault();		
		$.ajax({ 
			type: "POST", 
			url: request_source() + "/admin.php",
			data: $(this).serialize(),
			success: function(response){
				swal({   title: "Premium prices updated",   text: "Premium prices updated successfully",   type: "success" }, function(){ });	
			}
		});	
	});	
	
	$('#premium-acc-settings').submit(function(e) {
		e.preventDefault();		
		$.ajax({ 
			type: "POST", 
			url: request_source() + "/admin.php",
			data: $(this).serialize(),
			success: function(response){
				swal({   title: "Premium account updated",   text: "Premium account updated successfully",   type: "success" }, function(){ });	
			}
		});	
	});	
	
	$('#basic-acc-settings').submit(function(e) {
		e.preventDefault();		
		$.ajax({ 
			type: "POST", 
			url: request_source() + "/admin.php",
			data: $(this).serialize(),
			success: function(response){
				swal({   title: "Basic account updated",   text: "Basic account updated successfully",   type: "success" }, function(){ });	
			}
		});	
	});	
	
	$('#site-ads1').submit(function(e) {
		e.preventDefault();		
		$.ajax({ 
			type: "POST", 
			url: request_source() + "/admin.php",
			data: $(this).serialize(),
			success: function(response){
				swal({   title: "Advertise updated",   text: "Advertise updated successfully",   type: "success" }, function(){ });	
			}
		});	
	});	
	
	$('#site-ads2').submit(function(e) {
		e.preventDefault();		
		$.ajax({ 
			type: "POST", 
			url: request_source() + "/admin.php",
			data: $(this).serialize(),
			success: function(response){
				swal({   title: "Advertise updated",   text: "Advertise updated successfully",   type: "success" }, function(){ });	
			}
		});	
	});	
	
	$('#site-css').submit(function(e) {
		e.preventDefault();		
		$.ajax({ 
			type: "POST", 
			url: request_source() + "/admin.php",
			data: $(this).serialize(),
			success: function(response){
				swal({   title: "Custom css updated",   text: "Custom css updated successfully",   type: "success" }, function(){ });	
			}
		});	
	});	
	
	$('#site-js').submit(function(e) {
		e.preventDefault();		
		$.ajax({ 
			type: "POST", 
			url: request_source() + "/admin.php",
			data: $(this).serialize(),
			success: function(response){
				swal({   title: "Custom js updated",   text: "Custom js updated successfully",   type: "success" }, function(){ });	
			}
		});	
	});		
	
	$('#site-terms').submit(function(e) {
		e.preventDefault();		
		$.ajax({ 
			type: "POST", 
			url: request_source() + "/admin.php",
			data: $(this).serialize(),
			success: function(response){
				swal({   title: "Terms updated",   text: "Terms updated successfully",   type: "success" }, function(){ });	
			}
		});	
	});	
	
	$('#site-privacy').submit(function(e) {
		e.preventDefault();		
		$.ajax({ 
			type: "POST", 
			url: request_source() + "/admin.php",
			data: $(this).serialize(),
			success: function(response){
				swal({   title: "Privacy updated",   text: "Privacy updated successfully",   type: "success" }, function(){ });	
			}
		});	
	});	
	
	$('#site-user').submit(function(e) {
		e.preventDefault();		
		$.ajax({ 
			type: "POST", 
			url: request_source() + "/admin.php",
			data: $('#site-user').serialize(),
			success: function(response){
				swal({   title: "User updated",   text: "User updated successfully",   type: "success" }, function(){ });	
			}
		});	
	});		
	
	$("[data-gift]").keyup(function(){
		var giftid = $(this).attr('data-gift');
		var val = $(this).val();			
		$.ajax({ 
			type: "POST", 
			url: request_source() + "/admin.php",
			data: {
				action : "gift",
				giftid : giftid,
				val : val,					
			},
			beforeSend: function(){
				$('.box-body').find('input[data-gift$="'+giftid+'"]').css("background","#f1f1f1");	
			},
			success: function(response){
				$('.box-body').find('input[data-gift$="'+giftid+'"]').css("background","#ceffc9");					
			}
		});	
	});	
	
	$("[data-langid]").keyup(function(){
		var langid = $(this).attr('data-langid');
		var val = $(this).val();			
		var lid = $(this).attr('data-lid');	
		$.ajax({ 
			type: "POST", 
			url: request_source() + "/admin.php",
			data: {
				action : "editlang",
				langid : langid,
				val : val,					
				lid: lid
			},
			beforeSend: function(){
				$('.box-body').find('input[data-lid="'+lid+'"]').css("background","#f1f1f1");	
			},
			success: function(response){
				$('.box-body').find('input[data-lid="'+lid+'"]').css("background","#ceffc9");					
			}
		});	
	});
	
	$('#searchUser').keyup(function(){
		var dat = $(this).val();
		$.ajax({ 
			type: "POST", 
			url: request_source() + "/admin.php",
			data: {
				action : "usearch",
				dat : dat
			},
			beforeSend: function(){
				$('#userslist').html('<center><img src="'+ theme_source()+'/landing/images/status.gif"/></center>');
			},
			success: function(response){
				$('#userslist').html('<tr><th></th><th>ID</th><th>User</th><th>Email</th><th>City</th><th>Country</th><th>Credits</th>	<th>Total photos</th><th>Join date</th><th></th></tr>'+response);				
			}				
		});					
	});	
	
	$('[data-delete-user]').click(function(e) {
		var uid = $(this).attr('data-delete-user');								   
		swal({
			title: 'Account termination',
			text: 'The data will be lost without recovery, continue?',
			confirmButtonText: "Yes, delete it!",				
			type: "warning",
			showCancelButton: true,				
			},
			function(){
			$.ajax({ 
				data: {
					action: 'delete_profile',
					uid : uid
				},
				url:   request_source()+'/admin.php',
				type:  'post',
				beforeSend: function(){	
				},
				success: function(response){
					self.close();
				}
			});	
		});			
	});	
});
