var controller = '';
var obj_ref = null;
var init_func = null;

jQuery('document').ready(function () {
    var obj_cad_cms = new jQuery('html').CAD_CMS(jQuery('.editable'), controller);

    jQuery.noConflict();

    if (controller == "exercises") {
        //aktualisiereBilder();
        //aktualisiereMuskelgruppen();
    }
    else if (controller == "muscles" ||
        controller == "muscle-groups"
    ) {
        //aktualisiereMuskeln();
    }
    else if (controller == "device-groups") {
        //aktualisiereGeraete();
    } else if (controller == "devices") {
        refreshPictures();
    }
});

function changePreviewPicture(response) {
    if (jQuery('.preview-picture').length > 0 &&
        jQuery('.preview-picture').attr("src").length == 0 ||
        jQuery('.preview-picture').attr("src") == "/images/content/statisch/grafiken/kein_bild.png" ||
        (
        jQuery('.preview-picture').attr("src").length > 0 &&
        jQuery('.preview-picture').attr("src") != "/images/content/statisch/grafiken/kein_bild.png" &&
        confirm("Wollen Sie den bestehenden Inhalt gegen das hochgeladene Bild tauschen?")
        )
    ) {
        var obj_json = JSON.parse(response);

        jQuery().setEditedElements(jQuery('.preview-picture').attr("id"), obj_json.html_pfad);

        if (jQuery('.preview-picture').has('.cad-cms-dummy-blaeher')) {
            jQuery('.preview-picture').removeClass('cad-cms-dummy-blaeher');
        }
        jQuery('.preview-picture').attr("src", obj_json.html_pfad);
        jQuery('.preview-picture').data("src", Base64.encode(obj_json.html_pfad));
        jQuery('.preview-picture').data("file", Base64.encode(obj_json.file));
    }

    refreshPictures();
}

function refreshPictures() {
    var url = '/' + controller + '/get-pictures-for-edit';
    var id = jQuery('.id').val();

    var obj_params = {'id': id, 'ajax': true};

    jQuery.post(url, obj_params, function (response) {
        jQuery('.preview-pictures').html(response);
        edit_init();
    });
}

function aktualisiereGeraete() {
    var url = '/' + controller + '/get-devices-fuer-edit';
    var id = jQuery('.id').val();

    var obj_params = {'id': id, 'ajax': true};

    jQuery.post(url, obj_params, function (response) {
        jQuery('.geraetegruppe-geraete').html(response);
        edit_init();
    });
}

function aktualisiereMuskelgruppen() {
    var url = '/' + controller + '/get-muscle-groups-fuer-edit';
    var id = jQuery('.id').val();

    var obj_params = {'id': id, 'ajax': true};

    jQuery.post(url, obj_params, function (response) {
        jQuery('.uebung_muskelgruppen').html(response);
        edit_init();
    });
}

function aktualisiereMuskeln() {
    var url = '/' + controller + '/get-muscles-fuer-edit';
    var id = jQuery('.id').val();

    var obj_params = {'id': id, 'ajax': true};

    jQuery.post(url, obj_params, function (response) {
        jQuery('.muskelgruppe-muscles').html(response);
        edit_init();
    });
}

function handleDropEvent(event, ui) {
    var draggable = ui.draggable;
    console.log(ui);

    alert('The square with ID "' + draggable.attr('id') + '" was dropped onto me!');
}

