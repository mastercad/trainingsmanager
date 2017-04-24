(function(jQuery)
{ 
    jQuery.fn.CAD_CMS = function(objects, str_controller)
    {
        editable_objects = objects;
        editor_template = null;
        obj_edited = {};
        obj_edited['edited_elements'] = {};
        array_elemente = undefined;
        tagArray = [];
        controller = str_controller;
        obj_cad_message = null;

        jQuery.fn.convertEditable = function()
        {
            if(editable_objects.length > 0)
            {
                for(var i = 0; i < editable_objects.length; ++i)
                {
                    if("P" == editable_objects[i].nodeName ||
                       "H1" == editable_objects[i].nodeName ||
                       "H2" == editable_objects[i].nodeName ||
                       "H3" == editable_objects[i].nodeName ||
                       "H4" == editable_objects[i].nodeName ||
                       "H5" == editable_objects[i].nodeName ||
                       "H6" == editable_objects[i].nodeName ||
                       "DIV" == editable_objects[i].nodeName ||
                       "PRE" == editable_objects[i].nodeName ||
                       "SPAN" == editable_objects[i].nodeName)
                    {
//				console.log("Bearbeite");
//				console.log(editable_objects[i]);
                        this.convertEditableText(editable_objects[i]);
                    }
                    else if("IMG" == editable_objects[i].nodeName)
                    {
                        this.convertEditableImage(editable_objects[i]);
                    }
                    else if("A" == editable_objects[i].nodeName)
                    {
                        this.convertEditableLink(editable_objects[i]);
                    }
                }
            }
        };

        jQuery.fn.convertEditableText = function(editable_object)
        {
            if (!jQuery(editable_object).hasClass('cad-cms-initialized')) {
                jQuery(editable_object).addClass("cad-cms-editable");
                jQuery(editable_object).addClass('cad-cms-initialized');
                /*
                 var position = jQuery(editable_object).offset();

                 var object_x = position.left;
                 var object_y = position.top;
                 */

                var obj_wrapper = jQuery('<div style="position: relative; display: inline-block; min-width: 200px; width: auto;"></div>');
                var obj_clone = jQuery(editable_object).clone();

                /**
                 * wenn object leer, hoverbar machen, sonst wird der mauszeiger
                 * nicht gecatcht
                 */
                if (trim(jQuery(obj_clone).html()).length == 0) {
                    jQuery(obj_clone).addClass('cad-cms-dummy-blaeher');
                    //			jQuery(obj_clone).css("padding", "20px");
                }

                if (jQuery(obj_clone).css("float") != "none") {
                    jQuery(obj_wrapper).addClass("cad-cms-text-float-wrapper");
                    this.searchAndMoveAllFloatingSiblings(obj_clone, obj_wrapper);

                    //              	jQuery('<br class="clear-fix" />').insertAfter(jQuery(obj_wrapper).find(':last'));
                    jQuery(obj_wrapper).append('<br class="clear-fix" />');
                    jQuery(obj_wrapper).css('float', jQuery(obj_clone).css("float"));
                    jQuery(obj_wrapper).css('display', jQuery(obj_clone).css("display"));
                    jQuery(obj_wrapper).css('width', jQuery(obj_clone).css("width"));
                }
                else {
                    if (obj_clone.is("SPAN")) {
                        jQuery(obj_wrapper).css("display", "inline-block");
                    }
                    jQuery(obj_wrapper).addClass("cad-cms-text-dummy-wrapper");
                    jQuery(obj_wrapper).append(obj_clone);
                }
                jQuery(obj_wrapper).append('<img class="cad-cms-edit-image" src="/images/content/statisch/cms/edit.png" />');
                jQuery(editable_object).replaceWith(obj_wrapper);

                jQuery(obj_clone).parent().unbind('hover');
                jQuery(obj_clone).parent().hover(
                    function () {
                        jQuery(obj_clone).addClass('cad-cms-text-hover');
                        jQuery(this).find('.cad-cms-edit-image').stop(true, true).fadeIn();
                    },
                    function () {
                        jQuery(obj_clone).removeClass('cad-cms-text-hover');
                        jQuery(this).find('.cad-cms-edit-image').stop(true, true).fadeOut();
                    }
                );

                jQuery(obj_clone).unbind('click').click(function () {
                    jQuery('#cad-cms-editor').data('cad-cms-target', jQuery(obj_clone));
                    jQuery(obj_clone).openEditor();
                });

                jQuery(obj_clone).parent().find('.cad-cms-edit-image').unbind('click').click(function () {
                    jQuery('#cad-cms-editor').data('cad-cms-target', jQuery(obj_clone));
                    jQuery(obj_clone).openEditor();
                });
            }
        };

        jQuery.fn.convertEditableImage = function(editable_object)
        {
            if (!jQuery(editable_object).hasClass('cad-cms-initialized')) {
                jQuery(editable_object).addClass("cad-cms-editable");
                jQuery(editable_object).addClass('cad-cms-initialized');

                var obj_clone = jQuery(editable_object).clone();
                var obj_image_wrapper = jQuery('<div style="position: relative;"></div>');

                if(!jQuery('.cad-cms-bild-upload-form').length)
                {
                    var image_dummy_form = jQuery('<form id="cad-cms-bild-upload-form" method="post" action="/' + controller + '/upload-picture"  target="upload_target" enctype="multipart/form-data">');
                    jQuery(image_dummy_form).append(jQuery('<input type="file" class="cad-cms-image-file" name="cad-cms-image-file[]" multiple style="position: absolute; top: -500px; left: -500px;" />'));
                    jQuery(image_dummy_form).append(jQuery('<input type="hidden" class="cad-cms-hidden" name="ajax" value="true" />'));

                    jQuery('body').append(image_dummy_form);

                    jQuery('.cad-cms-image-file').unbind('change').bind('change', function()
                    {
                        jQuery(this).parent().submit();
                    });
                }
                /**
                 * wenn kein upload_target bisher existiert
                 */
                if (!jQuery('#upload_target').length) {
                    jQuery('body').append(jQuery('<iframe src="#" id="upload_target" name="upload_target" ></iframe>'));
                }

                /**
                 * wenn object leer, aufblähen sonst wird der mouseover
                 * nicht registriert
                 */
                if(trim(jQuery(obj_clone).attr("src")).length == 0) {
                    jQuery(obj_clone).addClass('cad-cms-dummy-blaeher');
                }

                if(jQuery(obj_clone).css("float") != "none") {
                    jQuery(obj_image_wrapper).addClass("cad-cms-image-float-wrapper");

                    this.searchAndMoveAllFloatingSiblings(obj_clone, obj_image_wrapper);
                    jQuery('<br class="clear-fix" />').insertAfter(jQuery(obj_image_wrapper).find(':last'));
                } else {
                    jQuery(obj_image_wrapper).addClass("cad-cms-image-dummy-wrapper").append(obj_clone);
                }

                jQuery(obj_image_wrapper).css("display", jQuery(obj_clone).css("display"));
                jQuery(obj_image_wrapper).css("float", jQuery(obj_clone).css("float"));
                jQuery(obj_image_wrapper).css("clear", jQuery(obj_clone).css("clear"));
                jQuery(obj_image_wrapper).css("margin-top", jQuery(obj_clone).css("margin-top"));
                jQuery(obj_image_wrapper).css("margin-right", jQuery(obj_clone).css("margin-right"));
                jQuery(obj_image_wrapper).css("margin-bottom", jQuery(obj_clone).css("margin-bottom"));
                jQuery(obj_image_wrapper).css("margin-left", jQuery(obj_clone).css("margin-left"));
                jQuery(obj_image_wrapper).css("padding-top", jQuery(obj_clone).css("padding-top"));
                jQuery(obj_image_wrapper).css("padding-right", jQuery(obj_clone).css("padding-right"));
                jQuery(obj_image_wrapper).css("padding-bottom", jQuery(obj_clone).css("padding-bottom"));
                jQuery(obj_image_wrapper).css("padding-left", jQuery(obj_clone).css("padding-left"));

                if(parseFloat(jQuery(obj_clone).css("width"))) {
                    jQuery(obj_image_wrapper).css("width", jQuery(obj_clone).css("width"));
                }
                if(parseFloat(jQuery(obj_clone).css("height"))) {
                    jQuery(obj_image_wrapper).css("height", jQuery(obj_clone).css("height"));
                }

                if(jQuery(obj_clone).css("width").match(/%/)) {
                    jQuery(obj_clone).css("width", "100%");
                }
                if(jQuery(obj_clone).css("height").match(/%/)) {
                    jQuery(obj_clone).css("height", "100%");
                }

                jQuery(editable_object).replaceWith(obj_image_wrapper);

                jQuery(obj_clone).parent().unbind('hover');
                jQuery(obj_clone).parent().hover(
                    function() {
                        jQuery(this).find('.cad-cms-edit-image').stop(true, true).fadeIn();
                    },
                    function() {
                        jQuery(this).find('.cad-cms-edit-image').stop(true, true).fadeOut();
                    }
                );

                jQuery(obj_clone).unbind('click').click(function() {
                    jQuery('.cad-cms-image-file').click();
                });
            }
        };

        jQuery.fn.convertEditableLink = function(editable_object)
        {
            if (!jQuery(editable_object).hasClass('cad-cms-initialized')) {
                jQuery(editable_object).addClass("cad-cms-editable");
                jQuery(editable_object).addClass('cad-cms-initialized');

                /**
                 * wenn object leer, hoverbar machen, sonst wird der mauszeiger
                 * nicht gecatcht
                 */
                if(trim(jQuery(editable_object).html()).length == 0)
                {
                    jQuery(obj_clone).addClass('cad-cms-dummy-blaeher');
    //                      jQuery(editable_object).css("padding", "20px");
                }

                var class_name_wrapper = "cad-cms-dummy-container";

                if(jQuery(editable_object).css("float") != "none")
                {
                    class_name_wrapper = "cad-cms-float-container";

                    var new_obj = jQuery('<div class="' + class_name_wrapper + '" style="position: relative;"></div>');

                    this.searchAndMoveAllFloatingSiblings(editable_object, new_obj);
                }
                else
                {
                    jQuery(editable_object).wrap('<div class="' + class_name_wrapper + '" style="position: relative;"></div>');
                }
                /* in den wrapper ein edit image packen */
                jQuery(editable_object).parent().append('<img class="cad-cms-edit-image" src="/images/content/statisch/cms/edit.png" style="position: absolute; top: 2px; right: 2px; display: none;" />');

                /* eventuelle hover events sicherheitshalber löschen */
                jQuery(editable_object).parent().unbind('hover');

                /* hover event für den wrapper registrieren */
                jQuery(editable_object).parent().hover(
                    function()
                    {
                        jQuery(editable_object).addClass('cad-cms-text-hover');
                        jQuery(this).find('.cad-cms-edit-image').stop(true, true).fadeIn();
                    },
                    function()
                    {
                        jQuery(editable_object).removeClass('cad-cms-text-hover');
                        jQuery(this).find('.cad-cms-edit-image').stop(true, true).fadeOut();
                    }
                );

                jQuery(editable_object).parent().find('.cad-cms-edit-image').unbind('click').click(function() {
                    jQuery('#cad-cms-editor').data('cad-cms-target', jQuery(editable_object));
                    jQuery(editable_object).openEditor();
                });
                jQuery(editable_object).addClass('cad-cms-initialized');
            }
        };

        jQuery.fn.searchAndMoveAllFloatingSiblings = function(editable_object, obj_wrapper)
        {
            var first_obj = this.searchFirstFloatingElement(jQuery(editable_object));

            array_elemente = [];
            this.addAllFloatingElements(jQuery(first_obj));

            var parent_obj = first_obj.parent();
            for(var i = 0; i < array_elemente.length; ++i)
            {
                jQuery(obj_wrapper).append(array_elemente[i]);
            }
            jQuery(parent_obj).append(obj_wrapper);
        };

        jQuery.fn.addAllFloatingElements = function(obj)
        {
            if(typeof array_elemente == 'undefined')
            {
                array_elemente = [];
            }

            if(jQuery(obj).next().length &&
               jQuery(obj).next().css("float") != "none")
            {
                this.addAllFloatingElements(jQuery(obj).next());
            }

            array_elemente.unshift(obj);

            return obj;
        };

        jQuery.fn.searchFirstFloatingElement = function(obj)
        {
            if(jQuery(obj).prev().length &&
               jQuery(obj).prev().css("float") != "none" &&
               jQuery(obj).prev().css("clear") != "left" &&
               jQuery(obj).prev().css("clear") != "right" &&
               jQuery(obj).prev().css("clear") != "both")
            {
                return this.searchFirstFloatingElement(jQuery(obj).prev());
            }
            return jQuery(obj);
        };

        /**
         * diese funktion lädt das template des editors und belegt 
         * die vorgesehenen wie bis jetzt schließen und später noch speichern
         * etc mit ihrer funktionalität
         * 
         */
        jQuery.fn.loadEditorTemplate = function()
        {
            var url = '/cms/get-editor-template';
            var obj_params = {'ajax': true};
            var self = this;

            if ( !editor_template)
            {
                jQuery.post(url, obj_params, function(response)
                {
                    editor_template = response;
                    self.setEditorTemplate();
                    return self;
                });
            }
            else
            {
                self.setEditorTemplate();
                return self;
            }
        };

        jQuery.fn.setEditorTemplate = function()
        {
            var self = this;
            if(jQuery('#cad-cms-editor').length == 0)
            {
                jQuery('body').append(editor_template);
                jQuery('#cad-cms-editor').draggable();
                jQuery('#cad-cms-editor').data('cad-cms-target', this);

                self.initEditorButtons();

                return self;
            }
        };

        jQuery.fn.initEditorButtons = function()
        {
            obj_cad_message = new CAD.Message(jQuery('#cad-cms-editor'));

            jQuery('.cad-cms-leiste').find('.cad-cms-button').unbind('hover');
            jQuery('.cad-cms-leiste').find('.cad-cms-button').hover(
                function()
                {
                    jQuery(this).addClass('cad-cms-button-hover');
                },
                function()
                {
                    jQuery(this).removeClass('cad-cms-button-hover');
                }
            );

            jQuery('.cad-cms-leiste').find('.cad-cms-button').unbind('mousedown');
            jQuery('.cad-cms-leiste').find('.cad-cms-button').mousedown(function()
            {
                jQuery(this).addClass('cad-cms-button-active');
            }
            ).mouseup(function()
            {
                jQuery(this).removeClass('cad-cms-button-active');
            });

            /**
             * beim schließen werden die eventuell getätigten änderungen
             * im element hinterlegt und der editor ausgeblendet
             */
            jQuery('#cad-cms-editor').find('#cad-cms-button-close').unbind('click').click(function()
            {
                jQuery(this).closeEditor();
            });

            jQuery('#cad-cms-editor').find('#cad-cms-button-save').unbind('click').click(function()
            {
                jQuery(this).saveEditor();
            });

            jQuery('#cad-cms-editor').find('.cad-cms-button-ubb').each(function()
            {
                jQuery(this).data('ubb-tag', jQuery(this).attr('data-ubb-tag'));
            });

            jQuery('#cad-cms-editor').find('.cad-cms-button-ubb').unbind('click').click(function()
            {
                jQuery(this).postUbb(jQuery(this).data('ubb-tag'));
            });

            jQuery('#cad-cms-editor').find('#cad-cms-textfeld').unbind('keypress').bind('keypress', function(e)
            {
                var self = this;
                if ((e.which == 115 && e.ctrlKey) || 
                    (e.which == 19))
                {
                    jQuery(self).saveEditor();
                    return false;
                }
                return true;
            });
        };

        /**
         * @
         */
        jQuery.fn.openEditor = function()
        {
            var self = this;
            self.setEditorTemplate();

            var clean_content = null;
            if(jQuery(self).data("cad-cms-content") != undefined)
            {
                // wenn attribut mit dem original content im template übergeben
                // dieses nutzen, vor allem wichtig bei ubb replace, da sonst
                // der replaced text im editor erscheinen würde
                clean_content = Base64.decode(jQuery(self).data("cad-cms-content"));
            }
            if(jQuery(self).attr("data-cad-cms-content-orig") != undefined)
            {
                // wenn attribut mit dem original content im template übergeben
                // dieses nutzen, vor allem wichtig bei ubb replace, da sonst
                // der replaced text im editor erscheinen würde
                jQuery(self).data("cad-cms-content-orig", jQuery(this).attr("data-cad-cms-content-orig"));
                if(!clean_content)
                {
                        clean_content = Base64.decode(jQuery(self).data("cad-cms-content-orig"));
                }
            }
            if(jQuery(self).data("cad-cms-content-orig") == undefined)
            {
                jQuery(self).data('cad-cms-content-orig', Base64.encode(jQuery(this).html()));
                clean_content = jQuery(self).html();
            }

//          CAD.Sperre.open(true, true, false);
//          CAD_Sperre.open(true, true, false);

            jQuery('#cad-cms-editor').find('#cad-cms-textfeld')
                .val(clean_content)
                .CAD_center();

            if (obj_cad_message) {
                obj_cad_message.open();
            } else {
                jQuery('#cad-cms-editor').fadeIn();
            }

//          jQuery('#cad-cms-editor').stop(true, true).fadeIn(function(){
                jQuery('#cad-cms-editor').find('#cad-cms-textfeld').focus();
//          });

        };

        jQuery.fn.closeEditor = function()
        {
            if (obj_cad_message) {
                obj_cad_message.close();
            } else {
                jQuery('#cad-cms-editor').fadeOut();
            }
        };

        jQuery.fn.saveEditor = function()
        {
                var obj = jQuery('#cad-cms-editor').data('cad-cms-target');
                var content_orig = null;
                var content = null;

                if(jQuery(obj).data('cad-cms-content-orig') != undefined &&
                   jQuery(obj).data('cad-cms-content-orig').length > 0)
                {
                        content_orig = Base64.decode(jQuery(obj).data('cad-cms-content-orig'));
                }
                if(jQuery('#cad-cms-editor').find('#cad-cms-textfeld').val().length > 0)
                {
                        content = jQuery('#cad-cms-editor').find('#cad-cms-textfeld').val();
                }

                if(content != content_orig)
                {
                        var id = jQuery(obj).attr("id");

                        if(id != undefined &&
                           id.length > 0)
                        {
                                if(content.length > 0)
                                {
                                        jQuery(obj).data('cad-cms-content', Base64.encode(content));
                                        obj_edited['edited_elements'][id] = Base64.encode(content);
                                        b_init_syntaxhighlight = false;

                                        if(jQuery(obj).hasClass('code') ||
                                           jQuery(obj).find('.blog-code').length > 0)
                                        {
                                                b_init_syntaxhighlight = true;
                                        }

                                        jQuery(obj).setUbbReplacedContent(content, b_init_syntaxhighlight);

                                        if(jQuery(obj).has('.cad-cms-dummy-blaeher'))
                                        {
                                                jQuery(obj).removeClass('cad-cms-dummy-blaeher');
                                        }
                                }
                        }
                        else
                        {
                                jQuery(obj).html(content);
                                console.log("Editiertes Element enthält keine ID! Breche ab");
                        }
                }
        };

        jQuery.fn.saveAll = function()
        {
                for(var i = 0; i < editable_objects.length; ++i)
                {
                        if(jQuery(editable_objects[i]).data("cad-cms-content-orig") != undefined &&
                           jQuery(editable_objects[i]).data("cad-cms-content") != undefined &&
                           jQuery(editable_objects[i]).data("cad-cms-content-orig") != jQuery(editable_objects[i]).data("cad-cms-content"))
                        {
                                obj_edited['edited_elements'][jQuery(editable_objects[i]).attr("id")] = jQuery(editable_objects[i]).data("cad-cms-content");
                        }
                }
        };

        jQuery.fn.reset = function()
        {
                obj_edited = {};
                obj_edited['edited_elements'] = {};

                return true;
        };

        jQuery.fn.getEditedElements = function()
        {
                return obj_edited;
        };

        jQuery.fn.setEditedElements = function(key, value)
        {
                obj_edited['edited_elements'][key] = value;
        };

        jQuery.fn.setAjax = function(b_ajax)
        {
                obj_edited['ajax'] = b_ajax;
        };

        jQuery.fn.postUbb = function(ubb, value)
        {
                if(ubb != undefined)
                {
                        var arrpos = tagArray.length;

                    switch(ubb.toUpperCase())
                    {
                        case "TAB+":
                        {
                                insert( "TAB+");
                                break;
                        }
                        case "TAB-":
                        {
                                insert( "TAB-");
                                break;
                        }
                        case "URL":
                        {
                            var adresse = prompt("Bitte geben Sie den Url ein !");
                            if( adresse)
                            {
                                adresse = trim( adresse); 
                                if( adresse.search(/^http:\/\/.+/i) == -1)
                                {
                                    adresse = "http://" + adresse;
                                }
                                this.insertUbb( "[URL]" + adresse, "[/URL]");
                            }
                            break;
                        }
                        case "URL=":
                        {
                            var adresse = prompt("Bitte geben Sie den Url ein !");
                            var name = prompt("Bitte geben Sie den Namen fuer den Url ein, der erscheinen soll !");

                            if( adresse && name)
                            {
                                adresse = trim( adresse); 
                                if( adresse.search(/^http:\/\/.+/i) == -1)
                                {
                                    adresse = "http://" + adresse;
                                }
                                this.insertUbb( "[URL=" + name + "]" + adresse, "[/URL]");
                            }
                            break;
                        }
                        case "EMAIL":
                        {
                            var adresse = prompt("Bitte geben Sie die Email Adresse ein !");
                            if( adresse)
                            {
                                this.insertUbb( "[EMAIL]" + adresse, "[/EMAIL]");
                            }
                            break;
                        }
                        case "EMAIL=":
                        {
                            var adresse = prompt("Bitte geben Sie die Email Adresse ein !");
                            var name = prompt("Bitte geben Sie den Namen der Email Adresse ein, der erscheinen soll !");
                            if( adresse && name)
                            {
                                this.insertUbb( "[EMAIL=" + name + "]" + adresse, "[/EMAIL]");
                            }
                            break;
                        }
                        case "COLOR":
                        {
                            tagArray[arrpos] = "[/COLOR]";
                            this.insertUbb( "[COLOR=" + value + "]", "[/COLOR]");
                            break;
                        }
                        case "SIZE":
                        {
                            tagArray[arrpos] = "[/SIZE]";
                            this.insertUbb( "[SIZE=" + value + "]", "[/SIZE]");
                            break;
                        }
                        case "IMG":
                        {
                                /*
                            var bild = prompt("Bitte geben Sie den Pfad zum Bild an!");
                            if( bild)
                            {
                                this.insertUbb( "[IMG]" + bild, "[/IMG]");
                            }
                            */
                                this.insertImage();
                            break;
                        }
                        case "UL":
                        {
                            tagArray[arrpos] = "[/ULISTE]";
                            this.insertUbb( "[ULISTE]", "[/ULISTE]");
                            break;
                        }
                        case "OL":
                        {
                            tagArray[arrpos] = "[/OLISTE]";
                            this.insertUbb( "[OLISTE]", "[/OLISTE]");
                            break;
                        }
                        case "LINIE":
                        {
                                this.insertUbb( "[LINIE]");
                            break;
                        }        
                        case "I":
                        {
                            tagArray[arrpos] = "[/I]";
                            this.insertUbb( "[I]", "[/I]");
                            break;
                        }
                        case "B":
                        {
                            tagArray[arrpos] = "[/B]";
                            this.insertUbb( "[B]", "[/B]");
                            break;
                        }
                        case "U":
                        {
                            tagArray[arrpos] = "[/U]";
                            this.insertUbb( "[U]", "[/U]");
                            break;
                        }
                        case "BLOCK":
                        {
                            tagArray[arrpos] = "[/BLOCK]";
                            this.insertUbb( "[BLOCK]", "[/BLOCK]");
                            break;
                        }
                        case "CENTER":
                        {
                            tagArray[arrpos] = "[/CENTER]";
                            this.insertUbb( "[CENTER]", "[/CENTER]");
                            break;
                        }
                        case "RIGHT":
                        {
                            tagArray[arrpos] = "[/RIGHT]";
                            this.insertUbb( "[RIGHT]", "[/RIGHT]");
                            break;
                        }
                        case "LEFT":
                        {
                            tagArray[arrpos] = "[/LEFT]";
                            this.insertUbb( "[LEFT]", "[/LEFT]");
                            break;
                        }
                    }
            }
        };

        jQuery.fn.insertImage = function()
        {
            var self = this;
            var obj_image_dialog = jQuery('<div class="cad-cms-image-dialog" style="width: 400px;"></div>');
            obj_image_dialog.css('position', 'absolute');
            obj_image_dialog.css('padding', '20px');
            obj_image_dialog.css('background-color', '#FFFFFF');

            obj_image_dialog.append(jQuery('<div class="button_close"></div>'));

            obj_image_dialog.append(jQuery('<br class="clear-fix" />'));
            obj_image_dialog.append(jQuery('<label for="cad-dms-image-dialog-bild-pfad" class="cad-cms-label" style="margin-top: 10px;">Geben Sie den Pfad zu einem Bild ein:</label>'));
            obj_image_dialog.append(jQuery('<br class="clear-fix" />'));
            obj_image_dialog.append(jQuery('<input type="text" id="cad-dms-image-dialog-bild-pfad" />'));

            if(jQuery('.miniaturbild-src').length)
            {
                obj_image_dialog.append(jQuery('<br class="clear-fix" />'));
                obj_image_dialog.append(jQuery('<label class="cad-cms-label" style="margin-top: 10px;">oder wählen Sie ein vorhandenes Bild aus:</label>'));
                obj_image_dialog.append(jQuery('<br class="clear-fix" />'));

                jQuery('.miniaturbild-src').each(function()
                {
                    var obj_image = jQuery('<img src="' + jQuery(this).attr("src") + '" class="cad-cms-image-dialog-miniaturbild" style="width: 50px; margin: 5px;" />');

                    obj_image.data('file', jQuery(this).data('file'));
                    obj_image.unbind('click').click(function() {
                        obj_image_dialog.find('.cad-cms-image-dialog-miniaturbild').removeClass("cad-cms-image-dialog-marked");

                        jQuery(this).addClass("cad-cms-image-dialog-marked");
                        obj_image_dialog.find('#cad-cms-image-dialog-file').data('file', Base64.decode(jQuery(this).data('file')));
                        obj_image_dialog.find('#cad-cms-image-dialog-file').val(obj_image_dialog.find('#cad-cms-image-dialog-file').data('file'));
                    });

                    obj_image_dialog.append(obj_image);
                });
            }

            obj_image_dialog.append(jQuery('<br class="clear-fix" />'));
            obj_image_dialog.append(jQuery('<label class="cad-cms-label" style="margin-top: 10px;">Optionale Parameter:</label>'));
            obj_image_dialog.append(jQuery('<br class="clear-fix" />'));
            obj_image_dialog.append(jQuery('<label for="cad-cms-image-dialog-name" class="cad-cms-label" style="margin-top: 10px;">Anzuzueigender Name für das Bild:</label>'));
            obj_image_dialog.append(jQuery('<br class="clear-fix" />'));
            obj_image_dialog.append(jQuery('<input type="text" id="cad-cms-image-dialog-name" />'));
            obj_image_dialog.append(jQuery('<br class="clear-fix" />'));
            obj_image_dialog.append(jQuery('<label for="cad-cms-image-dialog-width" class="cad-cms-label" style="margin-top: 10px;">Anzuzueigende Breite des Bildes:</label>'));
            obj_image_dialog.append(jQuery('<br class="clear-fix" />'));
            obj_image_dialog.append(jQuery('<input type="text" id="cad-cms-image-dialog-width" />'));
            obj_image_dialog.append(jQuery('<br class="clear-fix" />'));
            obj_image_dialog.append(jQuery('<label for="cad-cms-image-dialog-height" class="cad-cms-label" style="margin-top: 10px;">Anzuzueigende Höhe des Bildes:</label>'));
            obj_image_dialog.append(jQuery('<br class="clear-fix" />'));
            obj_image_dialog.append(jQuery('<input type="text" id="cad-cms-image-dialog-height" />'));
            obj_image_dialog.append(jQuery('<br class="clear-fix" />'));
            obj_image_dialog.append(jQuery('<label for="cad-cms-image-dialog-prio-width" class="cad-cms-label" style="margin-top: 10px;">Prio Width?:</label>'));
            obj_image_dialog.append(jQuery('<br class="clear-fix" />'));
            obj_image_dialog.append(jQuery('<input type="checkbox" id="cad-cms-image-dialog-prio-width" value="1" />'));
            obj_image_dialog.append(jQuery('<br class="clear-fix" />'));
            obj_image_dialog.append(jQuery('<label for="cad-cms-image-dialog-prio-height" class="cad-cms-label" style="margin-top: 10px;">Prio Height?:</label>'));
            obj_image_dialog.append(jQuery('<br class="clear-fix" />'));
            obj_image_dialog.append(jQuery('<input type="checkbox" id="cad-cms-image-dialog-prio-height" value="1" />'));

            obj_image_dialog.append(jQuery('<input type="hidden" id="cad-cms-image-dialog-file" value=""/>'));
//          obj_image_dialog.append(jQuery('<input type="hidden" id="cad-cms-image-dialog-name" value="" />'));

            obj_image_dialog.append('<br class="clear-fix" />');
            obj_image_dialog.append(jQuery('<button id="cad-cms-image-dialog-image-einfuegen" class="button">Einfügen</button>'));
            obj_image_dialog.find('#cad-cms-image-dialog-image-einfuegen').unbind('click').click(function() {
                var name = jQuery(obj_image_dialog).find('#cad-cms-image-dialog-name').val();
                var file = jQuery(obj_image_dialog).find('#cad-cms-image-dialog-file').val();
                var width = jQuery(obj_image_dialog).find('#cad-cms-image-dialog-width').val();
                var height = jQuery(obj_image_dialog).find('#cad-cms-image-dialog-height').val();
                var prio_width = jQuery(obj_image_dialog).find('#cad-cms-image-dialog-prio-width').is(':checked');
                var prio_height = jQuery(obj_image_dialog).find('#cad-cms-image-dialog-prio-height').is(':checked');

                var a_params = [];
                var params = '';

                console.log(jQuery(obj_image_dialog).find('#cad-cms-image-dialog-name'));
                console.log(jQuery(obj_image_dialog).find('#cad-cms-image-dialog-file'));

                if(name.length > 0)
                {
                    a_params['name='] = name;
                }
                if(width.length > 0)
                {
                    a_params['width='] = width;
                }
                if(height.length > 0)
                {
                    a_params['height='] = height;
                }
                if(prio_width)
                {
                    a_params['prio_width='] = prio_width;
                }
                if(prio_height)
                {
                    a_params['prio_height='] = prio_height;
                }

                if(a_params.size() > 0)
                {
                    params = ":" + a_params.CAD_join(':');
                }
                if(file.length > 0)
                {
                    jQuery(self).insertUbb("[IMG" + params + "]" + file, "[/IMG]");
                }
            });

            jQuery('body').append(obj_image_dialog);
            obj_image_dialog.CAD_center();

            var obj_message = new CAD.Message(obj_image_dialog);
            jQuery(obj_image_dialog).find('.button_close').unbind('click').click(function()
            {
                    obj_message.close(true);
            });
            obj_message.open();
        };

        jQuery.fn.insertUbb = function(openTag, closeTag)
        {
            var textArea = jQuery('#cad-cms-editor').find('#cad-cms-textfeld');
            textArea.focus();

            var len = textArea.val().length;
            var start = textArea[0].selectionStart;
            var end = textArea[0].selectionEnd;
            var selectedText = textArea.val().substring(start, end);
            var replacement = openTag + selectedText + closeTag;
            textArea.val(textArea.val().substring(0, start) + replacement + textArea.val().substring(end, len));
        };

        jQuery.fn.setUbbReplacedContent = function(content, b_init_syntaxhighlight)
        {
            var url = '/cms/get-ubb-replaced-content/';
            var language = '';

            // wenn von hand eine sprache eingegeben
            if(jQuery('#schnipsel_sprache').length > 0 &&
               undefined != jQuery('#schnipsel_sprache').data('cad-cms-content') &&
               jQuery('#schnipsel_sprache').data('cad-cms-content').length > 0)
            {
                language = jQuery('#schnipsel_sprache').data('cad-cms-content');
            }
            // oder eine selektiert
            else if(jQuery('#schnipsel_sprache_fk').length > 0 &&
                    jQuery('#schnipsel_sprache_fk').val() > 0)
            {
                language = jQuery('#schnipsel_sprache_fk :selected').text();
            }
            // oder keine selektiert und eine beim laden übergeben wurde
            else if(jQuery('#schnipsel_sprache').length > 0 &&
                    undefined != jQuery('#schnipsel_sprache').data('cad-cms-content-orig') &&
                    jQuery('#schnipsel_sprache').data('cad-cms-content-orig').length > 0)
            {
                language = jQuery('#schnipsel_sprache').data('cad-cms-content-orig');
            }

            var bild_pfad = jQuery('#bild_pfad').val();
            var bild_temp_pfad = jQuery('#bild_temp_pfad').val();

            if(language != undefined &&
               language.length > 0)
            {
                language = language.toLowerCase();
            }

            var obj_params = 
            {
                'content': Base64.encode(content),
                'language': Base64.encode(language),
                'bild_pfad': bild_pfad,
                'bild_temp_pfad': bild_temp_pfad,
                'ajax': true
            };
            var self = jQuery(this);

            if(content.length)
            {
                jQuery.post(url, obj_params, function(response)
                {
                    // prettyprint highlight linenums theme-balupton prettyprint-has
                    try
                    {
                        obj_content = JSON.parse(response);
                        response = obj_content.str_replaced_content;

                        if(b_init_syntaxhighlight)
                        {
                            beispiel_content = Base64.decode(obj_content.str_beispiel_content);
                        }

                        self.html(Base64.decode(response) + '<br class="clear-fix" />');

                        if(true === b_init_syntaxhighlight)
                        {
                            if(jQuery('.beispiel-code').length > 0)
                            {
                                jQuery('.beispiel-code').html(beispiel_content);
                                /* alte syntax highlight klassen entfernen zum reinitialisieren */
                                jQuery('.code').removeClass(function (index, klasse)
                                {
                                    return (klasse.match (/\language-\S+/g) || []).join(' ');
                                });
                                jQuery('.code').removeClass(function (index, klasse)
                                {
                                    return (klasse.match (/\lang-\S+/g) || []).join(' ');
                                });
                                jQuery('.code').removeClass(function (index, klasse)
                                {
                                    return (klasse.match (/highlight/g) || []).join(' ');
                                });
                                jQuery('.code').removeClass(function (index, klasse)
                                {
                                    return (klasse.match (/linenums/g) || []).join(' ');
                                });
                                jQuery('.code').removeClass(function (index, klasse)
                                {
                                    return (klasse.match (/prettyprint/g) || []).join(' ');
                                });
                                jQuery('.code').removeClass(function (index, klasse)
                                {
                                    return (klasse.match (/theme\-balupton/g) || []).join(' ');
                                });
                                jQuery('.code').removeClass(function (index, klasse)
                                {
                                    return (klasse.match (/prettyprint\-has/g) || []).join(' ');
                                });

                                jQuery('.code').addClass("language-" + language);
                            }
                            jQuery.SyntaxHighlighter.init();
                        }
                    }
                    catch(e)
                    {
                        var a_params_message = [];
                        a_params_message[0] = [];
                        a_params_message[0]['type'] = 'fehler';
                        a_params_message[0]['message'] = 'Beim Holen des Beispielcodes ist ein fehler aufgetreten:<br /><br />' + e + '<br /><br />' + response;

                        var obj_message = new CAD.Message();
                        obj_message.init(a_params_message);
                        obj_message.open();
                    }
                });
            }
        };

        jQuery.fn.setController = function(str_controller)
        {
            controller = str_controller;
        };

        this.loadEditorTemplate();
        this.convertEditable();
    };
})(jQuery);