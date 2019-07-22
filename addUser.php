<?php 
	require_once 'utils/utils.php';
	require_once 'utils/config.php';
	require_once 'utils/purifyconfig.php';
	//include 'security/CSRFcheck.php';
	include 'functions/addUserFunction.php';
	
	$error = false;
	$errormessage = "";
	
	$usernameOK = false;
	$passwordOK = false;
	$emailOK = false;
	$typeOK = false;
	
	print_r($_POST);
	//print_r($_GET);
	
	if (isSet($_POST['username']))
	{
		$username 		= $_POST['username'];
		$usernameOK 	= checkUsername($username);
	}
	else
	{
		$error = true;
		$errormessage = "username not set";
	}
	if (isSet($_POST['password']))
	{
		$password 		= $_POST['password'];
		$passwordOK 	= checkPassword($password);
	}
	else
	{
		$error = true;
		$errormessage = "password not set";
	}
	
	if (isSet($_POST['mango']))
	{
		$email 		= $_POST['mango'];
		$emailOK 	= filter_var($email, FILTER_VALIDATE_EMAIL) == $email;
	}
	else
	{
		$error = true;
		$errormessage = "email not set";
	}
	
	if (isSet($_POST['type']))
	{
		$type 		= $_POST['type'];
		$typeOK 	= (($type=="Client") || ($type=="Agent"));
	}
	else
	{
		$error = true;
		$errormessage = "type not set";
	}
	
	if (($usernameOK==1) && ($passwordOK==1) && ($emailOK==1) && ($typeOK==1) )
	{
		$error = addUserFunction($username, $password, $email, $type);
	}
	else if ($error == false)
	{
		$error = true;
		$errormessage = "FORMAT: bad format ".$usernameOK." and : ".$passwordOK." and : ".$emailOK;
	}
	
	if ($error == true)
	{
		logErrorMessage($errormessage);
		$jsonelement = array("Result"=>"User not created");
		$myJSON = json_encode($jsonelement);
		echo $myJSON;
	}
	else
	{
		logNormalMessage("Added user ".$username);
		$jsonelement = array("Result"=>"User created");
		$myJSON = json_encode($jsonelement);
		echo $myJSON;
	}
?>