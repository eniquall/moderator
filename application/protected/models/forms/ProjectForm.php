<?php
/**
 * User: dna
 * Date: 1/12/14
 * Time: 5:23 PM
 */

class ProjectForm extends CFormModel {
	public $_id;
	public $project;
	public $email;
	public $name;
	public $password;
	public $password2;

	public $newPassword;
	public $newPassword2;

	const REGISTRATION_SCENARIO = 'registration';
	const EDIT_PROFILE_SCENARIO = 'edit';

	public function rules() {
		return array(
			array('name, email, project', 'required'),
			array('password', 'required', 'on' => self::REGISTRATION_SCENARIO),

			array('_id', 'required', 'on' => 'edit'),
			array('name, project', 'length', 'min' => 2),
			array('email', 'email', 'allowEmpty' => false),
			array('email', 'uniqueEmail', 'on' => self::REGISTRATION_SCENARIO),
			array('password', 'length', 'min' => 6),

			array('password', 'checkForNewPassword', 'on' => self::EDIT_PROFILE_SCENARIO),
			array('password2', 'compare', 'compareAttribute' => 'password', 'on' => self::REGISTRATION_SCENARIO),

			array('newPassword', 'length', 'min' => 6, 'on' => self::EDIT_PROFILE_SCENARIO),
			array('newPassword2', 'compare', 'compareAttribute' => 'newPassword', 'on' => self::EDIT_PROFILE_SCENARIO),
		);
	}

	public function uniqueEmail($attribute, $params) {
		$email = $this->attributes[$attribute];

		// check email for new moderator
		if (!ProfileHelper::isEmailUnique('Project', $email, true)) {
			$this->addError($attribute, 'Email ' . $email . ' already exists');
		}
	}
	public function attributeLabels() {
		return array(
			'name'=>'Your name',
			'email'=>'Email',
			'password'=>'Password',
			'password2'=>'Enter password again',
			'project' => 'Your project name'
		);
	}

	public function populateFromModel(Project $model) {
		$this->name = $model->name;
		$this->email = $model->email;
		$this->project = $model->paypal;
		$this->_id = $model->_id;
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
}