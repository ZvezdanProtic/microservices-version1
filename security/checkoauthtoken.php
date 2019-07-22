<?php 
if (isset($_COOKIE['OAUTHtoken']))
{
	$rawtoken = $_COOKIE["OAUTHtoken"];
	$token = "tok".$_COOKIE["OAUTHtoken"];
}

require_once 'utils/utils.php';
require_once 'utils/config.php';

$GLOBALS['authenticatedUser'] = FALSE;

$error = false;
$errormessage = "";
if (!(!isset($token) || $token == null || $token=="tok"))
{
	// Check if the user with this ID is authenticated

	
	if (checkOauthToken($authTokenSize, $rawtoken)==true)
	{
		include 'database/databaseconnect.php';
		
		$query = "SELECT idusers,name FROM users WHERE oauthtoken=?";
		if (!($stmt = $link->prepare($query)))
		{
			$errormessage = "Could not prepare mysql statement";
			$error = true;
		}
		else
		{
			$stmt->bind_param("s", $rawtoken);
			$stmt->execute();
			if (!($result = $stmt->get_result()))
			{
				$errormessage = "No resutl from SQL ".$query;
				$error = true;
			}
			else
			{
				if (mysqli_num_rows($result)==1)
				{
					$GLOBALS['authenticatedUser'] = TRUE;
					$row = mysqli_fetch_assoc($result);
					$GLOBALS['idusers'] 	= $row['idusers'];
					$GLOBALS['name'] 		= $row['name'];
				}
				else
				{
					$errormessage = "More than one result returned for a token ".$rawtoken;
					$error = true;
				}
				mysqli_free_result($result);
			}
		}
	}
	else
	{
		$errormessage = "Token not in correct form ".$rawtoken;
		$error = true;
	}
	include 'database/databasedisconnect.php';
	
}

if ($error==true)
{
	logErrorMessage($errormessage);
}

?>