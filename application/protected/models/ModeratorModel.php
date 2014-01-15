<?php
class ModeratorModel extends CPModel {
	use BaseProfileTrait;

	public $_id;
	public $name;
	public $email;
	public $password;
	
	public $langs;
	public $projects;
	public $paypal;
	
	public $notes;
	public $isActive;
	public $isSuperModerator;
	public $createDate;
	public $lastActivity;

	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	public function getCollectionName()
	{
		return 'moderator';
	}

	public function getCollectionStructure()
	{
		return array('name' => 'moderator');
	}

	/**
	 * returns the primary key field for this model
	 */
	public function primaryKey()
	{
		return '_id';
	}
	
	public function indexes()
	{
		return array(
			//'location' => array(
			//	'key' => array(
			//		'location' => '2d'
			//	)
			//)
		);
	}
   
	
	public function attributeLabels() 
	{
		return array(
				'_id'	  => 'ID',
				'name'	 => 'Name',
		);
	}
	
	public function rules() 
	{
		return array(
			array('name, email, password, langs, paypal', 'required'),
			array('email', 'uniqueEmail'),
			array('isActive, isSuperModerator', 'in', 'range' => [0,1]),
		);
	}
}