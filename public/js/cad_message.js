/*
 * einfache message klasse, die an den wrapper gekoppelt
 * ist und damit gemeinsam mit loader, sperre
 * und catch_esc interagieren können
 */
CAD.Message = function(el) {
  
  if ( !el ) {
	
	var dom_element = jQuery('<div class="message"><div class="button_close"></div><div class="message_text"></div><br class="clearfix" /></div>');
	jQuery(dom_element).find('.button_close').unbind('click');
	jQuery(dom_element).find('.button_close').bind('click', function()
	{
		this.close(true);
	});
    this._element = dom_element;
	jQuery('body').append(jQuery(dom_element));
	
  } else {
    this._element = el;
  }
  jQuery(this._element).draggable();
  
  this._message_level = null;
  
//  console.log(CAD.Message.counter);
  
};

CAD.Message.prototype = new CAD.Wrapper;
CAD.Message.prototype.constructor = CAD.Message;
CAD.Message.parent = CAD.Wrapper.prototype;
CAD.Message.counter = 0;

CAD.Message.prototype.init = function(text, b_hide_sperre, b_blur) {

	jQuery(this._element).find('.message_text').html("");
	var self = this;
	
	if(typeof text != 'object')
	{
		try
		{
			text = JSON.parse(text);
		}
		catch(e)
		{
		}
	}
	
	if(typeof text == "object")
	{
		var content = '';
		var b_need_confirm = false;
		var confirm_func = '';
		var cancel_func = '';
		
		var button_1_text = "Ok";
		var button_2_text = "Abbrechen";
		
		if(b_hide_sperre == null ||
		   b_hide_sperre == undefined)
		{
			b_hide_sperre = true;
		}
		
		var a_status = {'meldung': 1, 'warnung': 2, 'fehler': 3};
		
		// array
		if(text[0])
		{
			for(var i = 0; i < text.length; i++)
			{
				/**
				 * schleife beim durchlaufen auf den jeweiligen
				 * status der meldung checken, ist eine meldung
				 * vom status her höher, wie der aktuelle status,
				 * zähler auf neuen status setzen
				 */
				if(a_status[text[i]['type']] > this._message_level)
				{
					this._message_level = a_status[text[i]['type']];
				}
				
				content += '<p>' + text[i]['message'] + '</p><br class="clear;" /></div>';

				// wenn ein dialog
				if(text[i]['confirm'])
				{
					if(text[i]['button_1'])
					{
						button_1_text = text[i]['button_1'];	
					}
					if(text[i]['confirm_func'])
					{
						confirm_func = text[i]['confirm_func'];
					}
					if(text[i]['cancel_func'])
					{
						cancel_func = text[i]['cancel_func'];
					}
					b_need_confirm = 1;
				}
				else if(text[i]['confirm_func'])
				{
					if(text[i]['button_1'])
					{
						button_1_text = text[i]['button_1'];	
					}
					confirm_func = text[i]['confirm_func'];
				}
                                if(text[i]['id'])
                                {
                                    jQuery('.id').val(text[i]['id']);
                                }
			}

			/**
			 * nur ein status symbol anzeigen, gemessen am maximalen 
			 * status der jeweiligen meldung
			 */
			switch(this._message_level)
			{
				// meldung
				case 1:
				{
					/**
					 * sperre nur ausblenden erzwingen, wenn die antwort
					 * keine fehler enthielt
					 */
					b_hide_sperre = true;
					content = '<div><img src="/images/content/statisch/grafiken/haekchen.png" />' + content;
					break;
				}
				// warnung
				case 2:
				{
					/**
					 * sperre nur ausblenden erzwingen, wenn die antwort
					 * keine fehler enthielt
					 */
					b_hide_sperre = true;
					content = '<div><img src="/images/content/statisch/grafiken/warnung.png" />' + content;
					break;
				}
				// fehler
				case 3:
				{
					content = '<div><img src="/images/content/statisch/grafiken/fehler.png" />' + content;
					break;
				}
					default:
				{
					b_hide_sperre = true;
					content = '<div>' + content;
					break;
				}
			}
		}
		// wenn ein dialog
		if(b_need_confirm)
		{
			
			content += '<b class="clearfix" /><div class="confirm_container">';
			content += '<div class="button_ok" >' + button_1_text + '</div>';
			content += '<div class="button_cancel" >' + button_2_text + '</div>';
			content += '<br class="clear;" />';
			content += '</div>';
		}
		else
		{
			content += '<b class="clearfix" /><div class="ok_container">';
			content += '<div class="button_ok" >' + button_1_text + '</div>';
			content += '<br class="clear;" />';
			content += '</div>';
		}
		
		jQuery(this._element).find('.message_text').html(content);
		
		jQuery(this._element).find('.button_close').unbind('click');
		jQuery(this._element).find('.button_close').bind('click', function(){
			self.close(true);
			eval(cancel_func);
		});
		
		jQuery(this._element).find('.button_ok').unbind('click');
		jQuery(this._element).find('.button_ok').bind('click', function(){
			self.close(b_hide_sperre);
			eval(confirm_func);
		});
		
		jQuery(this._element).find('.button_cancel').unbind('click');
		jQuery(this._element).find('.button_cancel').bind('click', function(){
			self.close(true);
//			cancel_func;
		});
		jQuery(this._element).CAD_center(true);
		
		self.open(b_blur);
	}
	else if(text)
	{
		jQuery(this._element).find('.message_text').html(text);
		
		jQuery(this._element).find('.button_close').unbind('click');
		jQuery(this._element).find('.button_close').bind('click', function(){
			self.close(true);
		});

		jQuery(this._element).CAD_center();
		self.open(b_blur);
	}
};

CAD.Message.prototype.open = function(b_blur, b_scroll) {

  CAD.instance_sperre.open();
  CAD.Message.parent.element = this._element;
  CAD.Message.parent.open(this);
  
  jQuery(this._element).fadeIn('slow', function()
  {
	jQuery(this).data("data-flag-visible", true);
  });

  if(this._message_level == 1)
  {
 	return true;
  }
  return false;
};

CAD.Message.prototype.close = function(b_hide_sperre) {
  
  CAD.Message.parent.close(this);
  
	jQuery(this._element).fadeOut('slow', function()
	{
		jQuery(this).data("data-flag-visible", false);
		jQuery(this).remove();
		
		if(b_hide_sperre)
		{
		    CAD.instance_sperre.close();
		}
		else
		{
//			console.log("Zeige Loader nach hideMessage an!");
//			CAD_Loader.open('/images/content/statisch/grafiken/ajax-loader_black_trans.gif');
		}
	});
	
  return this;
};
