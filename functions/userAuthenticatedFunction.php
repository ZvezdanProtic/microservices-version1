<?php

function userAuthenticatedFunction($username, $token)
{
	include 'database/databaseconnect.php';
	
	$result = false;
	
	$today 				= date("Y-m-d H:i:s");
	
	if (!($stmt = $link->prepare('SELECT logintimeout FROM userslogin, users WHERE users.iduser=userslogin.iduser AND username=? AND oauthtoken=? AND logintimeout>?')))
	{
	}
	else
	{
		$stmt->bind_param("sss", $username, $token, $today);
		$stmt->execute();
		if ($result2 = $stmt->get_result())
		{
			if (mysqli_num_rows($result2)==1)
			{
				$result = true;
				mysqli_free_result($result2);
			}
			else if (mysqli_num_rows($result2)!=0)
			{
				// Free resultset
				mysqli_free_result($result2);
			}
		}
		else
		{
		}
	}
	include 'database/databasedisconnect.php';
	
	return $result;
}

?>