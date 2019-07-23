<?php

$keylocation = __DIR__;

function encryptData($plaintext, $keyyear, $keylocation)
{
	$publicKey = file_get_contents($keylocation.'pblc'.$keyyear.'.pem');
	openssl_public_encrypt($plaintext, $encrypted, $publicKey);
	return $encrypted;
}

function decryptData($encrypted, $keyyear, $keylocation)
{
	$privKey = file_get_contents($keylocation.'prvt'.$keyyear.'.pem');
	openssl_private_decrypt($encrypted, $decrypted, $privKey);
	return $decrypted;
}

/**
* Create private/public key
 */
function generateKey($opensslConfigPath = NULL, $keyyear, $keylocation)
{
	if ($opensslConfigPath == NULL)
	{
		$opensslConfigPath = $keylocation."/openssl.cnf";
	}
	$config = array(
		"config" => $opensslConfigPath,
		"digest_alg" => "sha512",
		"private_key_bits" => 2048,
		"private_key_type" => OPENSSL_KEYTYPE_RSA,
	);

	$res = openssl_pkey_new($config);
	if (empty($res)) {return false;}

	openssl_pkey_export($res, $privKey, NULL, $config);
	
	$pubKey = openssl_pkey_get_details($res);
	if ($pubKey === FALSE){return false;}

	$pubKey = $pubKey["key"];

	file_put_contents($keylocation."/pblc".$keyyear.".pem", $pubKey);
	file_put_contents($keylocation."/prvt".$keyyear.".pem", $privKey);
	
}

function testEncryptDecrypt($keyyear, $keylocation)
{
	$data = 'TESTSECURITY';
	$res1 = encryptData($data,$keyyear, $keylocation);
	$res2 = decryptData($res1,$keyyear, $keylocation);
	
	echo $res2."<br>";
	$result = ($data == $res2);
	
	return $result;
}

function showHashedPass($pass)
{
	echo '-----'.password_hash($pass, PASSWORD_DEFAULT).'-----';
}
?>