function initDeviceGroupsEdit() {
    jQuery('.device-group-devices').find('.device-name').each(function () {
        var post_request = null;

        jQuery(this).unbind('keyup');
        jQuery(this).on('keyup', function (e) {
            var url = '/devices/get-device-proposals/';
            var search = Base64.encode(jQuery(this).val());
            var self = jQuery(this);

            jQuery(self).css('background-color', 'red');
            jQuery(self).data('device-name', '');
            jQuery(self).data('device-id', '');

            if (null !== post_request) {
                post_request.abort();
            }

            post_request = jQuery.ajax({
                type: "POST",
                url: url,
                data: "search=" + search + "&ajax=true",
                success: function (response) {
                    var offset = jQuery(self).offset();
                    jQuery('body').append(jQuery('.proposals'));
                    jQuery('.proposals')
                        .html(response)
                        .css('left', offset.left + "px")
                        .css('top', offset.top + 40 + "px")
                        .find('.proposal')
                        .unbind('click')
                        .bind('click', function () {
                            // check ob diese geraetegruppe bereits gesetzt wurde
                            var currentDevices = jQuery('.device-group-devices').find('.device');
                            for (var i = 0, len = currentDevices.length; i < len; i++) {
                                if (jQuery(currentDevices[i]).find('.device-name').data('device-id') == jQuery(this).data('proposal-id')) {
                                    var a_messages = new Array({
                                        'type': 'fehler',
                                        'message': 'Dieses Geraet ist bereits gesetzt!'
                                    });
                                    var obj_cad_message = new CAD.Message();
                                    obj_cad_message.init(a_messages);
                                    obj_cad_message.open();

                                    return false;
                                }
                            }
                            jQuery(self).css('background-color', 'green');
                            jQuery(self).val(Base64.decode(jQuery(this).data('proposal-text')));
                            jQuery(self).data('device-name', Base64.decode(jQuery(this).data('proposal-text')));
                            jQuery(self).data('device-id', jQuery(this).data('proposal-id'));
                            jQuery('.proposals').fadeOut();
                            return false;
                        }).parent().parent().fadeIn('normal', function() {initDeviceGroupsEdit();});

                }
            });
        });
    });

    jQuery('.device-add').unbind('click').bind('click', function () {
        var obj_cad_loader = new CAD.Loader();
        obj_cad_loader.open();

        var url = '/devices/get-devices-for-edit/';
        var obj_params = {'ajax': true};

        jQuery.post(url, obj_params, function (response) {
//                    jQuery(self).replaceWith(response);
            jQuery('.device-group-devices').append(response);
            obj_cad_loader.close(true);
            initDeviceGroupsEdit();
        });
    });

    jQuery('.device-delete').unbind('click').bind('click', function () {
        var a_messages = [];
        obj_ref = jQuery(this).parent();

        if (obj_ref.find('.device-name').val()) {
            obj_cad_messages = new CAD.Message();
            a_messages.push(
                {
                    'type': 'warnung',
                    'message': 'Wollen Sie dieses Geraet wirklich löschen?',
                    'confirm': true,
//                    'confirm_func': 'self.loescheGeraetegruppe()'
                    'confirm_func': 'deleteDevice()'
                });

            obj_cad_messages.init(a_messages);
            obj_cad_messages.open();
        } else {
            deleteDevice();
        }
    });
}

