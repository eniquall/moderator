<?php

/**
 * This is the MongoDB Document model class based on table "project".
 */
class Project extends CPModel {
	public $_id;
	public $project;
	public $apiKey;
	public $email;
	public $name;
	public $password;
	public $balance;
	public $notes;
	public $isActive;

	/**
	 * Returns the static model of the specified AR class.
	 * @return Project the static model class
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

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('_id, project, apiKey, email, name, password', 'required'),
			array('_id, balance, isActive', 'numerical', 'integerOnly'=>true),
			array('isActive', 'in', 'range' => [0,1]),
			array('project', 'length', 'max'=>50),
			array('apiKey, password', 'length', 'max'=>32),
			array('email', 'length', 'max'=>100),
			array('name', 'length', 'max'=>45),
			array('notes', 'length', 'max'=>1000),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('_id, project, apiKey, email, name, password, balance, notes, isActive', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'_id' => 'ID',
			'project' => 'Project',
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