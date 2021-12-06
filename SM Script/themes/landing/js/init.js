(function($) {
	var source = theme_source();
	skel.init({
		reset: 'full',
		breakpoints: {
			global:		{ range: '*', href: 'themes/landing/css/style.css', containers: '60em', grid: { gutters: ['2em', 0] } },
			wide:		{ range: '-1680', href: 'themes/landing/css/style-wide.css' },
			normal:		{ range: '-1280', href: 'themes/landing/css/style-normal.css', viewport: { scalable: false } },
			narrow:		{ range: '-980', href: 'themes/landing/css/style-narrow.css', containers: '90%' },
			narrower:	{ range: '-840', href: 'themes/landing/css/style-narrower.css', containers: '90%!' }
		},
		plugins: {
			layers: {
					config: {
						mode: function() { return (skel.vars.isMobile ? 'transform' : 'position'); }
					}

			}
		}
	});

	$(function() {

		var	$window = $(window),
			$body = $('body'),
			$header = $('#header'),
			$banner = $('#banner');

			$("[data-login]").on('click', function() {
				if($('#login-form').is(':visible')) {
					$('#login-form').hide();
					$('#main-message').show();
					$('#recover-form').hide();
				} else {
					$('#login-form').fadeIn();
					$('#main-message').hide();
					$('#recover-form').hide();
					$('html,body').scrollTop(0);
				}
			});
			
			$("#recover-pwd").on('click', function() {
					$('#login-form').hide();
					$('#main-message').hide();
					$('#recover-form').fadeIn();
			});			
			
			$("#go-to-register-form").on('click', function() {
				$('html,body').animate({
				scrollTop: $("#cta").offset().top},
				'slow');				
			});	
			
			$('#create-acc').on('click', function() {
				$('#register').submit();
				return false;
			});
			
			$('#login-now').on('click', function() {
				$('#login').submit();
				return false;
			});	
			$('#recover-now').on('click', function() {
				$('#recover').submit();
				return false;
			});				
			$("[data-lang]").click(function() {
				var lang = $(this).attr('data-lang');
				window.location.href = "index.php?page=index&lang="+lang;
			});
					
			$('#nav > ul').dropotron({
				alignment: 'right'
			});
			
			$('#register').submit(function(e) {
				e.preventDefault();
				var findme = "Error";
				$.ajax({
						data:  $(this).serialize(),
						url:   request_source()+'/user.php',
						type:  'post',
						beforeSend: function () {
							$("#create-acc").html(site_lang[340]['text']);
							$('#error').hide();
						},			
						success:  function (response) {  	
							if ( response.indexOf(findme) > -1 ) {
								response = response.replace('Error','');
								$('#error').html(response);
								$('#error').fadeIn();
								$("#create-acc").html(site_lang[41]['text']);
							} else {
								 window.location='index.php?page=index';
							}
						}
				});					
			});
			
			$('#login').submit(function(e) {
				e.preventDefault();
				var findme = "Error";
				$.ajax({
						data:  $(this).serialize(),
						url:   request_source()+'/user.php',
						type:  'post',
						beforeSend: function () {
							$("#login-now").html(site_lang[340]['text']);
							$('#login-error').hide();
						},			
						success:  function (response) {  	
							if ( response.indexOf(findme) > -1 ) {
								response = response.replace('Error','');
								$('#login-error').html(response);
								$('#login-error').fadeIn();
								$("#login-now").html(site_lang[13]['text']);
							} else {
								 window.location='index.php?page=index';
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
							$("#recover-now").html(site_lang[340]['text']);
							$('#recover-error').hide();
						},			
						success:  function (response) {  	
							if ( response.indexOf(findme) > -1 ) {
								response = response.replace('Error','');
								$('#recover-error').html(response);
								$('#recover-error').fadeIn();
								$("#recover-now").html(site_lang[15]['text']);
							} else {
								$('#recover-error').html(site_lang[341]['text']);
								$('#recover-error').fadeIn();
								$("#recover-now").hide();								
							}
						}
				});					
			});				
			
			
			
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
			if (!skel.vars.isMobile
			&&	$header.hasClass('alt')
			&&	$banner.length > 0) {

				$window.on('load', function() {

					$banner.scrollwatch({
						delay:		0,
						range:		0.5,
						anchor:		'top',
						on:			function() { $header.addClass('alt reveal'); },
						off:		function() { $header.removeClass('alt'); }
					});

				});

			}
	});
	
})(jQuery);