var isMobile = true;
var caller = null;
var tempIsMobile = null;
var tempCaller = null;
var accordionAjaxRequest = null;

jQuery(document).ready(function() {

    jQuery(window).resize(function() {
        checkIsMobile();
    });

    jQuery(function () {
        jQuery('[data-toggle="tooltip"]').tooltip()
    });

    function initItems() {

        jQuery('#current_training_plan .item').unbind('click').click(function() {
            var that = this;
            jQuery.post('/'+controller+'/get-training-plan', {id: jQuery(this).data('id'), ajax: true}, function(response) {
                var json = JSON.parse(response);

                if (200 == json.state) {
                    jQuery(that).addClass('selected');
                    resetOldTrainingPlanSelect();
                    addTrainingPlanContent(Base64.decode(json.htmlContent));
                } else {
                    var modal = jQuery('#modal');
                    considerResponseCodeForModal(json.state);
                    jQuery('#modal_save').hide();
                    jQuery('.modal-body').html('<p>' + json.message + '</p>');
                    modal.modal();
                }
            });
        });

        jQuery('#accordion_old_training_plans .item').unbind('click').click(function() {

            var id = jQuery(this).data('value');
            var that = this;

            if (0 < id) {
                jQuery.post('/' + controller + '/get-training-plan', {id: id, ajax: true}, function (response) {
                    var json = JSON.parse(response);

                    if (200 == json.state) {
                        jQuery('.item').removeClass('selected');
                        jQuery(that).parent().parent().parent().find('.dropdown-toggle ').html(jQuery(that).html() + '<span class="caret"></span>');
                        addTrainingPlanContent(Base64.decode(json.htmlContent));
                    } else {
                        var modal = jQuery('#modal');
                        considerResponseCodeForModal(json.state);
                        jQuery('#modal_save').hide();
                        jQuery('.modal-body').html('<p>' + json.message + '</p>');
                        modal.modal();
                    }
                });
            }
        });
    }

    checkIsMobile();
    initItems();
    initOptions();
});

function initOptions() {
    jQuery('.detail-options .edit-button').unbind('click').click(function() {
        loadEditContent(jQuery(this).data('id'));
    });
    jQuery('.detail-options .delete-button').unbind('click').click(function() {
        deleteEntry(jQuery(this).data('id'));
    });
}

function resetOldTrainingPlanSelect() {
    jQuery('#accordion_old_training_plans .dropdown .dropdown-toggle').html('<span class="caret"></span>');
}

function addTrainingPlanContent(content) {
    if (isMobile) {
        jQuery('#mobile_content_old_training_plan').html(content)
    } else {
        jQuery('#right').html(content);
    }
    initOptions();
}

function checkIsMobile() {
    isMobile = ('none' !== jQuery('#mobile').css('display'));

    if (tempIsMobile != isMobile) {
        tempIsMobile = isMobile;
        considerMobile();
    }
}

function considerMobile() {

    if (true === isMobile) {
        jQuery('#mobile_content_old_training_plan').html(jQuery('#right').html());
        jQuery('#right').html('');
        tempCaller = this;
    } else {

        if (null !== tempCaller) {
            jQuery('#right').html(jQuery('#mobile_content_old_training_plan').html());
            jQuery('#mobile_content_old_training_plan').html('');
        }
    }
    refreshImageProperties();
}

function refreshImageProperties() {
    jQuery('.tab-pane.active').find('img').each(function() {
        jQuery(this).width(jQuery(this).parent().outerWidth());
    });
}

function requestAction(id, action) {
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
            if (isMobile) {
                showSpinner(jQuery('#mobile_content_old_training_plan'));
            } else {
                showSpinner(jQuery('#right'));
            }
        },
        error: function () {
        },
        success: function (response) {
            var json = JSON.parse(response);

            if (200 == json.state) {
                addTrainingPlanContent(Base64.decode(json.htmlContent));
            } else {
                var modal = jQuery('#modal');
                considerResponseCodeForModal(json.state);
                jQuery('#modal_save').hide();
                jQuery('.modal-body').html('<p>' + json.message + '</p>');
                modal.modal();
            }
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
    if (isMobile) {
        jQuery('#mobile_content_old_training_plan').html(jQuery('<div class="spinner"></div>'));
    } else {
        jQuery('#right').html(jQuery('<div class="spinner"></div>'));
    }
}


