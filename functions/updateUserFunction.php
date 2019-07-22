<?php

function updateUserFunction($username, $password, $email)
{

	$error = false;
	
	include 'functions/existsUserFunction.php';
	
	$error = !existsUserFunction($username);
	
	if ($error == false)
	{
		require_once 'utils/utils.php';
		require_once 'utils/config.php';
		require_once 'utils/purifyconfig.php';
		require_once 'security/kljucsigurnost.php';
		
		include 'database/databaseconnect.php';
		
		$keyyear  = date("Y");
		
		// Encode email
		// Use current years key
		$encodedemail = encryptData($email, $keyyear, $keylocation);
		$passwordhash = password_hash($password, PASSWORD_DEFAULT);
		
		$authenticated_token = "";
		$authenticated = "yes";
		$confirmed_token = "";
		$confirmed = "yes";
		
		$query24 = "UPDATE users SET password=?, email=?, keyyear=? WHERE username=?";
		if (!($stmt24 = $link->prepare($query24)))
		{
			$errormessage = "Could not prepare mysql statement";
			$error = true;
		}
		else
		{
			$stmt24->bind_param("ssds", $passwordhash,$encodedemail,$keyyear,$username);
			if (!($stmt24->execute()))
			{
				$error = true;
				$errormessage = "MYSQL: Could not execute statement ".$link->error;
			}
		}
		
		include 'database/databasedisconnect.php';
	}
	
	
	return $error;
}

?>