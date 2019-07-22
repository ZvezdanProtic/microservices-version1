<?php 
	require_once 'utils/utils.php';
	require_once 'utils/config.php';
	require_once 'utils/purifyconfig.php';
	//include 'security/CSRFcheck.php';
	include 'functions/updateUserFunction.php';
	
	$error = false;
	$errormessage = "";
	
	$usernameOK = false;
	$passwordOK = false;
	$emailOK = false;
	
	//print_r($_POST);
	//print_r($_GET);
	
	if (isSet($_GET['username']))
	{
		$username 		= $_GET['username'];
		$usernameOK 	= checkUsername($username);
	}
	else
	{
		$error = true;
		$errormessage = "username not set";
	}
	if (isSet($_GET['password']))
	{
		$password 		= $_GET['password'];
		$passwordOK 	= checkPassword($password);
	}
	else
	{
		$error = true;
		$errormessage = "password not set";
	}
	
	if (isSet($_GET['mango']))
	{
		$email 		= $_GET['mango'];
		$emailOK 	= filter_var($email, FILTER_VALIDATE_EMAIL) == $email;
	}
	else
	{
		$error = true;
		$errormessage = "email not set";
	}
	if (($usernameOK==1) && ($passwordOK==1) && ($emailOK==1) )
	{
		$error = updateUserFunction($username, $password, $email);
	}
	else if ($error == false)
	{
		$error = true;
		$errormessage = "FORMAT: bad format ".$usernameOK." and : ".$passwordOK." and : ".$emailOK;
	}
	
	if ($error == true)
	{
		logErrorMessage($errormessage);
		$jsonelement = array("Result"=>"User not updated");
		$myJSON = json_encode($jsonelement);
		echo $myJSON;
	}
	else
	{
		logNormalMessage("Added user ".$username);
		$jsonelement = array("Result"=>"User updated");
		$myJSON = json_encode($jsonelement);
		echo $myJSON;
	}
?>