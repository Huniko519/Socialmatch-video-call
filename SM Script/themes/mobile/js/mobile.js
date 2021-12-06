jQuery(document).ready(function ($) { 
								 
		$('#register').submit(function(e) {
			e.preventDefault();
			var findme = "Error";
			$.ajax({
					data:  $(this).serialize(),
					url:   request_source()+'/user.php',
					type:  'post',
					beforeSend: function () {
						$("#create-acc").val(site_lang[340]['text']);
						$('#error').hide();
					},			
					success:  function (response) {  	
						if ( response.indexOf(findme) > -1 ) {
							response = response.replace('Error','');							
							$('#error').html(response);
							$('#error').fadeIn();
							$("#create-acc").val(site_lang[41]['text']);
						} else {
							 if(app_user != 0){
								 window.close();
							 } else {
								window.location='mobile.php?page=index'; 
							 }
						}
					}
			});					
		});								 
			$('#recover').submit(function(e) {
				e.preventDefault();
				var findme = "Error";
				$.ajax({
						data:  $(this).serialize(),
						url:   request_source()+'/user.php',
						type:  'post',
						beforeSend: function () {
							$("#recover-now").val(site_lang[340]['text']);
							$('#recover-error').hide();
						},			
						success:  function (response) {  	
							if ( response.indexOf(findme) > -1 ) {
								response = response.replace('Error','');
								$('#recover-error').html(response);
								$('#recover-error').fadeIn();
								$("#recover-now").val(site_lang[15]['text']);
							} else {
								$('#recover-error').html(site_lang[341]['text']);
								$('#recover-error').fadeIn();
								$("#recover-now").hide();								
							}
						}
				});					
			});		
	
	$('#login').submit(function(e) {
		e.preventDefault();
		var findme = "Error";
		var email = $('#login_email').val();
		var pass  = $('#login_pass').val();
		var dID  = $('#login_dID').val();		
		$.ajax({
				data:  {
					login_email: email,
					login_pass: pass,
					login_dID: dID,					
					action: 'login'
				},
				url:   request_source()+'/user.php',
				type:  'post',
				beforeSend: function () {
					
					$("#login-now").val(site_lang[340]['text']);
					$('#login-error').hide();

				},			
				success:  function (response) {  	
					if ( response.indexOf(findme) > -1 ) {
						response = response.replace('Error','');
						$('#login-error').html(response);
						$('#login-error').fadeIn();
						$("#login-now").val(site_lang[13]['text']);
					} else {
						 
						 window.location='mobile.php?page=discover';
					}
				}
		});					
	});
	

	function readURL(input) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			
			reader.onload = function (e) {
				$('#cpp').css('background-image', 'url('+e.target.result+')');
			}
			
			reader.readAsDataURL(input.files[0]);
		}
	}

	$('#cpp').on('click', function(e){
	  e.preventDefault();
	  $("#add-photos-file2").click();
	});
	$('#cpp2').on('click', function(e){
	  e.preventDefault();
	  $("#add-photos-file2").click();
	});		  
	$("#add-photos-file2").change(function() {
	  readURL(this);
	  $('#cpp2').hide();
	  $("#add-photos-form2").submit();
	});

	(function() {
	  $('#add-photos-form2').ajaxForm({
		  success: function(msg) {
		  },
		  complete: function(xhr) {
			  var result;
			  result = xhr.responseText;
			  result = $.parseJSON(result);
			  $.each(result, function(index, value){
				  if( value.s3 == 1 ){
					  $('#profilePhoto').val(value.photo);
					  $('#profileThumb').val(value.success);
					  $('#profilePrivate').val(value.private);					  
				  } else {
					  $('#profilePhoto').val(site_url()+'/assets/sources/uploads/'+value.photo);
					  $('#profileThumb').val(site_url()+'/assets/sources/uploads/'+value.success);
					  $('#profilePrivate').val(site_url()+'/assets/sources/uploads/'+value.private);	
				  }
	
			  });
		  }
	  });
	
	})();	
	
	var placeSearch, autocomplete;
	var componentForm = {
	  locality: 'long_name',
	  country: 'long_name'
	};
	
	function initialize() {
	  autocomplete = new google.maps.places.Autocomplete(
		  /** @type {HTMLInputElement} */(document.getElementById('loc')),
		  { types: ['geocode'] });
		google.maps.event.addListener(autocomplete, 'place_changed', function() {
		fillInAddress();
	  });
	}
	
	function fillInAddress() {

	  var place = autocomplete.getPlace();
	
	  for (var component in componentForm) {
		document.getElementById(component).value = '';
		document.getElementById(component).disabled = false;
	  }
	var lat = place.geometry.location.lat(),
		lng = place.geometry.location.lng();
		document.getElementById('lat').value = lat;
		document.getElementById('lng').value = lng;
	  for (var i = 0; i < place.address_components.length; i++) {
		var addressType = place.address_components[i].types[0];
		if (componentForm[addressType]) {
		  var val = place.address_components[i][componentForm[addressType]];
		  document.getElementById(addressType).value = val;
		}
	  }
	}
	initialize();	
});	