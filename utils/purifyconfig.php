<?php

	// For purifying HTML input
	require_once 'htmlpurifier-4.9.3/library/HTMLPurifier.auto.php';
	$config = HTMLPurifier_Config::createDefault();
	$purifier = new HTMLPurifier($config);

?>