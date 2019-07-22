<?php 
	require_once 'utils/utils.php';
	require_once 'utils/config.php';
	require_once 'utils/purifyconfig.php';
	//include 'security/CSRFcheck.php';
	include 'resultclasses.php';
	include 'functions/requestCallFunction.php';
	
	$error = false;
	$errormessage = "";
	
	$usernameOK = false;
	$tokenOK = false;
	
	//print_r($_POST);
	//print_r($_GET);
	
	$resultValue = new RequestCallResultClass;
	
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
	if (isSet($_POST['token']))
	{
		$token 		= $_POST['token'];
		$tokenOK 	= checkOauthToken($authTokenSize, $token);
	}
	else
	{
		$error = true;
		$errormessage = "login token not set";
	}
	
	if (($usernameOK==1) && ($tokenOK==1) )
	{
		$resultValue = requestCallFunction($username, $token, $deadlineHours);
	}
	else if ($error == false)
	{
		$resultValue->errormessage = "FORMAT: bad format ".$tokenOK." and : ".$passwordOK;
	}
	
	if ( $resultValue->error == true )
	{
		logErrorMessage($resultValue->errormessage);
		$jsonelement = array("Result"=>"User could not start a call");
		$myJSON = json_encode($jsonelement);
		echo $myJSON;
	}
	else 
	{
		logNormalMessage("User requested a call ".$username);
		$jsonelement = array("Result"=>"Call requested", "CallId"=>$resultValue->callid);
		$myJSON = json_encode($jsonelement);
		echo $myJSON;
	}
?>