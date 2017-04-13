<?php

require_once(APPLICATION_PATH . '/controllers/AbstractController.php');

class KontaktController extends AbstractController
{
    public function indexAction()
    {
    }
    
    public function parseKontaktanfrageAction()
    {
    	
    }
    
    public function sendenAction()
    {
    	$req = $this->getRequest();
    	$a_params = $req->getParams();
		
    	$name = '';
    	$email = '';
    	$nachricht = '';
    	$a_messages = array();
    	$b_fehler = false;
    	$b_ajax = false;
    	
    	if(isset($a_params['ajax']))
    	{
    		$b_ajax = true;
    	}
    	
    	if(isset($a_params['kontaktformular_name']) &&
    	   strlen(trim($a_params['kontaktformular_name'])))
    	{
    		if($b_ajax)
    		{
    			$name = base64_decode($a_params['kontaktformular_name']);
    		}
    		else
    		{
    			$name = $a_params['kontaktformular_name'];
    		}
    	}
    	else
    	{
    		$messages_count = count($a_messages);
    		$a_messages[$messages_count]['type'] = "fehler";
    		$a_messages[$messages_count]['message'] = "Bitte geben Sie einen Namen an!";
    		$b_fehler = true;
    	}
    	if(isset($a_params['kontaktformular_email']) &&
    	   strlen(trim($a_params['kontaktformular_email'])))
    	{
    		if($b_ajax)
    		{
    			$email = base64_decode($a_params['kontaktformular_email']);
    		}
    		else
    		{
    			$email = $a_params['kontaktformular_email'];	
    		}
    	}
    	else
    	{
    		$messages_count = count($a_messages);
    		$a_messages[$messages_count]['type'] = "fehler";
    		$a_messages[$messages_count]['message'] = "Bitte geben Sie einen E-Mail Adresse an!";
    		$b_fehler = true;
    	}
    	
    	if(isset($a_params['kontaktformular_nachricht']) &&
    	   strlen(trim($a_params['kontaktformular_nachricht'])))
    	{
    		if($b_ajax)
    		{
    			$nachricht = base64_decode($a_params['kontaktformular_nachricht']);
    		}
    		else
    		{
    			$nachricht = $a_params['kontaktformular_nachricht'];
    		}
    	}
    	else
    	{
    		$messages_count = count($a_messages);
    		$a_messages[$messages_count]['type'] = "fehler";
    		$a_messages[$messages_count]['message'] = "Wollen Sie mir gar nichts mitteilen?";
    		$b_fehler = true;
    	}
    	 
    	if(false === $b_fehler)
    	{
	    	$obj_mail = new Zend_Mail("UTF-8");
	    	$obj_mail->addTo("kontakt@byte-artist.de", "Kontaktadresse des byte-arist.de");
	    	$obj_mail->setBodyHtml("VON: " . $name . "(" . $email . ")<br />Nachricht : " . $nachricht);
	    	$obj_mail->setFrom("webservice@byte-artist.de", "Webservice des byte-artist.de");
	    	$obj_mail->setSubject('Kontaktanfrage auf byte-artist.de von ' . $name . '(' . $email . ')');
	    	
    		$result = $obj_mail->send();
    	
	    	if($result)
	    	{
	    		$a_messages[0] = array();
	    		$a_messages[0]['type'] = "meldung";
	    		$a_messages[0]['message'] = "Ihre Kontaktanfrage wurde erfolgreich versendet!";
	    	}
	    	if($b_ajax)
	    	{
    			$this->view->assign('json_string', json_encode($a_messages));
	    	}
	    	else
	    	{
	    		$this->view->assign('a_messages', $a_messages);
	    	}
    	}
    	else
    	{
    		$messages_count = count($a_messages);
    		
    		$a_messages[$messages_count] = array();
    		$a_messages[$messages_count]['type'] = "fehler";
    		$a_messages[$messages_count]['message'] = "Ihre Kontaktanfrage konnte leider nicht gesendet werden!";

    		if($b_ajax)
    		{
    			$this->view->assign('json_string', json_encode($a_messages));
    		}
    		else
    		{
    			$this->view->assign('kontaktformular_name', $name);
    			$this->view->assign('kontaktformular_email', $email);
    			$this->view->assign('kontaktformular_nachricht', $nachricht);

    			$kontaktformular = $this->view->render('kontakt/index.phtml');
    			
    			$this->view->assign('kontaktformular', $kontaktformular);
    			$this->view->assign('a_messages', $a_messages);
    		}
    	}
    	$this->view->assign('b_fehler', $b_fehler);
    }
}