function initDevicesEdit() {
    jQuery('.device-name').each(function () {
        var post_request = null;

        jQuery(this).unbind('keyup').on('keyup', function (e) {
            var url = '/devices/get-device-proposals/';
            var search = Base64.encode(jQuery(this).val());
            var self = jQuery(this);

            jQuery(self).css('background-color', 'red');
            jQuery(self).data('geraet-name', '');
            jQuery(self).data('geraet-id', '');

            if (null !== post_request) {
                post_request.abort();
            }

            post_request = jQuery.ajax({
                type: "POST",
                url: url,
                data: "search=" + search + "&ajax=true",
                success: function (response) {
                    var offset = jQuery(self).offset();
                    jQuery('body').append(jQuery('.proposals'));
                    jQuery('.proposals').html(response)
                        .css('left', offset.left + "px")
                        .css('top', offset.top + 40 + "px")
                        .find('.proposal').unbind('click').bind('click', function () {
                        // check ob diese geraetegruppe bereits gesetzt wurde
                        var vorhandene_geraete = jQuery('.device');
                        for (var i = 0, len = vorhandene_geraete.length; i < len; i++) {
                            if (jQuery(vorhandene_geraete[i]).find('.device-name').data('device-id') ==
                                jQuery(this).data('proposal-id')) {
                                var a_messages = new Array({
                                    'type': 'fehler',
                                    'message': 'Dieser Geraet ist bereits gesetzt!'
                                });
                                var obj_cad_message = new CAD.Message();
                                obj_cad_message.init(a_messages);
                                obj_cad_message.open();

                                return false;
                            }
                        }
                        jQuery(self).css('background-color', 'green');
                        jQuery(self).val(Base64.decode(jQuery(this).data('proposal-text')));
                        jQuery(self).data('device-name', Base64.decode(jQuery(this).data('proposal-text')));
                        jQuery(self).data('device-id', jQuery(this).data('proposal-id'));
                        jQuery('.proposals').fadeOut();
                        return false;
                    });
                    jQuery('.proposals').fadeIn();
                }
            });
        });
    });

    jQuery('.device-add').unbind('click').bind('click', function () {
        var obj_cad_loader = new CAD.Loader();
        obj_cad_loader.open();

        var url = '/devices/get-devices-for-edit/';
        var obj_params = {'ajax': true};

        jQuery.post(url, obj_params, function (response) {
//                    jQuery(self).replaceWith(response);
            jQuery('.device-group-devices').append(response);
            obj_cad_loader.close(true);
            initDevicesEdit();
        });
    });

    jQuery('.device-delete').unbind('click').bind('click', function () {
        var a_messages = [];
        obj_ref = jQuery(this).parent();
        obj_cad_messages = new CAD.Message();
        a_messages.push(
            {
                'type': 'warnung',
                'message': 'Wollen Sie Dieses Geraet wirklich löschen?',
                'confirm': true,
                'confirm_func': 'deleteDevice()'
            });

        obj_cad_messages.init(a_messages);
        obj_cad_messages.open();
    });

    jQuery('.device-group-delete').unbind('click').bind('click', function () {
        var a_messages = [];
        obj_ref = jQuery(this).parent();
        obj_cad_messages = new CAD.Message();
        a_messages.push(
            {
                'type': 'warnung',
                'message': 'Wollen Sie Diese Geraetegruppe wirklich löschen?',
                'confirm': true,
                'confirm_func': 'deleteDeviceGroup()'
            });

        obj_cad_messages.init(a_messages);
        obj_cad_messages.open();
    });
}

jQuery.fn.deleteDeviceGroup = function () {
    jQuery(this).find('.device-group-name').data('device-group-name', '');
    jQuery(this).find('.device-group-name').data('device-group-id', '');
    jQuery(this).fadeOut();
};

function deleteDeviceGroup() {
    jQuery(obj_ref).find('.device-group-name').data('device-group-name', '');
    jQuery(obj_ref).find('.device-group-name').data('device-group-id', '');
    jQuery(obj_ref).fadeOut();
}

function deleteDevice() {
    jQuery(obj_ref).find('.device-name').data('device-name', '');
    jQuery(obj_ref).find('.device-name').data('device-id', '');
    jQuery(obj_ref).fadeOut();
}

