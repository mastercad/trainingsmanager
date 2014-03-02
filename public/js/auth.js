var auth_init = false;

function init_auth()
{
	jQuery('#login_form').submit(function(e)
	{
		e.preventDefault();
		
		var user_login_name 	= jQuery('#login_form #user_login_name').val();
		var user_login_passwort = jQuery('#login_form #user_login_passwort').val();
		
		var url = '/auth/login/';
		var obj_params = {	'enc_user_login_name': Base64.encode(user_login_name),
							'enc_user_login_passwort': Base64.encode(user_login_passwort),
							'ajax': true
						};

		jQuery.post(url, obj_params, function(response)
		{
			var obj_cad_message = new CAD.Message();
			obj_cad_message.init(response);
			
			if(obj_cad_message.open())
			{
				location.href = location.href;
			}
			else
			{
//				jQuery('#login-form-fail-options').fadeIn();
				jQuery('#login-form-fail-options #passwort-vergessen').fadeIn();
			}
			return false;
		});
	});

	jQuery('#login-form-fail-options #passwort-vergessen').attr('href', 'Javascript: void(0);');
	jQuery('#login-form-fail-options #passwort-vergessen').unbind('click');
	jQuery('#login-form-fail-options #passwort-vergessen').bind('click', function()
	{
		var url = '/auth/passwort-vergessen-form/';
		var obj_params = {'ajax': true};
		
		jQuery.post(url, obj_params, function(response){
			var obj_cad_message = new CAD.Message();
			obj_cad_message.init(response);
			
			if(obj_cad_message.open())
			{
				
			}
		});
	});
	
	jQuery('#login-form-fail-options #registrieren').attr('href', 'Javascript: void(0);');
	jQuery('#login-form-fail-options #registrieren').unbind('click');
	jQuery('#login-form-fail-options #registrieren').bind('click', function()
	{
		var url = '/auth/register-form/';
		var obj_params = {'ajax': true};
		
		jQuery.post(url, obj_params, function(response){
			var obj_cad_message = new CAD.Message();
			obj_cad_message.init(response);
			
			if(obj_cad_message.open())
			{
				
			}
			
		});
	});
	auth_init = true;
	prepareInputs();
}

init_auth();