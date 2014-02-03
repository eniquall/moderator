<?php
/**
 * User: eniquall
 * Date: 1/15/14
 * Time: 1:24 AM
 */

trait BaseProfileValidationTrait {
	public function uniqueEmail($attribute, $params) {
		$email = $this->attributes[$attribute];

		$isNewInstance = ($this->getScenario() == BaseProfileForm::REGISTRATION_SCENARIO);
		// if this method is used in Form - get name of the model, if it used in Model class get the name of this class
		$modelClass = strpos(get_class($this), 'Form') ? $this->getModelClass() : get_class($this);

		$isUniqueEmail = ProfileHelper::isEmailUnique($modelClass, $email, $isNewInstance, $this->_id);

		// add form
		if (!$isUniqueEmail) {
			$this->addError($attribute, 'Email ' . $email . ' already exists');
		}
	}

	public function uniqueName($attribute, $params) {
		$name = $this->attributes[$attribute];

		$isNewInstance = ($this->getScenario() == BaseProfileForm::REGISTRATION_SCENARIO);
		// if this method is used in Form - get name of the model, if it used in Model class get the name of this class
		$modelClass = strpos(get_class($this), 'Form') ? $this->getModelClass() : get_class($this);
		$isUniqueName = ProfileHelper::isNameUnique($modelClass, $name, $isNewInstance, $this->_id);

		// add form
		if (!$isUniqueName) {
			$this->addError($attribute, 'Name ' . $name . ' already exists');
		}

	}

	public function checkForNewPassword($attribute, $params) {
		$password = $this->attributes[$attribute];
		$newPassword = $this->attributes['newPassword'];

		//if user entered new password - check if he entered correct current password
		if (!empty($newPassword)) {
			$userModel = Yii::app()->user->getModel();
			if (!$userModel->validatePassword($password)) {
				$this->addError($attribute, 'Current correct password should be entered');
			} else {
				Yii::app()->user->setFlash('success', 'Password changed sucessfully');
			}
		}
	}

	public function validatePassword($password) {
		$hash = $this->password;
		return SecurityHelper::validatePassword($password, $hash);
	}

	/**
	 * Method checks if all the languages posted by user is allowed
	 * @param $attribute
	 * @param $params
	 * @return bool
	 */
	public function LanguageAllowed($attribute, $params) {
		$languages = $this->attributes[$attribute];
		if (empty($languages)) {
			return false;
		}

		$allowedLanguagesList = array_keys(LanguagesHelper::getAllowedLanguagesList());
		foreach($languages as $language) {
			if (!in_array($language, $allowedLanguagesList)) {
				$this->addError($attribute, 'One of the languages (' . $language . ') is not allowed');
				return false;
			}
		}
		return true;
	}
}