window.modal = null;

function generateModalFromContent(content) {
    var script = undefined;
    var js = extractScriptsFromContent(content);
    content = removeScriptsFromContent(content);

    //                if (js) {
    script = generateScriptFromString(js);
    //                }

    window.modal = jQuery(content);

    window.modal.modal();

    window.modal.on('shown.bs.modal', function () {
        jQuery("head").append(script);
    });

    window.modal.on('hidden.bs.modal', function () {
        jQuery(this).remove();
        //                    if (undefined != script) {
        jQuery(script).remove();
        //                    }
    });
}

function extractScriptsFromContent(content) {

    // get all scripts from given html content
    var regEx = /<script\b[^>]*>([\s\S]*?)<\/script>/gm;
    var match;
    var js = '';
    while (match = regEx.exec(content)) {
        js += match[1];
    }
    return js;
}

function removeScriptsFromContent(content) {
    return content.replace(/<script[^>]*>(?:(?!<\/script>)[^])*<\/script>/g, "");
}

function generateScriptFromString(js) {

    var script = document.createElement("script");
    script.type = "text/javascript";
    script.innerHTML = js;

    return script;
}

function scrollToAnchor(id) {
    //            console.log('ID : ' + id);
    var element = jQuery("#" + id);
    //            console.log(element);
    if (element !== undefined) {
        jQuery('html,body').stop().animate({scrollTop: element.offset().top}, 'slow');
    }
}

/* Function to animate height: auto */
function autoHeightAnimate(element, time) {
    element.animate({height: 'toggle', opacity: 0}, 0, function () {
        element.css("display", "none");
    });
    var curHeight = element.height(), // Get Default Height
        autoHeight = element.css('height', 'auto').height(); // Get Auto Height
    element.height(curHeight); // Reset to Default Height
    element.animate({height: autoHeight, opacity: 1}, time, function () {
        element.css("display", "block");
    }); // Animate to Auto Height
}

function addContent(content) {
    //            console.log('addContent');
    if (isMobile) {
        //                console.log("Add Content for Mobile");
        var container = jQuery(caller).next();
        container.animate({height: 0, opacity: 0}, 0);
        jQuery(caller).addClass('selected ui-accordion-header-active ui-state-active');

        container
            .stop()
            .html(content)
            .height(0)
            .show()
            .animate({height: 1000, opacity: 1}, 1000, function () {
                container.css("display", "block")
                    .css('height', 'auto').prop('height', null)
                    .addClass('ui-accordion-content-active');
                scrollToAnchor(jQuery(caller).prop('id'));
            });
    } else {
        jQuery('#' + targetId).html(content);
        jQuery(caller).addClass('selected');
        scrollToAnchor(targetId);
    }
}

function considerResponse(response) {
    try {
        var json = JSON.parse(response);
        if (200 == json.state) {
            addContent(Base64.decode(json.htmlContent));
        } else {
            var modal = jQuery('#modal');
            considerResponseCodeForModal(json.state);
            jQuery('#modal_save').hide();
            jQuery('.modal-body').html('<p>' + json.message + '</p>');
            modal.modal();
        }
    } catch (exception) {
        alert(exception);
    }
}

function considerResponseCodeForModal(code) {
    var title = 'Notice:';
    switch (code) {
        case 404:
        case 400:
        case 500:
        case 401:
        case 300:
        {
            title = 'Error:';
            break;
        }
    }
    jQuery('.modal-title').html(title);
}

jQuery('#modal').on('hide.bs.modal', function () {
    jQuery('.spinner').fadeOut('slow').remove();
});

function removeContent() {
    //            console.log("Remove Content");
    if (isMobile) {
        //                console.log("Remove Content for Mobile");
        var container = jQuery(tempCaller).next();
        container
            .stop()
            .animate({height: 0, opacity: 0}, 1000, function () {
                container.css("display", "none")
                    .html('')
                    .hide()
                    .removeClass('ui-accordion-content ui-corner-bottom ui-helper-reset ui-widget-content ui-accordion-content-active');
                jQuery(tempCaller).removeClass('selected ui-accordion-header-active ui-state-active');
                scrollToAnchor(jQuery(tempCaller).prop('id'));
            })
        ;
    } else {
        //                console.log('Remove desktop content!');
        jQuery('#' + targetId).html('');
        jQuery(tempCaller).removeClass('selected');
        scrollToAnchor(targetId);
    }
}

function showDialog(message, state) {
    if (jQuery.isArray(message)) {
        var tempMessage = '';
        for (var pos = 0; pos < message.length; pos++) {
            tempMessage += message[pos].message + "\r\n";
        }
        message = tempMessage;
    }
    BootstrapDialog.show({
        message: message,
        type: state,
        buttons: [{
            label: 'Close',
            action: function (dialogItself) {
                dialogItself.close();
            }
        }]
    });
}

    //        BootstrapDialog.TYPE_DEFAULT,
    //        BootstrapDialog.TYPE_INFO,
    //        BootstrapDialog.TYPE_PRIMARY,
    //        BootstrapDialog.TYPE_SUCCESS,
    //        BootstrapDialog.TYPE_WARNING,
    //        BootstrapDialog.TYPE_DANGER
