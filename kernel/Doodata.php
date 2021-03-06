<?php

/**
* PHP, une classe PHP simple. Dans le but simplifié l'utilisation de PDO
* @author Dakia Franck <dakiafranckinfo@gmail.com>
* @package Doo;
* @version 0.1.0
*/

namespace Doo;

/**
* Doodata, class Doo permettant de crypter et decripter de donnee
*/
abstract class Doodata extends Doodb {

	private static $part = "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0";
	private static $mode = MCRYPT_MODE_CBC;
	private static $key = 'e10481ddd02acf366f57c60819877a32';
	private static $dataLen  = null;

	/**
	* decrypto, fonction permettant de decrypter une chaine de caractere encrypter avec la fonction soeur.
    *
	* @param string, donnee a decrypter.
	* @param function, une fonction de callback.
	* @return string, resultat
	*/

	public static function decrypto($data, $cb = null) {

		$decode = base64_decode($data);
		$r = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, self::$key, $decode, self::$mode, self::$part);

		if($cb !== null) {

			call_user_func($cb, (substr($r, 0, self::$dataLen)));

		}

		return $r;

	}


	/**
	* crypto, fonction permettant d'encrypter une chaine de caractere.
    *
	* @param string, donnee a encrypter.
	* @param function, une fonction de callback.
	* @return string, resultat
	*/
	public static function crypto($data, $cb = null) {

		# On recupere la taille de la donnee.
		self::$dataLen = strlen($data);
		$r = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, self::$key, $data, self::$mode, self::$part));

		if($cb !== null) {

			call_user_func($cb, $r);

		}

		return $r;

	}

}
