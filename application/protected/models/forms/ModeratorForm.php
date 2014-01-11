<?php
/**
 * Date: 1/8/14
 * Time: 11:37 PM
 */

class ModeratorForm extends CFormModel {
	public $name;
	public $email;
	public $password;
	public $password2;

	public $newPassword;
	public $newPassword2;

	public $langs;
	public $paypal;
	public $_id;

	const REGISTRATION_SCENARIO = 'registration';
	const EDIT_PROFILE_SCENARIO = 'edit';

	public function init() {
		// dafault scenario - registration
		$this->setScenario(self::REGISTRATION_SCENARIO);
	}

	public function rules() {
		return array(
			array('name, email, paypal', 'required'),
			array('password', 'required', 'on' => self::REGISTRATION_SCENARIO),

			array('_id', 'required', 'on' => 'edit'),
			array('name', 'length', 'min' => 2),
			array('email', 'email', 'allowEmpty' => false),
			array('email', 'uniqueEmail'),
			array('password', 'length', 'min' => 6),

			array('password', 'checkForNewPassword', 'on' => self::EDIT_PROFILE_SCENARIO),

			//array('password2', 'required', 'on' => self::REGISTRATION_SCENARIO),
			array('password2', 'compare', 'compareAttribute' => 'password', 'on' => self::REGISTRATION_SCENARIO),

			//array('newPassword', 'required', 'on' => self::EDIT_PROFILE_SCENARIO),
			array('newPassword', 'length', 'min' => 6, 'on' => self::EDIT_PROFILE_SCENARIO),

			//array('newPassword2', 'required', 'on' => self::EDIT_PROFILE_SCENARIO),
			array('newPassword2', 'compare', 'compareAttribute' => 'newPassword', 'on' => self::EDIT_PROFILE_SCENARIO),

			array('langs','type','type'=>'array','allowEmpty' => false, 'message' => 'Choose at least one language from the list'),
			array('langs', 'LanguageAllowed', 'message' => 'One of the languages is not allowed'),
		);
	}

	public function uniqueEmail($attribute, $params) {
		$email = $this->attributes[$attribute];

		// check email for new moderator
		if (!ModeratorHelper::isEmailUnique($email, true)) {
			$this->addError($attribute, 'Email ' . $email . ' already exists');
		}
	}
	public function attributeLabels() {
		return array(
			'name'=>'Your name',
			'email'=>'Email',
			'password'=>'Password',
			'password2'=>'Enter password again',
			'languages' => 'Which languages do you speak?',
			'paypal' => 'Your paypal account'
		);
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

	public function populateFromModel(Moderator $model) {
		$this->name = $model->name;
		$this->email = $model->email;
		$this->langs = $model->paypal;
		$this->_id = $model->id;
		$this->setScenario(self::EDIT_PROFILE_SCENARIO);
	}


	public function checkForNewPassword($attribute, $params) {
		$password = $this->attributes[$attribute];
		$newPassword = $this->attributes['newPassword'];

		//if user entered new password - check if he entered correct current password
		if (!empty($newPassword)) {
			$userModel = Yii::app()->user->getModel();
			if (!$userModel->verifyPassword($password)) {
				$this->addError($attribute, 'Current correct password should be entered');
			}
		}
	}

}