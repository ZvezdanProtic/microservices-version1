<?php


function getCallLinkFunctionAgent($username, $token)
{
	$resultValue = new GetCallLinkResultClass;

	include 'functions/existsUserFunction.php';
	$existsUser = existsUserFunction($username);
	if ($existsUser == true)
	{
		include 'functions/userAuthenticatedFunction.php';
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
						$resultValue->errormessage= "More users, or user not agent, for user name: ".$username;
					}
				}
			}

			// Check if the call exists
			if (!($stmt = $link->prepare('SELECT callconnecting.idcall FROM callregistration, callconnecting WHERE callregistration.idcall=callconnecting.idcall AND idagent=? AND callregistration.calltimeout>? AND callconnecting.calltimeout>? AND callaccepted<? AND callfinished=callaccepted')))
			{
				$resultValue->errormessage = "MYSQL: Could not prepare statement";
				$error = true;
			}
			else
			{
				$stmt->bind_param("ssss", $iduser , $today, $today, $today );
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
						$row = mysqli_fetch_assoc($result2);
						$idcall = $row['idcall'];
						$pagelink = "callpage.php";
						$pagelink .= "?roomID=".$idcall;
						$pagelink .= "&userID=".$iduser;
						$pagelink .= "&username=".$username;
						$pagelink .= "&token=".$token;
						$pagelink .= "&type=Agent".;
						$resultValue->error = false;
						$resultValue->linkaddress = $pagelink;
						$resultValue->roomid = $idcall;
					}
					else
					{
						$resultValue->errormessage= "Problems getting call data for user: ".$username;
						$error = true;
					}
				}
			}

			include 'database/databasedisconnect.php';
		}
	}
	
	return $resultValue;
}

?>