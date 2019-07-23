<?php 
	require_once 'utils/utils.php';
	require_once 'utils/config.php';
	require_once 'utils/purifyconfig.php';
	//include 'security/CSRFcheck.php';
	include 'resultclasses.php';
	include 'functions/getCallLinkFunctionAgent.php';
	
	$error = false;
	$errormessage = "";
	
	$usernameOK = false;
	$useridOK = false;
	$tokenOK = false;
	
	$resultValue = new GetCallLinkResultClass;
	
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
		$resultValue = getCallLinkFunctionAgent($username, $token);
	}
	else if ($error == false)
	{
		$resultValue->errormessage = "FORMAT: bad format ".$tokenOK." ".$usernameOK." ".$useridOK;
	}
	
	if ( $resultValue->error == true )
	{
		logErrorMessage($resultValue->errormessage);
		$jsonelement = array("Page"=>"");
		$myJSON = json_encode($jsonelement);
		echo $myJSON;
	}
	else 
	{
		logNormalMessage("User got a call link ".$username);
		$jsonelement = array("Page"=>$resultValue->linkaddress);
		$myJSON = json_encode($jsonelement);
		echo $myJSON;
	}
?>