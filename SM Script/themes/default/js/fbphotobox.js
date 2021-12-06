(function($) {
	function FBPhotoBox(item, opts) {
		this.settings = $.extend({}, $.fn.fbPhotoBox.defaults, opts);
		this.targetObj = item;
		this.tempImage = new Image();
		this.fullScreenMode = false;
		this.rightArrow = null;
		this.leftArrow = null;
		this.mainImage = null;
		this.bodyDimension = {width:0,height:0};
		this.init();
	}
	
	FBPhotoBox.prototype = {
		init: function() {
			var $this = this;
			this.initDOM();
			this.initSettings();
			this.rightArrow = $(".right-arrow");
			this.leftArrow = $(".left-arrow");
			this.mainImage = $(".fbphotobox-main-image");
			this.bodyDimension.width = $('body').width();
			this.bodyDimension.height = $('body').height();
			var num = 0;
			this.tempImage.onload = function() { $this.refreshBoxSize(this); }
			
			$(window).resize(function() {
				$this.refreshBoxSize();
				$this.refreshFullScreenSize();
				if(navigator.appVersion.indexOf("MSIE 7.") != -1) { $this.repositionBox(); }
			});
			
			this.targetObj.click(function() { $this.show($(this)); });
			
			this.mainImage.bind("onFbBoxImageShow", this.settings.onImageShow);

			$(".fbphotobox-a").hover(function() {
				$(this).fadeTo("fast", 1);
			}, function() {
				$(this).fadeTo("fast", 0.5);
			});
			
			// Start of Normal Mode Binding //
			$(".fbphotobox-close-btn a").click(function() {
				$this.hide();
				return false;
			});
			
			$(".fbphotobox-fc-btn").click(function() {
				$this.fullScreenMode = true;
				$this.showFullScreen($this.mainImage);
				return false;
			});
			
			$('body').keyup(function(e) {
					switch (e.keyCode) {
						case 27:  
							if ($this.settings.profile === false) {
								$(".fbphotobox-overlay").remove();
								$(".fbphotobox-main-container").remove();
								$(".fbphotobox-fc-main-container").remove();
								$(".fbphotobox-main-container").remove();                    
							}
							$this.tempImage.src = "";                  
							$this.hide();
							return false;
						break;			
					}
			});				
			
			$(".fbphotobox-overlay").click(function() {  
                
                if ($this.settings.profile === false) {
                    $(".fbphotobox-overlay").remove();
                    $(".fbphotobox-main-container").remove();
                    $(".fbphotobox-fc-main-container").remove();
                    $(".fbphotobox-main-container").remove();                    
                }
                $this.tempImage.src = "";                  
                $this.hide();
				return false;
			});
			
			this.mainImage.click(function() {
				$this.rightArrow.click();
				return false;
			});
			
			$(".fbphotobox-main-image-dummy").load(function() {
				$this.refreshBoxSize(this);
			});
			
			$(".fbphotobox-container-left").hover(function() {
				$(".fbphotobox-image-stage-overlay").fadeIn($this.settings.imageOverlayFadeSpeed);
			}, function() {
				$(".fbphotobox-image-stage-overlay").fadeOut($this.settings.imageOverlayFadeSpeed);
			});
			
			this.leftArrow.click(function() {
				var image = $this.targetObj.get($this.leftArrow.attr("data-prev-index"));
				if (image) $this.show($(image));
			});
			
			this.rightArrow.click(function() {
				var image = $this.targetObj.get($this.rightArrow.attr("data-next-index"));
				if (image) $this.show($(image));
			});
			
			$(".fc-left-arrow .fbphotobox-a").click(function() {
				$this.leftArrow.click();
				$this.showFullScreen($this.mainImage);
			});
			
			$(".fc-right-arrow .fbphotobox-a").click(function() {
				$this.rightArrow.click();
				$this.showFullScreen($this.mainImage);
			});
			
			$(".fbphotobox-fc-close-btn").click(function() {
				$this.fullScreenMode = false;
				$this.hideFullScreen();
				return false;
			});
			// End of FullScreen Mode Binding //
		},
		
		initSettings: function() {
			if (this.settings.rightWidth != "") {
				$(".fbphotobox-container-right").css("width", this.settings.rightWidth);
			}
			
			if (this.settings.leftBgColor != "") {
				$(".fbphotobox-container-left").css("backgroundColor", this.settings.leftBgColor);
			}
			
			if (this.settings.rightBgColor != "") {
				$(".fbphotobox-container-right").css("backgroundColor", this.settings.rightBgColor);
			}
			
			if (this.settings.footerBgColor != "") {
				$(".fbphotobox-container-left-footer-bg").css("backgroundColor", this.settings.footerBgColor);
			}
			
			if (this.settings.overlayBgColor != "") {
				$(".fbphotobox-overlay").css("backgroundColor", this.settings.overlayBgColor);
			}
			
			if (this.settings.overlayBgOpacity != "") {
				$(".fbphotobox-overlay").css("opacity", this.settings.overlayBgOpacity);
			}
		},
		
		initDOM: function() {
			if (this.settings.admin === true) {
			var html = ['<div class="fbphotobox-main-container">',
					'<div class="fbphotobox-container-left">',
						'<table class="fbphotobox-main-image-table"><tr><td>',
							'<div class="tag-container"><img class="fbphotobox-main-image" src=""/></div>',
						'</td></tr></table>',
						'<div class="fbphotobox-image-stage-overlay">',
							'<div data-prev-index="" class="left-arrow">',
								'<table style="height:100%"><tr><td style="vertical-align:middle;">',
									'<a class="fbphotobox-a" title="Previous"></a>',
								'</td></tr></table>',
							'</div>',
							'<div data-next-index="" class="right-arrow">',
								'<table style="height:100%;"><tr><td style="vertical-align:middle;">',
									'<a class="fbphotobox-a" title="Next"></a>',
								'</td></tr></table>',
							'</div>',
							'<div class="fbphotobox-container-left-footer">',
								'<div style="margin:20px;">',
									'<span style="font-weight:bold;">USE DELETE KEYBOARD KEY FOR REMOVE THE PHOTO </span>',
									'<span style="color:#B3B3B3;" id="current_photo_viewer"> </span>',
								'</div>',
							'</div>',
							'<div class="fbphotobox-container-left-footer-bg"></div>',
						'</div>',
					'</div>',
					'<div class="fbphotobox-container-right">',
                        
						'<div class="fbphotobox-close-btn">',
							'<a title="Close" href="" style="float:right;margin:8px">',
								'<img src="images/close.png" style="height:10px;width:10px"/>',
							'</a>',
							'<div style="clear:both"></div>',
						'</div>',
						'<div class="fbphotobox-image-content"></div>',
					'</div>',
					'<div style="clear:both"></div>',				
				'</div>',
				'<div class="fbphotobox-fc-main-container">',
					'<div class="fbphotobox-fc-header">',
						'<a class="fbphotobox-fc-close-btn" href="">Exit</a>',
						'<div style="clear:both"></div>',
					'</div>',
					'<div style="position:fixed;top:0px;right:0px;left:0px;bottom:0px;margin:auto;">',
						'<table style="width:100%;height:100%;text-align:center;">',
							'<tr align="center">',
								'<td class="fc-left-arrow" style="width:50px;text-align:center;">',
									'<a class="fbphotobox-a" title="Previous"></a>',
								'</td>',
								'<td align="center">',
									'<center><img class="fbphotobox-fc-main-image" src=""/></center>',
								'</td>',
								'<td class="fc-right-arrow" style="width:50px;text-align:center;">',
									'<a class="fbphotobox-a" title="Next"></a>',
								'</td>',
							'</tr>',
						'</table>',
					'</div>',
				'</div>',
				'<div class="fbphotobox-overlay"></div>',
				];				
			} else {
			var html = ['<div class="fbphotobox-main-container" id="viewer'+this.settings.instance+'" >',
					'<div class="fbphotobox-container-left">',
						'<table class="fbphotobox-main-image-table"><tr><td>',
							'<div class="tag-container"><img class="fbphotobox-main-image" src=""/></div>',
						'</td></tr></table>',
						'<div class="fbphotobox-image-stage-overlay">',
							'<div data-prev-index="" class="left-arrow">',
								'<table style="height:100%"><tr><td style="vertical-align:middle;">',
									'<a class="fbphotobox-a" title="Previous"></a>',
								'</td></tr></table>',
							'</div>',
							'<div data-next-index="" class="right-arrow">',
								'<table style="height:100%;"><tr><td style="vertical-align:middle;">',
									'<a class="fbphotobox-a" title="Next"></a>',
								'</td></tr></table>',
							'</div>',
							'<div class="fbphotobox-container-left-footer">',
								'<div style="margin:20px;">',

									'<span style="font-weight:bold;" id="photo_viewer_name"></span>',
									'<span style="color:#B3B3B3;" id="current_photo_viewer"> </span>',
								'</div>',
							'</div>',
							'<div class="fbphotobox-container-left-footer-bg"></div>',
						'</div>',
					'</div>',
					'<div class="fbphotobox-container-right">',
                        '<div class="user-details" >',
                        '<a href="javascript:;" id="like-photo" alt="Like" class="waves-effect waves-teal btn-flat blue lighten-3 like"><i class="mdi-action-favorite"></i></a>',
                            '<div id="profile-photo-user" class="profile-photo" data-src="'+profile_info.profile_photo+'"></div>',
							'<h1></h1>',
							'<span></span>',
                            '<p></p>', 
                        '<div class="likes" style="display:none" ></div>',
                        '<div class="comments" >',
                        '<ul>',                        
                        '</ul>',
                        '</div>',
                        '<div class="new-comment"><div id="new-comment-photo" class="profile-photo" data-src="'+user_info.profile_photo+'"></div><div id="new-comment-form"></div></div>',
                        '</div>',
                        
                        '</div>',
						'<div class="close">',
							'<a title="Close" href="" style="float:right;margin:8px">',
								'',
							'</a>',
							
						'</div>',
						'<div class="fbphotobox-image-content"></div>',
					'</div>',				
				'</div>',
				'<div class="fbphotobox-fc-main-container">',
					'<div class="fbphotobox-fc-header">',
						'<a class="fbphotobox-fc-close-btn" href="">Exit</a>',
					
					'</div>',
					'<div style="position:fixed;top:0px;right:0px;left:0px;bottom:0px;margin:auto;">',
						'<table style="width:100%;height:100%;text-align:center;">',
							'<tr align="center">',
								'<td class="fc-left-arrow" style="width:50px;text-align:center;">',
									'<a class="fbphotobox-a" title="Previous"></a>',
								'</td>',
								'<td align="center">',
									'<center><img class="fbphotobox-fc-main-image" src=""/></center>',
								'</td>',
								'<td class="fc-right-arrow" style="width:50px;text-align:center;">',
									'<a class="fbphotobox-a" title="Next"></a>',
								'</td>',
							'</tr>',
						'</table>',
					'</div>',
				'</div>',
				'<div class="fbphotobox-overlay"></div>',
				];	
			}

			$("body").append(html.join(""));
			this.settings.afterInitDOM();
            
		},
		
		show: function(image) {
			
			if (image.attr("fbphotobox-src")) this.tempImage.src = image.attr("fbphotobox-src");
			else this.tempImage.src = image.attr("src");
            
            //CLEAR DATA
            $(".user-details p").html('');
            $(".comments ul").html('');
			$(".user-details .likes").hide();
			$(".user-details a").hide();
			
            //DATA
			var photoid= image.attr("data-id");
			this.tempImage.dataid = image.attr("data-id");
            this.tempImage.datadesc = image.attr("data-desc");
            this.tempImage.datacomments = image.attr("data-comments");
			this.tempImage.datauser = image.attr("data-user");
			this.tempImage.datadate = image.attr("data-date");
			this.tempImage.dataphoto = image.attr("data-photo");
			this.tempImage.datalikes = image.attr("data-likes");
			this.tempImage.datalike = image.attr("data-like");			
			$(".fbphotobox-tag").remove();
			var index = this.targetObj.index(image);
			
            
            //DATA
            $(".user-details p").html(this.tempImage.datadesc);
			$('#new-comment-form').html('<form id="photo-comment'+photoid+'"><input id="new-comment" placeholder="Leave a comment" name="new-comment"/><form>');
			if(this.tempImage.datalikes != "" ){
				$(".user-details .likes").show();
				$(".user-details .likes").html(this.tempImage.datalikes);
			}
			if(this.tempImage.datalike == 0 ){
				$(".user-details a").show();
					$("#like-photo").click(function() {	
					$.ajax({ 
						type: "POST", 
						url: request_source() + "/user.php",
						data: {
							action : "likephoto",
							uname : user_info.name,
							pid : photoid
						},
						beforeSend: function(){
							$("#like-photo").fadeOut();
						},
						success: function(response){		
						}
					});	
				});			
			}
			$('#photo-comment'+photoid).submit(function(e) {			
				e.preventDefault();
				var message = $('#new-comment').val();	
				if(message.length == 0){ return false};		
				$.ajax({ 
					type: "POST", 
					dataType: "JSON",
					url: request_source() + "/user.php",
					data: {
						action : "photocomment",
						pid : photoid,
						message: message	
					},
					beforeSend: function(){
						$('.new-comment').css("opacity","0.5");
					},
					success: function(response){
						$('#new-comment').val('');
						$(".comments ul").append('<li><div class="profile-photo" data-src="'+user_info.profile_photo+'"></div><h3><a href="'+site_config.site_url+'profile/'+user_info.id+'/comment">'+user_info.first_name+'</a> <i class="mdi-image-brightness-1"></i></h3><div class="comment">'+response.comment+'</div></li>');
						$(".comments").scrollTop(100000);	
						$('.new-comment').css("opacity","1");
						$(".profile-photo").each(function(){
							var src = $(this).attr("data-src");
							$(this).css('background-image', 'url('+src+')');
						}); 
					}
				});	
			});
 			
			$(".profile-photo").each(function(){
				var src = $(this).attr("data-src");
				$(this).css('background-image', 'url('+src+')');
			});
			$(".user-details h1").html(this.tempImage.datauser);
			$.ajax({ 
				type: "POST", 
				url: request_source() + "/user.php",
				data: {
					action : "photocomments",
					pid : photoid
				},
				beforeSend: function(){
				},
				success: function(response){
					$(".comments ul").html(response);
					$(".profile-photo").each(function(){
						var src = $(this).attr("data-src");
						$(this).css('background-image', 'url('+src+')');
					}); 					  
				}
			});	       
			$("#photo_viewer_name").html(site_lang[338]['text']+" " + this.tempImage.datauser + " ");	
			$("#current_photo_viewer").html(index+1 + " "+ site_lang[339]['text'] +" " + this.targetObj.length);
           
			if (this.settings.admin === true) {
				var id = image.attr("data-id");				
				$('html').keyup(function(e){
					if(e.keyCode == 46){
						$.ajax({ 
							type: "POST", 
							url: request_source() + "/admin.php",
							data: {
								action : "delphoto",
								id : id				
							},
							beforeSend: function(){
							},
							success: function(response){		
							}
						});								
					}
				}) 				
			}
           
			this.leftArrow.attr("data-prev-index", index-1);
			this.rightArrow.attr("data-next-index", index+1);
			if (index-1 < 0) this.leftArrow.hide();
			else this.leftArrow.show();
			if (index+1 >= this.targetObj.length) this.rightArrow.hide();
			else this.rightArrow.show();
		},
		
		showFullScreen: function(image) {
			$(".fbphotobox-fc-main-container").show();
			if (this.leftArrow.css("display") == "none") $(".fc-left-arrow a").hide();
			else $(".fc-left-arrow a").show();
			if (this.rightArrow.css("display") == "none") $(".fc-right-arrow a").hide();
			else $(".fc-right-arrow a").show();
			this.refreshFullScreenSize();
		},
		
		hideFullScreen: function() {
			$(".fbphotobox-fc-main-container").hide();
		},
		
		hide: function() {
			$(".fbphotobox-overlay").hide();
			$(".fbphotobox-main-container").hide();
			
		},
		
		addTags: function(tagsCo) {
			var imgHeight = this.mainImage.height();
			var imgWidth = this.mainImage.width();
			var tagNode = $(document.createElement('div')).attr("class", "fbphotobox-tag");
			for (var i=0; i < tagsCo.length; i++) {
				var tempNode = tagNode.clone();
				tempNode.css({
					position: "absolute",
					left: (tagsCo[i].x * imgWidth),
					top: (tagsCo[i].y * imgHeight),
					width: (tagsCo[i].w * imgWidth),
					height: (tagsCo[i].h * imgHeight),
					zIndex: 999
				}).attr({
					x: tagsCo[i].x,
					y: tagsCo[i].y,
					w: tagsCo[i].w,
					h: tagsCo[i].h
				});
				if (true || tagsCo[i].text) {
					var tipNode = $('<div style="background-color:white;">hello</div>');
					tempNode.append(tipNode);
					tipNode.html(tagsCo[i].text);
				}
				tempNode.appendTo(this.mainImage.closest("div"));
			}
		},
		

		refreshBoxSize: function(image) {
			var isShow = image == null? false : true;
			image = image == null? this.tempImage : image;
			var leftContainer = $(".fbphotobox-container-left");
			var rightContainer = $(".fbphotobox-container-right");
			var commentsContainer = $(".fbphotobox-container-right .user-details .comments"); 

            var photoDesc = $('.user-details p');
			var imageWidth = image.width;
			var imageHeight = image.height;
			var maxWidth = Math.max($(window).width() - this.settings.rightWidth - this.settings.normalModeMargin*3);
			var maxHeight = Math.max($(window).height() - this.settings.normalModeMargin*2);
			
			
			
			if (imageHeight < maxHeight) {
				leftContainer.height(maxHeight);
				this.mainImage.css("max-height",maxHeight);
			}
			else {
				leftContainer.height(maxHeight);
				this.mainImage.css("max-height",maxHeight);
			}
			if (imageWidth < maxWidth) {
				leftContainer.width(maxWidth);
				this.mainImage.css("max-width",maxWidth);
			}
			else {
				leftContainer.width(maxWidth);
				this.mainImage.css("max-width",maxWidth);
			}
						
			rightContainer.css("height", leftContainer.height());
            
            var textLength = photoDesc.text().length;
            
            if(textLength == 0){
                var commentsHeight = leftContainer.height() - 190;
            } else if(textLength < 48){
                var commentsHeight = leftContainer.height() - 210;    
            } else {
                var commentsHeight = leftContainer.height() - 242;   
            }
            
            commentsContainer.css("height",commentsHeight);
            
			$(".fbphotobox-image-content").css("height", leftContainer.height() - $(".fbphotobox-close-btn").height());
			
			$(".fbphotobox-main-container").css({
				width: (leftContainer.width() + rightContainer.width()),
				height: leftContainer.height()
			});
          
			
			if (isShow) {
				if(navigator.appVersion.indexOf("MSIE 7.") != -1) this.repositionBox();
				this.mainImage.hide().attr("src", "").attr("src", image.src);
				this.mainImage.hide().attr("data-id", "").attr("data-id", image.dataid);
				$(".fbphotobox-overlay").show();
				$(".fbphotobox-main-container").show();
                 $("#new-comment").focus();
				this.mainImage.show(10, function() { $(this).trigger("onFbBoxImageShow"); });
				$(".fbphotobox-fc-main-image").attr("src","").attr("src", image.src);
			}

			if (!isShow) this.refreshTagSize();
		}, 
		
		refreshTagSize: function() {
			var $tag = $(".fbphotobox-tag");
			var newHeight = this.mainImage.height();
			var newWidth = this.mainImage.width();
			$tag.each(function() {
				$(this).css({
					left: $(this).attr("x")*newWidth,
					top: $(this).attr("y")*newHeight,
					width: $(this).attr("w")*newWidth,
					height: $(this).attr("h")*newHeight
				});
			});
		},
		
		refreshFullScreenSize: function() {
			$(".fbphotobox-fc-main-image").css({
				"max-width": $(window).width() - $(".fc-left-arrow").width() - $(".fc-right-arrow").width() - 20,
				"max-height": $(window).height() - $(".fbphotobox-fc-header").outerHeight(true) - $(".fbphotobox-fc-footer").outerHeight(true)
			});
			$(".fbphotobox-fc-main-image").closest("div").css("height", $(window).height() - $(".fbphotobox-fc-header").outerHeight(true) - $(".fbphotobox-fc-footer").outerHeight(true));
		},
		
		repositionBox: function() {
			var container = $(".fbphotobox-main-container");
			var left = ($(window).width() - container.width())/2;
			var top = ($(window).height() - container.height())/2;
			$(".fbphotobox-main-container").css({left: left, top: top});
		},
		
		hideScroll: function() {
			$('body').css({width:$(window).width(),height:$(window).height(), overflow:"hidden"});
		},
		
		displayScroll: function() {
			$('body').css({width:this.bodyDimension.width, height:this.bodyDimension.height, overflow:"scroll"});
		}
	};
		
	$.fn.fbPhotoBox = function(options) {
		var args = Array.prototype.slice.call(arguments, 1);
		var item = this;
        
        if(options.profile === true){
           	var instance = item.data('FBPhotoBox');
        }else {
            var instance = item.data('');   
        }
		if(!instance) {
			item.data('FBPhotoBox', new FBPhotoBox(this, options));
		} else {
			if(typeof options === 'string') {
				return instance[options].apply(instance, args);
			}
		}
	};
	
	$.fn.fbPhotoBox.defaults = {
		rightWidth: 0,
		minLeftWidth: 520,
		minHeight: 520,
		leftBgColor: "black",		
		rightBgColor: "white",
		footerBgColor: "black",
		overlayBgColor: "black",
		admin: false,
        profile: false,
		overlayBgOpacity: 0.96,
		onImageShow: function() {},
		afterInitDOM: function() {},
		imageOverlayFadeSpeed: 150,
		normalModeMargin: 40
	};
}(jQuery));