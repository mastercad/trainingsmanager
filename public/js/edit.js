	var controller = '';
        var obj_ref = null;
        var init_func = null;
        
	jQuery('document').ready(function()
	{
		var obj_cad_cms = new jQuery('html').CAD_CMS(jQuery('.editable'), controller);

		jQuery.noConflict();
		
                if(controller == "uebungen")
                {
                    aktualisiereBilder();
                    aktualisiereMuskelgruppen();
                }
                else if(controller == "muskeln" ||
                        controller == "muskelgruppen")
                {
                    aktualisiereMuskeln();
                }
                else if(controller == "geraetegruppen")
                {
                    aktualisiereGeraete();
                }
                else if(controller == "geraete")
                {
                    aktualisiereBilder();
                }
	});

	function wechselBild(response)
	{
            if(jQuery('.vorschaubild').length > 0 &&
               jQuery('.vorschaubild').attr("src").length == 0 ||
               jQuery('.vorschaubild').attr("src") == "/images/content/statisch/grafiken/kein_bild.png" ||
               (
                    jQuery('.vorschaubild').attr("src").length > 0 &&
                    jQuery('.vorschaubild').attr("src") != "/images/content/statisch/grafiken/kein_bild.png" &&
                    confirm("Wollen Sie den bestehenden Inhalt gegen das hochgeladene Bild tauschen?")
               )
            ) 
            {
                var obj_json = JSON.parse(response);

                jQuery().setEditedElements(jQuery('.vorschaubild').attr("id"), 		obj_json.html_pfad);

                if(jQuery('.vorschaubild').has('.cad-cms-dummy-blaeher'))
                {
                    jQuery('.vorschaubild').removeClass('cad-cms-dummy-blaeher');
                }
                jQuery('.vorschaubild').attr("src", obj_json.html_pfad);
                jQuery('.vorschaubild').data("src", Base64.encode(obj_json.html_pfad));
                jQuery('.vorschaubild').data("file", Base64.encode(obj_json.file));
            }

            aktualisiereBilder();
	}

	function aktualisiereBilder()
	{
            var url = '/' + controller + '/hole-bilder-fuer-edit';
            var id = jQuery('.id').val();

            var obj_params = {'id': id, 'ajax': true};

            jQuery.post(url, obj_params, function(response)
            {
                jQuery('.vorschau_bilder').html(response);
                edit_init();
            });
	}

    function aktualisiereGeraete()
    {
        var url = '/' + controller + '/get-geraete-fuer-edit';
        var id = jQuery('.id').val();

        var obj_params = {'id': id, 'ajax': true};

        jQuery.post(url, obj_params, function(response)
        {
            jQuery('.geraetegruppe-geraete').html(response);
            edit_init();
        });
    }

	function aktualisiereMuskelgruppen()
	{
            var url = '/' + controller + '/get-muskelgruppen-fuer-edit';
            var id = jQuery('.id').val();

            var obj_params = {'id': id, 'ajax': true};

            jQuery.post(url, obj_params, function(response)
            {
                jQuery('.uebung_muskelgruppen').html(response);
                edit_init();
            });
	}

	function aktualisiereMuskeln()
	{
            var url = '/' + controller + '/get-muskeln-fuer-edit';
            var id = jQuery('.id').val();

            var obj_params = {'id': id, 'ajax': true};

            jQuery.post(url, obj_params, function(response)
            {
                jQuery('.muskelgruppe-muskeln').html(response);
                edit_init();
            });
	}

	function handleDropEvent( event, ui )
	{
	  var draggable = ui.draggable;
	  console.log(ui);
	  
	  alert( 'The square with ID "' + draggable.attr('id') + '" was dropped onto me!' );
	}

    function initGeraetegruppenEdit()
    {
        jQuery('.geraetegruppe-geraete').find('.geraet-name').each(function()
        {
            var post_request = null;

            jQuery(this).unbind('keyup');
            jQuery(this).on('keyup', function(e)
            {
                var url = '/geraete/get-geraet-vorschlaege/';
                var suche = Base64.encode(jQuery(this).val());
                var self = jQuery(this);

                jQuery(self).css('background-color', 'red');
                jQuery(self).data('geraet-name', '');
                jQuery(self).data('geraet-id', '');

                if(null !== post_request)
                {
                    post_request.abort();
                }

                post_request = jQuery.ajax({
                    type: "POST",
                    url: url,
                    data: "suche=" + suche + "&ajax=true",
                    success: function(response){
                        var offset = jQuery(self).offset();
                        jQuery('body').append(jQuery('.geraet-vorschlaege'));
                        jQuery('.geraet-vorschlaege').html(response);
//                            jQuery('.geraetegruppen_vorschlaege').css('left', e.currentTarget.offsetLeft + "px");
//                            jQuery('.geraetegruppen_vorschlaege').css('top', e.currentTarget.offsetTop + 20 + "px");
//                            jQuery('.geraetegruppen_vorschlaege').css('left', "5px");
//                            jQuery('.geraetegruppen_vorschlaege').css('top', "20px");
                        jQuery('.geraet-vorschlaege').css('left', offset.left + "px");
                        jQuery('.geraet-vorschlaege').css('top', offset.top + 10 + "px");

                        jQuery('.geraet-vorschlaege').find('.vorschlag').unbind('click');
                        jQuery('.geraet-vorschlaege').find('.vorschlag').bind('click', function()
                        {
                            // check ob diese geraetegruppe bereits gesetzt wurde
                            var vorhandene_geraete = jQuery('.geraetegruppe-geraete').find('.geraet');
                            for(var i = 0, len = vorhandene_geraete.length; i < len; i++)
                            {
                                if(jQuery(vorhandene_geraete[i]).find('.geraet-name').data('geraet-id') ==
                                    jQuery(this).data('vorschlag-id'))
                                {
                                    var a_messages = new Array({'type': 'fehler', 'message': 'Dieses Geraet ist bereits gesetzt!'});
                                    var obj_cad_message = new CAD.Message();
                                    obj_cad_message.init(a_messages);
                                    obj_cad_message.open();

                                    return false;
                                }
                            }
                            jQuery(self).css('background-color', 'green');
                            jQuery(self).val(Base64.decode(jQuery(this).data('vorschlag-text')));
                            jQuery(self).data('geraet-name', Base64.decode(jQuery(this).data('vorschlag-text')));
                            jQuery(self).data('geraet-id', jQuery(this).data('vorschlag-id'));
                            jQuery('.geraet-vorschlaege').fadeOut();
                            return false;
                        });
                        jQuery('.geraet-vorschlaege').fadeIn();
                    }
                });
            });
        });

        jQuery('.geraet-add').unbind('click');
        jQuery('.geraet-add').bind('click', function()
        {
            var obj_cad_loader = new CAD.Loader();
            obj_cad_loader.open();

            var url = '/geraete/get-geraete-fuer-edit/';
            var obj_params = {'ajax': true};

            jQuery.post(url, obj_params, function(response)
            {
//                    jQuery(self).replaceWith(response);
                jQuery('.geraetegruppe-geraete').append(response);
                obj_cad_loader.close(true);
                initGeraetegruppenEdit();
            });
        });

        jQuery('.geraet-delete').unbind('click');
        jQuery('.geraet-delete').bind('click', function()
        {
            var a_messages = new Array();
            obj_ref = jQuery(this).parent();
            obj_cad_messages = new CAD.Message();
            a_messages.push(
                {
                    'type': 'warnung',
                    'message': 'Wollen Sie dieses Geraet wirklich löschen?',
                    'confirm': true,
//                    'confirm_func': 'self.loescheGeraetegruppe()'
                    'confirm_func': 'loescheGeraet()'
                });

            obj_cad_messages.init(a_messages);
            obj_cad_messages.open();
        });
    }

    function initGeraeteEdit()
    {
        jQuery('.geraetegruppe-geraete').find('.geraet-beanspruchung').unbind('mouseover');
        jQuery('.geraetegruppe-geraete').find('.geraet-beanspruchung').unbind('mouseout');
        jQuery('.geraetegruppe-geraete').find('.geraet-beanspruchung').unbind('click');

        jQuery('.geraetegruppe-geraete').find('.geraet-beanspruchung').hover(
            function()
            {
                var x = null;
                jQuery(this).unbind('mousemove');
                jQuery(this).mousemove(function(e)
                {
                    var parentOffset = jQuery(this).parent().offset();
                    x = -100 + ( parseFloat(e.pageX) - parentOffset.left);
                    x = Math.round(x / 20) * 20;
                    jQuery(this).css('background-position', x + "px " + x + "px");
                });

                jQuery(this).unbind('click');
                jQuery(this).click(function()
                {
                    jQuery(this).data('background-position', x + "px " + x + "px");
                    jQuery(this).data('geraet-beanspruchung', Math.round(5 + (x / 20)));
                    jQuery(this).css('background-position',  jQuery(this).data('background-position'));
                    jQuery(this).attr('title', Math.round(5 + (x / 20)));
                });
            },
            function()
            {
                if(undefined !== jQuery(this).data('background-position'))
                {
                    jQuery(this).css('background-position', jQuery(this).data('background-position'));
                }
                else
                {
                    jQuery(this).css('background-position', '-100px 0');
                }
            }
        );

        jQuery('.geraetegruppe-geraete').find('.geraet-name').each(function()
        {
            var post_request = null;

            jQuery(this).unbind('keyup');
            jQuery(this).on('keyup', function(e)
            {
                var url = '/geraete/get-geraet-vorschlaege/';
                var suche = Base64.encode(jQuery(this).val());
                var self = jQuery(this);

                jQuery(self).css('background-color', 'red');
                jQuery(self).data('geraet-name', '');
                jQuery(self).data('geraet-id', '');

                if(null !== post_request)
                {
                    post_request.abort();
                }

                post_request = jQuery.ajax({
                    type: "POST",
                    url: url,
                    data: "suche=" + suche + "&ajax=true",
                    success: function(response){
                        var offset = jQuery(self).offset();
                        jQuery('body').append(jQuery('.geraet-vorschlaege'));
                        jQuery('.geraet-vorschlaege').html(response);
                        jQuery('.geraet-vorschlaege').css('left', offset.left + "px");
                        jQuery('.geraet-vorschlaege').css('top', offset.top + 10 + "px");

                        jQuery('.geraet-vorschlaege').find('.vorschlag').unbind('click');
                        jQuery('.geraet-vorschlaege').find('.vorschlag').bind('click', function()
                        {
                            // check ob diese geraetegruppe bereits gesetzt wurde
                            var vorhandene_geraete = jQuery('.geraetegruppe-geraete').find('.geraet');
                            for(var i = 0, len = vorhandene_geraete.length; i < len; i++)
                            {
                                if(jQuery(vorhandene_geraete[i]).find('.geraet-name').data('geraet-id') ==
                                    jQuery(this).data('vorschlag-id'))
                                {
                                    var a_messages = new Array({'type': 'fehler', 'message': 'Dieser Geraet ist bereits gesetzt!'});
                                    var obj_cad_message = new CAD.Message();
                                    obj_cad_message.init(a_messages);
                                    obj_cad_message.open();

                                    return false;
                                }
                            }
                            jQuery(self).css('background-color', 'green');
                            jQuery(self).val(Base64.decode(jQuery(this).data('vorschlag-text')));
                            jQuery(self).data('geraet-name', Base64.decode(jQuery(this).data('vorschlag-text')));
                            jQuery(self).data('geraet-id', jQuery(this).data('vorschlag-id'));
                            jQuery('.geraet-vorschlaege').fadeOut();
                            return false;
                        });
                        jQuery('.geraet-vorschlaege').fadeIn();
                    }
                });
            });
        });

        jQuery('.geraet-add').unbind('click');
        jQuery('.geraet-add').bind('click', function()
        {
            var obj_cad_loader = new CAD.Loader();
            obj_cad_loader.open();

            var url = '/geraete/get-geraete-fuer-edit/';
            var obj_params = {'ajax': true};

            jQuery.post(url, obj_params, function(response)
            {
//                    jQuery(self).replaceWith(response);
                jQuery('.geraetegruppe-geraete').append(response);
                obj_cad_loader.close(true);
                initGeraeteEdit();
            });
        });

        jQuery('.geraet-delete').unbind('click');
        jQuery('.geraet-delete').bind('click', function()
        {
            var a_messages = new Array();
            obj_ref = jQuery(this).parent();
            obj_cad_messages = new CAD.Message();
            a_messages.push(
                {
                    'type': 'warnung',
                    'message': 'Wollen Sie Dieses Geraet wirklich löschen?',
                    'confirm': true,
//                    'confirm_func': 'self.loescheGeraetegruppe()'
                    'confirm_func': 'loescheGeraet()'
                });

            obj_cad_messages.init(a_messages);
            obj_cad_messages.open();
        });
    }

    jQuery.fn.loescheGeraetegruppe = function()
    {
        jQuery(this).find('.geraetegruppe_name').data('geraetegruppe-name', '');
        jQuery(this).find('.geraetegruppe_name').data('geraetegruppe-id', '');
        jQuery(this).find('.geraetegruppe_beanspruchung').data('geraetegruppe-beanspruchung', '');
        jQuery(this).fadeOut();
    };

    function loescheGeraetegruppe()
    {
        jQuery(obj_ref).find('.geraetegruppe_name').data('geraetegruppe-name', '');
        jQuery(obj_ref).find('.geraetegruppe_name').data('geraetegruppe-id', '');
        jQuery(obj_ref).fadeOut();
    }

    function loescheGeraet()
    {
        jQuery(obj_ref).find('.geraet-name').data('geraet-name', '');
        jQuery(obj_ref).find('.geraet-name').data('geraet-id', '');
        jQuery(obj_ref).fadeOut();
    }

        function initMuskelgruppenEdit()
        {
            jQuery('.muskelgruppe').find('.muskelgruppe_beanspruchung').unbind('mouseover');
            jQuery('.muskelgruppe').find('.muskelgruppe_beanspruchung').unbind('mouseout');
            jQuery('.muskelgruppe').find('.muskelgruppe_beanspruchung').unbind('click');
            
            jQuery('.muskelgruppe').find('.muskelgruppe_beanspruchung').hover(
                function()
                {
                    var x = null;
                    jQuery(this).unbind('mousemove');
                    jQuery(this).mousemove(function(e)
                    {
                        var parentOffset = jQuery(this).parent().offset(); 
                        x = -100 + ( parseFloat(e.pageX) - parentOffset.left);
                        x = Math.round(x / 20) * 20;
                        jQuery(this).css('background-position', x + "px " + x + "px");
                    });
                    
                    jQuery(this).unbind('click');
                    jQuery(this).click(function()
                    {
                        jQuery(this).data('background_position', x + "px " + x + "px");
                        jQuery(this).data('muskelgruppe-beanspruchung', Math.round(5 + (x / 20)));
                        jQuery(this).css('background-position',  jQuery(this).data('background_position'));
                        jQuery(this).attr('title', Math.round(5 + (x / 20)));
                    });
                },
                function()
                {
                    if(undefined !== jQuery(this).data('background_position'))
                    {
                        jQuery(this).css('background-position', jQuery(this).data('background_position'));
                    }
                    else
                    {
                        jQuery(this).css('background-position', '-100px 0');
                    }
                }
            );
            
            jQuery('.muskelgruppe').find('.muskelgruppe_name').each(function()
            {
                var post_request = null;
                
                jQuery(this).unbind('keyup');
                jQuery(this).on('keyup', function(e)
                {
                    var url = '/muskelgruppen/get-muskelgruppen-vorschlaege/';
                    var suche = Base64.encode(jQuery(this).val());
                    var self = jQuery(this);
                    
                    jQuery(self).css('background-color', 'red');
                    jQuery(self).data('muskelgruppe-name', '');
                    jQuery(self).data('muskelgruppe-id', '');
                    
                    if(null !== post_request)
                    {
                        post_request.abort();
                    }

                    post_request = jQuery.ajax({
                        type: "POST",
                        url: url,
                        data: "suche=" + suche + "&ajax=true",
                        success: function(response){
                            var offset = jQuery(self).offset();
                            jQuery('body').append(jQuery('.muskelgruppen_vorschlaege'));
                            jQuery('.muskelgruppen_vorschlaege').html(response);
//                            jQuery('.muskelgruppen_vorschlaege').css('left', e.currentTarget.offsetLeft + "px");
//                            jQuery('.muskelgruppen_vorschlaege').css('top', e.currentTarget.offsetTop + 20 + "px");
//                            jQuery('.muskelgruppen_vorschlaege').css('left', "5px");
//                            jQuery('.muskelgruppen_vorschlaege').css('top', "20px");
                            jQuery('.muskelgruppen_vorschlaege').css('left', offset.left + "px");
                            jQuery('.muskelgruppen_vorschlaege').css('top', offset.top + 10 + "px");
                            
                            jQuery('.muskelgruppen_vorschlaege').find('.vorschlag').unbind('click');
                            jQuery('.muskelgruppen_vorschlaege').find('.vorschlag').bind('click', function()
                            {
                                // check ob diese muskelgruppe bereits gesetzt wurde
                                var vorhandene_muskelgruppen = jQuery('.uebung_muskelgruppen').find('.muskelgruppe');
                                for(var i = 0, len = vorhandene_muskelgruppen.length; i < len; i++)
                                {
                                    if(jQuery(vorhandene_muskelgruppen[i]).find('.muskelgruppe_name').data('muskelgruppe-id') ==
                                        jQuery(this).data('vorschlag-id'))
                                    {
                                        var a_messages = new Array({'type': 'fehler', 'message': 'Diese Muskelgruppe ist bereits gesetzt!'});
                                        var obj_cad_message = new CAD.Message();
                                        obj_cad_message.init(a_messages);
                                        obj_cad_message.open();
                                        
                                        return false;
                                    }
                                }
                                jQuery(self).css('background-color', 'green');
                                jQuery(self).val(Base64.decode(jQuery(this).data('vorschlag-text')));
                                jQuery(self).data('muskelgruppe-name', Base64.decode(jQuery(this).data('vorschlag-text')));
                                jQuery(self).data('muskelgruppe-id', jQuery(this).data('vorschlag-id'));
                                jQuery('.muskelgruppen_vorschlaege').fadeOut();
                                return false;
                            });
                            jQuery('.muskelgruppen_vorschlaege').fadeIn();
                        }
                    });
                });
            });
            
            jQuery('.muskelgruppe-add').unbind('click');
            jQuery('.muskelgruppe-add').bind('click', function()
            {
                var obj_cad_loader = new CAD.Loader();
                obj_cad_loader.open();
                
                var url = '/muskelgruppen/get-muskelgruppe-fuer-edit/';
                var obj_params = {'ajax': true};
                
                jQuery.post(url, obj_params, function(response)
                {
//                    jQuery(self).replaceWith(response);
                    jQuery('.uebung_muskelgruppen').append(response);
                    obj_cad_loader.close(true);
                    initMuskelgruppenEdit();
                });
            });
            
            jQuery('.muskelgruppe-delete').unbind('click');
            jQuery('.muskelgruppe-delete').bind('click', function()
            {
                var a_messages = new Array();
                obj_ref = jQuery(this).parent();
                obj_cad_messages = new CAD.Message();
                a_messages.push(
                {
                    'type': 'warnung', 
                    'message': 'Wollen Sie Diese Muskelgruppe wirklich löschen?',
                    'confirm': true,
//                    'confirm_func': 'self.loescheMuskelgruppe()'
                    'confirm_func': 'loescheMuskelgruppe()'
                });
                
                obj_cad_messages.init(a_messages);
                obj_cad_messages.open();
            });
        }
	
        function initMuskelnEdit()
        {
            jQuery('.muskelgruppe-muskeln').find('.muskel-beanspruchung').unbind('mouseover');
            jQuery('.muskelgruppe-muskeln').find('.muskel-beanspruchung').unbind('mouseout');
            jQuery('.muskelgruppe-muskeln').find('.muskel-beanspruchung').unbind('click');
            
            jQuery('.muskelgruppe-muskeln').find('.muskel-beanspruchung').hover(
                function()
                {
                    var x = null;
                    jQuery(this).unbind('mousemove');
                    jQuery(this).mousemove(function(e)
                    {
                        var parentOffset = jQuery(this).parent().offset(); 
                        x = -100 + ( parseFloat(e.pageX) - parentOffset.left);
                        x = Math.round(x / 20) * 20;
                        jQuery(this).css('background-position', x + "px " + x + "px");
                    });
                    
                    jQuery(this).unbind('click');
                    jQuery(this).click(function()
                    {
                        jQuery(this).data('background-position', x + "px " + x + "px");
                        jQuery(this).data('muskel-beanspruchung', Math.round(5 + (x / 20)));
                        jQuery(this).css('background-position',  jQuery(this).data('background-position'));
                        jQuery(this).attr('title', Math.round(5 + (x / 20)));
                    });
                },
                function()
                {
                    if(undefined !== jQuery(this).data('background-position'))
                    {
                        jQuery(this).css('background-position', jQuery(this).data('background-position'));
                    }
                    else
                    {
                        jQuery(this).css('background-position', '-100px 0');
                    }
                }
            );
            
            jQuery('.muskelgruppe-muskeln').find('.muskel-name').each(function()
            {
                var post_request = null;
                
                jQuery(this).unbind('keyup');
                jQuery(this).on('keyup', function(e)
                {
                    var url = '/muskeln/get-muskel-vorschlaege/';
                    var suche = Base64.encode(jQuery(this).val());
                    var self = jQuery(this);
                    
                    jQuery(self).css('background-color', 'red');
                    jQuery(self).data('muskel-name', '');
                    jQuery(self).data('muskel-id', '');
                    
                    if(null !== post_request)
                    {
                        post_request.abort();
                    }

                    post_request = jQuery.ajax({
                        type: "POST",
                        url: url,
                        data: "suche=" + suche + "&ajax=true",
                        success: function(response){
                            var offset = jQuery(self).offset();
                            jQuery('body').append(jQuery('.muskel-vorschlaege'));
                            jQuery('.muskel-vorschlaege').html(response);
                            jQuery('.muskel-vorschlaege').css('left', offset.left + "px");
                            jQuery('.muskel-vorschlaege').css('top', offset.top + 10 + "px");
                            
                            jQuery('.muskel-vorschlaege').find('.vorschlag').unbind('click');
                            jQuery('.muskel-vorschlaege').find('.vorschlag').bind('click', function()
                            {
                                // check ob diese muskelgruppe bereits gesetzt wurde
                                var vorhandene_muskeln = jQuery('.muskelgruppe-muskeln').find('.muskel');
                                for(var i = 0, len = vorhandene_muskeln.length; i < len; i++)
                                {
                                    if(jQuery(vorhandene_muskeln[i]).find('.muskel-name').data('muskel-id') ==
                                        jQuery(this).data('vorschlag-id'))
                                    {
                                        var a_messages = new Array({'type': 'fehler', 'message': 'Dieser Muskel ist bereits gesetzt!'});
                                        var obj_cad_message = new CAD.Message();
                                        obj_cad_message.init(a_messages);
                                        obj_cad_message.open();
                                        
                                        return false;
                                    }
                                }
                                jQuery(self).css('background-color', 'green');
                                jQuery(self).val(Base64.decode(jQuery(this).data('vorschlag-text')));
                                jQuery(self).data('muskel-name', Base64.decode(jQuery(this).data('vorschlag-text')));
                                jQuery(self).data('muskel-id', jQuery(this).data('vorschlag-id'));
                                jQuery('.muskel-vorschlaege').fadeOut();
                                return false;
                            });
                            jQuery('.muskel-vorschlaege').fadeIn();
                        }
                    });
                });
            });
            
            jQuery('.muskel-add').unbind('click');
            jQuery('.muskel-add').bind('click', function()
            {
                var obj_cad_loader = new CAD.Loader();
                obj_cad_loader.open();
                
                var url = '/muskeln/get-muskeln-fuer-edit/';
                var obj_params = {'ajax': true};
                
                jQuery.post(url, obj_params, function(response)
                {
//                    jQuery(self).replaceWith(response);
                    jQuery('.muskelgruppe-muskeln').append(response);
                    obj_cad_loader.close(true);
                    initMuskelnEdit();
                });
            });
            
            jQuery('.muskel-delete').unbind('click');
            jQuery('.muskel-delete').bind('click', function()
            {
                var a_messages = new Array();
                obj_ref = jQuery(this).parent();
                obj_cad_messages = new CAD.Message();
                a_messages.push(
                {
                    'type': 'warnung', 
                    'message': 'Wollen Sie Dieser Muskel wirklich löschen?',
                    'confirm': true,
//                    'confirm_func': 'self.loescheMuskelgruppe()'
                    'confirm_func': 'loescheMuskel()'
                });
                
                obj_cad_messages.init(a_messages);
                obj_cad_messages.open();
            });
        }
        
        jQuery.fn.loescheMuskelgruppe = function()
        {
            jQuery(this).find('.muskelgruppe_name').data('muskelgruppe-name', '');
            jQuery(this).find('.muskelgruppe_name').data('muskelgruppe-id', '');
            jQuery(this).find('.muskelgruppe_beanspruchung').data('muskelgruppe-beanspruchung', '');
            jQuery(this).fadeOut();
        };
        
        function loescheMuskelgruppe()
        {
            jQuery(obj_ref).find('.muskelgruppe_name').data('muskelgruppe-name', '');
            jQuery(obj_ref).find('.muskelgruppe_name').data('muskelgruppe-id', '');
            jQuery(obj_ref).find('.muskelgruppe_beanspruchung').data('muskelgruppe-beanspruchung', '');
            jQuery(obj_ref).fadeOut();
        }
        
        function loescheMuskel()
        {
            jQuery(obj_ref).find('.muskel-name').data('muskel-name', '');
            jQuery(obj_ref).find('.muskel-name').data('muskel-id', '');
            jQuery(obj_ref).find('.muskel-beanspruchung').data('muskel-beanspruchung', '');
            jQuery(obj_ref).fadeOut();
        }
        
	function CADEditSave()
	{
            var obj_cad_loader = new CAD.Loader();
            obj_cad_loader.open();
            
            var edited_elements = jQuery().getEditedElements();

            var url = '/' + controller + '/speichern';

            jQuery.post(url, edited_elements, function(response)
            {
                try
                {
                    obj_cad_loader.close();
                    var obj_json = JSON.parse(response);
                    
                    if(undefined != obj_json[0].type &&
                       undefined != obj_json[0].message)
                    {
                       var obj_cad_messages = new CAD.Message();
                       obj_cad_messages.init(obj_json);
                       obj_cad_messages.open();
                    }
                    if(obj_json.blog_id)
                    {
                        jQuery('.id').val(obj_json.blog_id);
                    }
                    if(obj_json.id)
                    {
                        jQuery('.id').val(obj_json.id);
                    }
                }
                catch(e)
                {
                    obj_cad_loader.close();
                    alert(response);
                }
            });
	}

	function edit_init(b_lade_spezials)
	{
        var post_request = null;

        jQuery('.miniaturbild-src').unbind('click');
        jQuery('.miniaturbild-src').bind('click', function()
        {
            var time = new Date().getTime();
            jQuery('.vorschaubild').attr('src', Base64.decode(jQuery(this).attr('data-src')) + "?" + time);
            jQuery('.vorschaubild').data('src', jQuery(this).attr('data-src'));
            jQuery('.vorschaubild').data('file', jQuery(this).attr('data-file'));

            if(jQuery('.vorschaubild').has('.cad-cms-dummy-blaeher'))
            {
                jQuery('.vorschaubild').removeClass('cad-cms-dummy-blaeher');
            }
            /**
             * @TODO änderungen werden hier noch von hand ins cms eingefügt! das muss
             * noch angepasst werden und fürs cms verfügbar gemacht werden!
             */

            obj_edited['edited_elements'][jQuery('.vorschaubild').attr("id")] = jQuery('.vorschaubild').attr("data-src");
        });

        jQuery('.miniaturbild-delete').unbind('click');
        jQuery('.miniaturbild-delete').bind('click', function()
        {
            var url = '/' + controller + '/loesche-bild';
            var bild_pfad = jQuery(this).parent().find('.miniaturbild-src').attr('data-src');
            var obj_params = {'bild': bild_pfad, 'ajax': true};

            jQuery.post(url, obj_params, function(response)
            {
                var vorschaubild_url = jQuery('.vorschaubild').attr("src").replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");;
                var bild_url =  Base64.decode(bild_pfad);

                if(vorschaubild_url.match(new RegExp(bild_url)))
                {
                    jQuery('.vorschaubild').attr("src", '');
                    jQuery('.vorschaubild').addClass("cad-cms-dummy-blaeher");
                }
                var obj_cad_messages = new CAD.Message();
                obj_cad_messages.init(response);
                obj_cad_messages.open();

                /*
                if(true == b_lade_spezials)
                {
                    aktualisiereBilder();
                }
                */
                aktualisiereBilder();
            });
        });

        jQuery('.miniaturbild-add').unbind('click');
        jQuery('.miniaturbild-add').bind('click', function()
        {
            jQuery('#cad-cms-bild-upload-form').find('.cad-cms-image-file').click();
        });

/*
        jQuery('.vorschau_bild').parent().draggable();
        jQuery('#projekt_edit_vorschaubild').droppable(
        {
    drop: handleDropEvent
        });
*/

        jQuery('.tags').unbind('keyup');
        jQuery('.tags').bind('keyup', function()
        {
            var suche = Base64.encode(jQuery('.tags').val());
            var url = '/' + controller + '/get-tag-vorschlaege/';

            if(null !== post_request)
            {
                post_request.abort();
            }

            post_request = jQuery.ajax({
                type: "POST",
                url: url,
                data: "suche=" + suche + "&ajax=true",
                success: function(response){
                    jQuery('.tags_vorschlaege').html(response);
                    jQuery('.tags_vorschlaege').find('.vorschlag').unbind('click');
                    jQuery('.tags_vorschlaege').find('.vorschlag').bind('click', function()
                    {
                            jQuery('.tags').html(jQuery(this).data('vorschlag-text'));
                            jQuery('.tags_ids').html(jQuery(this).data('vorschlag-ids'));
                            jQuery('.tags').data('tag-namen', jQuery(this).data('vorschlag-namen'));
                            jQuery('.tags').data('tag-ids', jQuery(this).data('vorschlag-ids'));
                            jQuery('.tags').val(Base64.decode(jQuery(this).data('vorschlag-namen-formatiert')));
                            jQuery('.tags_vorschlaege').fadeOut();
                            return false;
                    });
                    jQuery('.tags_vorschlaege').fadeIn();
                }
            });
        });

        jQuery('#uebung_geraet_name').unbind('keyup');
        jQuery('#uebung_geraet_name').bind('keyup', function()
        {
            var suche = Base64.encode(jQuery('#uebung_geraet_name').val());
            var url = '/geraete/get-geraet-vorschlaege/';

            if(null !== post_request)
            {
                post_request.abort();
            }

            post_request = jQuery.ajax({
                type: "POST",
                url: url,
                data: "suche=" + suche + "&ajax=true",
                success: function(response){
                    jQuery('.muskelgruppen_vorschlaege').html(response);
                    var offset = jQuery('#uebung_geraet_name').offset();
                    jQuery('body').append(jQuery('.muskelgruppen_vorschlaege'));
                    jQuery('.muskelgruppen_vorschlaege').html(response);
                    jQuery('.muskelgruppen_vorschlaege').css('left', offset.left + "px");
                    jQuery('.muskelgruppen_vorschlaege').css('top', offset.top + 20 + "px");
                    
                    jQuery('.muskelgruppen_vorschlaege').find('.vorschlag').unbind('click');
                    jQuery('.muskelgruppen_vorschlaege').find('.vorschlag').bind('click', function()
                    {
                        if(jQuery('#uebung_geraet_name').data('geraet-name') != jQuery(this).data('vorschlag-text'))
                        {
                            aktualisiereGeraetMoeglicheEinstellungen(jQuery(this).data('vorschlag-id'));
                            aktualisiereGeraetMoeglicheSitzpositionen(jQuery(this).data('vorschlag-id'));
                            aktualisiereGeraetMoeglicheGewichte(jQuery(this).data('vorschlag-id'));
                        }
                        
                        jQuery('#uebung_geraet_name').data('geraet-name', jQuery(this).data('vorschlag-text'));
                        jQuery('#uebung_geraet_name').data('geraet-id', jQuery(this).data('vorschlag-id'));
                        jQuery('#uebung_geraet_name').val(Base64.decode(jQuery(this).data('vorschlag-text')));
                        jQuery('.muskelgruppen_vorschlaege').fadeOut();
                        return false;
                    });

//                    jQuery('.muskelgruppen_vorschlaege').CAD_center();
                    jQuery('.muskelgruppen_vorschlaege').fadeIn();
                }
            });
        
            function aktualisiereGeraetMoeglicheEinstellungen(i_geraet_id)
            {
                jQuery('#uebung_geraet_einstellung_name').val('');
                
                var url = '/geraete/optionen-moegliche-einstellungen';
                var obj_params = {'id': i_geraet_id, 'ajax': true};

                jQuery.post(url, obj_params, function(response)
                {
                    jQuery('#uebung_geraet_moegliche_einstellungen_container').html(response);
                });
            }

            function aktualisiereGeraetMoeglicheSitzpositionen(i_geraet_id)
            {
                jQuery('#uebung_geraet_sitzposition_name').val('');
                
                var url = '/geraete/optionen-moegliche-sitzpositionen';
                var obj_params = {'id': i_geraet_id, 'ajax': true};

                jQuery.post(url, obj_params, function(response)
                {
                    jQuery('#uebung_geraet_moegliche_sitzpositionen_container').html(response);
                });
            }

            function aktualisiereGeraetMoeglicheGewichte(i_geraet_id)
            {
                jQuery('#uebung_geraet_gewicht_name').val('');
                
                var url = '/geraete/optionen-moegliche-gewichte';
                var obj_params = {'id': i_geraet_id, 'ajax': true};

                jQuery.post(url, obj_params, function(response)
                {
                    jQuery('#uebung_geraet_moegliche_gewichte_container').html(response);
                });
            }
        });
    }
