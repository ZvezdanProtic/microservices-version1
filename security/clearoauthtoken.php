<?php 

$GLOBALS['logoff'] = FALSE;

require_once 'utils/utils.php';

$error = false;
$errormessage = "";
if ($GLOBALS['authenticatedUser'] == TRUE)
{
	// LOGOFF 
	$GLOBALS['logoff'] = TRUE;

	include 'database/databaseconnect.php';
	
	if (!($stmt2 = $link->prepare('UPDATE users SET oauthtoken=\'\' WHERE idusers=?')))
	{
		$error = true;
		$errormessage = "Could not prepare mysql statement".$link->error;
	}
	else
	{
		$stmt2->bind_param("s", $idusers);
		if (!($stmt2->execute()))
		{
			$error = true;
			$errormessage = "Could not remove oauthtoken from user ".$idusers;
		}
	}
	setcookie("OAUTHtoken","",time()-3600);
	
	include 'database/databasedisconnect.php';
}

if ($error==true)
{
	logErrorMessage($errormessage);
}
	
?>