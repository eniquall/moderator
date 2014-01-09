<?php
/**
 * User: eniquall
 * Date: 1/8/14
 * Time: 11:37 PM
 */

class ModeratorForm extends CFormModel {
	public $name;
	public $email;
	public $password;
	public $password2;
	public $langs;
	public $paypal;
	public $_id;

	public function init() {
		$scenario = empty($_id) ? "registration" : "edit";
		$this->setScenario($scenario);
	}

	public function rules() {
		return array(
			array('name, email, password, password2, paypal', 'required'),
			array('_id', 'required', 'on' => 'edit'),
			array('name', 'length', 'min' => 2),
			array('email', 'email', 'allowEmpty' => false),
			array('email', 'uniqueEmail'),
			array('password', 'length', 'min' => 6),
			array('password2', 'compare', 'compareAttribute' => 'password'),
			//array('languages', 'in', 'strict' => true, 'range' => array_keys(LanguagesHelper::$allowedLanguagesList()), 'message' => 'Choose one or more languages from the list'),
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

}