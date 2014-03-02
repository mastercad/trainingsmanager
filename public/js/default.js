
CAD_Sperre = null;
CAD_Loader = null;
CAD_Message = null;
CAD_Catch_ESC = null;
CAD_Kontaktformular = null;

function init()
{
    CAD_Sperre = new CAD.Sperre();
    CAD_Loader = new CAD.Loader();
    CAD_Message = new CAD.Message();
    CAD_Catch_ESC = new CAD.Catch_ESC();
    CAD_Catch_ESC.init();

    /**
     * links neutralisieren um sie per JQuery verfügbar zu machen
     */
    jQuery('#link_kontaktanfrage').attr("href", "Javascript: void(0);");
    jQuery('#link_kontaktanfrage').unbind('click');
    jQuery('#link_kontaktanfrage').bind('click', function()
    {
    	CAD_Sperre.open(true, true, true);
    	CAD_Loader.open();
    	
    	var url = "/kontakt";
    	var obj_params = {'ajax': 'true'};
    	
    	jQuery.get(url, obj_params, function(response)
    	{
    		CAD_Kontaktformular = new CAD.Message();
    		CAD_Kontaktformular.init(response);
    		prepareInputs();
    		CAD_Loader.close();
    		
    		CAD_Kontaktformular.open();
    	});
    });
    
    jQuery('#login-button').attr("href", "Javascript: void(0);");
    jQuery('#login-button').unbind('click');
    jQuery('#login-button').bind('click', function()
    {
    	CAD_Sperre.open(true, true, true);
    	CAD_Loader.open();
    	
    	var url = "/auth/login-form";
    	var obj_params = {'ajax': 'true'};
    	
    	jQuery.post(url, obj_params, function(response)
    	{
    		var obj_cad_message = new CAD.Message();
    		obj_cad_message.init(response);
//    		prepareInputs();
    		CAD_Loader.close();
    		
    		obj_cad_message.open();
    	});
    });
    
    jQuery('.delete-button').unbind('click');
    jQuery('.delete-button').each(function()
    {
    	if(!jQuery(this).data('id'))
    	{
        	var id = gup(jQuery(this).attr("href"), "id");
        	jQuery(this).data('id', id);
            jQuery(this).attr("href", "Javascript: void(0);");
    	}
    });
    
    jQuery('.delete-button').bind('click', function()
    {
    	CAD_Loader.open();
    	var url = '/' + controller + '/loeschen/';
		var id = jQuery(this).data('id');
    	var obj_params = {'id': id, 'ajax': true};
    	var self = jQuery(this);
    	
    	jQuery.post(url, obj_params, function(response){
    		response = JSON.parse(response);

        	CAD_Loader.close();
        	
    		obj_cad_message = new CAD.Message();
    		obj_cad_message.init(response);
    		if(obj_cad_message.open())
			{
    			self.parent().fadeOut();
			}
    	});
    });
}

jQuery(window).load(function()
{
    if(jQuery('.fixed_bar') != undefined &&
       jQuery('.fixed_bar').length)
    {
    	var parent_margin_top = parseInt(jQuery('.fixed_bar').parent().css('margin-top'));
		var position_top = jQuery('.fixed_bar').offset().top - parent_margin_top;
		var width = parseInt(jQuery('.fixed_bar').width());
		var height = parseInt(jQuery('.fixed_bar').height());
		
		jQuery(window).bind('scroll', function()
	    {
			var pos_bottom = parseInt(jQuery(window).height() + jQuery(window).scrollTop());
			
	    	if (jQuery(window).scrollTop() > parseInt(position_top) &&
	    		pos_bottom < jQuery(document).height())
	        {
//		    	parent_margin_top = parseInt(jQuery('.fixed_bar').parent().css('margin-top'));
//				position_top = jQuery('.fixed_bar').offset().top - parent_margin_top;
				
	    		if(!jQuery('.fixed_bar').parent().hasClass('fixed-container-reset'))
	    		{
	    			jQuery('.fixed_bar').parent().addClass('fixed-container-reset');
	    			jQuery('.fixed_bar').data("data-parent-height", jQuery('.fixed_bar').parent().height());
	    			
	    			jQuery('.fixed_bar').parent().css('height', jQuery('.fixed_bar').parent().height() + "px");
		        	jQuery('.fixed_bar').width(width);
	    		}
	        	jQuery('.fixed_bar').addClass('fixed');
	        }
	        else if(jQuery(window).scrollTop() < parseInt(position_top) &&
	        		pos_bottom < jQuery(document).height())
	        {
    			jQuery('.fixed_bar').parent().css("height", jQuery('.fixed_bar').data("data-parent-height"));
    			jQuery('.fixed_bar').removeData('data-parent-height');
	        	jQuery('.fixed_bar').removeClass('fixed-container-reset');
	        	jQuery('.fixed_bar').removeClass('fixed');
//	        	position_top = jQuery(window).scrollTop();
//	        	parent_margin_top = 0;
	        }
	    });
    }
});

