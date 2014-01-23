<?php
class AdminModel extends CPModel {
	use BaseProfileValidationTrait;

	public $_id;
	public $name;
	public $email;
	public $password;

	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	public function getCollectionName()
	{
		return 'admin';
	}

	public function getCollectionStructure()
	{
		return array('name' => 'admin');
	}

	/**
	 * returns the primary key field for this model
	 */
	public function primaryKey()
	{
		return '_id';
	}

	public function attributeLabels()
	{
		return array(
			'_id'	=> 'ID',
			'email'	=> 'Email',
			'password'	=> 'Password',
		);
	}

	public function rules()
	{
		return array(
			array('email, password, name', 'required'),
		);
	}
}