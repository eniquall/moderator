<?php
/**
 * Date: 1/9/14
 * Time: 4:57 PM
 */

class ProfileHelper {
	public static function isEmailUnique($className, $email, $isNewDocument = true, $_id = null) {
		if (!$isNewDocument && is_null($_id)) {
			throw new CException("_id of mongo doc should not be empty if it is not new doc");
		}

		$criteria = new EMongoCriteria();
		$criteria->email = new MongoRegex('/' . trim($email) . '/i');

		if (!$isNewDocument) {
			$criteria->_id('!=', new MongoId($_id));
		}

		$class = new $className();
		if ($class::model()->count($criteria)) {
			return false;
		}

		return true;
	}

	public static function isNameUnique($className, $name, $isNewDocument = true, $_id = null) {
		if (!$isNewDocument && is_null($_id)) {
			throw new CException("_id of mongo doc should not be empty if it is not new doc");
		}

		$criteria = new EMongoCriteria();
		$criteria->name = new MongoRegex('/' . trim($name) . '/i');

		if (!$isNewDocument) {
			$criteria->_id('!=', new MongoId($_id));
		}

		$class = new $className();
		if ($class::model()->count($criteria)) {
			return false;
		}

		return true;
	}
}