<?php

/**
 * This is the MongoDB Document model class based on table "moderationRule".
 */
class ModerationRule extends CPModel {
	public $_id;
	public $project;
	public $type;
	public $text;
	public $level;

	/**
	 * Returns the static model of the specified AR class.
	 * @return ModerationRule the static model class
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
		return 'moderationRule';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('project, type', 'required'),
			array('level', 'numerical', 'integerOnly'=>true),
			array('project, type', 'length', 'max'=>45),
			array('text', 'length', 'max'=>1000),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('_id, project, type, text, level', 'safe', 'on'=>'search'),
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
			'type' => 'Type',
			'text' => 'Text',
			'level' => 'Level',
		);
	}
}