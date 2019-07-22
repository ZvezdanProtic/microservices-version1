<?php 
	require_once 'utils/utils.php';
	require_once 'utils/config.php';
	require_once 'utils/purifyconfig.php';
	//include 'security/CSRFcheck.php';
	include 'resultclasses.php';
	include 'functions/logoffUserFunction.php';
	
	$error = false;
	$errormessage = "";
	
	$usernameOK = false;
	$passwordOK = false;
	
	//print_r($_POST);
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
	
	if (($usernameOK==1) && ($passwordOK==1) )
	{
		$result = logoffUserFunctionByPassword($username, $password);
	}
	else
	{
		$error = true;
		$errormessage = "FORMAT: bad format ".$usernameOK." and : ".$passwordOK;
	}
	
	if ( ($error == true) || ($result->error == true) )
	{
		logErrorMessage($errormessage);
		$jsonelement = array("Result"=>"User not logged out");
		$myJSON = json_encode($jsonelement);
		echo $myJSON;
	}
	else 
	{
		logNormalMessage("Logged out user ".$username);
		$jsonelement = array("Result"=>"User logged out");
		$myJSON = json_encode($jsonelement);
		echo $myJSON;
	}
?>