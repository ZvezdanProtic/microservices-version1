<?php

function addUserFunction($username, $password, $email, $type)
{

	$error = false;
	
	include 'functions/existsUserFunction.php';
	
	$error = existsUserFunction($username);
	
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
		
		$query24 = "INSERT INTO users (username,password,email,confirmationToken,confirmed,activationToken,active,keyyear,type) VALUES (?,?,?,?,?,?,?,?,?)";
		if (!($stmt24 = $link->prepare($query24)))
		{
			$errormessage = "Could not prepare mysql statement";
			$error = true;
		}
		else
		{
			$stmt24->bind_param("sssssssds", $username, $passwordhash, $encodedemail, $confirmed_token, $confirmed, $authenticated_token, $authenticated,$keyyear,$type);
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