/**
 * funktion zum senden des kontaktformulars
 * 
 * 
 */
function senden()
{
	CAD_Loader.open('/images/content/statisch/grafiken/ajax-loader_black_trans.gif');
	
	var url = "/kontakt/senden";
	var obj_params = {};
	
	var name = jQuery('#kontakt_container #name').val();
	var name_default = jQuery('#kontakt_container #name').attr("data-default");
	
	obj_params.ajax = true;
	
	if(name &&
	   name != undefined &&
	   name != name_default)
	{
		obj_params.kontaktformular_name = Base64.encode(name);
	}
	var email = jQuery('#kontakt_container #email').val();
	var email_default = jQuery('#kontakt_container #email').attr("data-default");
	if(email &&
	   email != undefined &&
	   email != email_default)
	{
		obj_params.kontaktformular_email = Base64.encode(email);
	}
	var nachricht = jQuery('#kontakt_container #nachricht').val();
	var nachricht_default = jQuery('#kontakt_container #nachricht').attr("data-default");
	if(nachricht &&
	   nachricht != undefined &&
	   nachricht != nachricht_default)
	{
		obj_params.kontaktformular_nachricht = Base64.encode(nachricht);
	}
	
	jQuery.post(url, obj_params, function(response)
	{
		CAD_Loader.close();
		obj_cad_message = new CAD.Message();
		response = eval("(" + response + ")");
		
		if(typeof response == "object")
		{
			obj_cad_message.init(response, false, true);
			
			if(obj_cad_message.open())
			{
			  CAD_Kontaktformular.close();
			}
		}
		else
		{
			alert(response);
		}
	});
}

/**
 * funktion zum vorbereiten des kontaktformulars
 * gibt es default werte in den elementen und
 * sind noch keine inhalte in den elementen, dann
 * wird eine klasse namens "pflichtfeld" gesetzt
 * und der default wert als value übernommen
 * 
 * erhält das feld focus wird die klasse und der
 * default wert entfernt, wirft das element blur
 * und das value entspricht dem default wert,
 * wird die klasse "pflichtfeld" wieder gesetzt
 */
function prepareInputs()
{
//	CAD_Sperre.open(true, true, true);
//	CAD_Loader.open('/images/content/statisch/grafiken/ajax-loader_black_trans.gif');
	
	jQuery('input[data-default], textarea[data-default]').each(function()
	{
		var self = jQuery(this);
		self.focus(function()
		{
			if(self.is('.password') &&
			   self.attr('type') == "text")
			{
//				self.setAttribute("type","password");
			}
			if(self.val() == self.attr('data-default'))
			{
				self.val("");
				self.removeClass("pflichtfeld");
			}
		});
		
		self.blur(function(){
			if(self.val().length < 1 &&
					self.val() != self.attr('data-default'))
			{
				self.val(self.attr('data-default'));
				self.addClass("pflichtfeld");
			}
		});
		
		/**
		 * nur setzen, wenn nicht bereits ein default wert
		 * übergeben wurde!
		 */
		if(self.val().length < 1)
		{
			if(self.is('.password') &&
			   self.attr('type') == "password")
			{
//				self.setAttribute("type", "text");
			}
			self.val(self.attr('data-default'));
		}
		self.addClass('pflichtfeld');
	}).promise().done(function()
	{
//		CAD_Loader.close(true);
	});
}

function gup(url, index)
{
	url = url.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
    var regex = new RegExp("/" + index + "/([0-9]*)");
    var results = regex.exec(url);
    
    if( null == results)
    {
        return "";
    }
    else
	{
        return results[1];
	}
}