function initMuskelgruppenEdit() {

    jQuery('.muscle-group').find('.muscle-group-name').each(function () {
        var post_request = null;

        jQuery(this).unbind('keyup').on('keyup', function (e) {
            var url = '/muscle-groups/get-muscle-group-proposals/';
            var search = Base64.encode(jQuery(this).val());
            var self = jQuery(this);

            jQuery(self).css('background-color', 'red');
            jQuery(self).data('muscle-group-name', '');
            jQuery(self).data('muscle-group-id', '');

            if (null !== post_request) {
                post_request.abort();
            }

            post_request = jQuery.ajax({
                type: "POST",
                url: url,
                data: "search=" + search + "&ajax=true",
                success: function (response) {
                    var offset = jQuery(self).offset();
                    jQuery('body').append(jQuery('.proposals'));
                    jQuery('.proposals').html(response)
                        .css('left', offset.left + "px")
                        .css('top', offset.top + 20 + "px");

                    jQuery('.proposals').find('.proposal').unbind('click').bind('click', function () {
                        // check ob diese muskelgruppe bereits gesetzt wurde
                        var vorhandene_muskelgruppen = jQuery('.exercise-muscle-groups').find('.muscle-group');

                        for (var i = 0, len = vorhandene_muskelgruppen.length; i < len; i++) {
                            if (jQuery(vorhandene_muskelgruppen[i]).find('.muscle-group-name').data('muscle-group-id') ==
                                jQuery(this).data('proposal-id')
                            ) {
                                var a_messages = new Array({
                                    'type': 'fehler',
                                    'message': 'Diese Muskelgruppe ist bereits gesetzt!'
                                });
                                var obj_cad_message = new CAD.Message();
                                obj_cad_message.init(a_messages);
                                obj_cad_message.open();

                                return false;
                            }
                        }

                        addMuskelGruppe(jQuery(this).data('proposal-id'));
                        jQuery(self).parent().remove();
//                                jQuery(self).css('background-color', 'green');
//                                jQuery(self).val(Base64.decode(jQuery(this).data('vorschlag-text')));
//                                jQuery(self).data('muskelgruppe-name', Base64.decode(jQuery(this).data('vorschlag-text')));
//                                jQuery(self).data('muskelgruppe-id', jQuery(this).data('vorschlag-id'));
                        jQuery('.proposals').fadeOut();
                        return false;
                    });
                    jQuery('.proposals').fadeIn();
                }
            });
        });
    });

    function addMuskelGruppe(iMuskelGruppeId) {
        var obj_cad_loader = new CAD.Loader();
        obj_cad_loader.open();

        var url = '/muscle-groups/get-muscle-group-for-edit/';
        var obj_params = {'ajax': true, 'id': iMuskelGruppeId};

        jQuery.post(url, obj_params, function (response) {
            jQuery('.exercise-muscle-groups').append(response);
            obj_cad_loader.close(true);
            initMuskelgruppenEdit();
        });
    }

    jQuery('.muscle-group-add').unbind('click').bind('click', function () {
        var obj_cad_loader = new CAD.Loader();
        obj_cad_loader.open();

        var url = '/muscle-groups/get-muscle-group-for-edit/';
        var obj_params = {'ajax': true};

        jQuery.post(url, obj_params, function (response) {
            //jQuery(self).replaceWith(response);
            jQuery('.exercise-muscle-groups').append(response);
            obj_cad_loader.close(true);
            initMuskelgruppenEdit();
        });
    });

    //jQuery('.muskelgruppe-delete').unbind('click');
    //jQuery('.muskelgruppe-delete').bind('click', function()
    //{
    //    var a_messages = new Array();
    //    obj_ref = jQuery(this).parent();
    //    obj_cad_messages = new CAD.Message();
    //    a_messages.push(
    //    {
    //        'type': 'warnung',
    //        'message': 'Wollen Sie Diese Muskelgruppe wirklich löschen?',
    //        'confirm': true,
    //            'confirm_func': 'self.loescheMuskelgruppe()'
    //'confirm_func': 'loescheMuskelgruppe()'
    //});
    //
    //obj_cad_messages.init(a_messages);
    //obj_cad_messages.open();
    //});
}

