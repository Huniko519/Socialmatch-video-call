user_info.credits = parseInt(user_info.credits);
var meet_age_array = user_info.s_age.split(",");
var meet_age = meet_age_array[2];
var meet_gender = user_info.s_gender;
var meet_radius = user_info.s_radius;
var meet_online = 0;
var meet_limit = 0;
var backspace_alert = 0;
var searchIndex = [];
var emojiInit = false;
var d_url = 'about';
var replaceText = function (el, selection, emo) {
var val = el.value || el.innerHTML || '';

if (selection.ce) { // if contenteditable
  el.focus();
  document.execCommand('insertText', false, emo);
} else {
  el.value = val.substring(0, selection.start) + emo;

  // @todo - [needim] - check browser compatibilities
  el.selectionStart = el.selectionEnd = selection.start + emo.length;
  el.focus();
}
};

/**
* Fire custom events
*
* @param eventName
* @param params
*/
var fire = function (eventName, params) {
var handler, i, len, ref;
ref = wdtEmojiBundle.dispatchHandlers[eventName];
for (i = 0, len = ref.length; i < len; i++) {
  handler = ref[i];
  handler(params);
}
};


var live = function (eventType, elementQuerySelector, cb) {
document.addEventListener(eventType, function (event) {

  var qs = document.querySelectorAll(elementQuerySelector);

  if (qs) {
	var el = event.target, index = -1;
	while (el && ((index = Array.prototype.indexOf.call(qs, el)) === -1)) {
	  el = el.parentElement;
	}

	if (index > -1) {
	  cb.call(el, event);
	}
  }
});
};

live('click', '.wdt-emoji-list a.wdt-emoji', function (event) {
  var selection = getSelection(wdtEmojiBundle.input);

  replaceText(wdtEmojiBundle.input, selection, ':' + this.dataset.wdtEmojiShortname + ':');
  fire('select', {el: wdtEmojiBundle.input, event: event, emoji: ':' + this.dataset.wdtEmojiShortname + ':'});

  var ce = new Event('input');
  wdtEmojiBundle.input.dispatchEvent(ce);
  wdtEmojiBundle.close();

  return false;
});

wdtEmojiBundle.defaults.emojiSheets = {
	'apple'   : theme_source()+'/sheets/sheet_apple_64.png',
	'google'  : theme_source()+'/sheets/sheet_apple_64.png',
	'twitter' : theme_source()+'/sheets/sheet_apple_64.png',
	'emojione': theme_source()+'/sheets/sheet_apple_64.png'
};


function interestSuggest(){
	$('[data-interest-add]').click(function(){
		var val = $(this).attr('data-interest-add');
		var html = $('#new-int').html();
		$('#new-int').html(html+'<div class="int"><span>'+ val +'</span></div>');
		$('#searchBox').val('');
		$('#searchResults').addClass('hiddden');
		
		$.ajax({
			url: request_source()+'/user.php', 
			data: {
				action:"add_interest",
				name: val
			},	
			type: "post",			
			success: function(response) {
				
			}
		});			
	});
}
function deleteInterest(val){
	$('[data-interest='+val+']').hide();
	$.ajax({
		url: request_source()+'/user.php', 
		data: {
			action:"del_interest",
			id: val
		},	
		type: "post",			
		success: function(response) {
			
		}
	});			
}


function profilePhoto(){
	$(".profile-photo").each(function(){
		var src = $(this).attr("data-src");
		$(this).css('background-image', 'url('+src+')');			
	});
	$(".profile-photo").preload(function(){
		$(this).show();
	});		
}

function profilePhotoViewer(){
	
	$(".photo").each(function(){
		$(this).hover(function(){
			$(this).find('.data').fadeIn(); 
		  },function(){
			$(this).find('.data').fadeOut(); 
		});        
		var src = $(this).attr("data-src");
		$(this).css('background-image', 'url('+src+')');
	});
	
	$(".fbphotobox-overlay").remove();
	$(".fbphotobox-main-container").remove();
	$(".fbphotobox-fc-main-container").remove();
	$(".fbphotobox-main-container").remove();		
	$(".viewer .data").fbPhotoBox({
		rightWidth: 350,
		leftBgColor: "black",
		rightBgColor: "white",
		footerBgColor: "black",
		overlayBgColor: "#1D1D1D",
		profile: true,
	}); 		
}

function profileLinks(){
	$(".profile-content").mCustomScrollbar({
		autoHideScrollbar:true,
		theme:"dark",
		scrollButtons:{
			enable: true 
		},
		mouseWheel:{
			preventDefault: true,
			deltaFactor: 180
		}                
	});
	if(user_info.id == profile_info.id && d_url == 'about'){
		var input = document.getElementById("searchBox"),
			ul = document.getElementById("searchResults"),
			inputTerms, termsArray, prefix, terms, results, sortedResults;
		
		
		var search = function() {
		  inputTerms = input.value.toLowerCase();
		  results = [];
		  termsArray = inputTerms.split(' ');
		  prefix = termsArray.length === 1 ? '' : termsArray.slice(0, -1).join(' ') + ' ';
		  terms = termsArray[termsArray.length -1].toLowerCase();
		  
		  for (var i = 0; i < searchIndex.length; i++) {
			var a = searchIndex[i].toLowerCase(),
				t = a.indexOf(terms);
			
			if (t > -1) {
			  results.push(a);
			}
		  }
		  
		  evaluateResults();
		};
		
		var evaluateResults = function() {
		  if (results.length > 0 && inputTerms.length > 0 && terms.length !== 0) {
			sortedResults = results.sort(sortResults);
			appendResults();
		  } 
		  else if (inputTerms.length > 0 && terms.length !== 0) {
		   return;
			
		  }
		  else if (inputTerms.length !== 0 && terms.length === 0) {
			return;
		  }
		  else {
			clearResults();
		  }
		};
		
		var sortResults = function (a,b) {
		  if (a.indexOf(terms) < b.indexOf(terms)) return -1;
		  if (a.indexOf(terms) > b.indexOf(terms)) return 1;
		  return 0;
		}
		
		var appendResults = function () {
		  clearResults();
		  
		  for (var i=0; i < sortedResults.length && i < 5; i++) {
			var val = sortedResults[i];
			var li = document.createElement("li"),
				result = prefix 
				  + sortedResults[i].toLowerCase().replace(terms, '<strong>' 
				  + terms 
				  +'</strong>');
			li.id = 'int-'+val;
			li.setAttribute('data-interest-add', val);
			li.innerHTML = result;
			ul.appendChild(li);
		  }
		  
		  if ( ul.className !== "term-list") {
			ul.className = "term-list";
		  }
		  interestSuggest();
		};
		
		var clearResults = function() {
		  ul.className = "term-list hiddden";
		  ul.innerHTML = '';
		};
		  
		input.addEventListener("keyup", search, false);							
	}
	$('[data-url]').click(function(){
		var uid = $(this).attr('data-uid');
		var durl = $(this).attr('data-url');
		
		switch (durl) {
			
			case "about":
				d_url = 'about';
				$('#data-content').css("opacity","0.5");
				$.ajax({
					url: request_source()+'/user.php', 
					data: {
						action:"wall",
						id : uid
					},	
					type: "post",			
					success: function(response) {
						$('#data-content').html(response);
						$('#data-content').css("opacity","1");
					},
					complete: function(){	
						profileLinks();
					}
				});					
			break;	
			
			case "photos":
				d_url = 'photos';			
				$('#data-content').css("opacity","0.5");
				$.ajax({
					url: request_source()+'/user.php', 
					data: {
						action:"photos",
						id : uid
					},	
					type: "post",			
					success: function(response) {
						$('#data-content').html(response);
						$('#data-content').css("opacity","1");
					},
					complete: function(){
						profilePhoto();
						profilePhotoViewer();	
						profileLinks();
					}
				});					
			break;
			
			case "p_photos":
				d_url = 'photos';				
				$('#data-content').css("opacity","0.5");
				$.ajax({
					url: request_source()+'/user.php', 
					data: {
						action:"p_photos",
						id : uid
					},	
					type: "post",			
					success: function(response) {
						$('#data-content').html(response);
						$('#data-content').css("opacity","1");
					},
					complete: function(){
						profilePhoto();
						profilePhotoViewer();	
						profileLinks();
						privateLinks();
					}
				});					
			break;				
	
		}
	});		
}

function reportUser(uid,name,photo){
	swal({   title: site_lang[326]['text'],   text: site_lang[327]['text']+' '+name,   imageUrl: photo,   showCancelButton: true,   confirmButtonColor: "#09c66e",   confirmButtonText: site_lang[259]['text'], cancelButtonText: site_lang[195]['text'],   closeOnConfirm: true }, function(){
						$.ajax({
							url: request_source()+'/user.php', 
							data: {
								action:"report",
								id : uid
							},
							dataType: "JSON",
							type: "post",			
							success: function(response) {
							},
							complete: function(){
								$.ajax({
									url: request_source()+'/user.php', 
									data: {
										action:"block",
										id : uid
									},	
									type: "post",			
									success: function(response) {
										window.location.href = site_config.site_url+'meet';
									}
								});								
							}
						});
					});
}
function goToProfile(uid){
	if(mobile == true){
		window.location.href = 'mobile.php?page=profile&id='+uid;	
	}
	$('#data-content').css("opacity","0.5");
	$.ajax({
		url: request_source()+'/user.php', 
		data: {
			action:"wall",
			id : uid
		},	
		type: "post",			
		success: function(response) {
			$('#data-content').html(response);
			$('.profile-top-left .profile-photo').attr('data-src',profile_info.profile_photo);
			$('.user-name h1').html(profile_info.name + ' '+ profile_info.online + '');
			if(profile_info.id != user_info.id){
				$('.gift').attr('data-tooltip','Like');
				$('.gift').attr('data-act','like');
				$('.gift').html('<i class="mdi-action-favorite"></i>');
				$('.videocall').attr('data-tooltip',site_lang[16]['text']);				
				$('.videocall').attr('data-uid',profile_info.id);
				$('.videocall').attr('data-surl','chat');			
				$('.videocall').html('<i class="mdi-communication-chat"></i>');
			}
			window.history.pushState("profile",profile_info.name + ", " + profile_info.age + " | " + site_title(),site_config.site_url+'profile/'+profile_info.id+'/'+profile_info.link);							
			$('#data-content').css("opacity","1");
			if(backspace_alert < 2 && profile_info.id != user_info.id){
				flatNotify().alert(site_lang[328]['text'],5000)
				backspace_alert++;
			}
			profilePhoto();
		},
		complete: function(){
			profileLinks();		
		}
	})		
}

	function privateLinks(){
		$('#ask-permission').click(function(){
			$('#data-content').css("opacity","0.5");
			url = "chat";
			$.ajax({
				url: request_source()+'/user.php', 
				data: {
					action:"chat_p",
					id : profile_info.id
				},	
				type: "post",			
				success: function(response) {
					window.location.href = site_config.site_url+'chat/'+profile_info.id+'/'+profile_info.link	
				},
				complete: function(){
				}
			});
		});	
		$('#buy-permission').click(function(){
				if(user_info.credits < site_prices.private){
					$('#payment_module').show();
				} else {											
swal({   title: site_lang[191]['text'],   text: site_lang[192]['text']+' '+profile_info.first_name+' '+ site_lang[193]['text'] +' '+ site_prices.private +' ' + site_lang[73]['text'],   imageUrl: profile_info.profile_photo,   showCancelButton: true,   confirmButtonColor: "#09c66e",   confirmButtonText: site_lang[194]['text'], cancelButtonText: site_lang[195]['text'],   closeOnConfirm: true }, function(){
					$.ajax({
						url: request_source()+'/user.php', 
						data: {
							action:"p_access",
							id : profile_info.id
						},	
						type: "post",			
						success: function(response) {
							$('.profile-menu .selected').click();
						}
					});
				});
			}
		});			
	}

