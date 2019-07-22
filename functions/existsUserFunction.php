<?php

function existsUserFunction($username)
{
	include 'database/databaseconnect.php';
	
	$error = false;
	
	if (!($stmt = $link->prepare('SELECT * FROM users WHERE username=?')))
	{
		$error = true;
		$errormessage = "MYSQL: Could not prepare statement";
	}
	else
	{
		$stmt->bind_param("s", $username);
		$stmt->execute();
		if ($result = $stmt->get_result())
		{
			if (mysqli_num_rows($result)==1)
			{
				$error = true;
				$errormessage = "User already registered: ".$username;
			}
			
			// Free resultset
			mysqli_free_result($result);
		}
		else
		{
			$error = true;
			$errormessage = "MYSQL: Could not execute statement ".$link->error;
		}
	}
	include 'database/databasedisconnect.php';
	
	return $error;
}

?>