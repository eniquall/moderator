<?php
class MongoHelper {
	/**
	 * @return boolean true for valid / false for invalid.
	 */
	public static function isValidId($id) {
		$regex = '/^[0-9a-z]{24}$/';
		if (class_exists("MongoId"))
		{
			try {
				$tmp = new MongoId($id);
				if ($tmp->{'$id'} == $id) {
					return true;
				}
			} catch (Exception $e) {
				return false;
			}
			return false;
		}

		if (preg_match($regex, $id))
		{
			return true;
		}
		return false;
	}
}