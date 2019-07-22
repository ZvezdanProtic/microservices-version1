<?php 
	require_once 'utils/utils.php';
	require_once 'utils/config.php';
	require_once 'utils/purifyconfig.php';
	//include 'security/CSRFcheck.php';
	include 'resultclasses.php';
	include 'functions/endCallFunction.php';
	
	$error = false;
	$errormessage = "";
	
	$usernameOK = false;
	$idcallOK = false;
	$tokenOK = false;
	
	//print_r($_POST);
	//print_r($_GET);
	
	$resultValue = new EndCallResultClass;
	
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
	
	
	if (($usernameOK==1) && ($idcallOK==true) && ($tokenOK==1) )
	{
		$resultValue = endCallFunctionUser($username, $token, $idcall);
	}
	else if ($error == true)
	{
		$resultValue->errormessage = "FORMAT: bad format ".$usernameOK." and : ".$idcallOK;
	}
	
	if ( $resultValue->error == true )
	{
		logErrorMessage($resultValue->errormessage);
		$jsonelement = array("Result"=>"Call not finished");
		$myJSON = json_encode($jsonelement);
		echo $myJSON;
	}
	else 
	{
		logNormalMessage("User cancelled a call ".$username);
		$jsonelement = array("Result"=>"Call finished");
		$myJSON = json_encode($jsonelement);
		echo $myJSON;
	}
?>