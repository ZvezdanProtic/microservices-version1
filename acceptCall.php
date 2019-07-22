<?php 
	require_once 'utils/utils.php';
	require_once 'utils/config.php';
	require_once 'utils/purifyconfig.php';
	//include 'security/CSRFcheck.php';
	include 'resultclasses.php';
	include 'functions/acceptCallFunction.php';
	
	$error = false;
	$errormessage = "";
	
	$usernameOK = false;
	$tokenOK = false;
	
	$resultValue = new AcceptCallResultClass;
	
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
	/*
	if (isSet($_POST['idcall']))
	{
		$idcall 		= $_POST['idcall'];
		$idcallOK 	= true;
	}
	else
	{
		$error = true;
		$errormessage = "login token not set";
	}
	*/
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
	
	
	if (($usernameOK==1) && ($tokenOK==1) ) //&& ($idcallOK==true))
	{
		//$resultValue = acceptCallFunction($username, $token, $idcall, $deadlineHours);
		$resultValue = acceptCallFunction($username, $token, $deadlineHours);
	}
	else if ($error == false)
	{
		$resultValue->errormessage = "FORMAT: bad format ".$tokenOK." and : ".$usernameOK;
	}
	
	if ( $resultValue->error == true )
	{
		logErrorMessage($resultValue->errormessage);
		$jsonelement = array("Result"=>"ERROR");
		$myJSON = json_encode($jsonelement);
		echo $myJSON;
	}
	else 
	{
		logNormalMessage("Agent accepted a call ".$username);
		$jsonelement = array("Result"=>"Agent accepted a call", "CallId"=>$resultValue->idcall);
		$myJSON = json_encode($jsonelement);
		echo $myJSON;
	}
?>