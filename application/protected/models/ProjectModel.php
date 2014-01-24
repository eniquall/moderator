<?php

/**
 * This is the MongoDB Document model class based on table "project".
 */
class ProjectModel extends CPModel {
	use BaseProfileValidationTrait;

	public $_id;
	public $apiKey;
	public $email;
	public $name;
	public $password;
	public $balance;
	public $notes;
	public $isActive;

	/**
	 * Returns the static model of the specified AR class.
	 * @return ProjectModel the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * returns the primary key field for this model
	 */
	public function primaryKey()
	{
		return '_id';
	}

	/**
	 * @return string the associated collection name
	 */
	public function getCollectionName()
	{
		return 'project';
	}

	public function getCollectionStructure()
	{
		return array('name' => 'project');
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('email, name, password', 'required'),
			array('isActive', 'numerical', 'integerOnly'=>true),
			array('balance', 'numerical'),
			array('isActive', 'in', 'range' => [0,1]),
			array('apiKey, password', 'length', 'is'=>32, 'on' => BaseProfileForm::EDIT_PROFILE_SCENARIO),
			array('email', 'uniqueEmail'),
			array('email', 'length', 'max'=>100),
			array('name', 'length', 'max'=>45),
			array('notes', 'length', 'max'=>1000),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('_id, apiKey, email, name, password, balance, notes, isActive', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'_id' => 'ID',
			'apiKey' => 'Api Key',
			'email' => 'Email',
			'name' => 'Name',
			'password' => 'Password',
			'balance' => 'Balance',
			'notes' => 'Notes',
			'isActive' => 'Is Active',
		);
	}
}