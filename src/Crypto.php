<?php

namespace Paykun\Checkout;
 
class Crypto {

    public static function encrypt ($text, $key) {
        //$iv = random_bytes(16);
        $iv = openssl_random_pseudo_bytes(16);

		$value = openssl_encrypt(serialize($text), 'AES-256-CBC', $key, 0, $iv);
		$bIv = base64_encode($iv);
		$mac = hash_hmac('sha256', $bIv.$value, $key);
		$c_arr = array('iv'=>$bIv,'value'=>$value,'mac'=>$mac);
		$json = json_encode($c_arr);
		$crypted = base64_encode($json);
		return $crypted;
    }

    public static function decrypt ($sStr, $sKey) {
        $payload = json_decode(base64_decode($sStr), true);
		
        if (!is_array($payload) && isset($payload['iv'], $payload['value'], $payload['mac'])) {
            throw new DecryptException('The payload is invalid.');
        }
		
		//$bytes = random_bytes(16);
		$bytes = openssl_random_pseudo_bytes(16);
		$calculated = hash_hmac('sha256', hash_hmac('sha256', $payload['iv'].$payload['value'], $sKey), $bytes, true);
        if (!hash_equals(hash_hmac('sha256', $payload['mac'], $bytes, true), $calculated)) {
            echo 'mac not matched';
        }
			
		$iv = base64_decode($payload['iv']);
		
		$decrypted = \openssl_decrypt(
            $payload['value'], 'AES-256-CBC', $sKey, 0, $iv
        );
		
		if ($decrypted === false) {
            echo 'can\'t decrypt';
        }
		
		echo unserialize($decrypted);
    }

}

?>