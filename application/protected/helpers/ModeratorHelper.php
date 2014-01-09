<?php
/**
 * User: dna
 * Date: 1/9/14
 * Time: 4:57 PM
 */

Yii::import('application.models.Moderator');
class ModeratorHelper {
	public static function isEmailUnique($email, $isNewDocument = false, $_id = null) {
		if (!$isNewDocument && is_null($_id)) {
			throw new CException("_id of mongo doc should not be empty if it is not new doc");
		}

		$criteria = new EMongoCriteria();
		$criteria->email = $email;

		if (!$isNewDocument) {
			$criteria->_id('!=', $_id);
		}

		if (Moderator::model()->count($criteria)) {
			return false;
		}

		return true;
	}
}