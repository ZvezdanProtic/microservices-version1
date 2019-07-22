<?php 

require_once 'utils/utils.php';

//CSRF
// In order for this to work put: Header set Access-Control-Allow-Origin "*" in Directory element of apache httpd.conf
$referer = $_SERVER['HTTP_REFERER'];

$mydomainnotsecure = 'http://localhost/microservicesv1';
$mydomainsec = 'https://localhost/microservicesv1';

$obtaineddomainnotsecure = substr($referer,0,21);
$obtaineddomainsec = substr($referer,0,22);

if ($obtaineddomainnotsecure != $mydomainnotsecure)
{
	if ($mydomainsec != $obtaineddomainsec)
	{
		$errormessage = "HACKING: Cross site scripting attack from ".$referer;
		logErrorMessage($errormessage);
		//header('Location: index.php'); 
	}
}

?>