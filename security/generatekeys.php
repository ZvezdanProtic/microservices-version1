<?php

	require_once 'kljucsigurnost.php';
	
	generateKey(NULL, 2019, $keylocation);
	
	testEncryptDecrypt(2019, $keylocation);
?>