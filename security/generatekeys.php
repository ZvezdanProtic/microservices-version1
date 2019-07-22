<?php

	require_once 'security/kljucsigurnost.php';
	
	generateKey(NULL, 2019, $keylocation);
	
	testEncryptDecrypt(2019, $keylocation);
?>