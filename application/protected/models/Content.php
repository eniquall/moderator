<?php

/**
 * This is the MongoDB Document model class based on table "content".
 */
class Content extends CPModel {
	public $_id;
	public $uid;
	public $project;
	public $type;
	public $lang;
	public $data;
	public $context;
	public $result;
	public $isDelivered;
	public $stat;
	public $createdDate;
	public $checkedDate;
	public $resultDate;

	/**
	 * Returns the static model of the specified AR class.
	 * @return Content the static model class
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
		return 'content';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('uid, project, type, lang, createdDate', 'required'),
			array('result, isDelivered', 'numerical', 'integerOnly'=>true),
			array('uid', 'length', 'max'=>100),
			array('project, type, data, context, stat', 'length', 'max'=>45),
			array('lang', 'length', 'max'=>2),
			array('checkedDate, resultDate', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('_id, uid, project, type, lang, data, context, result, isDelivered, stat, createdDate, checkedDate, resultDate', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'_id' => 'ID',
			'uid' => 'Uid',
			'project' => 'Project',
			'type' => 'Type',
			'lang' => 'Lang',
			'data' => 'Data',
			'context' => 'Context',
			'result' => 'Result',
			'isDelivered' => 'Is Delivered',
			'stat' => 'Stat',
			'createdDate' => 'Created Date',
			'checkedDate' => 'Checked Date',
			'resultDate' => 'Result Date',
		);
	}
}