<?php

function checkIfUserIsLoggedFunction($iduser)
{
	include 'database/databaseconnect.php';
	
	$result = true;
	
	$today 				= date("Y-m-d H:i:s");
	
	if (!($stmt = $link->prepare('SELECT * FROM userslogin WHERE iduser=? AND loginstart<? AND logintimeout>? AND loginend>?')))
	{
		//$error = true;
		//$errormessage = "MYSQL: Could not prepare statement";
	}
	else
	{
		$stmt->bind_param("ssss", $iduser, $today, $today, $today);
		$stmt->execute();
		if ($result = $stmt->get_result())
		{
			if (mysqli_num_rows($result)==0)
			{
				$result = false;
			}
			else
			{
				// Free resultset
				mysqli_free_result($result);
			}
		}
		else
		{
			//$result = true;
			//$errormessage = "MYSQL: Could not execute statement ".$link->error;
		}
	}
	include 'database/databasedisconnect.php';
	
	return $result;
}

?>