var isMobile = true;
var caller = null;
var tempIsMobile = null;
var targetId = null;
var tempCaller = null;
var accordionAjaxRequest = null;

jQuery(document).ready(function() {

    jQuery(window).resize(function() {
        checkIsMobile();
    });

    jQuery(function () {
        jQuery('[data-toggle="tooltip"]').tooltip()
    });

    checkIsMobile();
    initItems();
});

function checkIsMobile() {
    isMobile = ('none' !== jQuery('#mobile').css('display'));

    if (tempIsMobile != isMobile) {
        tempIsMobile = isMobile;
        considerMobile();
    }

    if (targetId === null
        && caller !== null
    ) {
        refreshTargetId();
    }
}

function refreshTargetId() {
    if (isMobile
        && caller
    ) {
        targetId = jQuery(caller).next().prop('id');
    } else {
        targetId = 'right';
    }
}

function considerMobile() {
    var accordion = jQuery("#accordion");
    var accordionHeaderClasses = 'ui-accordion-header ui-corner-top ui-accordion-header-collapsed ui-corner-all ui-state-default ui-accordion-icons';
    var accordionContentClasses = 'ui-accordion-content ui-corner-bottom ui-helper-reset ui-widget-content';
    var accordionActiveHeaderClasses = 'ui-accordion-header-active ui-state-active';
    var accordionActiveContainerClasses = 'ui-accordion-content-active';
//                console.log('considerMobile');
    if (true === isMobile) {
//                    console.log('isMobile!');
        accordion.addClass('ui-accordion ui-widget ui-helper-reset');
        jQuery('#accordion .item').addClass(accordionHeaderClasses);
        jQuery('#accordion .item-content').addClass(accordionContentClasses);

        if (null !== caller) {
//                        console.log("Caller exist!");
            var rightPanel = jQuery('#right');
            jQuery(caller).addClass(accordionActiveHeaderClasses);
            jQuery(caller).next()
                .html(rightPanel.html())
                .delay(1000)
                .css('height', 'auto')
                .css('display', 'block')
                .addClass(accordionActiveContainerClasses);
            rightPanel.html('');
            scrollToAnchor(jQuery(caller).prop('id'));
        }
    } else {
//                    console.log("isNotMobile!");
        accordion.removeClass('ui-accordion ui-widget ui-helper-reset');
        jQuery('#accordion .item').removeClass(accordionHeaderClasses).removeClass(accordionActiveHeaderClasses);
        jQuery('#accordion .item-content').removeClass(accordionContentClasses).removeClass(accordionActiveContainerClasses);
        if (null !== caller) {
            jQuery('#right').html(jQuery(caller).next().html());
            jQuery(caller).next().html('').css('display', 'none').hide().fadeOut();
            scrollToAnchor(jQuery(caller).prop('id'));
        }
    }
    refreshTargetId();
}

function singleClick(e) {
    var id = jQuery(this).data('id');
    loadShowContent(id);
}

function doubleClick(e) {
    var id = jQuery(this).data('id');
    loadEditContent(id);
}

function deleteEntry(id) {
    if (confirm('Entry ' + jQuery('#' + id).html() + ' realy delete?')) {
        requestAction(id, 'delete');
    }
}

function initItems() {
    var previousCaller = null;
    var touchTime = new Date().getTime();

    jQuery('#accordion .item').unbind('click').click(function(event) {
        event.preventDefault();
        var that = this;

//                    console.log("Status : " + jQuery(this).data('status'));
//                    console.log("Previous Caller:");
//                    console.log(previousCaller);

        if (caller
            && caller != this
        ) {
//                        console.log('remove content because other element clicked!');
            jQuery(caller).data('status', null);
            tempCaller = caller;
            caller = this;
            removeContent();
        }

        //compare first click to this click and see if they occurred within double click threshold
        if (((new Date().getTime()) - touchTime) < 800
            && previousCaller == caller
        ) {
            //double click occurred
            caller = this;
//                        console.log('double click');
            doubleClick.call(this, event);
            touchTime = new Date().getTime();
        } else {
            //not a double click so set as a new first click
//                        console.log('single click');
            previousCaller = this;
            touchTime = new Date().getTime();

            if ('show' == jQuery(this).data('status')
                || 'edit' == jQuery(this).data('status')
            ) {
//                            console.log('remove content!');
                jQuery(this).data('status', null);
                tempCaller = this;
                caller = null;
                removeContent();
            } else {
                caller = this;
                singleClick.call(that, event);
            }
        }
    });
}

function requestAction(id, action) {
    if (isMobile) {
        targetId = jQuery('#' + id).next().prop('id');
    }

    if (accordionAjaxRequest) {
        accordionAjaxRequest.abort();
        jQuery('.item').removeClass('selected ui-accordion-header-active ui-state-active')
            .next().removeClass('ui-accordion-content ui-corner-bottom ui-helper-reset ui-widget-content ui-accordion-content-active');
    }

    accordionAjaxRequest = jQuery.ajax({
        url: '/' + controller + '/' + action,
        data: {
            id: id,
            ajax: true,
            format: 'json'
        },
        beforeSend: function () {
            showSpinner(jQuery('#' + targetId));
        },
        error: function () {
            //                    alert('<p>An error has occurred</p>');
        },
        //                dataType: 'json',
        //                dataType: 'jsonp',
        success: function (response) {
            jQuery(caller).data('status', action);
            considerResponse(response);
        },
        always: function () {
            accordionAjaxRequest = null;
        },
        type: 'POST'
    });
}

function loadShowContent(id) {
    requestAction(id, 'show');
}

function loadEditContent(id) {
    requestAction(id, 'edit')
}

function showSpinner(element) {
    jQuery('#' + targetId).html(jQuery('<div class="spinner"></div>'));
}
