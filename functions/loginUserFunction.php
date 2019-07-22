<?php

function loginUserFunction($username, $password, $authTokenSizeTogenerate, $deadlineHours)
{
	$error = false;
	
	include 'checkIfUserIsLoggedFunction.php';
	
	$resultValue = new LoginUserResultClass;
	
	$errormessage = "";
	
	if ($error == false)
	{
		include 'database/databaseconnect.php';
		
		if (!($stmt = $link->prepare('SELECT * FROM users WHERE username=?')))
		{
			$error = true;
			$errormessage = "MYSQL: Could not prepare statement";
		}
		else
		{
			$stmt->bind_param("s", $username);
			$stmt->execute();
			if (!($result = $stmt->get_result()))
			{
				$error = true;
				$errormessage = "No users for user name: ".$username." and password: ".$password;
			}
			else
			{
				if (mysqli_num_rows($result)==1)
				{
					$row = mysqli_fetch_assoc($result);
					$storedpassword = $row['password'];
					$iduser = $row['iduser'];

					$userloggedIn = checkIfUserIsLoggedFunction($iduser);
					
					if ($userloggedIn == false)
					{
						if (password_verify($password, $storedpassword))
						{
							$oauthtoken = generateRandomString($authTokenSizeTogenerate);
							$today 				= date("Y-m-d H:i:s");
							$deadline  			= date("Y-m-d H:i:s", mktime(date("H")+$deadlineHours, date("i"), date("s"), date("n")  , date("j"), date("Y")));
							
							if (!($stmt2 = $link->prepare("INSERT INTO userslogin VALUES(?,?,?,?,?)"))) 
							{
								$error = true;
								$errormessage = "MYSQL: Prepare statement failed ";
							}
							else
							{
								$stmt2->bind_param('sssss', $iduser, $today, $deadline, $deadline, $oauthtoken);
							
								if (!$stmt2->execute())
								{
									$error = true;
									$errormessage = "MYSQL: Update oauthtoken failed for user ".$username."-".$oauthtoken."-".$today."-".$deadline;
								}
								else
								{
									include 'database/databasedisconnect.php';
									$resultValue->error = $error;
									$resultValue->authtoken = $oauthtoken;
									return $resultValue;
								}
							}
						}
						else
						{
							$error = true;
							$errormessage = "PASSWORD: Password failed for user ".$username." and password: ".$password;
						}
					}
					else
					{
						$error = true;
						$errormessage = "User already logged in";
					}
				}
				else
				{
					$error = true;
					$numrows = mysqli_num_rows($result);
					$errormessage = "MYSQL: Select user query returned ".$numrows." results for ".$username." and ".$password;
				}
			}
		}
		include 'database/databasedisconnect.php';
	}
	
	$resultValue->errormessage = $errormessage;
	$resultValue->error = $error;
	
	return $resultValue;
}

?>