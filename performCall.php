<?php 
	require_once 'utils/utils.php';
	require_once 'utils/config.php';
	require_once 'utils/purifyconfig.php';
	//include 'security/CSRFcheck.php';
	include 'resultclasses.php';
	include 'functions/performCallFunction.php';
	include 'functions/callFinishedFunction.php';
	
	
	$error = false;
	$errormessage = "";
	
	$usernameOK = false;
	$tokenOK = false;
	$idcallOK = false;
	$sounddataOK = false;
	
	$resultValue = new PerformCallResultClass;
	$callFinishedValue = new CallFinishedResultClass;
	
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
	if (isSet($_POST['wavBase64']))
	{
		$sounddataraw = $_POST['wavBase64'];
		$sounddataraw = str_replace('data:audio/wav;base64,', '', $sounddataraw);
		$sounddataraw = str_replace(' ', '+', $sounddataraw);
		$sounddata = base64_decode($sounddataraw);
		$sounddataOK = true;
	}
	else
	{
		$error = true;
		$errormessage = "sound data not set";
	}
	
	if (($usernameOK==1) && ($tokenOK==1) && ($idcallOK==true)  && ($sounddataOK==true))
	{
		$callFinishedValue = callFinishedFunction($username, $token, $idcall);
		if ($callFinishedValue->error == false)
		{
			if ($callFinishedValue->callfinished==false)
			{
				$resultValue = performCallFunction($username, $token, $idcall, $sounddata);
			}
			else
			{
				$resultValue->errormessage = "Call is finished ";
			}
		}
		else
		{
			$resultValue->errormessage = $callFinishedValue->errormessage;
		}
	}
	else if ($error == false)
	{
		$resultValue->errormessage = "FORMAT: bad format ".$tokenOK." ".$usernameOK." ".$idcallOK." ".$sounddataOK;
	}
	
	if ( $resultValue->error == true )
	{
		logErrorMessage($resultValue->errormessage);
		$jsonelement = array("Result"=>"Call not posible");
		$myJSON = json_encode($jsonelement);
		echo $myJSON;
	}
	else 
	{
		logNormalMessage("User sent sound file ".$username);
		$jsonelement = array("Result"=>"Communication received");
		$myJSON = json_encode($jsonelement);
		echo $myJSON;
	}
?>