function initMusclesEdit() {
    //jQuery('.muscles-in-muscle-group').find('.muskel-beanspruchung').unbind('mouseover');
    //jQuery('.muscles-in-muscle-group').find('.muskel-beanspruchung').unbind('mouseout');
    //jQuery('.muskelgruppe-muscles').find('.muskel-beanspruchung').unbind('click');

    //jQuery('.muscles-in-muscle-group').find('.muskel-beanspruchung').hover(
    //    function() {
    //        var x = null;
    //        jQuery(this).unbind('mousemove');
    //        jQuery(this).mousemove(function(e)
    //        {
    //            var parentOffset = jQuery(this).parent().offset();
    //            x = -100 + ( parseFloat(e.pageX) - parentOffset.left);
    //            x = Math.round(x / 20) * 20;
    //            jQuery(this).css('background-position', x + "px " + x + "px");
    //        });
    //
    //        jQuery(this).unbind('click');
    //        jQuery(this).click(function()
    //        {
    //            jQuery(this).data('background-position', x + "px " + x + "px");
    //            jQuery(this).data('muskel-beanspruchung', Math.round(5 + (x / 20)));
    //            jQuery(this).css('background-position',  jQuery(this).data('background-position'));
    //            jQuery(this).attr('title', Math.round(5 + (x / 20)));
    //        });
    //    },
    //    function() {
    //        if(undefined !== jQuery(this).data('background-position'))
    //        {
    //            jQuery(this).css('background-position', jQuery(this).data('background-position'));
    //        }
    //        else
    //        {
    //            jQuery(this).css('background-position', '-100px 0');
    //        }
    //    }
    //);

    jQuery('.muscles-in-muscle-group').find('.muscle-name').each(function () {
        var post_request = null;

        jQuery(this).unbind('keyup');
        jQuery(this).on('keyup', function (e) {
            var url = '/muscles/get-muscle-proposals/';
            var search = Base64.encode(jQuery(this).val());
            var self = jQuery(this);

            jQuery(self).css('background-color', 'red');
            jQuery(self).data('muscle-name', '');
            jQuery(self).data('muscle-id', '');

            if (null !== post_request) {
                post_request.abort();
            }

            if (0 == search.length) {
                jQuery('.muscle-proposals').fadeOut();
                return false;
            }

            post_request = jQuery.ajax({
                type: "POST",
                url: url,
                data: "search=" + search + "&ajax=true",
                success: function (response) {
                    var offset = jQuery(self).offset();
                    jQuery('body').append(jQuery('.proposals'));
                    jQuery('.proposals').html(response)
                        .css('left', offset.left + "px")
                        .css('top', offset.top + 40 + "px")
                        .find('.proposal')
                        .unbind('click')
                        .click(function () {

                            // check ob diese muskelgruppe bereits gesetzt wurde
                            var vorhandene_muskeln = jQuery('.muscles-in-muscle-group').find('.muscle');
                            for (var i = 0, len = vorhandene_muskeln.length; i < len; i++) {
                                if (jQuery(vorhandene_muskeln[i]).find('.muscle-name').data('muscle-id') == jQuery(this).data('proposal-id')) {
                                    var a_messages = new Array({
                                        'type': 'fehler',
                                        'message': 'Dieser Muskel ist bereits gesetzt!'
                                    });
                                    var obj_cad_message = new CAD.Message();
                                    obj_cad_message.init(a_messages);
                                    obj_cad_message.open();

                                    return false;
                                }
                            }
                            jQuery(self).css('background-color', 'green');
                            jQuery(self).val(Base64.decode(jQuery(this).data('proposal-text')));
                            jQuery(self).data('muscle-name', Base64.decode(jQuery(this).data('proposal-text')));
                            jQuery(self).data('muscle-id', jQuery(this).data('proposal-id'));
                            jQuery('.proposals').fadeOut();
                            return false;
                        });
                    jQuery('.proposals').fadeIn();
                }
            });
        });
    });

    jQuery('.muscle-add').unbind('click').bind('click', function () {
        var obj_cad_loader = new CAD.Loader();
        obj_cad_loader.open();

        var url = '/muscles/get-muscle-for-edit/';
        var obj_params = {'ajax': true};

        jQuery.post(url, obj_params, function (response) {
//                    jQuery(self).replaceWith(response);
            jQuery('.muscles-in-muscle-group').append(response);
            obj_cad_loader.close(true);
            initMusclesEdit();
        });
    });

    jQuery('.muscle-delete').unbind('click').bind('click', function () {
        var a_messages = [];
        obj_ref = jQuery(this).parent();

        if (0 < obj_ref.find('.muscle-name').val().length) {
            obj_cad_messages = new CAD.Message();
            a_messages.push({
                'type': 'warnung',
                'message': 'Wollen Sie Dieser Muskel wirklich löschen?',
                'confirm': true,
                //                    'confirm_func': 'self.loescheMuskelgruppe()'
                'confirm_func': 'deleteMuscle()'
            });

            obj_cad_messages.init(a_messages);
            obj_cad_messages.open();
        } else {
            deleteMuscle();
        }
    });
}

