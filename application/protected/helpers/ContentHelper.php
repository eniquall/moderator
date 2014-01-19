<?php
/**
 * Created by PhpStorm.
 * User: eniquall
 * Date: 1/14/14
 * Time: 11:39 PM
 */

class ContentHelper {
	const CONTENT_TYPE_TEXT = 1;
	const CONTENT_TYPE_IMAGE = 2;
	const CONTENT_TYPE_IMAGE_AND_TEXT = 3;

	public static function getAllowedTypesList() {
		return array(
			self::CONTENT_TYPE_TEXT => 'text',
			self::CONTENT_TYPE_IMAGE => 'image',
			self::CONTENT_TYPE_IMAGE_AND_TEXT => 'image with text'
		);
	}

	public static function getTypeNameByType($type) {
		$list = self::getAllowedTypesList();
		return $list[$type];
	}
} 