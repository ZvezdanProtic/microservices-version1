<?php

function CallAPI($method, $url, $data = false)
{
    $curl = curl_init();

	echo '<br>ARGUMENTS '.$method.'-'.$url.'-'.join(',', $data) ;
	
    switch ($method)
    {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);

            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_PUT, 1);
            break;
        default:
            if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
    }

    // Optional Authentication:
    //curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    //curl_setopt($curl, CURLOPT_USERPWD, "username:password");

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($curl);

	if ($result === FALSE) {
		printf("cUrl error (#%d): %s<br>\n", curl_errno($curl),
			   htmlspecialchars(curl_error($curl)));
	}
		
	echo '<br>API RESULT '.$result ;
	
    curl_close($curl);

    return $result;
}

define('TEST_DIR', 'tests/');

set_time_limit(600);

$username = 'Agent'.rand(1,2000);
$password = "Test123456";
$mango = "test@test.com";
$type = "Agent";

// Add agent
$addagentdata = array("username" => $username, "password" => $password, "mango"=>$mango, "type"=>$type );
$addagentresult = CallAPI("POST", "http://localhost/microservicesv1/addUser.php", $addagentdata);

?>

<?php
// Wait between 1 and 5 seconds
sleep(1);

// Login agent
$loginuserdata = array("username" => $username, "password" => $password);
$loginuserresult = CallAPI("POST", "http://localhost/microservicesv1/loginUser.php", $loginuserdata);
$loginuserresult = json_decode($loginuserresult);
$token = $loginuserresult->Token;

?>

<?php

// Wait between 1 and 5 seconds
sleep(1);

// Do for 5 calls or 10 minutes (so 1 minute max waiting time between calls)
$timeelapsed = 0;
$callcount = 0;
$testfiledata = "";

while(($callcount <5) && ($timeelapsed<600))
{
	
	
	// Check if call is available
	$acceptcalldata = array("username" => $username, "token" => $token);
	$acceptcallserviceresult = CallAPI("POST", "http://localhost/microservicesv1/acceptCall.php", $acceptcalldata);
	$acceptcallserviceresult = json_decode($acceptcallserviceresult);
	$acceptcallresult = $acceptcallserviceresult->Result;

	if ($acceptcallresult!="ERROR")
	{
		$callcount = $callcount + 1;
		
		echo $timeelapsed.' ACCEPTED A CALL <br>';
		// Wait for 1-2 seconds
		$callid = $acceptcallserviceresult->CallId;
		
		$timeelapsed = $timeelapsed +2;
		sleep(1);

		// Get example test data from a file
		
		$file = TEST_DIR . 'test.webm';
		$testfiledata = file_get_contents($file);
		$wavdata = base64_encode($testfiledata);

		// Send 12 chunks of 5 second data with 5 seconds delay in betwen
		for ($i = 1; $i <= 12; $i++) {
			sleep(5);
			$timeelapsed = $timeelapsed + 5;
			
			$performcalldata = array("username" => $username, "token" => $token, "idcall"=>$callid, "wavBase64"=>$wavdata);
			$performcallserviceresult = CallAPI("POST", "http://localhost/microservicesv1/performCall.php", $performcalldata);
		}
		
		// End a call
		$endcalldata = array("username" => $username, "token" => $token, "idcall"=>$callid);
		$endcallserviceresult = CallAPI("POST", "http://localhost/microservicesv1/endCallAgent.php", $endcalldata);
		
	}
	else
	{
		echo $timeelapsed.' NO CALL AVAILABLE <br>';
		// Wait 2 seconds and re-try
		sleep(1);
		$timeelapsed = $timeelapsed + 2;
	}

}

?>

<?php

// Log off agent
$logoffuserdata = array("username" => $username, "password" => $password);
$logoffuserresult = CallAPI("POST", "http://localhost/microservicesv1/logoffUser.php", $logoffuserdata);

?>