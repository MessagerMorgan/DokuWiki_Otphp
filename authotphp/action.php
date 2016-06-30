<?php
/*
* @author     Morgan Messager <messager.morgan.83@gmail.com>
*/
// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();


class action_plugin_authotphp extends DokuWiki_Action_Plugin 
{
    public function register(Doku_Event_Handler $controller) 
	{
        $controller->register_hook('HTML_LOGINFORM_OUTPUT', 'BEFORE', $this, 'handle_loginform');
	}


	public function handle_loginform(Doku_Event &$event, $param) 
	{
		global $auth;

		/* Check for activated authotphp plugin */
		if(!is_a($auth, 'auth_plugin_authotphp')) return;

		/* Get a reference to $form */
		$form =& $event->data;

		// add select box
		$element = form_makeTextField('otp', '', 'OTP', '', 'block');
		$pos     = $form->findElementByAttribute('name', 'p');
		$form->insertElement($pos + 1, $element);
	}
	public function getMenuText($language) 
	{
        $menutext = $this->getLang('menu');
        if (!$menutext) {
            $info = $this->getInfo();
            $menutext = $info['name'].' ...';
        }
        return $menutext;
    }
}