//jQuery.fn.deleteMuscleGroup = function()
//{
//    jQuery(this).find('.muscle-group-name').data('muscle-group-name', '');
//    jQuery(this).find('.muscle-group-name').data('muscle-id', '');
//    jQuery(this).fadeOut();
//};

//function deleteMuscleGroup()
//{
//    jQuery(obj_ref).find('.muscle-group-name').data('muscle-group-name', '');
//    jQuery(obj_ref).find('.muscle-group-name').data('muscle-group-id', '');
//    jQuery(obj_ref).fadeOut();
//}

function deleteMuscle() {
    jQuery(obj_ref).find('.muscle-name').data('muscle-name', '');
    jQuery(obj_ref).find('.muscle-name').data('muscle-id', '');
    jQuery(obj_ref).fadeOut();
}

function CADEditSave() {
    var obj_cad_loader = new CAD.Loader();
    obj_cad_loader.open();

    var edited_elements = jQuery().getEditedElements();

    var url = '/' + controller + '/save';

    jQuery.post(url, edited_elements, function (response) {
        try {
            obj_cad_loader.close();
            var obj_json = JSON.parse(response);

            if (undefined != obj_json[0].type
                && undefined != obj_json[0].message
            ) {
                var obj_cad_messages = new CAD.Message();
                obj_cad_messages.init(obj_json);
                obj_cad_messages.open();
            }
            if (obj_json.id) {
                jQuery('.id').val(obj_json.id);
            }
        } catch (e) {
            obj_cad_loader.close();
            alert(response);
        }
    });
}

