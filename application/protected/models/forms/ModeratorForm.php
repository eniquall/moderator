<?php
/**
 * Date: 1/8/14
 * Time: 11:37 PM
 */

class ModeratorForm extends BaseProfileForm {
	use BaseProfileValidationTrait;

	public $name;
	public $email;
	public $password;
	public $password2;

	public $newPassword;
	public $newPassword2;

	public $langs;
	public $paypal;
	public $_id;

	public function rules() {
		return array(
			array('name, email, paypal', 'required'),
			array('password', 'required', 'on' => self::REGISTRATION_SCENARIO),

			array('_id', 'required', 'on' => 'edit'),
			array('name', 'length', 'min' => 2),
			array('email', 'email', 'allowEmpty' => false),
			array('email', 'uniqueEmail', 'on' => self::REGISTRATION_SCENARIO),
			array('password', 'length', 'min' => 6),

			array('password', 'checkForNewPassword', 'on' => self::EDIT_PROFILE_SCENARIO),
			array('password2', 'compare', 'compareAttribute' => 'password', 'on' => self::REGISTRATION_SCENARIO),

			array('newPassword', 'length', 'min' => 6, 'on' => self::EDIT_PROFILE_SCENARIO),
			array('newPassword2', 'compare', 'compareAttribute' => 'newPassword', 'on' => self::EDIT_PROFILE_SCENARIO),

			array('langs','type','type'=>'array','allowEmpty' => false, 'message' => 'Choose at least one language from the list'),
			array('langs', 'LanguageAllowed', 'message' => 'One of the languages is not allowed'),
		);
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

	public function populateFromModel(ModeratorModel $model) {
		$this->name = $model->name;
		$this->email = $model->email;
		$this->langs = $model->langs;
		$this->paypal = $model->paypal;
		$this->_id = $model->_id;
	}

	public function getModelClass() {
		return 'ModeratorModel';
	}
}