var auth_init = false;

function initAuth() {
	jQuery('#login_form').submit(function(e) {
		e.preventDefault();
		
		var userLoginName 	= jQuery('#login_form #user_login_name').val();
		var userLoginPassword = jQuery('#login_form #user_login_password').val();
		
		var url = '/auth/login/';
		var obj_params = {
            'enc_user_login_name': Base64.encode(userLoginName),
            'enc_user_login_password': Base64.encode(userLoginPassword),
            'ajax': true
        };

		jQuery.post(url, obj_params, function(response) {
			//var obj_cad_message = new CAD.Message();
			//obj_cad_message.init(response);
			//
			//if(obj_cad_message.open()) {
				location.reload();
			//} else {
			//	jQuery('#login_form_fail_options #password_forgotten').fadeIn();
			//}
			return false;
		});
	});

	jQuery('#login_form_fail_options #password_forgotten').attr('href', 'Javascript: void(0);').unbind('click').click(function() {
		var url = '/auth/password-forgotten-form/';
		var obj_params = {'ajax': true};
		
		jQuery.post(url, obj_params, function(response ){
			//var obj_cad_message = new CAD.Message();
			//obj_cad_message.init(response);

			//if(obj_cad_message.open()) {

			//}
		});
	});
	
	jQuery('#login_form_fail_options #register').attr('href', 'Javascript: void(0);').unbind('click').click(function() {
		var url = '/auth/register-form/';
		var obj_params = {'ajax': true};
		
		jQuery.post(url, obj_params, function(response){
			//var obj_cad_message = new CAD.Message();
			//obj_cad_message.init(response);
			
			//if(obj_cad_message.open())
			//{
				
			//}
			
		});
	});

    jQuery('#password_forgotten').unbind('click').click(function(event) {
        event.preventDefault();
        jQuery.post('/auth/password-lost', {ajax: true}, function(response) {
            var json = JSON.parse(response);
            json.htmlContent = Base64.decode(json.htmlContent);

            generateModalFromContent(json.htmlContent);
        });
    });

    jQuery('#register').unbind('click').click(function(event) {
        event.preventDefault();
        jQuery.post('/auth/register', {ajax: true}, function(response) {
            var json = JSON.parse(response);
            json.htmlContent = Base64.decode(json.htmlContent);

            generateModalFromContent(json.htmlContent);
        });
    });

	auth_init = true;
}