<?php

function callFinishedFunction($username, $token, $idcall)
{

	$resultValue = new CallFinishedResultClass;

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
			if (!($stmt = $link->prepare('SELECT iduser FROM users WHERE username=? AND type=\'Agent\' ')))
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
						$error = true;
					}
				}
			}
			
			$today 				= date("Y-m-d H:i:s");
			
			if ($error == false)
			{
				$query24 = "SELECT * FROM callconnecting WHERE idcall=? AND callfinished < ? ";
				if (!($stmt24 = $link->prepare($query24)))
				{
					$resultValue->errormessage= "Could not prepare mysql statement";
				}
				else
				{
					
					$deadline  			= date("Y-m-d H:i:s", mktime(date("H")+$deadlineHours, date("i"), date("s"), date("n")  , date("j"), date("Y")));
					$stmt24->bind_param("ss",$idcall,$today);
					if (!($stmt24->execute()))
					{
						$resultValue->errormessage= "MYSQL: Could not execute statement ".$link->error;
					}
					else
					{
						if (mysqli_num_rows($result)==1)
						{
							$resultValue->callfinished = false;
							$resultValue->error = false;
						}
						else
						{
							$resultValue->errormessage= "More users, or user not agent,  for user name: ".$username;
						}
						
					}
				}
			}
			include 'database/databasedisconnect.php';
		}
	}
	
	return $resultValue;
}

?>