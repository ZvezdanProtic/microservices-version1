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

$username = 'User'.rand(1,2000);
$password = "Test123456";
$mango = "test@test.com";
$type = "Client";

// Add client
$addclientdata = array("username" => $username, "password" => $password, "mango"=>$mango, "type"=>$type );
$addclientresult = CallAPI("POST", "http://localhost/microservicesv1/addUser.php", $addclientdata);

// Wait between 1 and 5 seconds
sleep(1);

// Login user
$loginuserdata = array("username" => $username, "password" => $password);
$loginuserresult = CallAPI("POST", "http://localhost/microservicesv1/loginUser.php", $loginuserdata);
$loginuserresult = json_decode($loginuserresult);
$token = $loginuserresult->Token;

// Wait between 1 and 5 seconds

sleep(1);

// Ask for a call

$requestcalldata = array("username" => $username, "token" => $token);
$requestcallresult = CallAPI("POST", "http://localhost/microservicesv1/requestCall.php", $requestcalldata);

// Wait until a call is accepted by an agent, or 60 seconds, in this case quit
$callaccepted = false;
$timeelapsed = 0;
$waitdelta = 2;
$resultingpage = "ERROR";
$calllinkserviceresult = "";
while(($callaccepted == false)&&($timeelapsed<180))
{
	sleep($waitdelta );
	$timeelapsed = $timeelapsed + $waitdelta;

	$getcalllinkservicedata = array("username" => $username, "token" => $token);
	$calllinkserviceresult = CallAPI("POST", "http://localhost/microservicesv1/getCallLinkService.php", $getcalllinkservicedata);
	$calllinkserviceresult = json_decode($calllinkserviceresult);
	$resultingpage = $calllinkserviceresult->Page;
	
	if ($resultingpage!="ERROR") $callaccepted = true;
}

if ($callaccepted==true)
{
	// Get the ID of a call
	$callid = $calllinkserviceresult->CallID;
	
	// Get example test data from a file
	
	$file = TEST_DIR . 'test.webm';
	$testfiledata = file_get_contents($file);
	$wavdata = base64_encode($testfiledata);

	// Send 12 chunks of 5 second data with 5 seconds delay in betwen
	for ($i = 1; $i <= 12; $i++) {

		sleep(5);
		$performcalldata = array("username" => $username, "token" => $token, "idcall"=>$callid, "wavBase64"=>$wavdata);
		$performcallserviceresult = CallAPI("POST", "http://localhost/microservicesv1/performCall.php", $performcalldata);
	}
	
	// End a call
	$endcalldata = array("username" => $username, "token" => $token, "idcall"=>$callid);
	$endcallserviceresult = CallAPI("POST", "http://localhost/microservicesv1/endCallUser.php", $endcalldata);
	
}
else
{
	echo "ERROR CALL NOT ACCEPTED!<br>";
}
// Logoff user

$logoffuserdata = array("username" => $username, "password" => $password);
$logoffuserresult = CallAPI("POST", "http://localhost/microservicesv1/logoffUser.php", $logoffuserdata);

?>