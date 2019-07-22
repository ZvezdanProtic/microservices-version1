<?php 

	$link = new mysqli('localhost:3306', 'testuser', 'testpassword', 'microservicesv1');
	
	if ($link->connect_errno) {
		echo "Failed to connect to MySQL: (" . $link->connect_errno . ") " . $link->connect_error;
		// ERROR
	}

?>