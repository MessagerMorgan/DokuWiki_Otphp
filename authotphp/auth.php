<?php
/**
 * Example Action Plugin:   Example Component.
 *
 * @author     Morgan Messager <messager.morgan.83@gmail.com>
 */
 
if(!defined('DOKU_INC')) die();
 
date_default_timezone_set('Europe/Paris');
 
class auth_plugin_authotphp extends DokuWiki_Auth_Plugin 
{
	public function __construct() 
	{
        parent::__construct();
        
		
    }
	
	//function for calcul the otp
	public function checkOTP($key)
	{
		$decalage = 0; 
		$maintenant = time() + $decalage;
		require_once dirname(__FILE__).'/otphp/lib/otphp.php';
		$totp = new \OTPHP\TOTP("$key",array('interval'=>30)); // Decrypt Key

		return $totp->at($maintenant); // calcul otp at now and return it
	}
	
	
	//get user data
	 public function getUserData($user)
	{
		//Where is the file
		 $chemin = __DIR__ . '\users.php';
		 $etatSeatchUser = FALSE; // user no found ( base )
		 $fp=fopen($chemin,'r');
		
		
		
		if(is_resource($fp))
		{
			
			$etatSeatchUser = FALSE;
			while( (!feof($fp)) && ($etatSeatchUser == FALSE) )
			{
				
				$ligne=fgets($fp);
				
				if (preg_match("#$user#", $ligne))
				{
					list($user, $keyPass, $uid, $gid, $extra,$end) = explode(":", $ligne); // extract data user from the file
					$etatSeatchUser = TRUE; // User found
				}
			}
			fclose($fp);
		}
		
	  $data['pass'] = $keyPass; // $keyPass is  the key and pin in one data
	  $data['name'] = $uid; 
	  $data['mail'] = $gid;
	  $data['grps'] = explode(",", $extra);
	  
	 
	  return $data;
	}
	
	public function checkPass($user, $pass)	
	{	
		$authOtp=$_REQUEST['otp']; //request value from the third box
		
		$userinfo = $this->getUserData($user); // get user data

		$keyPin = $userinfo['pass'];
		$keyPass = '';
		$pinPass = '';
	
		$keyOtpPass = substr($keyPin, 0, 16);   // get Key
		$pinPass =  substr($keyPin,16); // get Pin

		$authOtpNow = $this->checkOTP($keyOtpPass); // calcul OTP
				
		

		if( ($authOtp == $authOtpNow) && ($pass == $pinPass)) //authenticate
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	
	
	
}