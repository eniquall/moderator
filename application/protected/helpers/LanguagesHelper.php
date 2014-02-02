<?php
/**
 * User: eniquall
 * Date: 1/9/14
 * Time: 12:24 AM
 */

class LanguagesHelper {
	const ENGLISH_LANG = 1;
	const RUSSIAN_LANG = 2;
	const CHINESE_LANG = 3;
	const GERMAN_LANG  = 4;

	public static function getAllowedLanguagesList() {
		return [
			self::ENGLISH_LANG => 'en',
			self::RUSSIAN_LANG => 'ru',
			self::CHINESE_LANG => 'ch',
			self::GERMAN_LANG  => 'de'
		];
	}

	public static function getLanguageNameById($id) {
		$list = self::getAllowedLanguagesList();

		return !empty($list[$id]) ? $list[$id] : '';
	}
} 