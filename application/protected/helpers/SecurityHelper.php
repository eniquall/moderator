<?php
/**
 * User: eniquall
 * Date: 1/15/14
 * Time: 1:58 AM
 */

class SecurityHelper {
	public static function generatePasswordHash($password) {
		return md5($password);
	}

	public static function validatePassword($password, $hash) {
		return self::generatePasswordHash($password) === $hash;
	}

	public static function generateApiKey($email, $_id) {
		return md5($email . $_id);
	}
} 