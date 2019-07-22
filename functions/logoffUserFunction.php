<?php

function logoffUserFunctionByPassword($username, $password)
{
	$error = false;
	
	include 'checkIfUserIsLoggedFunction.php';
	
	$resultValue = new LogoffUserResultClass;
	
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
					
					if ($userloggedIn == true)
					{
						if (password_verify($password, $storedpassword))
						{
							if (!($stmt = $link->prepare('SELECT MAX(loginstart) AS loginstart from userslogin WHERE iduser=?')))
							{
								$error = true;
								$errormessage = "MYSQL: Could not prepare statement";
							}
							else
							{
								$stmt->bind_param("s", $iduser);
								$stmt->execute();
								if (!($result = $stmt->get_result()))
								{
									$error = true;
									$errormessage = "No users for user name: ".$iduser." and password: ".$password;
								}
								else
								{
									if (mysqli_num_rows($result)==1)
									{
										$row = mysqli_fetch_assoc($result);
										$loginstart = $row['loginstart'];

										$today 				= date("Y-m-d H:i:s");
										if (!($stmt2 = $link->prepare("UPDATE userslogin SET loginend = ? WHERE iduser=? AND loginstart=?"))) 
										{
											$error = true;
											$errormessage = "MYSQL: Prepare statement failed ";
										}
										else
										{
											$stmt2->bind_param('sss', $today, $iduser, $loginstart);
										
											if (!$stmt2->execute())
											{
												$error = true;
												$errormessage = "MYSQL: Logoff failed for user ".$username;
											}
											else
											{
												include 'database/databasedisconnect.php';
												$resultValue->error = $error;
												return $resultValue;
											}
										}
									}
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

function logoffUserFunctionByToken($username, $authtoken)
{
	$error = false;
	
	include 'checkIfUserIsLoggedFunction.php';
	
	$resultValue = new LogoffUserResultClass;
	
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
							$today 				= date("Y-m-d H:i:s");
							if (!($stmt2 = $link->prepare("UPDATE userslogin SET loginend = ? WHERE iduser=? AND oauthtoken=?"))) 
							{
								$error = true;
								$errormessage = "MYSQL: Prepare statement failed ";
							}
							else
							{
								$stmt2->bind_param('sss', $today, $iduser, $authtoken);
							
								if (!$stmt2->execute())
								{
									$error = true;
									$errormessage = "MYSQL: Logoff failed for user ".$username;
								}
								else
								{
									include 'database/databasedisconnect.php';
									$resultValue->error = $error;
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
						$errormessage = "User already logged off";
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