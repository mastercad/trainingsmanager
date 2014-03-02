/*
 * einfache sperre klasse, die an den wrapper gekoppelt
 * ist und damit gemeinsam mit message, loader
 * und catch_esc interagieren können
 *
 * eigentlich sollte diese klasse ein singleton werden,
 * ich bekomms aber nicht hin, dass ich auf ihr parent
 * zugreifen kann, wenn sie von Wrapper erbt, wenn sie
 * als singleton in einem closure gekapselt ist, daher
 * lege ich erstmal im namespace eine instanz ab und
 * checke bei jedem constructor aufruf, ob diese schon
 * existiert
 */

CAD.Sperre = function(el)
{
  // instanz existiert im namespace bereits => zürckgeben
  if ( CAD.instance_sperre)
  {
    return CAD.instance_sperre;
  }

  // wenn instanz noch nicht existiert, normale klasseninstanz
  // erstellen und im namespace ablegen
  if ( !el ) {
	  this._element = jQuery('<div id="sperre"></div>');
	  jQuery('body').append(this._element);
  } else {
    this._element = el;
  }

  this._b_status = false;
  this._b_blur = false;
  this._b_scroll = false;
  this._b_show_loader = false;
	
  CAD.instance_sperre = this;
};

CAD.Sperre.prototype = new CAD.Wrapper;
CAD.Sperre.prototype.constructor = CAD.Sperre;
CAD.Sperre.parent = CAD.Wrapper.prototype;

CAD.Sperre.prototype.open = function(b_blur, b_scroll, b_show_loader) {

    if(false === this._b_status) {
      
      CAD.Sperre.parent.element = this._element;
      CAD.Sperre.parent.open(this);
      this._b_status = true;

		if(b_blur)
		{
			this._b_blur = b_blur;
		}
		
		if(b_scroll)
		{
			this._b_scroll = b_scroll;
		}
		
		if(b_show_loader)
		{
			this._b_show_loader = b_show_loader;
		}
		
		if(!jQuery(this._element).is(':visible') &&
		   this._b_scroll)
		{
			var overflow = jQuery('html').css('overflow');
			
			if(overflow == undefined)
			{
				overflow = "auto";
			}
			jQuery('html').data('data-scroll-top', jQuery(window).scrollTop());
			jQuery('html').data('data-scroll-left', jQuery(window).scrollLeft());
			jQuery('html').data('data-overflow', overflow);
			jQuery('html').css('overflow', 'hidden');
		}
		
		if(!jQuery(this._element).is(':visible'))
		{
			jQuery(this._element).fadeTo('slow', 0.7, function()
			{
				jQuery(this).data("data-flag-visible", false);
	
				if(this._b_show_loader)
				{
					CAD_Loader.open('/images/content/statisch/grafiken/ajax-loader_black_trans.gif');
				}
				
				if(this._b_blur && 
				   jQuery.browser.msie)
				{
					jQuery('#wrapper_content').attr("orig-filters", jQuery('#wrapper_content').css("filter"));
					jQuery('#wrapper_content').css("filter","progid:DXImageTransform.Microsoft.Blur(PixelRadius='10');");
					jQuery('#wrapper_content *').filter(function()
					{
						if(
							!jQuery(this).hasClass("verbose_blur") &&
							(
								jQuery(this).css('position').toLowerCase().indexOf('relative') > -1 ||
								jQuery(this).css('position').toLowerCase().indexOf('absolute') > -1
							)
						)
						{
							jQuery(this).attr("orig-filters", jQuery(this).css("filter"));
							jQuery(this).css("filter","progid:DXImageTransform.Microsoft.Blur(PixelRadius='10');");
						}
						else if(jQuery(this).hasClass("verbose_blur"))
						{
							if(jQuery(this).attr("orig-filters"))
							{
								jQuery(this).attr("orig-filters", jQuery(this).css("filter"));
							}
							else
							{
								jQuery(this).css("filter","progid:DXImageTransform.Microsoft.Blur(PixelRadius='0');");
							}
						}
					});
				}
				else if(this._b_blur)
				{
					jQuery('#wrapper_content').blurjs(
					{
						persist: false,
					    radius: 10
					});
				}
			});
		}
    }
    return this;
};

CAD.Sperre.prototype.close = function(b_hide_loader) {

//	console.log("In Sperre Close!");
//	console.log("Opened Objects : " + CAD.getCountOpenedObjects());
//	console.log(CAD.getOpenedObjects());

  if ( CAD.getCountOpenedObjects() <= 1) {

    CAD.Sperre.parent.close();
    
    this._b_status = false;
	
	if(this._b_show_loader)
	{
//		CAD_Loader.close();
	}
	
	if(jQuery)
	{		
		if(this._b_scroll ||
		   jQuery('html').data("data-overflow") != undefined
		)
		{
			jQuery('html').css('overflow', jQuery('html').data('data-overflow'));
			
			if(jQuery('html').data('data-scroll-top'))
			{
				jQuery('html,body').animate({scrollTop: jQuery('html').data('data-scroll-top')});
			}
		}
		
		jQuery(this._element).fadeTo('slow', 0, function()
		{
			jQuery(this).css('display', 'none');
			jQuery(this).data("data-flag-visible", false);
		});
		
		if(jQuery.browser.msie &&
		   this._b_blur)
		{
			this._b_blur = false;
			
			jQuery('#wrapper_content').css("filter", jQuery('#wrapper_content').attr("orig-filters"));
			jQuery('#wrapper_content *').filter(function()
			{
				if(jQuery(this).css('position').toLowerCase().indexOf('relative') > -1 ||
				   jQuery(this).css('position').toLowerCase().indexOf('absolute') > -1)
				{
					jQuery(this).css("filter", jQuery(this).attr("orig-filters"));
				}
			});
		}
		else if(_b_blur)
		{
//			console.log("Entferne Blur für alle nicht IE browser!");
			_b_blur = false;
			jQuery.blurjs('reset');
		}
		return true;
	}
	else if(document.getElementById('sperre'))
	{
		document.getElementById('sperre').style.display = "none";
		document.getElementsByTagName('html')[0].style.overflow = "auto";
		return true;
	}
	return true;
  }

  return this;
};