jQuery(document).ready(function ($) { 
								 
 	var in_videocall = false;
	var search_users = false;
	var called = false;	
	var peer;
	var payment_method = 0;
	var video_user = 0;
	var meet_pages = 0;	
	var videocall_user = 0;	
	var sec = 0;
	var gift_price = 0;
	var photos_count = 0;	
	var timer;
	var callSound;
	var profile_url;
	var current_user_chat;
	var current_user;
	var user_name;
	var title = 0;	
	var my_profile = 0;		
	var galleria_photos;
	var noti = 0;

	
	var sendPhoto = { 
		success: function(data) {
			if(mobile == false){
				$(".chat").append(data);
				$('.chat').mCustomScrollbar("destroy");
				photosChatWall();
				profilePhoto();
				scroller();
			} else {
				$(".list-chats").append(data);
			}
		},
		resetForm: true
	}; 	
	
    $(".menu ul li").hover(function(){
        $(this).addClass('menu-selected');
        $(this).find("i").css("color","#fff");
    },function(){
        $(this).removeClass('menu-selected');
        $(this).find("i").css("color","#9ec7f7");        
    });
	
	$('body').keyup(function(e) {
			switch (e.keyCode) {
				case 8:  // Backspace
					if(backspace_alert > 0 && url != 'chat' && profile_info.id != user_info.id){
						
						$('#data-content').css("opacity","0.5");
							$.ajax({
								url: request_source()+'/user.php', 
								data: {
									action:"meet_back"						
								},	
								type: "post",			
								success: function(response) {
									$('#data-content').html(response);
									filterBtn();
									$('.profile-top-left .profile-photo').attr('data-src',user_info.profile_photo);	
									profilePhoto();
									$('.user-name h1').html(user_info.name + ' '+ user_info.online + '');
									$('.gift').attr('data-tooltip',site_lang[54]['text']);
									$('.gift').attr('data-act','mphotos');
									$('.gift').html('<i class="mdi-image-photo-camera"></i>');				
									$('.videocall').attr('data-tooltip',site_lang[47]['text']);
									$('.videocall').attr('data-uid',user_info.id);
									$('.videocall').attr('data-surl','settings');			
									$('.videocall').html('<i class="mdi-action-settings"></i>');		
									window.history.pushState("meet",site_title(),site_config.site_url+'meet');
									$('#meet_section').css("opacity","0.5");	
									$.ajax({
										url: request_source()+'/user.php', 
										data: {
											action:"meet_filter",
											age: meet_age,
											gender: meet_gender,
											radius: meet_radius,
											online: meet_online,
											limit: meet_limit					
										},	
										type: "post",			
										success: function(response) {
											$('#meet_section').mCustomScrollbar("destroy");					
											$('#meet_section').html(response);
											scroller();	
											profilePhoto();	
											meetPagination();
											$('#meet_section').css("opacity","1");							
										},
									});										
									$('#data-content').css("opacity","1");							
								},
							});														
					}
				break;
			}
	});	
	
    function startGalleria(data) {
        if(url == "discover"){
			$(".liked").hide();
			$(".disliked").hide();			
            Galleria.loadTheme(theme_source()+'/css/galleria/galleria.classic.min.js');
			var y = $('body').height();
			y = y-60;
			if(mobile == true){
				Galleria.configure({
					thumbnails: "hide"
				});				
				Galleria.run(".discover",{
					autoplay: true,
					height: y,
					dataSource: data,
					transition: "fade",
					imageCrop: true
	
				});				
			} else {
				Galleria.run(".discover",{
					autoplay: true,
					dataSource: data,
					transition: "fade",
					imageCrop: true
	
				});				
			}

            Galleria.ready(function(options){
                this.attachKeyboard({
                    left: this.prev,
                    right: this.next
                });
            });    
        }
        if(url == "profile"){		
            Galleria.loadTheme(theme_source()+'/css/galleria/galleria.classic.min.js');
			var y = $('body').height();
			y = y-60;
			$('.hero-image').css('height',y);
			$('.hero-image-private').css('height',y);
			$('.hero-image-private').css('width','100%');
			if(mobile == true){			
				Galleria.run(".hero-image-img",{
					autoplay: true,
					height: y,
					dataSource: data,
					transition: "fade",
					imageCrop: true
	
				});				
			}

            Galleria.ready(function(options){
                this.attachKeyboard({
                    left: this.prev,
                    right: this.next
                });
				this.bind('image', function(e) {
					var string = e.galleriaData.big;
					if (string.indexOf("private") !=-1) {
						$('.hero-image-private').show();	
					} else {
						$('.hero-image-private').hide();
					}
				});				
            });    
        }		
    }

	function suggestLike(){
		$('[data-action]').click(function(){								  
			var uid = $(this).attr('data-uid');
			var action = $(this).attr('data-action');
			var div = $(this).parent('.follow-suggest');
			var show = $('body').find(".follow-suggest:hidden:first");
			switch (action) {
				case "like":
					$.ajax({
						url: request_source()+'/user.php', 
						data: {
							action:"like",
							id : uid
						},	
						type: "post",
						beforeSend: function(){
							div.fadeOut('fast');
							div.remove();
						},
						success: function(response) {
						},
						complete: function(){
							show.fadeIn('slow');
						}
					});			
				break;				
			}
		});
	}
	suggestLike();
	
	switch (url) {
		case "profile":	
			$('.profile-top-left .profile-photo').attr('data-src',profile_info.profile_photo);	
			$('.user-name h1').html(profile_info.name + ' '+ profile_info.online + '');
			if(profile_info.id != user_info.id){
				$('.gift').attr('data-tooltip',site_lang[77]['text']);
				$('.gift').attr('data-act','gift');
				$('.gift').html('<i class="mdi-action-wallet-giftcard"></i>');
				$('.videocall').attr('data-tooltip',site_lang[16]['text']);				
				$('.videocall').attr('data-uid',profile_info.id);
				$('.videocall').attr('data-surl','chat');			
				$('.videocall').html('<i class="mdi-communication-chat"></i>');
			} else {
				$('.gift').attr('data-tooltip',site_lang[54]['text']);
				$('.gift').attr('data-act','mphotos');
				$('.gift').html('<i class="mdi-image-photo-camera"></i>');				
				$('.videocall').attr('data-tooltip',site_lang[47]['text']);
				$('.videocall').attr('data-uid',profile_info.id);
				$('.videocall').attr('data-surl','settings');			
				$('.videocall').html('<i class="mdi-action-settings"></i>');		
			}
			if(mobile == true){
				startGalleria(profile_user_photos);	
				privateLinks();
				$("#block-mobile").click(function(){ reportUser(profile_info.id,'"'+profile_info.name+'"','"'+profile_info.profile_photo+'"'); });
			}
					
			setTimeout(function(){
				profileLinks();
			},1000);
		break;
		
		case "chat":	

					
			$('.profile-top-left .profile-photo').attr('data-src',profile_info.profile_photo);	
			$('.user-name h1').html(profile_info.name + ' '+ profile_info.online + '');
			if(profile_info.id != user_info.id){
				$('.gift').attr('data-tooltip',site_lang[77]['text']);
				$('.gift').attr('data-act','gift');
				$('.gift').html('<i class="mdi-action-wallet-giftcard"></i>');
				$('.videocall').attr('data-tooltip','Like');
				$('.videocall').attr('data-act','like');
				$('.videocall').html('<i class="mdi-action-favorite"></i>');
			} else {
				$('.gift').attr('data-tooltip',site_lang[54]['text']);
				$('.gift').attr('data-act','mphotos');
				$('.gift').html('<i class="mdi-image-photo-camera"></i>');				
				$('.videocall').attr('data-tooltip',site_lang[47]['text']);
				$('.videocall').attr('data-uid',profile_info.id);
				$('.videocall').attr('data-surl','settings');			
				$('.videocall').html('<i class="mdi-action-settings"></i>');		
			}
			$('[data-cuid='+profile_info.id+']').attr('data-message',0);
			
			$('#r_id').val(profile_info.id);
			$('#rid').val(profile_info.id);
			current_user = profile_info.id;
			current_user_id = profile_info.id;
			user_name = profile_info.name;	
			current_chat(profile_info.id);
			chatMessage();			
			if(mobile == true){
				$('#send-photo').on('click', function(e){
					 e.preventDefault();
					$("#photo-to-send").click(); 
				});
				$("#photo-to-send").change(function() {
					$("#sendPhoto").submit();
				});
				$('#sendPhoto').submit(function() { 
						$(this).ajaxSubmit(sendPhoto);  			
						return false; 
				});	
				$('li').each(function(){
					var rend = $(this).html();					 
					var rended = wdtEmojiBundle.render(rend);
					$(this).html(rended);
				});				
			} else {
				$('.post').each(function(){
					var rend = $(this).html();					 
					var rended = wdtEmojiBundle.render(rend);
					$(this).html(rended);
				});				
			}

			videocallBtn();
		
		break;
		case "messages":
			wdtEmojiBundle.init('#moti');	
			$('.message-text').each(function(){
				var rend = $(this).html();					 
				var rended = wdtEmojiBundle.render(rend);
				$(this).html(rended);
			});			
		break;		
		case "discover":
			$('.profile-top-left .profile-photo').attr('data-src',user_info.profile_photo);
			$('.user-name h1').html(user_info.name + ' '+ user_info.online + '');		
				$('.gift').attr('data-tooltip',site_lang[54]['text']);
				$('.gift').attr('data-act','mphotos');
				$('.gift').html('<i class="mdi-image-photo-camera"></i>');				
				$('.videocall').attr('data-tooltip',site_lang[47]['text']);
				$('.videocall').attr('data-uid',user_info.id);
				$('.videocall').attr('data-surl','settings');			
				$('.videocall').html('<i class="mdi-action-settings"></i>');				
			game_start();
			game_btns();				
		break;	
		
		case "settings":
			$('.profile-top-left .profile-photo').attr('data-src',user_info.profile_photo);
			$('.user-name h1').html(user_info.name + ' '+ user_info.online + '');		
				$('.gift').attr('data-tooltip',site_lang[54]['text']);
				$('.gift').attr('data-act','mphotos');
				$('.gift').html('<i class="mdi-image-photo-camera"></i>');				
				$('.videocall').attr('data-tooltip',site_lang[47]['text']);
				$('.videocall').attr('data-uid',user_info.id);
				$('.videocall').attr('data-surl','settings');			
				$('.videocall').html('<i class="mdi-action-settings"></i>');			
			profileForms();				
		break;
		
		case "mobile_photos":
		  managePhotos();
		  $('[data-mobile-edit-photo]').click(function() {    
			$('.add-photo').fadeIn(300);
			setTimeout(function(){
			   $('.btn').addClass('animation')      
			},10)      
		  });  
		  $('.add-photo').click(function(){
			setTimeout(function(){
			  $('.add-photo .btn').addClass('hide');
			  $('.btn').removeClass('hide');
			  setTimeout(function(){
				$('.add-photo').fadeOut(300);       
			  },300) 
			},10)   
			$('.btn').removeClass('animation');     
		  });		
		break;
		
		case "fans":
			if(mobile == true){
				$('a').each(function(){
					var href = $(this).attr('href');
					var re = href.replace('profile','m_profile');
					$(this).attr('href',re);
				});
			}
		break;
		case "visits":
			if(mobile == true){
				$('a').each(function(){
					var href = $(this).attr('href');
					var re = href.replace('profile','m_profile');
					$(this).attr('href',re);
				});
			}
		break;		
		case "popular":
		break;	
		case "matches":
			if(mobile == true){
				$('a').each(function(){
					var href = $(this).attr('href');
					var re = href.replace('profile','m_profile');
					$(this).attr('href',re);
				});
			}
		break;			
		
		default:
		$('.profile-top-left .profile-photo').attr('data-src',user_info.profile_photo);
		$('.user-name h1').html(user_info.name + ' '+ user_info.online + '');
				$('.gift').attr('data-tooltip',site_lang[54]['text']);
				$('.gift').attr('data-act','mphotos');
				$('.gift').html('<i class="mdi-image-photo-camera"></i>');				
				$('.videocall').attr('data-tooltip',site_lang[47]['text']);
				$('.videocall').attr('data-uid',user_info.id);
				$('.videocall').attr('data-surl','settings');			
				$('.videocall').html('<i class="mdi-action-settings"></i>');			
		break;
	}	
	

	
	$('#filter_ok').click(function(){
		$("#m-filter-form").submit();
	});

	
	function spotLinks(){
		$('[data-surl]').click(function(){
			var uid = $(this).attr('data-uid');
			var surl = $(this).attr('data-surl');
			
			switch (surl) {
				
				case "profile":	
					$('#data-content').css("opacity","0.5");
					$.ajax({
						url: request_source()+'/user.php', 
						data: {
							action:"wall",
							id : uid
						},	
						type: "post",			
						success: function(response) {
							$('#data-content').html(response);
							$('.profile-top-left .profile-photo').attr('data-src',profile_info.profile_photo);	
							$('.user-name h1').html(profile_info.name + ' '+ profile_info.online + '');
							if(profile_info.id != user_info.id){
								$('.gift').attr('data-tooltip',site_lang[77]['text']);
								$('.gift').attr('data-act','gift');
								$('.gift').html('<i class="mdi-action-wallet-giftcard"></i>');
								$('.videocall').attr('data-tooltip',site_lang[16]['text']);				
								$('.videocall').attr('data-uid',profile_info.id);
								$('.videocall').attr('data-surl','chat');			
								$('.videocall').html('<i class="mdi-communication-chat"></i>');
							} else {
								$('.gift').attr('data-tooltip',site_lang[54]['text']);
								$('.gift').attr('data-act','mphotos');
								$('.gift').html('<i class="mdi-image-photo-camera"></i>');				
								$('.videocall').attr('data-tooltip',site_lang[47]['text']);
								$('.videocall').attr('data-uid',profile_info.id);
								$('.videocall').attr('data-surl','settings');			
								$('.videocall').html('<i class="mdi-action-settings"></i>');		
							}
							window.history.pushState("profile",site_config.site_url+'profile/'+uid+'/spotlight');							
							$('#data-content').css("opacity","1");							
						},
						complete: function(){
							profilePhoto();
							profileLinks();
						}
					})				
				break;
				
				case "chat":		
					$('#data-content').css("opacity","0.5");
					url = "chat";
					$.ajax({
						url: request_source()+'/user.php', 
						data: {
							action:"chat",
							id : uid
						},	
						type: "post",			
						success: function(response) {
							response = wdtEmojiBundle.render(response);
							$('#data-content').html(response);
							$('#chat-message').focus();	
							title = 0;							
							document.title = site_title();								
							current_user = uid;
							current_user_id = uid;
							user_name = response.name;					
							$('[data-cuid='+uid+']').attr('data-message',0);
							$('#r_id').val(uid);
							$('#rid').val(uid);

							scroller();
							$('#data-content').css("opacity","1");	
							profilePhoto();
							photosChatWall();							
							$('#send-photo').on('click', function(e){
								 e.preventDefault();
								$("#photo-to-send").click(); 
							});
							$("#photo-to-send").change(function() {
								$("#sendPhoto").submit();
							});
							$('#sendPhoto').submit(function() { 
									$(this).ajaxSubmit(sendPhoto);  			
									return false; 
							});
							window.history.pushState("chat",site_config.site_url+'chat/'+profile_info.id+'/'+profile_info.link);							
							current_chat(uid);
							videocallBtn();
						},
						complete: function(){
							chatMessage();

						}
					});
				break;
				case 'like':
					
					$.ajax({
						url: request_source()+'/user.php', 
						data: {
							action:"game_like",
							id : uid,
							like: 1
						},	
						type: "post",
						beforeSend: function(){
							
						},
						success: function(response) {
							game_start();
						},
						complete: function(){
						}
					});
				break;
				case "settings":	
					window.location.href = site_config.site_url+'settings';
				break;				
			
			}
		});	
	}
	
	function suggestLinks(){
		$('[data-ssurl]').click(function(){
			var uid = $(this).attr('data-uid');
			var ssurl = $(this).attr('data-ssurl');
			
			switch (ssurl) {
				
				case "profile":	
					$('#data-content').css("opacity","0.5");
					$.ajax({
						url: request_source()+'/user.php', 
						data: {
							action:"wall",
							id : uid
						},	
						type: "post",			
						success: function(response) {
							$('#data-content').html(response);
							$('.profile-top-left .profile-photo').attr('data-src',profile_info.profile_photo);	
							$('.user-name h1').html(profile_info.name + ' '+ profile_info.online + '');
							if(profile_info.id != user_info.id){
								$('.gift').attr('data-tooltip',site_lang[77]['text']);
								$('.gift').attr('data-act','gift');
								$('.gift').html('<i class="mdi-action-wallet-giftcard"></i>');
								$('.videocall').attr('data-tooltip',site_lang[16]['text']);				
								$('.videocall').attr('data-uid',profile_info.id);
								$('.videocall').attr('data-surl','chat');			
								$('.videocall').html('<i class="mdi-communication-chat"></i>');
							}
							window.history.pushState("profile",site_config.site_url+'profile/'+profile_info.id+'/'+profile_info.link);							
							$('#data-content').css("opacity","1");							
						},
						complete: function(){
							profilePhoto();
							profileLinks();
						}
					})				
				break;					
			}
		});	
	}
	
	
	
	function chatLinks(){
		$('[data-purl]').click(function(){
			var uid = $(this).attr('data-uid');
			var purl = $(this).attr('data-purl');
			
			switch (purl) {
				
				case "profile":	
					$('#data-content').css("opacity","0.5");
					$.ajax({
						url: request_source()+'/user.php', 
						data: {
							action:"wall",
							id : uid
						},	
						type: "post",			
						success: function(response) {
							$('#data-content').html(response);
							$('.profile-top-left .profile-photo').attr('data-src',profile_info.profile_photo);	
							$('.user-name h1').html(profile_info.name + ' '+ profile_info.online + '');
							if(profile_info.id != user_info.id){
								$('.gift').attr('data-tooltip',site_lang[77]['text']);
								$('.gift').attr('data-act','gift');
								$('.gift').html('<i class="mdi-action-wallet-giftcard"></i>');
								$('.videocall').attr('data-tooltip',site_lang[16]['text']);				
								$('.videocall').attr('data-uid',profile_info.id);
								$('.videocall').attr('data-surl','chat');			
								$('.videocall').html('<i class="mdi-communication-chat"></i>');
							} else {
								$('.gift').attr('data-tooltip',site_lang[54]['text']);
								$('.gift').attr('data-act','mphotos');
								$('.gift').html('<i class="mdi-image-photo-camera"></i>');				
								$('.videocall').attr('data-tooltip',site_lang[47]['text']);
								$('.videocall').attr('data-uid',profile_info.id);
								$('.videocall').attr('data-surl','settings');			
								$('.videocall').html('<i class="mdi-action-settings"></i>');		
							}
							window.history.pushState("profile",site_config.site_url+'profile/'+profile_info.id+'/'+profile_info.link);							
							$('#data-content').css("opacity","1");							
						},
						complete: function(){
							profilePhoto();
							profileLinks();
						}
					})				
				break;					
			}
		});	
	}	
	
	function menuLinks(){
		$('[data-murl]').click(function(){
			var murl = $(this).attr('data-murl');
			var menu = $(this);
			
			switch (murl) {
				
				case "meet":	
					$('#data-content').css("opacity","0.5");
					$.ajax({
						url: request_source()+'/user.php', 
						data: {
							action:"meet_back"						
						},	
						type: "post",			
						success: function(response) {
							$('#data-content').html(response);
							filterBtn();	
							scroller();
							$('.profile-top-left .profile-photo').attr('data-src',user_info.profile_photo);	
							profilePhoto();
							meetFilter();
							meetPagination();
							$('.user-name h1').html(user_info.name + ' '+ user_info.online + '');
							$('.gift').attr('data-tooltip',site_lang[54]['text']);
							$('.gift').attr('data-act','mphotos');
							$('.gift').html('<i class="mdi-image-photo-camera"></i>');				
							$('.videocall').attr('data-tooltip',site_lang[47]['text']);
							$('.videocall').attr('data-uid',user_info.id);
							$('.videocall').attr('data-surl','settings');			
							$('.videocall').html('<i class="mdi-action-settings"></i>');		
							window.history.pushState("meet",site_title(),site_config.site_url+'meet');							
							$('#data-content').css("opacity","1");							
						},
					});				
				break;
				
				case "popular":	
					$('#data-content').css("opacity","0.5");
					$.ajax({
						url: request_source()+'/user.php', 
						data: {
							action:"popular"						
						},	
						type: "post",			
						success: function(response) {
							$('#data-content').html(response);	
							scroller();
							$('.profile-top-left .profile-photo').attr('data-src',user_info.profile_photo);	
							profilePhoto();
							$('.user-name h1').html(user_info.name + ' '+ user_info.online + '');
							$('.gift').attr('data-tooltip',site_lang[54]['text']);
							$('.gift').attr('data-act','mphotos');
							$('.gift').html('<i class="mdi-image-photo-camera"></i>');				
							$('.videocall').attr('data-tooltip',site_lang[47]['text']);
							$('.videocall').attr('data-uid',user_info.id);
							$('.videocall').attr('data-surl','settings');			
							$('.videocall').html('<i class="mdi-action-settings"></i>');
							window.history.pushState("populars",site_title(),site_config.site_url+"popular");							
							$('#data-content').css("opacity","1");							
						},
					});				
				break;

				case "fans":	
					$('#data-content').css("opacity","0.5");
					$.ajax({
						url: request_source()+'/user.php', 
						data: {
							action:"fans"						
						},	
						type: "post",			
						success: function(response) {
							$('#data-content').html(response);	
							scroller();
							$('.profile-top-left .profile-photo').attr('data-src',user_info.profile_photo);	
							profilePhoto();
							$('.user-name h1').html(user_info.name + ' '+ user_info.online + '');
							$('.gift').attr('data-tooltip',site_lang[54]['text']);
							$('.gift').attr('data-act','mphotos');
							$('.gift').html('<i class="mdi-image-photo-camera"></i>');				
							$('.videocall').attr('data-tooltip',site_lang[47]['text']);
							$('.videocall').attr('data-uid',user_info.id);
							$('.videocall').attr('data-surl','settings');			
							$('.videocall').html('<i class="mdi-action-settings"></i>');
							window.history.pushState("fans",site_title(),site_config.site_url+"fans");							
							$('#data-content').css("opacity","1");							
						},
					});				
				break;
				
				case "visits":	
					$('#data-content').css("opacity","0.5");
					$.ajax({
						url: request_source()+'/user.php', 
						data: {
							action:"visits"						
						},	
						type: "post",			
						success: function(response) {
							$('#data-content').html(response);	
							scroller();
							$('.profile-top-left .profile-photo').attr('data-src',user_info.profile_photo);	
							profilePhoto();
							$('.user-name h1').html(user_info.name + ' '+ user_info.online + '');
							$('.gift').attr('data-tooltip',site_lang[54]['text']);
							$('.gift').attr('data-act','mphotos');
							$('.gift').html('<i class="mdi-image-photo-camera"></i>');				
							$('.videocall').attr('data-tooltip',site_lang[47]['text']);
							$('.videocall').attr('data-uid',user_info.id);
							$('.videocall').attr('data-surl','settings');			
							$('.videocall').html('<i class="mdi-action-settings"></i>');
							window.history.pushState("visits",site_title(),site_config.site_url+"visits");							
							$('#data-content').css("opacity","1");							
						},
					});				
				break;				
				
				case "matches":	
					$('#data-content').css("opacity","0.5");
					$.ajax({
						url: request_source()+'/user.php', 
						data: {
							action:"matches"						
						},	
						type: "post",			
						success: function(response) {
							$('#data-content').html(response);	
							scroller();
							$('.profile-top-left .profile-photo').attr('data-src',user_info.profile_photo);	
							profilePhoto();
							$('.user-name h1').html(user_info.name + ' '+ user_info.online + '');
							$('.gift').attr('data-tooltip',site_lang[54]['text']);
							$('.gift').attr('data-act','mphotos');
							$('.gift').html('<i class="mdi-image-photo-camera"></i>');				
							$('.videocall').attr('data-tooltip',site_lang[47]['text']);
							$('.videocall').attr('data-uid',user_info.id);
							$('.videocall').attr('data-surl','settings');			
							$('.videocall').html('<i class="mdi-action-settings"></i>');
							window.history.pushState("matches",site_title(),site_config.site_url+"matches");							
							$('#data-content').css("opacity","1");							
						},
					});				
				break;
				case "settings":	
					window.location.href = site_config.site_url+"settings";
				break;
				
				case "discover":	
					$('#data-content').css("opacity","0.5");
					$.ajax({
						url: request_source()+'/user.php', 
						data: {
							action:"discover"						
						},	
						type: "post",			
						success: function(response) {
							$('#data-content').html(response);
							$('.profile-top-left .profile-photo').attr('data-src',user_info.profile_photo);
							$('.user-name h1').html(user_info.name + ' '+ user_info.online + '');									
							$('.gift').attr('data-tooltip',site_lang[54]['text']);
							$('.gift').attr('data-act','mphotos');
							$('.gift').html('<i class="mdi-image-photo-camera"></i>');				
							$('.videocall').attr('data-tooltip',site_lang[47]['text']);
							$('.videocall').attr('data-uid',user_info.id);
							$('.videocall').attr('data-surl','settings');			
							$('.videocall').html('<i class="mdi-action-settings"></i>');
							window.history.pushState("discover",site_title(),site_config.site_url+"discover");
							game_start();
							game_btns();							
							$('#data-content').css("opacity","1");							
						},
					});				
				break;				
			}
		});	
	}	
	
	spotLinks();
	menuLinks();
	suggestLinks();
	filterBtn();
	
	function meetPagination(){
		$("[data-meet]").click(function() {
			$('#meet_section').css("opacity","0.5");
			var p = $(this).attr('data-meet');
			$('#meet_filter_limit').val(p); 
			meet_age = $('#meet_filter_age').val();
			meet_gender = $('#meet_filter_gender').val();
			meet_radius = $('#meet_filter_radius').val();
			meet_online = $('#meet_filter_online').val();
			meet_limit = $('#meet_filter_limit').val();			
			$.ajax({
				url: request_source()+'/user.php', 
				data: {
					action:"meet_filter",
					age: meet_age,
					gender: meet_gender,
					radius: meet_radius,
					online: meet_online,
					limit: meet_limit					
				},	
				type: "post",			
				success: function(response) {
					$('#meet_section').mCustomScrollbar("destroy");					
					$('#meet_section').html(response);
					scroller();	
					profilePhoto();
					meetPagination();
					$('#meet_section').css("opacity","1");							
				},
			});
		});		
	}
	meetPagination();
	
	function meetFilter(){
		$('#meet_section').css("opacity","0.5");		
		$.ajax({
			url: request_source()+'/user.php', 
			data: {
				action:"meet_filter",
				age: meet_age,
				gender: meet_gender,
				radius: meet_radius,
				online: meet_online,
				limit: meet_limit					
			},	
			type: "post",			
			success: function(response) {
				$('#meet_section').mCustomScrollbar("destroy");					
				$('#meet_section').html(response);
				scroller();	
				profilePhoto();	
				meetPagination();
				$('#meet_section').css("opacity","1");							
			},
		});		
		$("[data-filter]").change(function() {
			$('#meet_section').css("opacity","0.5");
						
			meet_age = $('#meet_filter_age').val();
			meet_gender = $('#meet_filter_gender').val();
			meet_radius = $('#meet_filter_radius').val();
			meet_online = $('#meet_filter_online').val();
			meet_limit = 0;			
			$.ajax({
				url: request_source()+'/user.php', 
				data: {
					action:"meet_filter",
					age: meet_age,
					gender: meet_gender,
					radius: meet_radius,
					online: meet_online,
					limit: meet_limit					
				},	
				type: "post",			
				success: function(response) {
					$('#meet_section').mCustomScrollbar("destroy");					
					$('#meet_section').html(response);
					scroller();	
					profilePhoto();	
					meetPagination();
					$('#meet_section').css("opacity","1");							
				},
			});
		});		
		
		$('[data-action]').click(function(){
			var action = $(this).attr('data-action');
			switch (action) {		
				case "online":
					$('#allusers').removeClass("selected");
					$(this).addClass("selected");
					$('#meet_filter_online').val(1);
					$('#meet_filter_limit').val(0); 					
					$('#meet_section').css("opacity","0.5");
					var age = $('#meet_filter_age').val();
					var gender = $('#meet_filter_gender').val();
					var radius = $('#meet_filter_radius').val();
					var online = $('#meet_filter_online').val();
					var limit = $('#meet_filter_limit').val();						
					$.ajax({
						url: request_source()+'/user.php', 
						data: {
							action:"meet_filter",
							age: age,
							gender: gender,
							radius: radius,
							online: online,
							limit: limit
						},	
						type: "post",			
						success: function(response) {
							$('#meet_section').mCustomScrollbar("destroy");					
							$('#meet_section').html(response);
							scroller();	
							profilePhoto();	
							meetPagination();
							$('#meet_section').css("opacity","1");							
						},
					});		
				break;
				case "allusers":
					$('#onlineusers').removeClass("selected");
					$(this).addClass("selected");
					$('#meet_filter_online').val(0);
					$('#meet_filter_limit').val(0); 					
					$('#meet_section').css("opacity","0.5");
					var age = $('#meet_filter_age').val();
					var gender = $('#meet_filter_gender').val();
					var radius = $('#meet_filter_radius').val();
					var online = $('#meet_filter_online').val();
					var limit = $('#meet_filter_limit').val();						
					$.ajax({
						url: request_source()+'/user.php', 
						data: {
							action:"meet_filter",
							age: age,
							gender: gender,
							radius: radius,
							online: online,
							limit: limit
						},	
						type: "post",			
						success: function(response) {
							$('#meet_section').mCustomScrollbar("destroy");					
							$('#meet_section').html(response);
							scroller();	
							profilePhoto();	
							meetPagination();
							$('#meet_section').css("opacity","1");							
						},
					});		
				break;	
			}
		});	
	}
	
	if(mobile == false){
		meetFilter();
	}

	
	$(".photo").each(function(){
		$(this).hover(function(){
			$(this).find('.data').fadeIn(); 
		  },function(){
			$(this).find('.data').fadeOut(); 
		});        
		var src = $(this).attr("data-src");
		$(this).css('background-image', 'url('+src+')');
	});	
	
	$("[data-act]").each(function(){
		if(mobile === true && url == 'chat'){
			$(this).show();
			$(this).css("font-size",24);	
		}
	});		
	
	$('[data-act]').click(function(e){
		e.preventDefault();
		var action = $(this).attr('data-act');
		if(action == "mphotos"){
			if($('#manage-photos').is(':visible')) {
				$('#manage-photos').hide();
			} else {
				$('#manage-photos').show();
			}
		}
		if(action == "gift"){
			if($('#send-gift').is(':visible')) {
				$('#send-gift').hide();
			} else {
				$('#send-gift').show();
				$('#g-name').html(profile_info.name);
				$('#g-name2').html(profile_info.name);					
			}
		}
		if(action == "like"){
			var uid = profile_info.id;
			
			$.ajax({
				url: request_source()+'/user.php', 
				data: {
					action:"game_like",
					id : uid,
					like: 1
				},	
				type: "post",
				beforeSend: function(){
					
				},
				success: function(response) {
					game_start();
				},
				complete: function(){
				}
			});
		}		
	});	
	
	$('#insta-import').on('click',function(e){
	swal({   title: "Instagram",   text: site_lang[329]['text'],   type: "input",   showCancelButton: true, showLoaderOnConfirm: true,  closeOnConfirm: false,   animation: "slide-from-top",   inputPlaceholder: site_lang[331]['text'] }, function(inputValue){   if (inputValue === false) return false;      if (inputValue === "") {     swal.showInputError(site_lang[330]['text']);     return false   } 
		$.ajax({
			url: request_source()+'/user.php', 
			data: {
				action:"instagram",
				insta: inputValue
			},	
			type: "post",
			beforeSend: function() {
			},		
			success: function(response) {
				if(mobile == true){
					goToProfile(user_info.id);
				} else {
					window.location.href= site_config.site_url+'profile/'+user_info.id+'/photo';
				}
			}
		});																																																																																																																																																													});									   
	});
	$('#add-photos,#add-photos-big').on('click', function(e){
		 e.preventDefault();
		$("#add-photos-file").click(); 
	});
	$('#private-photos').on('click', function(e){
		 e.preventDefault();
		$("#add-private-photos-file").click(); 
	});	
	$("#add-photos-file").change(function() {
		$("#add-photos-form").submit();
	});	
	$("#add-private-photos-file").change(function() {
		$("#add-private-photos-form").submit();
	});		
	
	$("[data-settings]").click(function() {
		$('.header-settings').removeClass("selected");									   
		$(this).addClass("selected");		
		var i = $(this).attr('data-settings');
		$("[data-settings-page]").hide();
		$('[data-settings-page='+i+']').fadeIn();
		
	});
	
   $("input:radio[name=fans]").change(function() {
        var val = $(this).val();
		var radio = "fan";
		$.ajax({
			url: request_source()+'/user.php', 
			data: {
				action:"user_notifications",
				val: val,
				col: radio
			},	
			type: "post",
			success: function(response) {
			}
		});		
    });	
   
   $("input:radio[name=near]").change(function() {
        var val = $(this).val();
		var radio = "near_me";
		$.ajax({
			url: request_source()+'/user.php', 
			data: {
				action:"user_notifications",
				val: val,
				col: radio
			},	
			type: "post",
			success: function(response) {
			}
		});		
    });	
   
   $("input:radio[name=message]").change(function() {
        var val = $(this).val();
		var radio = "message";
		$.ajax({
			url: request_source()+'/user.php', 
			data: {
				action:"user_notifications",
				val: val,
				col: radio
			},	
			type: "post",
			success: function(response) {
			}
		});		
    });	
   
   $("input:radio[name=match_m]").change(function() {
        var val = $(this).val();
		var radio = "match_m";
		$.ajax({
			url: request_source()+'/user.php', 
			data: {
				action:"user_notifications",
				val: val,
				col: radio
			},	
			type: "post",
			success: function(response) {
			}
		});		
    });
   
   $("input:checkbox[name=match_m]").change(function() {
        var val = $(this).val();
		var radio = "match_m";
		if(val == 1){
			val = 0;
			$(this).val(0);
		}else{
			val = 1;
			$(this).val(1);
		}
		$.ajax({
			url: request_source()+'/user.php', 
			data: {
				action:"user_notifications",
				val: val,
				col: radio
			},	
			type: "post",
			success: function(response) {
			}
		});		
    });	
   
   $("input:checkbox[name=n_like]").change(function() {
        var val = $(this).val();
		var radio = "fan";
		if(val == 1){
			val = 0;
			$(this).val(0);
		}else{
			val = 1;
			$(this).val(1);
		}
		$.ajax({
			url: request_source()+'/user.php', 
			data: {
				action:"user_notifications",
				val: val,
				col: radio
			},	
			type: "post",
			success: function(response) {
			}
		});		
    });	 
   
   $("input:checkbox[name=n_join]").change(function() {
        var val = $(this).val();
		var radio = "near_me";
		if(val == 1){
			val = 0;
			$(this).val(0);
		}else{
			val = 1;
			$(this).val(1);
		}
		$.ajax({
			url: request_source()+'/user.php', 
			data: {
				action:"user_notifications",
				val: val,
				col: radio
			},	
			type: "post",
			success: function(response) {
			}
		});		
    });	    
	
   $("input:checkbox[name=n_message]").change(function() {
        var val = $(this).val();
		var radio = "message";
		if(val == 1){
			val = 0;
			$(this).val(0);
		}else{
			val = 1;
			$(this).val(1);
		}
		$.ajax({
			url: request_source()+'/user.php', 
			data: {
				action:"user_notifications",
				val: val,
				col: radio
			},	
			type: "post",
			success: function(response) {
			}
		});		
    });	 	
	$("[data-payment]").click(function() {
		$('.subscribe').addClass("grayScale");									   
		$(this).removeClass("grayScale");		
		payment_method = $(this).attr('data-payment');
	});		
	
	$('[data-premium-send]').on('click', function(e){
		 e.preventDefault();
		var price = $(this).attr('data-price');
		var days = $(this).attr('data-premium');
		$('#payment-custom3').val(user_info.id+','+days);
		$('#payment-amount3').val(price);
		$('#payment-name3').val(site_config.name + ' - ' + days + ' ' + site_lang[332]['text']);	
		$('#buy-premium').submit();
	});
			
	$('#payment-submit').click(function(){
		var price = $('#payment-select').find(':selected').attr('data-price');
		var quantity = $('#payment-select').find(':selected').attr('data-quantity');		
		$('#payment-custom').val(user_info.id+','+quantity);
		$('#payment-custom2').val(user_info.id+','+quantity);
		$('#payment-amount,#payment-amount2').val(price);
		$('#payment-name,#payment-name2').val(site_config.name + ' ' + quantity + ' '+site_lang[73]['text']);		
		
		if(payment_method == 0){
			swal({   title: site_lang[333]['text'],   text: site_lang[196]['text'],   type: 'error' }, function(){ });
			return false;			
		}
		if(payment_method == 1){
			$('#method01').submit();
		}
		if(payment_method == 2){
			$('#method02').submit();
		}
		if(payment_method == 4){
		var name = site_config.name + ' ' + quantity + ' '+site_lang[73]['text'];
		var encode = 'amount='+quantity+'callback_url='+site_config.site_url+'credits-okcredit_name='+name+'cuid='+user_info.id+'currency='+site_config.currency+'display_type=userprice='+price+'v=web';			
		$.ajax({ 
			type: "POST", 
			url: request_source() + "/user.php",
			data: {
				action: 'fortumo',
				encode: encode
			},
			success: function(response){
				var md5 = response;
				var callback = encodeURI(site_config.site_url+'credits-ok');
				name = encodeURI(name);
				var href= 'http://pay.fortumo.com/mobile_payments/'+site_config.fortumo+'?amount='+quantity+'&callback_url='+callback+'&credit_name='+name+'&cuid='+user_info.id+'&currency='+site_config.currency+'&display_type=user&price='+price+'&v=web&sig='+md5;
				window.location.href = href;				
			}
		});				

		}
		if(payment_method == 3){
			price = price*100;
			var app = 1;
			var handler = StripeCheckout.configure({
				key: site_config.stripe,
				image: site_config.logo,
				locale: 'auto',
				token: function(token) {
					$.ajax({
						url: request_source()+'/stripe.php', 
						data: {
							token:token.id,
							price: price,
							app: app,
							quantity: quantity,
							uid: user_info.id,
							de: site_config.name + ' ' + quantity + ' '+site_lang[73]['text']
						},	
						type: "post",
						success: function(response) {
						},
						complete: function(){
							if(app == 1){
								window.location.href = site_url()+'credits-ok';
							}
						}
					});
				}
			});
			handler.open({
				name: site_config.name,
				description: site_config.name + ' ' + quantity + ' '+site_lang[73]['text'],
				amount: price
			});
		
			$(window).on('popstate', function() {
				handler.close();
			});				
		}
	});	
	
	
	$('[data-spotlight]').click(function(){
		$('body').find(".photos .selected").removeClass('selected');								 
		$(this).addClass('selected');
		var psrc = $(this).attr('data-src');
		$('#s_photo').val(psrc);
	});	
	$('.add-yourself').click(function(){
		$('#add-spotlight').show();
	});	
	$('#s_close').click(function(){
		$('#add-spotlight').hide();
	});
	
	$('.overlay').click(function(){
		$('#payment_module').hide();
	});	
	
	
	$('#add-sphoto,#add-sphoto2').on('click', function(e){
		 e.preventDefault();
		 if(user_info.credits < site_prices.spotlight){
			$('#payment_module').show();
		 } else {

			swal({   
				 title: site_lang[334]['text'],  
				 text: site_lang[335]['text'] +" "+ site_prices.spotlight + " "+site_lang[73]['text'],   
				 imageUrl: user_info.profile_photo ,
				 showCancelButton: true,   
				 confirmButtonText: site_lang[287]['text'],  
				 closeOnConfirm: true 
				 }, function(){
					$("#add-photo-spotlight").submit(); 	
				});			 			 
		 }
		
	})
	
	$('#add-photo-spotlight').submit(function(e) {
		e.preventDefault();	
		var photo = $('#s_photo').val();
		if(photo.length == 0){ alert(site_lang[197]['text']); return false};		
		$.ajax({ 
			type: "POST", 
			url: request_source() + "/user.php",
			data: $(this).serialize(),
			success: function(response){
				window.location.href = site_config.site_url+'meet';
			}
		});	
	});	
	
	$('.send-gift').click(function(){
		$('body').find(".photos .selected").removeClass('selected');								 
		$(this).addClass('selected');
		var gsrc = $(this).attr('data-src');
		gift_price = $(this).attr('data-gprice');
		$('#g_src').val(gsrc);
		$('#g_id').val(profile_info.id);
		$('#g_price').val(gift_price);
	});	

	$('#g_close').click(function(){
		$('#send-gift').hide();
	});
	
	$('#send-gift-btn').on('click', function(e){
		 e.preventDefault();
		 if(gift_price > user_info.credits){
			$('#send-gift').hide();
			$('#payment_module').show();
		 } else {
			$("#send-gift-form").submit(); 			 
		 }
		
	});
	
	$('#send-gift-form').submit(function(e) {
		e.preventDefault();	
		var gift = $('#g_src').val();
		var findme = 'error';
		if(gift.length == 0){ alert(site_lang[198]['text']); return false};		
		$.ajax({ 
			type: "POST", 
			url: request_source() + "/user.php",
			data: $(this).serialize(),
			success: function(response){
				if ( response.indexOf(findme) > -1 ) {
					alert(site_lang[199]['text']);
				} else {
					window.location.href = site_config.site_url+'chat/'+profile_info.id+'/'+profile_info.link;
				}
			}
		});	
	});	

	function managePhotos(){
		$('[data-set-profile]').on('click', function(e){
			e.preventDefault();
			var pid = $(this).attr('data-pid');
			var div = $(this).parent('.photo');			 
			$.ajax({ 
				type: "POST",
				url: request_source() + "/user.php",
				data: {
					action : "manage",
					pid : pid,
					profile : 1,
					block : 0,
					unblock : 0,
					del : 0
				},
				success: function(response){
					window.location.reload();
				}
			});
		});
		$('[data-unblock-photo]').on('click', function(e){
			e.preventDefault();
			var pid = $(this).attr('data-pid');
			var div = $("#pid"+pid);		 
			$.ajax({ 
				type: "POST",
				url: request_source() + "/user.php",
				data: {
					action : "manage",
					pid : pid,
					profile : 0,
					block : 0,
					unblock : 1,
					del : 0
				},
				success: function(response){
					window.location.reload(false);
				}
			});
		});
		
		$('[data-block-photo]').on('click', function(e){
			e.preventDefault();
			var pid = $(this).attr('data-pid');
			var div = $("#pid"+pid);			 
			$.ajax({ 
				type: "POST",
				url: request_source() + "/user.php",
				data: {
					action : "manage",
					pid : pid,
					profile : 0,
					block : 1,
					unblock : 0,
					del : 0
				},
				success: function(response){
					window.location.reload(false);
				}
			});
		});
		
		$('[data-delete-photo]').on('click', function(e){
			e.preventDefault();
			var pid = $(this).attr('data-pid');
			var div = $("#pid"+pid);
			$.ajax({ 
				type: "POST",
				url: request_source() + "/user.php",
				data: {
					action : "manage",
					pid : pid,
					profile : 0,
					block : 0,
					unblock : 0,
					del : 1
				},
				success: function(response){
					div.fadeOut();
				}
			});
		});		
	}
	
	managePhotos();
	
	(function() {
		$('#add-photos-form').ajaxForm({	
			beforeSend:function(){
				if(mobile == true){
					$('#new-stack').removeClass('stop-loading');	
				} else {
					$('#user-photos').hide();
					$('#loading-photos').show();
				}
			},
			success: function(msg) {
			},
			complete: function(xhr) {
				result = xhr.responseText;
				result = $.parseJSON(result);
				$.each(result, function(index, value){
					if( value.success ){
						$.ajax({ 
							type: "POST",
							url: request_source() + "/user.php",
							data: {
								action : "photo"
							},
							success: function(response){
								var photos = user_info.total_photos;								
								if(photos == 0 || mobile == true){
									window.location.reload();	
								}
								$('#loading-photos').hide();
								$('#add-photos-big').hide();
								$('#user-photos').html(response);
								$('#user-photos').show();
								$(".photo").each(function(){
									$(this).hover(function(){
										$(this).find('.data').fadeIn(); 
									  },function(){
										$(this).find('.data').fadeOut(); 
									});        
									var src = $(this).attr("data-src");
									$(this).css('background-image', 'url('+src+')');
									managePhotos();									
								});									
							}
						});
					} else if( value.error ){
						$('#user-photos').show();
						$('#loading-photos').hide();						
						error = value.error
						html = '<br><center>';
						html+='<p>'+error+'</p></center>';
						$('#user-photos').append( html );
					}
					
				});
			}
		}); 
		
	})();
	
(function() {
		$('#add-private-photos-form').ajaxForm({	
			beforeSend:function(){
				$('#new-stack').removeClass('stop-loading');
			},
			success: function(msg) {
			},
			complete: function(xhr) {
				result = xhr.responseText;
				result = $.parseJSON(result);
				$.each(result, function(index, value){
					if( value.success ){
						$.ajax({ 
							type: "POST",
							url: request_source() + "/user.php",
							data: {
								action : "photo"
							},
							success: function(response){
								window.location.reload();									
							}
						});
					} else if( value.error ){
						$('#user-photos').show();
						$('#loading-photos').hide();						
						error = value.error
						html = '<br><center>';
						html+='<p>'+error+'</p></center>';
						$('#user-photos').append( html );
					}
					
				});
			}
		}); 
		
	})();		
    
	function filterBtn() {
		$('.post-btn-right').click(function(){
			url = $(this).attr('data-url');
			var h = $('.wall').height();
			$('.wall').height(h-85);
			$(this).hide();
			if(url == "search"){
				$('.search-post').fadeIn();
			} else {
				$('.wall-post').fadeIn();
				$('#update-status').focus();            
			}
			$('.post-btn-right-close').show();
		});
		
		$('.post-btn-right-close').click(function(){
			var h = $('.wall').height();
			$('.wall').height(h+85);
			if(url == "search"){
				$('.search-post').hide();
			} else {
				$('.wall-post').hide();           
			}        
	
			$(this).hide();
			$('.post-btn-right').show();
		}); 
	}

	
    $('#refresh-notification').click(function(){
        $(this).toggleClass('rotate');
        $(this).toggleClass('rotate-rest');
    });

    $('.load-more').click(function(){
        var a = $('#user-spot').scrollTop();
		 $('#user-spot').mCustomScrollbar('scrollTo','-=500');														      
    });     
    
	

	profilePhoto();
    
    $(".wall").bind('scroll', function() {
       var top = $(this).scrollTop();
       if(top > 100 ){
           
       }else {
       }
    });    
    $("[class^=post-photo]").each(function(){
        var src = $(this).attr("data-src");
        $(this).css('background-image', 'url('+src+')');       
    });
    
	function photosChatWall(){
		
		$("[class^=post-photo]").each(function(){
			var src = $(this).attr("data-src");
			$(this).css('background-image', 'url('+src+')');       
		});		
		
		$("[class^=post-photo]").hover(function(){
			var instance = $(this).attr("data-instance");
			if ($("#viewer" + instance)[0]){ 
			} else {
				$(".fbphotobox-overlay").remove();
				$(".fbphotobox-main-container").remove();
				$(".fbphotobox-fc-main-container").remove();
				$(".fbphotobox-main-container").remove();
				if(url == "chat" || url == "profile"){
					$("[data-instance='" + instance +"']").fbPhotoBox({
						rightWidth: 0.1,
						leftBgColor: "black",
						rightBgColor: "white",
						footerBgColor: "black",
						overlayBgColor: "#1D1D1D",
						profile: false,
						instance: instance
					}); 
				} else {
					$("[data-instance='" + instance +"']").fbPhotoBox({
						rightWidth: 350,
						leftBgColor: "black",
						rightBgColor: "white",
						footerBgColor: "black",
						overlayBgColor: "#1D1D1D",
						profile: false,
						instance: instance
					});                
				}
				
			} 
		});
	}
	photosChatWall();
    
  $('#profile-menu').dropdown({
      inDuration: 300,
      outDuration: 225,
      constrain_width: false, 
      hover: false, 
      alignment: 'center', 
      gutter: 0, 
      belowOrigin: false 
    }
  );    

	function scroller(){
	
		$(".chat").mCustomScrollbar({
			autoHideScrollbar:true,
			theme:"dark",
			setTop: 100000,
			scrollButtons:{
				enable: true 
			},
			mouseWheel:{
				preventDefault: true,
				deltaFactor: 180
			}                
		});		
		$("#meet_section").mCustomScrollbar({
			autoHideScrollbar:true,
			theme:"dark",			
			scrollButtons:{
				enable: true 
			},
			mouseWheel:{
				preventDefault: true,
				deltaFactor: 110
			}                
		}); 
		
		$("#site-settings").mCustomScrollbar({
			autoHideScrollbar:true,
			theme:"dark",			
			scrollButtons:{
				enable: true 
			},
			mouseWheel:{
				preventDefault: true,
				deltaFactor: 90
			}                
		}); 		

		$("#user-spot").mCustomScrollbar({
			autoHideScrollbar:true,
			theme:"dark",
			scrollbarPosition: "outside",
			scrollButtons:{
				enable: true 
			},
			mouseWheel:{
				preventDefault: true,
				deltaFactor: 80
			}                
		});
		$('#chat-container').mCustomScrollbar({
			autoHideScrollbar:true,
			theme:"dark",
			scrollbarPosition: "outside",
			scrollButtons:{
				enable: true 
			},
			mouseWheel:{
				preventDefault: true,
				deltaFactor: 80
			}                
		});		
	}
	scroller();
	
	function sidebarChat(){
		$(".sidebar-friends").each(function(){
			var value = $(this).attr("data-message");	
			var entry = $(this).attr("data-chat");	
			if (value == 1) {
				$('[data-chat='+entry+'] h3').css('color','#ff5f35');
			}
		});			
		$('.sidebar-friends a').on('click', function() {
			var userid = $(this).attr("data-uid");				
			var c_url = window.location.href; 
			$('.selected').removeClass('selected');
			$(this).find('div').addClass('selected');
			$(this).closest('div').find('.notification').remove();
			$(this).closest('div').find('h3').css('color','#666');
			$('[data-uid='+userid+']').attr('data-message',0);
			//$('.pagenotfound').hide();		

			if(in_videocall === true && video_user == userid) {
				$('.videocall-chat').hide();					
				$('.videocall-container').fadeIn();			
			}
			if(in_videocall === true && video_user != userid) {
				$('[data-uid='+video_user+'] .friend-list').append('<div class="invideocall"><i class="fa fa-video-camera"></i></div>');
				$('[data-uid='+video_user+']').attr('data-message',1);
				$('[data-uid='+video_user+'] h3').css('color','#3ab0ff');
				$('.videocall-chat').fadeIn();
				$('.videocall-chat').draggable();
				$('.videocall-container').hide();				
			}			

			$('#data-content').css("opacity","0.5");
			url = "chat";
			$.ajax({
				url: request_source()+'/user.php', 
				data: {
					action:"chat",
					id : userid
				},	
				type: "post",			
				success: function(response) {
					response = wdtEmojiBundle.render(response);
					$('#data-content').html(response);
					$('#chat-message').focus();	
					title = 0;							
					document.title = site_title();
					window.history.pushState("chat",site_title(),site_config.site_url+'chat/'+profile_info.id+'/'+profile_info.link);
					$('.profile-top-left .profile-photo').attr('data-src',profile_info.profile_photo);	
					$('.user-name h1').html(profile_info.name + ' '+ profile_info.online + '');
					$('.gift').attr('data-tooltip',site_lang[77]['text']);
					$('.gift').attr('data-act','gift');
					$('.gift').html('<i class="mdi-action-wallet-giftcard"></i>');
					$('.videocall').attr('data-tooltip','Like');
					$('.videocall').attr('data-act','like');
					$('.videocall').attr('data-surl','like');					
					$('.videocall').html('<i class="mdi-action-favorite"></i>');
					current_user = userid;
					current_user_id = userid;
					user_name = response.name;					
					$('[data-cuid='+userid+']').attr('data-message',0);
					$('#r_id').val(userid);
					$('#rid').val(userid);	
					scroller();
					$('#data-content').css("opacity","1");	
					profilePhoto();
					photosChatWall();							
					$('#send-photo').on('click', function(e){
						 e.preventDefault();
						$("#photo-to-send").click(); 
					});
					$("#photo-to-send").change(function() {
						$("#sendPhoto").submit();
					});
					$('#sendPhoto').submit(function() { 
							$(this).ajaxSubmit(sendPhoto);  			
							return false; 
					});					
					current_chat(userid);	
					videocallBtn();
				},
				complete: function(){
					chatMessage();
					
				}
			})																	 
		});	
	}
	sidebarChat();	
	
	$("#chat-filter").change(function() {
		var val = $(this).val();
		chatFilter(val);
	});
	
	function chatFilter(val){
		if(val == 1) {
			$(".sidebar-friends").each(function(){
				var value = $(this).attr("data-all");								
				if (value != 1) {
					$(this).fadeOut();
				} else {
					$(this).show();
				}
			});
		}
		if(val == 2) {
			$(".sidebar-friends").each(function(){
				var value = $(this).attr("data-fan");								
				if (value != 1) {
					$(this).fadeOut();
				} else {
					$(this).show();
				}
			});			
		}
		
		if(val == 4) {
			$(".sidebar-friends").each(function(){
				var value = $(this).attr("data-status");								
				if (value != 1) {
					$(this).hide();
				} else {
					$(this).show();
				}
			});			
		}
		if(val == 3) {
			$(".sidebar-friends").each(function(){
				var value = $(this).attr("data-conv");								
				if (value != 1) {
					$(this).hide();
				} else {
					$(this).show();
				}
			});		
		}
		if(val == 5) {
			$(".sidebar-friends").each(function(){
				var value = $(this).attr("data-message");								
				if (value != 1) {
					$(this).hide();
				} else {
					$(this).show();
				}
			});			
		}		
	}

	$("[data-photos-filter]").click(function() {
		var val = $(this).attr('data-photos-filter');
		photosFilter(val);
	});	
	
	function photosFilter(val){
		if(val == 1) {
			$("[id^=pid]").each(function(){
				var value = $(this).attr("data-blocked");								
				if (value != 0) {
					$(this).fadeOut();
				} else {
					$(this).show();
				}
			});
		}
		if(val == 2) {
			$("[id^=pid]").each(function(){
				var value = $(this).attr("data-blocked");								
				if (value != 1) {
					$(this).fadeOut();
				} else {
					$(this).show();
				}
			});
		}
		if(val == 3) {
			$("[id^=pid]").each(function(){
				$(this).show();
			});
		}		
	}	
	
	function chat_notification(){
		noti = 0;
		var curl = window.location.href; 		
		if(curl.indexOf("page=chat") > -1) {
			var user = current_user_id;
			$.ajax({ 
				type: "POST", 
				dataType: "JSON",
				url: request_source() + "/chat.php",
				data: {
					action : "notification",
					user : user
				},
				success: function(response){
					
					response.forEach(function(entry) {
						title = title+1; 
						document.title = '( '+title+' ) '+ site_title();
						if ( $('[data-chat='+entry+']').length ) {
							$('[data-chat='+entry+']').attr('data-message',1);
							$('[data-chat='+entry+'] h3').css('color','#ff5f35');
							$('#chat-filter').val(5).change();
						} else {
							new_message();
						}
					});	
				},
				complete: function(){
					setTimeout(function(){ chat_notification(); }, 5000);
				}
			});	
		} else {
			$.ajax({ 
				type: "POST", 
				dataType: "JSON",
				url: request_source() + "/chat.php",
				data: {
					action : "notification",
					user : 0
				},
				success: function(response){
					
					if(mobile == true){
						
						response.forEach(function(entry) {
							if(noti == 0){
								$('#notiSound')[0].play();	
								noti = 1;
							}
							title = title+1; 
							document.title = '( '+title+' ) '+ site_title();
							$('#mobile-new-message').html(title);
							$('#mobile-new-message').show();
						});						
					} else {
						response.forEach(function(entry) {
							if(noti == 0){
								$('#notiSound')[0].play();	
								noti = 1;
							}												  
							title = title+1; 
							document.title = '( '+title+' ) '+ site_title();
							if ( $('[data-chat='+entry+']').length ) {
								$('[data-chat='+entry+']').attr('data-message',1);
								$('[data-chat='+entry+'] h3').css('color','#ff5f35');
								$('#chat-filter').val(5).change();
							} else {
								new_message();
							}
						});
					}
				},
				complete: function(){
					setTimeout(function(){ chat_notification(); }, 5000);
				}
			});			
		}
	}
	chat_notification();	

	function new_message(){
			$.ajax({ 
				type: "POST", 
				url: request_source() + "/chat.php",
				data: {
					action : "new"
				},
				success: function(response){
					$('#chat-container').html(response);
					sidebarChat();
					profilePhoto();
					scroller();
					$('#chat-filter').val(5).change();
				}
			});		
	}

	function current_chat(user) {
		var mob = 0;
		if (mobile == true){
			mob = 1	
		}
		$.ajax({
			data: {
			action: "current",	
			uid: user,
			mobile: mob
			},		 
			url: request_source()+'/chat.php',	
			type:  'post',
			dataType: 'JSON',
			success: function(response) 
			{	
			if(response.result == 1){
					response.message = wdtEmojiBundle.render(response.message);
					response.chat = wdtEmojiBundle.render(response.chat);
					if(mobile == true){
						$(".list-chats").append(response.chat);
						$('html, body').animate({
							  scrollTop: $("#bottom").offset().top
						}, 1000);							
					} else {				
						var lastChild = $(".chat .post:last-child" ).find(".left").attr('id');
						var length = $(".chat .post:last-child" ).find("p").length;					
						if(lastChild == "you" && response.photo == 0 && length >= 1){
							$(".chat .post:last-child" ).find("p:first").append('<br>'+response.message);
						} else {
							$(".chat").append(response.chat);				
						}			
						$('.chat').mCustomScrollbar("destroy");
						
						photosChatWall();					
						scroller();	
					}
					profilePhoto();
					title = title+1; 
					document.title = '( '+title+' ) '+ site_title();
				}
			},
			complete: function() {		
				current_user_chat = setTimeout(function() { current_chat(current_user) }, 3000);	
			}
		});
	}

	$('[data-premium]').on("click", function() {
			if($('#payment_module').is(':visible')) {
				$('#payment_module').hide();
			} else {
				$('#payment_module').show();
			}
	});	
	
		
	
	function chatMessage(){
		$('#chat-send').click(function(e) {
			$('#c-send').submit();							 
		});
		
		
		chatLinks();
		if(emojiInit == false){
			if(chatLimit == false){
				wdtEmojiBundle.init('#chat-message');		
				emojiIinit = true;
			}
		}
		$(document).on("click", function() {
			$('.emoticons').hide();
		});	
		
		$('[data-access]').on("click", function() {
			var a = $(this).attr('data-access');
			$('[data-access]').fadeOut();
			var lastChild = $(".chat .post:last-child" ).find(".left").attr('id');		
			if(a == "yes") {
				$.ajax({ 
					type: "POST",
					url: request_source() + "/chat.php",
					data: {
						action : "access",
						access : 1,
						r_id : current_user_id
					},
					beforeSend: function() {
						if(lastChild == "me"){
							$(".chat .post:last-child" ).find("p:first").append('<span id="rmas"><br>'+site_lang[189]['text']+'</span>');
						} else {
							$(".chat").append('<div class="post"><div class="left"><div class="profile-photo" data-src="'+ user_info.profile_photo +'"></div><h1>'+ user_info.name +'</h1> <p class="me">'+site_lang[189]['text']+'</p></div></div></div>');					
						}			
						$('.chat').mCustomScrollbar("destroy");
						scroller();	
						profilePhoto();
					},
					success: function(response){
						
					}
				});					
			} else {
				$.ajax({ 
					type: "POST",
					url: request_source() + "/chat.php",
					data: {
						action : "access",
						access : 2,
						r_id : current_user_id
					},
					beforeSend: function() {
						if(lastChild == "me"){
							$(".chat .post:last-child" ).find("p:first").append('<span id="rmas"><br>'+site_lang[190]['text']+'</span>');
						} else {
							$(".chat").append('<div class="post"><div class="left"><div class="profile-photo" data-src="'+ user_info.profile_photo +'"></div><h1>'+ user_info.name +'</h1> <p class="me">'+site_lang[190]['text']+'</p></div></div></div>');					
						}			
						$('.chat').mCustomScrollbar("destroy");
						scroller();	
						profilePhoto();
					},
					success: function(response){
						
					}
				});					
			}
		});			
		
		$('[data-credits]').on("click", function() {
												 
		swal({   
			 title: site_lang[200]['text'],  
			 text: site_prices.chat+ " credits",   
			 type: "warning",  
			 showCancelButton: true,   
			 confirmButtonText: 'Buy',  
			 closeOnConfirm: true
			 }, function(){
				if(site_prices.chat > user_info.credits){
					$('#payment_module').show();
				} else {
					$.ajax({ 
						type: "POST",
						url: request_source() + "/chat.php",
						data: {
							action : "chat_limit"	
						},
						success: function(response){
							window.location.reload();
						}
					});	
				}
			});											 
		});
		
		$('[data-premium]').on("click", function() {
			$('#payment_module').show();
		});		
		

		$('#c-send').submit(function(e) {
			e.preventDefault();
			var r_id = $('#r_id').val();
			var message = $('#chat-message').val();	
			var mob = 0;
			if (mobile == true){
				mob = 1;
			}			
			if(message.length == 0){ return false};		
			$.ajax({ 
				type: "POST", 
				dataType: "JSON",
				url: request_source() + "/chat.php",
				data: {
					action : "send",
					r_id : r_id,
					message: message,
					mobile : mob
				},
				beforeSend: function(){
					var message2 = escapeHtml(message);
					message2 = wdtEmojiBundle.render(message2);
					if(mobile == true){
							$(".list-chats").append('<li class="list-chat right" data-ix="list-item" style="opacity: 1; transform: translateX(0px) translateY(0px); transition: opacity 500ms cubic-bezier(0.23, 1, 0.32, 1), transform 500ms cubic-bezier(0.23, 1, 0.32, 1);"><div class="w-clearfix column-right chat right"><div class="arrow-globe right"></div><div class="chat-text right">'+message2+'</div><div class="chat-time right"></div></div></li>');
							$('html, body').animate({
                  				  scrollTop: $("#bottom").offset().top
                			}, 1000);
					} else {
						var n = $( ".post" ).length;
						var lastChild = $(".chat .post:last-child" ).find(".left").attr('id');
						if(lastChild == "me"){
							$(".chat .post:last-child" ).find("p:first").append('<span id="rmas" style="opacity:0.4"><br>'+message2+'</span>');
						} else {
							$(".chat").append('<div class="post" style="opacity:0.4"><div class="left"><div class="profile-photo" data-src="'+ user_info.profile_photo +'"></div><h1>'+ user_info.name +'</h1> <p class="me">'+message2+' </p></div></div></div>');					
						}
						$('.chat').mCustomScrollbar("destroy");
						scroller();							
					}
					$('#chat-message').val("");
					if(n == 0){
						newChat();
					}
				},
				success: function(response){
					response.message = wdtEmojiBundle.render(response.message);
					response.chat = wdtEmojiBundle.render(response.chat);					
					if(mobile == true){

							$("[data-ix=list-item]:last-child" ).hide();
							$(".list-chats").append(response.chat);								
						
					} else {
						
						var lastChild = $(".chat .post:last-child" ).find(".left").attr('id')
						var length = $('#rmas').length;
						if(lastChild == "me" && length >= 1){
							$("#rmas").remove();
							$(".chat .post:last-child" ).find("p:first").append('<br>'+response.message);
						} else {
							$(".chat .post:last-child" ).hide();
							$(".chat").append(response.chat);				
						}						
						
					}

					$('.chat').mCustomScrollbar("destroy");
					scroller();					
					profilePhoto();					
				}
			});	
		});	
	}

	function newChat(){
		$.ajax({ 
			type: "POST",
			url: request_source() + "/chat.php",
			data: {
				action : "today"
			},
			success: function(response){	
			}
		});			
	}

	function profileForms(){
		locInitialize();
		
		$('#add-credits').click(function(){
			if($('#payment_module').is(':visible')) {
				$('#payment_module').hide();
			} else {
				$('#payment_module').show();
			}
		});	
		
		$('#update-profile').submit(function(e) {
			e.preventDefault();	
			var findme = "Error";
			$.ajax({ 
				data:  $(this).serialize(),
				url:   request_source()+'/user.php',
				type:  'post',
				beforeSend: function(){	
				$('#update-error').hide();
				$('#update-success').hide();
				$('#upd-btn').html('Working..');
				},
				success: function(response){
					if ( response.indexOf(findme) > -1 ) {
						response = response.replace('Error','');
						$('#update-error').html(response);
						$('#update-error').fadeIn();
						$("#upd-btn").html(site_lang[135]['text']);
					} else {
						$('#update-success').html(site_lang[203]['text']);
						$('#update-success').fadeIn();
						$("#upd-btn").html(site_lang[135]['text']);
					}					
				}
			});	
		});
		
		$('#change-password').submit(function(e) {
			e.preventDefault();	
			var findme = "Error";
			$.ajax({ 
				data:  $(this).serialize(),
				url:   request_source()+'/user.php',
				type:  'post',
				beforeSend: function(){	
				$('#change-pwd-btn').html('Working..');
				},
				success: function(response){
					if ( response.indexOf(findme) > -1 ) {
						alert(response);
						$("#change-pwd-btn").html(site_lang[130]['text']);
					} else {
						alert(site_lang[336]['text']);
						$("#change-pwd-btn").html(site_lang[130]['text']);
					}					
				}
			});	
		});	
		
		$('#delete-acc').click(function(e) {
			swal({
				title: site_lang[204]['text'],
				text: site_lang[205]['text'],
				confirmButtonText: site_lang[206]['text'],				
				type: "warning",
				showCancelButton: true,				
				},
				function(){
					$.ajax({ 
						data: {
							action: 'delete_profile'	
						},
						url:   request_source()+'/user.php',
						type:  'post',
						beforeSend: function(){	
						},
						success: function(response){
							window.location.href = site_config.site_url;
						}
					});	
				});			
		});			
	}
	
	var placeSearch, autocomplete;
	var componentForm = {
	  locality: 'long_name',
	  country: 'long_name'
	};
	
	function locInitialize() {
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
		
	
	function game_start(){
		$.ajax({
			data: {
				action: "game",
			},
			url:   request_source()+'/user.php',
			type:  'post',
			dataType: 'JSON',
			success:  function (response) {	
				if(mobile == true){
				$("#dis_name").html('<a href="mobile.php?page=profile&id='+response.id+'"><b>'+ response.name +'</b>, '+ response.age +' <span style="font-size:12px;">'+ response.status +'</span></a>');
				} else {
				$("#dis_name").html('<a href="'+site_config.site_url+'profile/'+response.id+'/game"><b>'+ response.name +'</b>, '+ response.age +' <span style="font-size:12px;">'+ response.status +'</span></a>');					
				}
				$("#dis_profile_photo").attr('data-src', response.photo);
				$(".like").attr('data-id', response.id);
				$(".unlike").attr('data-id', response.id);

				if(mobile == true){
				$("#dis_distance").html(response.distance + ' KM');
				} else {
				  $("#dis_fans").html(response.total);	
				}
				$("#dis_city").html(response.city);
				galleria_photos = response.photos;
				profilePhoto();			
				startGalleria(galleria_photos);
			}
		});		
	}
	
	function game_btns(){
		$('.like').click(function(){								  
			var uid = $(this).attr('data-id');
			$(this).toggleClass( "press", 1000 );
			$(".liked").show();
			$.ajax({
				url: request_source()+'/user.php', 
				data: {
					action:"game_like",
					id : uid,
					like: 1
				},	
				type: "post",
				beforeSend: function(){
					
				},
				success: function(response) {
					game_start();
				},
				complete: function(){
				}
			});						
		});	
		$('.unlike').click(function(){								  
			var uid = $(this).attr('data-id');
			$(this).toggleClass( "press", 1000 );
			$(".disliked").show();
			$.ajax({
				url: request_source()+'/user.php', 
				data: {
					action:"game_like",
					id : uid,
					like: 0
				},	
				type: "post",
				success: function(response) {
					game_start();						
				},
				complete: function(){
				
				}
			});						
		});			
	}
	
    //Videocall system
	function updatePeer(peer) {
	  $.ajax({
		url: request_source()+'/videocall.php', 
		data: {
			action:"update",
			peer: peer
		 },	
		type: "post",
		success: function(data) {	
		}
	  });
	}
	
	function setInVideoCall() {
	  $.ajax({
		url: request_source()+'/videocall.php',
		data: {
			action:"invideocall"
		 },			
		type: "post",
		success: function(data) {	
		}
	  });
	}



	function peerConnect(con) {
		if(con == 1){
			peer.destroy();
		}
	    navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia;		
		peer = new Peer({host: ''+site_config['videocall']+'', secure:true, port:443, key: 'peerjs'});
		
		peer.on('open', function(){
			updatePeer(peer.id);
		});
					
		peer.on('close', function(){
		});	
		peer.on('disconnected', function() {										 
		});	
		peer.on('call', function(call){
		$.ajax({
			url: request_source()+'/videocall.php', 
			data: {
			action: "income",
			peer: call.peer,		
			 },		
			type: "post",
			dataType: 'JSON',	
			success: function(data) {
				$("#turn-video").click(function(){
					$(this).toggleClass('sbulb');
					window.localStream.getVideoTracks()[0].enabled = !(window.localStream.getVideoTracks()[0].enabled);
					var check = $(this).hasClass( "sbulb" );
					if(check === true){
						$('.profile-photo1 video').hide();
						$('.profile-photo1 img').show();							
					}else {
						$('.profile-photo1 video').show();
						$('.profile-photo1 img').hide();							
					}

				});
				$("#turn-mic").click(function(){
						$(this).toggleClass('sbulb');					  
						window.localStream.getAudioTracks()[0].enabled = !(window.localStream.getAudioTracks()[0].enabled);
				});				
				video_user = data.id;
				$('#call-name').html(data.name);				
				$('.ball').css("background-image",'url(' + data.photo + ')');
				$('#text_videocall').html(data.name+" " +site_lang[337]['text']);
				$('.videocall-notify').fadeIn();
				called = true;
				callSound = setInterval(function(){
					$('#callSound')[0].play();
				},4000);
				setTimeout(function() {
				 $('body').toggleClass('anim-start');
				}, 300);
				$("#acept-video").click(function(){ aceptcall(call); });
				$("#reject-video").click(function(){ rejectVideo(); });
			}
		});						 
		setInVideoCall();
		});
		
		
		
		peer.on('error', function(err){
			if(err.type === "network") {						
			} else {
			}
		});
	}
	peerConnect(0);
	
	function rejectVideo(){	
		if(in_videocall === true){window.localStream.stop(); window.localStream = null;}
		peerConnect(1);
		$('.videocall-notify').fadeOut();
		in_videocall = false;
		setTimeout(function() {
	 		$('body').toggleClass('anim-start');
		}, 2000);
		clearInterval(callSound);
		$('#callSound')[0].pause();		
	}
	
	setInterval(function(){ if(in_videocall === false){peerConnect(1);} }, 50000);
	function videocall(callfrom,callto) 
	{
	var pid = "";	
	$.ajax({
		url: request_source()+'/videocall.php', 
		data: {
		action: "getpeerid",
		id: callto,		
		 },		
		type: "post",
		dataType: 'JSON',	
		success: function(data) {	
				pid = data.peer;
				if(data.status == 2) {
					swal({   title: "</3",   text: data.name + ' ' + site_lang[207]["text"],   imageUrl: data.photo }, function(){ });
					return false;
				}
				else if(data.status == 0) {	
					swal({   title: "T_T",   text: data.name + ' ' + site_lang[208]["text"],   imageUrl: data.photo }, function(){ });
					return false;
				} else {
				in_videocall = true;
				setInVideoCall();				
				$('#call_status').html(site_lang[209]["text"]);
				$('#call-name').html(data.name);	
				startVideoCall(pid);
				$('.videocall-container').show();
				callSound = setInterval(function(){
					$('#callSound')[0].play();
				},4000);
				if(mobile == true){		
					$('.ball').css("background-image",'url(' + data.photo + ')');
					setTimeout(function() {
						$('body').toggleClass('anim-start');
					}, 300);				
				} else {
					$('.profile-photo2').prop('src', data.photo);
					$('.avatar-profile').hide();				   
					$('.profile-photo2').animate({								 
						left: '39%',
						top: '25%'
						}, 1000, function() {
					});					
				}		
			}			
		}
	});	
	}
	
    function videocallBtn(){
		$( "#videocall" ).click(function(e) {
			e.preventDefault();
			if(in_videocall === true) {
				swal({   title: "Error", text: site_lang[210]["text"],   type: "error"}, function(){ });	
				return false;	
			}
			
			if(user_info.premium == 0 && account_basic.videocall == 0) {
				swal({   title: "Error", text: site_lang[211]["text"],   type: "error"}, function(){ $('#payment_module').show(); });	
				return false;	
			}			
			
			video_user = $('#r_id').val();
			videocall_user = $('#r_id').val();
			$.ajax({
				url: request_source()+'/videocall.php', 
				data: {
					action:"check",
					id: videocall_user
				 },	
				type: "post",
				success: function(data) {	
					if(data == 1){
						videocall(peer.id,$('#r_id').val());
					} else {
swal({   title: profile_info.first_name+' ' +site_lang[382]["text"] ,   text: profile_info.first_name+' '+ site_lang[383]["text"],   imageUrl: profile_info.profile_photo,   showCancelButton: false,  confirmButtonText: site_lang[384]["text"],   closeOnConfirm: true }, function(){});						
					}
				}
			});	
		});	
	}
	
	$('#end-call').click(function(){
		window.existingCall.close();
		window.location.reload();
	});
	
	function aceptcall(call) {
		in_videocall = true;
		//$('#callSound')[0].pause();
		$('#call_status').html(site_lang[209]["text"]);	
		$('.videocall-notify').fadeOut();										   
		$('.videocall-container').fadeIn();	
		$('.profile-photo2').animate({								 
			left: '39%',
			top: '25%'
			}, 1000, function() {
	  	});		
      navigator.getUserMedia({audio: true, video: true}, function(stream){
		timer = setInterval(function () {
			document.getElementById("seconds").innerHTML = pad(++sec % 60);
			document.getElementById("minutes").innerHTML = pad(parseInt(sec / 60, 10));
		}, 1000);
		if(mobile == true){
        	$('#myCam').prop('src', (window.URL ? URL : webkitURL).createObjectURL(stream));
		}else {
			$('#my-video').prop('src', (window.URL ? URL : webkitURL).createObjectURL(stream));
		}
        window.localStream = stream;
		call.answer(window.localStream);		
		makeTheCall(call);	
		  }, function(){
				swal({
					title: site_lang[212]["text"],
					text: site_lang[213]["text"],
					type: "error",
					},
					function(isConfirm){
						if (isConfirm){
							location.reload();	
						}
					});	
			  });
	}
	

    function startVideoCall (callto) {
      // Get audio/video stream
      navigator.getUserMedia({audio: true, video: true}, function(stream){
        // Set your video displays
		if(mobile == true){
        	$('#myCam').prop('src', (window.URL ? URL : webkitURL).createObjectURL(stream));
		}else {
			$('#my-video').prop('src', (window.URL ? URL : webkitURL).createObjectURL(stream));
		}
		
	    $('#call_status').html(site_lang[381]["text"]);
		timer = setInterval(function () {
			document.getElementById("seconds").innerHTML = pad(++sec % 60);
			document.getElementById("minutes").innerHTML = pad(parseInt(sec / 60, 10));
		}, 1000);		
		$("#turn-video").click(function(){
			$(this).toggleClass('sbulb');
			window.localStream.getVideoTracks()[0].enabled = !(window.localStream.getVideoTracks()[0].enabled);
			var check = $(this).hasClass( "sbulb" );
			if(check === true){
				$('.profile-photo1 video').hide();
				$('.profile-photo1 img').show();							
			}else {
				$('.profile-photo1 video').show();
				$('.profile-photo1 img').hide();							
			}

		});
		$("#turn-mic").click(function(){
				$(this).toggleClass('sbulb');					  
				window.localStream.getAudioTracks()[0].enabled = !(window.localStream.getAudioTracks()[0].enabled);
		});	
        window.localStream = stream;
		
		var call = peer.call(callto, window.localStream);
		
		makeTheCall(call);			
      }, function(){
		  	swal({
				title: site_lang[212]["text"],
				text: site_lang[213]["text"],
				type: "error",
				},
				function(isConfirm){
					if (isConfirm){
						location.reload();	
					}
				});	
		  });
    }

	function finishCall() {
		var minu = $('#minutes').text();
		var secu = $('#seconds').text();
		clearInterval(timer);
		$('.videocall-notify').fadeOut();
		$('.videocall-container').fadeOut();
		in_videocall = false;
		if(called == true){
			$('body').toggleClass('anim-start');
		}
		$('.videocall-container').fadeOut();
		$('#message_status').remove();
		window.localStream.stop();	
		window.localStream = null; 	
		window.location.reload();
		$(".chat-container").scrollTop(100000);
		clearInterval(callSound);
		$('#callSound')[0].pause();
	}

    function makeTheCall (call) {
	  var in_call = false;
      if (window.existingCall) {
        window.existingCall.close();		
      }

	  var photo = $('.profile-photo2').attr('src');
		setTimeout(function() {
				if(in_call == false) {
swal({   title: site_lang[214]["text"],   text: site_lang[215]["text"],   imageUrl: photo,   showCancelButton: true,   confirmButtonColor: "#DD6B55",   confirmButtonText: site_lang[216]["text"], cancelButtonText: site_lang[217]["text"],   closeOnConfirm: false }, function(){  location.reload(); });
				}
		}, 50000);	  
      call.on('stream', function(stream){
		in_call = true;
		clearInterval(callSound);
		$('#callSound')[0].pause();		
		if(mobile == true){
			$('#chatUserVideo').prop('src', (window.URL ? URL : webkitURL).createObjectURL(stream));
			$('.myCam').draggable();
			$('.video-profile').show();
		} else {
			
			$('#their-video').prop('src', (window.URL ? URL : webkitURL).createObjectURL(stream));
			$('#video-chat').prop('src', (window.URL ? URL : webkitURL).createObjectURL(stream));
			$('.profile-photo1').animate({								 
				left: '80%'
				}, 500, function() {
					$('.profile-photo2').hide();
					$('.video').show();
			});	
			$('#chat-call').on('click', function() {
			  $('.videocall-chat').show();
			  $('.videocall-chat').draggable();
			  $('.videocall-container').hide();
			});				
		}
      });

      window.existingCall = call;
      call.on('close', finishCall);	  
    }	
	
	$( ".videocall-chat" ).dblclick(function() {
		  $('.videocall-chat').hide();
		  $('.videocall-container').fadeIn();
	});	
	$( "#their-video" ).dblclick(function() {
		  $('.videocall-chat').fadeIn();
		  $('.videocall-chat').draggable();
		  $('.videocall-container').hide();
	});	
	
	function clean_galleria() {
		var data = [];
		Galleria.run('.discover', {
			dataSource: data
		});		
	}	
	function escapeHtml(text) {
	  var map = {
		'&': '&amp;',
		'<': '&lt;',
		'>': '&gt;',
		'"': '&quot;',
		"'": '&#039;'
	  };
	
	  return text.replace(/[&<>"']/g, function(m) { return map[m]; });
	}
	
	function detect_device() {
		var w = window,
			d = document,
			e = d.documentElement,
			g = d.getElementsByTagName('body')[0],
			x = w.innerWidth || e.clientWidth || g.clientWidth,
			y = w.innerHeight|| e.clientHeight|| g.clientHeight;
		var isiPhone = 		navigator.userAgent.toLowerCase().indexOf("iphone");
		var isiPad = 		navigator.userAgent.toLowerCase().indexOf("ipad");
		var isiPod = 		navigator.userAgent.toLowerCase().indexOf("ipod");
		var isiAndroid = 	navigator.userAgent.toLowerCase().indexOf("android");
		if(isiPhone > -1 || isiPad > -1 || isiPod > -1 || isiAndroid > -1 && mobile == false){
			window.location='mobile.php?page=login';
		}

	}

	window.onresize = function() {
		var w = window,
			d = document,
			e = d.documentElement,
			g = d.getElementsByTagName('body')[0],
			x = w.innerWidth || e.clientWidth || g.clientWidth,
			y = w.innerHeight|| e.clientHeight|| g.clientHeight;	
			
		if(x <= 700 && mobile == false){
			window.location=site_config.site_url+'mobile.php?page=index';	
		}
		if(x > 701 && mobile == true){
			window.location=site_config.site_url+'index.php?page=index';	
		}		
	};
	
	$.ajax({
		url: request_source()+'/user.php', 
		data: {
			action:"interest"
		},	
		type: "post",	
		dataType: "JSON",
		success: function(response) {
			response.forEach(function(entry) {
				searchIndex.push(entry);
			});
		}
	});		
	
	$('body').keyup(function(e) {
			switch (e.keyCode) {
				case 13:  
					if($('#searchBox').is(':focus')){
						var html = $('#new-int').html();
						var name = $('#searchBox').val();
						$('#new-int').html(html+'<div class="int"><span>'+ $('#searchBox').val() +'</span></div>');
						$('#searchBox').val('');
						$('#searchResults').addClass('hiddden');
						$.ajax({
							url: request_source()+'/user.php', 
							data: {
								action:"add_interest",
								name: name
							},	
							type: "post",			
							success: function(response) {
								
							}
						});						
					}
				break;			
			}
	});		
	
	function pad(val) {
    	return val > 9 ? val : "0" + val;
	} 
	
	
	var w = window,
		d = document,
		e = d.documentElement,
		g = d.getElementsByTagName('body')[0],
		x = w.innerWidth || e.clientWidth || g.clientWidth,
		y = w.innerHeight|| e.clientHeight|| g.clientHeight;
	var left = x/2 - 38;
	
	$("#sendmessage input").focusout(function(){
		if($(this).val() == ""){
			$(this).val("Send message...");
			
		}
	});
	$("#sendmessage input").focus(function(){
		if($(this).val() == "Send message..."){
			$(this).val("");
		}
	});	
	$(".friend").each(function(){		
		$(this).click(function(){
			var childOffset = $(this).offset();
			var parentOffset = $(this).parent().parent().offset();
			var childTop = childOffset.top - parentOffset.top;
			var clone = $(this).find('img').eq(0).clone();
			var top = childTop+12+"px";
			
			
			$(clone).css({'top': top}).addClass("floatingImg").appendTo("[data-chatbox]");									
			
			setTimeout(function(){$("#profile p").addClass("animate");$("#profile").addClass("animate");}, 100);
			setTimeout(function(){
				$("#chat-messages").addClass("animate");
				$('.cx, .cy').addClass('s1');
				setTimeout(function(){$('.cx, .cy').addClass('s2');}, 100);
				setTimeout(function(){$('.cx, .cy').addClass('s3');}, 200);			
			}, 150);														
			
			$('.floatingImg').velocity({
				'width': "68px",
				'left': left,
				'top':'20px'
			}, 200);
			
			var name = $(this).find("p strong").html();
			var email = $(this).find("p span").html();														
			$("#profile p").html(name);
			$("#profile span").html(email);			
			
			$(".message").not(".right").find("img").attr("src", $(clone).attr("src"));									
			$('#friendslist').velocity("fadeOut", { duration: 300 });
			$('#chatview').velocity("fadeIn", { duration: 500 });
		
			
			$('#close').unbind("click").click(function(){				
				$("#chat-messages, #profile, #profile p").removeClass("animate");
				$('.cx, .cy').removeClass("s1 s2 s3");
				$('.floatingImg').velocity({
					'width': "40px",
					'top':top,
					'left': '12px'
				}, 200, function(){$('.floatingImg').remove()});				
				
				setTimeout(function(){
					$('#chatview').velocity("fadeOut", { duration: 300 });
					$('#friendslist').velocity("fadeIn", { duration: 500 });			
				}, 50);
			});
			
		});
	});			
});
