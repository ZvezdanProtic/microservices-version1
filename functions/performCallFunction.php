<?php

function file_put_contents_atomically($filename, $data, $flags = 0, $context = null) {
    if (file_put_contents($filename."~", $data, $flags, $context) === strlen($data)) {
        return rename($filename."~",$filename,$context);
    }

    @unlink($filename."~", $context);
    return FALSE;
}

function performCallFunction($username, $token, $idcall, $sounddata)
{
	define('UPLOAD_DIR', 'uploads/');

	$resultValue = new PerformCallResultClass;

	require_once 'functions/existsUserFunction.php';
	$existsUser = existsUserFunction($username);
	if ($existsUser == true)
	{
		require_once 'functions/userAuthenticatedFunction.php';
		$userAuthenticated = userAuthenticatedFunction($username, $token);
		if ($userAuthenticated == true)
		{
			include 'database/databaseconnect.php';
			
			$today 				= date("Y-m-d H:i:s");
			$error = false;
			$iduser = 0;
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
						$resultValue->errormessage= "More users, or user not agent,  for user name: ".$username;
					}
				}
			}

			// Check if the call exists
			if (!($stmt = $link->prepare('SELECT * FROM callregistration, callconnecting WHERE callregistration.idcall=callconnecting.idcall AND callconnecting.idcall=? AND callcancel=callrequested AND callregistration.calltimeout>? AND callconnecting.calltimeout>?')))
			{
				$resultValue->errormessage = "MYSQL: Could not prepare statement";
				$error = true;
			}
			else
			{
				$stmt->bind_param("sss", $idcall , $today, $today );
				$stmt->execute();
				if (!($result2 = $stmt->get_result()))
				{
					$resultValue->errormessage = "No users for user name: ".$username;
					$error = true;
				}
				else
				{
					if (mysqli_num_rows($result2)==1)
					{
						// ALL OK
					}
					else
					{
						$resultValue->errormessage= "More users, or user not agent,  for user name: ".$username;
						$error = true;
					}
				}
			}
			
			if ($error == false)
			{
				$uniqueid = uniqid();
				$file = UPLOAD_DIR . $uniqueid . '.webm';
				$success = file_put_contents($file, $sounddata);
				if ( $success == FALSE )
				{
					$resultValue->errormessage= "Could not make a file ";
					$error = true;
				}
				else
				{
					$query24 = "INSERT INTO callprocessing (idcall,iduser,rawsounddatareceptionstarted,filename) VALUES (?,?,?,?)";
					if (!($stmt24 = $link->prepare($query24)))
					{
						$resultValue->errormessage= "Could not prepare mysql statement";
						$error = true;
					}
					else
					{
						
						$stmt24->bind_param("ssss",$idcall, $iduser, $today, $file);
						if (!($stmt24->execute()))
						{
							$resultValue->errormessage= "MYSQL: Could not execute statement ".$link->error;
							$error = true;
						}
						else
						{
							$resultValue->error = false;
						}
					}
				}
			}
			include 'database/databasedisconnect.php';
		}
	}
	else
	{
		$resultValue->errormessage= "User does not exist ".$username;
	}
	
	return $resultValue;
}

?>