function edit_init(b_lade_spezials) {
    var post_request = null;

    jQuery('.thumb-src').unbind('click').bind('click', function () {
        var time = new Date().getTime();
        jQuery('.preview-picture').attr('src', Base64.decode(jQuery(this).attr('data-src')) + "?" + time)
            .data('src', jQuery(this).attr('data-src'))
            .data('file', jQuery(this).attr('data-file'));

        if (jQuery('.preview-picture').has('.cad-cms-dummy-blaeher')) {
            jQuery('.preview-picture').removeClass('cad-cms-dummy-blaeher');
        }
        /**
         * @TODO änderungen werden hier noch von hand ins cms eingefügt! das muss
         * noch angepasst werden und fürs cms verfügbar gemacht werden!
         */

        obj_edited['edited_elements'][jQuery('.preview-picture').attr("id")] = jQuery('.preview-picture').attr("data-src");
    });

    jQuery('.thumb-delete').unbind('click').bind('click', function () {
        var url = '/' + controller + '/delete-picture';
        var bild_pfad = jQuery(this).parent().find('.thumb-src').attr('data-src');
        var obj_params = {'bild': bild_pfad, 'ajax': true};

        jQuery.post(url, obj_params, function (response) {
            var vorschaubild_url = jQuery('.preview-picture').attr("src").replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
            var bild_url = Base64.decode(bild_pfad);

            if (vorschaubild_url.match(new RegExp(bild_url))) {
                jQuery('.preview-picture').attr("src", '')
                    .addClass("cad-cms-dummy-blaeher");
            }
            var obj_cad_messages = new CAD.Message();
            obj_cad_messages.init(response);
            obj_cad_messages.open();

            refreshPictures();
        });
    });

    jQuery('.thumb-add').unbind('click').bind('click', function () {
        jQuery('#cad-cms-bild-upload-form').find('.cad-cms-image-file').click();
    });

    /*
     jQuery('.vorschau_bild').parent().draggable();
     jQuery('#projekt_edit_vorschaubild').droppable(
     {
     drop: handleDropEvent
     });
     */

    jQuery('.tags').unbind('keyup').bind('keyup', function () {
        var search = Base64.encode(jQuery('.tags').val());
        var url = '/' + controller + '/get-tag-proposals/';

        if (null !== post_request) {
            post_request.abort();
        }

        post_request = jQuery.ajax({
            type: "POST",
            url: url,
            data: "search=" + search + "&ajax=true",
            success: function (response) {
                jQuery('.tag-proposals').html(response)
                    .find('.proposal').unbind('click')
                    .bind('click', function () {
                        jQuery('.tags').html(jQuery(this).data('proposal-text'));
                        jQuery('.tags_ids').html(jQuery(this).data('proposal-ids'));
                        jQuery('.tags').data('tag-names', jQuery(this).data('proposal-names'));
                        jQuery('.tags').data('tag-ids', jQuery(this).data('proposal-ids'));
                        jQuery('.tags').val(Base64.decode(jQuery(this).data('proposal-names-formatted')));
                        jQuery('.tag-proposals').fadeOut();
                        return false;
                    });
                jQuery('.tag-proposals').fadeIn();
            }
        });
    });

    jQuery('#exercise_device_name').unbind('keyup').bind('keyup', function () {
        var search = Base64.encode(jQuery('#exercise_device_name').val());
        var url = '/devices/get-device-proposals/';

        if (null !== post_request) {
            post_request.abort();
        }

        post_request = jQuery.ajax({
            type: "POST",
            url: url,
            data: "search=" + search + "&ajax=true",
            success: function (response) {
                jQuery('.muscle-group-proposals').html(response);
                var offset = jQuery('#exercise_device_name').offset();
                jQuery('body').append(jQuery('.muscle-group-proposals'));
                jQuery('.muscle-group-proposals').html(response);
                jQuery('.muscle-group-proposals').css('left', offset.left + "px");
                jQuery('.muscle-group-proposals').css('top', offset.top + 20 + "px");

                jQuery('.muscle-group-proposals').find('.proposal').unbind('click').bind('click', function () {
                    if (jQuery('#exercise_device_name').data('device-name') != jQuery(this).data('proposal-text')) {
                        aktualisiereGeraetMoeglicheEinstellungen(jQuery(this).data('proposal-id'));
                        aktualisiereGeraetMoeglicheSitzpositionen(jQuery(this).data('proposal-id'));
                        aktualisiereGeraetMoeglicheRueckenpolster(jQuery(this).data('proposal-id'));
                        aktualisiereGeraetMoeglicheBeinpolster(jQuery(this).data('proposal-id'));
                        aktualisiereGeraetMoeglicheGewichte(jQuery(this).data('proposal-id'));
                    }

                    jQuery('#exercise_device_name').data('device-name', jQuery(this).data('proposal-text'));
                    jQuery('#exercise_device_name').data('device-id', jQuery(this).data('proposal-id'));
                    jQuery('#exercise_device_name').val(Base64.decode(jQuery(this).data('proposal-text')));
                    jQuery('.muscle-group-proposals').fadeOut();
                    return false;
                });

//                    jQuery('.muskelgruppen_vorschlaege').CAD_center();
                jQuery('.muscle-group-proposals').fadeIn();
            }
        });
    });

    jQuery('.muscle-group-add').unbind('click').bind('click', function () {
        var obj_cad_loader = new CAD.Loader();
        obj_cad_loader.open();

        var url = '/muscle-groups/get-muscle-group-for-edit/';
        var obj_params = {'ajax': true};

        jQuery.post(url, obj_params, function (response) {
//                    jQuery(self).replaceWith(response);
            jQuery('.exercise-muscle-groups').append(response);
            obj_cad_loader.close(true);
            initMuskelgruppenEdit();
        });
    });

    jQuery('.muscle-group-delete').unbind('click').bind('click', function () {
        var a_messages = [];
        obj_ref = jQuery(this).parent();
        obj_cad_messages = new CAD.Message();
        a_messages.push(
            {
                'type': 'warnung',
                'message': 'Wollen Sie Diese Muskelgruppe wirklich löschen?',
                'confirm': true,
//                    'confirm_func': 'self.loescheMuskelgruppe()'
                'confirm_func': 'deleteMuscleGroup()'
            });

        obj_cad_messages.init(a_messages);
        obj_cad_messages.open();
    });

    jQuery('.save').unbind('click').click(function () {
        save();
    })
}
