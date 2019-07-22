<?php

function endCallFunctionAgent($username, $token, $idcall)
{

	$resultValue = new EndCallResultClass;

	include 'functions/existsUserFunction.php';
	$existsUser = existsUserFunction($username);
	if ($existsUser == true)
	{
		include 'functions/userAuthenticatedFunction.php';
		$userAuthenticated = userAuthenticatedFunction($username, $token);
		if ($userAuthenticated == true)
		{
			include 'database/databaseconnect.php';
		
			$error = false;
			if (!($stmt = $link->prepare('SELECT iduser FROM users WHERE username=? ')))
			{
				$resultValue->errormessage = "MYSQL: Could not prepare statement";
				$error = true;
			}
			else
			{
				$stmt->bind_param("s", $username);
				$stmt->execute();
				if (!($result = $stmt->get_result()))
				{
					$resultValue->errormessage = "No users for user name: ".$username;
					$error = true;
				}
				else
				{
					if (mysqli_num_rows($result)==1)
					{
						$row = mysqli_fetch_assoc($result);
						$iduser = $row['iduser'];
					}
					else
					{
						$resultValue->errormessage= "More users for user name: ".$username;
						$error = true;
					}
				}
			}
			
			$today 				= date("Y-m-d H:i:s");
			
			if ($error == false)
			{
				$query24 = "UPDATE callconnecting SET callfinished = ? WHERE idcall=? AND idagent=?";
				if (!($stmt24 = $link->prepare($query24)))
				{
					$resultValue->errormessage= "Could not prepare mysql statement";
				}
				else
				{
					$stmt24->bind_param("sss",$today, $idcall, $iduser);
					if (!($stmt24->execute()))
					{
						$resultValue->errormessage= "MYSQL: Could not execute statement ".$link->error;
					}
					else
					{
						$resultValue->error = false;
					}
				}
			}
			include 'database/databasedisconnect.php';
		}
	}
	
	return $resultValue;
}

function endCallFunctionUser($username, $token, $idcall)
{

	$resultValue = new EndCallResultClass;

	include 'functions/existsUserFunction.php';
	$existsUser = existsUserFunction($username);
	if ($existsUser == true)
	{
		include 'functions/userAuthenticatedFunction.php';
		$userAuthenticated = userAuthenticatedFunction($username, $token);
		if ($userAuthenticated == true)
		{
			include 'database/databaseconnect.php';
		
			$error = false;
			if (!($stmt = $link->prepare('SELECT iduser FROM users WHERE username=? AND type=\'Client\' ')))
			{
				$resultValue->errormessage = "MYSQL: Could not prepare statement";
				$error = true;
			}
			else
			{
				$stmt->bind_param("s", $username);
				$stmt->execute();
				if (!($result = $stmt->get_result()))
				{
					$resultValue->errormessage = "No users for user name: ".$username;
					$error = true;
				}
				else
				{
					if (mysqli_num_rows($result)==1)
					{
						$row = mysqli_fetch_assoc($result);
						$iduser = $row['iduser'];
					}
					else
					{
						$resultValue->errormessage= "More users for user name: ".$username;
						$error = true;
					}
				}
			}
			
			$today 				= date("Y-m-d H:i:s");
			
			if ($error == false)
			{
				$query24 = "UPDATE callregistration SET callfinished = ? WHERE idcall=? AND idclient=?";
				if (!($stmt24 = $link->prepare($query24)))
				{
					$resultValue->errormessage= "Could not prepare mysql statement";
				}
				else
				{
					$stmt24->bind_param("sss",$today, $idcall, $iduser);
					if (!($stmt24->execute()))
					{
						$resultValue->errormessage= "MYSQL: Could not execute statement ".$link->error;
					}
					else
					{
						$resultValue->error = false;
					}
				}
			}
			include 'database/databasedisconnect.php';
		}
	}
	
	return $resultValue;
}
?>