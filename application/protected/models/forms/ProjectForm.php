<?php
/**
 * User: dna
 * Date: 1/12/14
 * Time: 5:23 PM
 */

class ProjectForm extends BaseProfileForm {
	public $_id;
	public $email;
	public $name;
	public $password;
	public $password2;

	public $newPassword;
	public $newPassword2;

	public $isActive;
	public $notes;
	public $balance;

	public function rules() {
		return array(
			array('name, notes','filter','filter'=>array($obj=new CHtmlPurifier(),'purify')),
			array('name, notes','filter','filter'=>'strip_tags'),

			array('name, email', 'required'),
			array('password', 'required', 'on' => self::REGISTRATION_SCENARIO),

			array('_id', 'required', 'on' => self::EDIT_PROFILE_SCENARIO),
			array('name', 'length', 'min' => 2),
			array('email', 'email', 'allowEmpty' => false),
			array('email', 'uniqueEmail'),
			array('name', 'uniqueName'),

			array('email', 'length', 'max'=>100),
			array('name', 'length', 'max'=>45),
			array('notes', 'length', 'max'=>1000),

			array('password', 'length', 'min' => 6),

			array('password', 'checkForNewPassword', 'on' => self::EDIT_PROFILE_SCENARIO),
			array('password2', 'compare', 'compareAttribute' => 'password', 'on' => self::REGISTRATION_SCENARIO),

			array('newPassword', 'length', 'min' => 6, 'on' => self::EDIT_PROFILE_SCENARIO),
			array('newPassword2', 'compare', 'compareAttribute' => 'newPassword', 'on' => self::EDIT_PROFILE_SCENARIO),
			array('isActive', 'in', 'range' => [0,1]),

			array('balance', 'numerical', 'min' => 0, 'on' => self::EDIT_PROFILE_SCENARIO),
		);
	}

	public function attributeLabels() {
		return array(
			'name'=>'Project name',
			'email'=>'Email',
			'password'=>'Password',
			'password2'=>'Enter password again',
		);
	}

	public function populateFromModel(ProjectModel $model) {
		$this->name = $model->name;
		$this->email = $model->email;
		$this->_id = $model->_id;
		$this->balance = $model->balance;
		$this->notes = $model->notes;
		$this->isActive = $model->isActive;
	}

	public function getModelClass() {
		return 'ProjectModel';
	}
}