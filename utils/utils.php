<?php
	function generateRandomString($length = 10) {
		$randomString = bin2hex(random_bytes($length));
		return $randomString;
	}

	function checkUsername($username)
	{
		return preg_match('/^[a-zA-Z0-9_]{4,25}$/', $username);
	}
	
	function checkPassword($password)
	{
		return preg_match('/^[a-zA-Z0-9_]{4,25}$/', $password);
	}
	
   function validateEmail($email) {
      return filter_var($email, FILTER_VALIDATE_EMAIL);
   }
	
	function checkOauthToken($length, $token)
	{
		return preg_match('/^[a-fA-F0-9]{'.$length.','.$length.'}$/', $token);
	}
	
	/*
	Logging error messages, plus the page where it occured, the adress that called it and the timestamp
	*/
	function logErrorMessage($message)
	{	
		$curentdate  = date("Ymd H:i:s");
		$curentday	= date("Ymd");
		
		$logfile = './logs/errorlog'.$curentday.'.txt';
		
		$adres = "255.255.255.255";
		if (isSet($_SERVER['REMOTE_ADDR']))
			$adres = $_SERVER['REMOTE_ADDR'];
		
		$page = basename($_SERVER['PHP_SELF']);
		
		$errorid = generateRandomString(10);
		
		error_log($curentdate."-".$errorid."-".$page."-".$adres."-".$message."\r", 3, $logfile);
		
		return $curentdate."-".$errorid;
	}
	
		/*
	Logging messages, plus the page where it occured, the adress that called it and the timestamp
	*/
	function logNormalMessage($message)
	{	
		$curentdate  = date("Ymd H:i:s");
		$curentday	= date("Ymd");
		
		$logfile = './logs/normallog'.$curentday.'.txt';
		
		$adres = "255.255.255.255";
		if (isSet($_SERVER['REMOTE_ADDR']))
			$adres = $_SERVER['REMOTE_ADDR'];
		
		$page = basename($_SERVER['PHP_SELF']);
		
		error_log($curentdate."-".$page."-".$adres."-".$message."\r", 3, $logfile);
	}
	
?>