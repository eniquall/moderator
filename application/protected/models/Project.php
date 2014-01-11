<?php
/**
 * User: dna
 * Date: 1/11/14
 * Time: 1:38 AM
 */

class Project extends CPModel {
	public $name;
	public $email;
	public $password;

	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	public function getCollectionName()
	{
		return 'project';
	}

	public function getCollectionStructure()
	{
		return array('name' => 'project');
	}

	public function getMongoDBComponent()
	{
		return Yii::app()->getComponent('db');
	}
}