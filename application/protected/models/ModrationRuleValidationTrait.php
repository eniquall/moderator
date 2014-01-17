<?php
/**
 * User: eniquall
 * Date: 1/16/14
 * Time: 11:28 PM
 */

trait ModrationRuleValidationTrait {

	public function Odd($attribute, $params) {
		$level = $this->attributes[$attribute];

		if ($level % 2 != 1) {
			$this->addError($attribute, 'Level ( ' . $level . ' ) should be odd (1,3,5,7...)');
		}
	}

	public function TypeAllowed($attribute, $params) {
		$type = $this->attributes[$attribute];

		$allowedTypesList = array_keys(ContentHelper::getAllowedTypesList());
		if (!in_array($type, $allowedTypesList)) {
			$this->addError($attribute, 'Type (' . $type . ') is not allowed');
			return false;
		}
		return true;
	}

	public function UniqueRule($attribute, $params) {
		$type = $this->attributes[$attribute];
		$projectId = $this->attributes['projectId'];

		$isNewInstance = ($this->getScenario() == ModerationRuleForm::ADD_RULE_SCENARIO);

		$criteria = new EMongoCriteria();
		$criteria->type = $type;
		$criteria->projectId = $projectId;

		if (!$isNewInstance) {
			$_id = $this->attributes['_id'];
			$criteria->_id('!=', $_id);
		}

		if (ModerationRuleModel::model()->count($criteria)) {
			return false;
		}
		return true;
	}
} 