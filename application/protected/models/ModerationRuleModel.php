<?php

/**
 * This is the MongoDB Document model class based on table "moderationRule".
 */
class ModerationRuleModel extends CPModel {
	use ModrationRuleValidationTrait;

	public $_id;
	public $projectId;
	public $type;
	public $text;
	public $level;

	/**
	 * Returns the static model of the specified AR class.
	 * @return ModerationRuleModel the static model class
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

	public function getCollectionStructure()
	{
		return array('name' => 'moderationRule');
	}
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('projectId, type', 'required'),
			array('projectId', 'length', 'is'=>24),
			array('level', 'numerical', 'integerOnly'=>true),
			array('level', 'Odd'),

			//array('type', 'TypeAllowed', 'message' => 'Content type is not allowed'),
			array('type', 'UniqueRule', 'You already have rule of this type'),

			array('text', 'length', 'max'=>1000),
			array('type', 'length', 'max'=>200),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('_id, projectId, type, text, level', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'_id' => 'ID',
			'projectId' => 'Project id',
			'type' => 'Type',
			'text' => 'Text',
			'level' => 'Level',
		);
	}
}