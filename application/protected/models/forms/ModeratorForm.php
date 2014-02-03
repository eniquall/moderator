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

	public $notes;
	public $isActive;
	public $isSuperModerator;

	public function rules() {
		return array(
			array('name, notes','filter','filter'=>array($obj=new CHtmlPurifier(),'purify')),
			array('name, notes','filter','filter'=>'strip_tags'),

			array('email', 'length', 'max'=>100),
			array('name', 'length', 'max'=>45),

			array('name, email', 'required'),
			array('paypal', 'length', 'max' => 150, 'on' => self::EDIT_PROFILE_SCENARIO),
			array('password', 'required', 'on' => self::REGISTRATION_SCENARIO),

			array('_id', 'required', 'on' => 'edit'),
			array('name', 'length', 'min' => 2),
			array('email', 'email', 'allowEmpty' => false),
			array('email', 'uniqueEmail'),
			array('password', 'length', 'min' => 6),

			array('password', 'checkForNewPassword', 'on' => self::EDIT_PROFILE_SCENARIO),
			array('password2', 'compare', 'compareAttribute' => 'password', 'on' => self::REGISTRATION_SCENARIO),

			array('newPassword', 'length', 'min' => 6, 'on' => self::EDIT_PROFILE_SCENARIO),
			array('newPassword2', 'compare', 'compareAttribute' => 'newPassword', 'on' => self::EDIT_PROFILE_SCENARIO),

			array('langs','type','type'=>'array','allowEmpty' => false, 'message' => 'Choose at least one language from the list'),
			array('langs', 'LanguageAllowed', 'message' => 'One of the languages is not allowed'),

			array('isActive, isSuperModerator', 'in', 'range' => [0,1]),
			array('notes', 'length', 'max'=>1000),
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

	public function populateFromModel(ModeratorModel $model) {
		$this->name = $model->name;
		$this->email = $model->email;
		$this->langs = $model->langs;
		$this->paypal = $model->paypal;
		$this->_id = $model->_id;

		$this->notes = $model->notes;
		$this->isActive = $model->isActive;
		$this->isSuperModerator = $model->isSuperModerator;
	}

	public function getModelClass() {
		return 'ModeratorModel';
	}
}