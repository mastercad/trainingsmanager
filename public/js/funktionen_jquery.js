
	// counter für ueberwacheInputs
	var form_changed = 0;
	
	if(typeof jQuery === "undefined")
	{
		load_script("http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js");
	}
	
	function load_script(script)
	{
		a = document.createElement('script');
		a.setAttribute('type', 'text/javascript');
		a.setAttribute('src', script);
		document.getElementsByTagName('head')[0].appendChild(a);
	}
	
	jQuery.fn.CAD_maxlength = function(e)
	{
		if(jQuery(this).attr('maxlength'))
		{
			return this.each(function()
			{
				var self = jQuery(this);
			    function limitInput()
			    {
			    	if(self.val().length >= self.attr('maxlength'))
			    	{
			    		var text = self.val();
			    		self.val(text.substr(0, self.attr('maxlength')));
			    		if(self.data("data-background-color") == undefined)
			    		{
			    			self.data("data-background-color", self.css('background-color'));
			    		}
			    		if(self.data("data-color") == undefined)
			    		{
			    			self.data("data-color", self.css('color'));
			    		}
			    		self.css("background-color", "#CCCCCC");
			    		self.css("color", "#666666");
						self.data("data-disabled-set", true);
			    	}
			    	else if(self.val().length < self.attr('maxlength') &&
				    		self.data("data-disabled-set") == true)
			    	{
			    		self.data("data-disabled-set", false);
			    		self.css("background-color", self.data("data-background-color"));
			    		self.css("color", self.data("data-color"));
			    	}
			    		
			    }
			    jQuery(this).keypress(limitInput)
			    .keydown(limitInput)
			    .keyup(limitInput)
			    .mouseup(limitInput)
			    .blur(limitInput)
			    .css('overflow','hidden');
		  	});
		}
		return false;
	};
	
	jQuery.fn.CAD_generatePassword = function(e)
	{		    
		if(obj_blur_input)
		{
			obj_blur_input.value = '';
		}
		else
		{
			obj_blur_input = document.aForm.passField;
		}
		
	    if (parseInt(navigator.appVersion) <= 3)
	    { 
	        alert("Sorry this only works in 4.0 browsers"); 
	        return true; 
	    }
	    
	    var length=8;
	    var sPassword = "";
	    length = document.aForm.charLen.options[document.aForm.charLen.selectedIndex].value;
	    
	    var noPunction = (document.aForm.punc.checked);
	    var randomLength = (document.aForm.rLen.checked);
	    
	    if (randomLength)
	    { 
	        length = Math.random(); 
	        
	        length = parseInt(length * 100);
	        length = (length % 7) + 6;
	    }
	    
	    
	    for (var i = 0; i < length; i++)
	    {
	    
	        numI = getRandomNum();
	        if (noPunction) { while (checkPunc(numI)) { numI = getRandomNum(); } }
	        
	        sPassword = sPassword + String.fromCharCode(numI);
	    }
	    
	    jQuery(this).val(sPassword);
	    
	    return true;
	};
	
	jQuery.fn.CAD_catchReturn = function()
	{
		jQuery(this).keydown(function (e)
		{
		    if(e.keyCode == 13)
		    {
		        return false;
		    }
		    return true;
		});
	};
	
	jQuery.fn.CAD_catchESC = function()
	{
		jQuery(this).keydown(function (e)
		{
		    if(e.keyCode == 27)
		    {
		    	console.log("CATCH!");
		    	if(jQuery('#editor_wrapper').is(':visible'))
		    	{
		    		jQuery('#editor_wrapper').fadeOut();
		    		return false;
		    	}
		    	if(jQuery('#context_menu').is(':visible'))
		    	{
		    		jQuery('#context_menu').fadeOut();
		    		return false;
		    	}
		    	if(jQuery('#message').is(':visible'))
		    	{
		    		hideMessage(true);
		    		return false;
		    	}
		    	if(jQuery('#progress').is(':visible') ||
		    	   jQuery('#work_progress').is(':visible'))
		    	{
		    		return false;
		    	}
		    	if(jQuery('#sperre').is(':visible'))
		    	{
		    		hideSperre(true);
		    		return false;
		    	}
		        return true;
		    }
		    return true;
		});
	};
	
	jQuery.fn.CAD_elasticArea = function()
	{
		return this.each(function()
		{
		    function resizeTextarea()
		    {
		     	this.style.height = parseInt( this.scrollHeight / 2) + 'px';
				this.style.height = parseInt( this.scrollHeight + 8) + 'px';
		    }
		    jQuery(this).keypress(resizeTextarea)
		    .keydown(resizeTextarea)
		    .keyup(resizeTextarea)
		    .mouseup(resizeTextarea)
		    .css('overflow','hidden');
		    resizeTextarea.call(this);
	  	});
	};
	
	jQuery.fn.CAD_ueberwacheInputs = function()
	{
		return this.each(function()
		{
			var self = jQuery(this);
			
			function setChanged()
			{
				self = jQuery(this);

				if(self.data('changed') != self.val() &&
				   !self.data('b_changed'))
				{
					form_changed++;
					self.data('changed', self.val());
					self.data('b_changed', true);
				}
				else if(self.data('default_value') == self.val() &&
						self.data('changed') != self.val())
				{
					form_changed--;
					self.data('changed', self.val());
					self.data('b_changed', false);
				}
			}
			
			self.data('changed', self.val());
			self.data('default_value', self.val());
			
			jQuery(this).keypress(setChanged)
			.keydown(setChanged)
			.keyup(setChanged)
			.mousedown(setChanged)
			.mouseup(setChanged);
		});
	};
	
	jQuery.fn.CAD_Accordion = function()
	{
		jQuery(this).find('li > ul').hide();
		jQuery(this).find('.header.aktiv ul').show();

		jQuery(this).find('.header span').unbind('click');
		jQuery(this).find('.header span').click(function()
		{
			if(!jQuery(this).parent().parent().is('.aktiv'))
			{
				jQuery(this).parent().parent().find('class="aktiv"').css("background-image", "url(/images/content/statisch/pfeil_weis_rechts_eckig.gif");
				jQuery(this).parent().parent().find('li > ul').hide();
				jQuery(this).parent().find('ul').show(function(){'slow', checkContainerHoehe();});
			}
		});
	};
	
	/*
	 * funktion um in einem html element einen loader anzuzeigen
	 */
	jQuery.fn.CAD_loader = function()
	{
		if(jQuery(this).data('loader') == true)
		{
			var obj_loader = jQuery(this).data('obj_loader');
			
			if(obj_loader)
			{
				obj_loader.remove();
				jQuery(this).data('loader', false);
				jQuery(this).fadeIn();
			}
		}
		else
		{
			var height = jQuery(this).height();
			var width = jQuery(this).width();
			if(height <= 0)
			{
				height = 31;
			}
			var container = jQuery(document.createElement('div'));
			
			if(height < 31)
			{
				container.append('<img src="/images/content/statisch/grafiken/ajax-loader_black_trans.gif" style="height: ' + jQuery(this).height() + 'px;" />');
			}
			else
			{
				container.append('<img src="/images/content/statisch/grafiken/ajax-loader_black_trans.gif" />');
			}
			
			container.css('position', jQuery(this).css('position'));
			container.css('left', jQuery(this).css('left'));
			container.css('top', jQuery(this).css('top'));
			container.css('margin-left', jQuery(this).css('margin-left'));
			container.css('margin-top', jQuery(this).css('margin-top'));
			container.css('float', jQuery(this).css('float'));
			
			container.width(width);
			container.height(height);
			
			jQuery(this).data('obj_loader', container);
			jQuery(this).data('loader', true);
			jQuery(this).hide().after(container);
		}
	};
	
	/**
	 * wenn b_fixed gesetzt wird, wird das zu zentrierende
	 * element fixed dargestellt
	 * 
	 * @param boolean b_fixed
	 */
	jQuery.fn.CAD_center = function(b_fixed)
	{
		var self = jQuery(this);
		
		if(jQuery(this).css('position') == 'fixed')
		{
			b_fixed = true;
		}
		
		jQuery('body').css('position', 'relative');
		
		var document_breite = parseInt(jQuery(document).width());
		var document_hoehe = parseInt(jQuery(document).height());
		
		var view_breite = parseInt(jQuery(window).width());
		var view_hoehe = parseInt(jQuery(window).height());

//		var obj_breite = parseInt(jQuery(this).css('width'));
//		var obj_hoehe = parseInt(jQuery(this).css('height'));
		
		var obj_breite = parseInt(jQuery(this).width());
		var obj_hoehe = parseInt(jQuery(this).height());
		
		var scroll_x = parseInt(jQuery(window).scrollLeft());
		var scroll_y = parseInt(jQuery(window).scrollTop());
		/*
		alert("Document Breite : " + document_breite + " - Document Hoehe : " + 
			   document_hoehe + " - View Breite : " + view_breite + " - View Höhe : " +
			   view_hoehe + " - Obj Breite : " + obj_breite + " - Obj Höhe : " + 
			   obj_hoehe + " - Scroll X : " + scroll_x + " - Scroll Y : " + scroll_y);
		*/
//		jQuery(this).appendTo('body');
		
		var obj_x = 0;
		var obj_y = 0;
		
		if(b_fixed)
		{
			jQuery(this).css('position', 'fixed');
			obj_x = ( parseInt( view_breite - obj_breite) / 2) + "px";
			obj_y = ( parseInt( view_hoehe - obj_hoehe) / 2) + "px";
		}
		else
		{
			obj_x = parseInt( parseInt( ( view_breite - obj_breite) / 2) + scroll_x) + "px";
			obj_y = parseInt( parseInt( ( view_hoehe - obj_hoehe) / 2) + scroll_y) + "px";
		}
		
//		obj_x = parseInt( parseInt( ( document_breite - view_breite - obj_breite) / 2) + scroll_x) + "px";
//		obj_y = parseInt( parseInt( ( document_hoehe - view_hoehe - obj_hoehe) / 2) + scroll_y) + "px";

//		console.log("Document Breite : " + document_breite);
//		console.log("Document Höhe : " + document_hoehe);
//		console.log("View Breite : " + view_breite);
//		console.log("View Höhe : " + view_hoehe);
//		console.log("Object Breite : " + obj_breite);
//		console.log("Object Höhe : " + obj_hoehe);
//		console.log("Scroll Left : " + scroll_x);
//		console.log("Scroll Top : " + scroll_y);
		
//		console.log(this);
//		console.log(this.html());
		
		if(jQuery(this).css('position') != 'fixed' &&
		   jQuery(this).css('position') != 'absolute')
		{
			orig_position = jQuery(this).css('position');
			jQuery(this).data('position', orig_position);
			jQuery(this).css('position', 'absolute');
		}
		
		jQuery(this).css('z-index', jQuery('#sperre').css('z-index') + 1);
		jQuery(this).css('margin', 0);
		jQuery(this).css('left', obj_x);
		jQuery(this).css('top', obj_y);
		
		return this;
	};
	
	jQuery.fn.CAD_copyCSS = function(source)
	{
	    var dom = jQuery(source).get(0);
	    var style;
	    var dest = {};
	    if(window.getComputedStyle)
	    {
	        var camelize = function(a,b)
	        {
	            return b.toUpperCase();
	        };
	        style = window.getComputedStyle(dom, null);
	        for(var i = 0, l = style.length; i < l; i++)
	        {
	            var prop = style[i];
	            var camel = prop.replace(/\-([a-z])/g, camelize);
	            var val = style.getPropertyValue(prop);
	            dest[camel] = val;
	        };
	        return this.css(dest);
	    };
	    if(dom.currentStyle != undefined)
	    {
	    	style = dom.currentStyle;
	        for(var prop in style)
	        {
	            dest[prop] = style[prop];
	        };
	        return this.css(dest);
	   };
	   if(dom.style != undefined)
	   {
		   style = dom.style;
		   for(var prop in style)
		   {
			   if(typeof style[prop] != 'function')
			   {
				   dest[prop] = style[prop];
			   };
		   };
	    };
	    return this.css(dest);
	};

	jQuery.fn.CAD_close = function()
	{
		jQuery(this).parent().fadeOut(function()
		{
			/**
			 * @TODO hier vielleicht noch dafür sorgen, dass eventuell die 
			 * sperre ausgeblendet wird
			 */
			CAD_Sperre.close();
		});
		
	};
	
	// workaround um ein unbekanntes console event abzufangen
	if (typeof console == "undefined")
	{
	    this.console = {"log": function() {}};
	}