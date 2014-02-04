<?php

/**
 * This is the MongoDB Document model class based on table "content".
 */
class ContentModel extends CPModel {
	public $_id;
	public $id; // external contentId
	public $projectId;
	public $type;
	public $lang;
	public $data;
	public $context;
	public $reason;
	public $isDelivered;
	public $stat;
	public $addedDate;
	public $checkedDate;
	public $reasonDate;

	/**
	 * Returns the static model of the specified AR class.
	 * @return ContentModel the static model class
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

	public function getCollectionStructure()
	{
		return array('name' => 'content');
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id, projectId, type, lang, addedDate', 'required'),
			array('reason, isDelivered', 'numerical', 'integerOnly'=>true),
			array('isDelivered', 'in', 'range' => [0,1]),
			array('id', 'length', 'max'=>100),
			array('type', 'length', 'max'=>45),
			array('projectId', 'isProjectExists'),
			array('stat', 'type', 'type'=>'array','allowEmpty'=>true),
			array('lang', 'length', 'max'=>2),
			array('checkedDate, reasonDate', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('_id, id, projectId, type, lang, data, context, reason, isDelivered, stat, addedDate, checkedDate, reasonDate', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'_id' => 'ID',
			'id' => 'id',
			'projectId' => 'Project Id',
			'type' => 'Type',
			'lang' => 'Lang',
			'data' => 'Data',
			'context' => 'Context',
			'result' => 'Result',
			'isDelivered' => 'Is Delivered',
			'stat' => 'Stat',
			'addedDate' => 'Added Date',
			'checkedDate' => 'Checked Date',
			'resultDate' => 'Result Date',
		);
	}

	public function isProjectExists ($attribute, $params) {
		$projectId = $this->attributes[$attribute];
		$project = ProjectModel::model()->findByPk(new MongoId($projectId));

		if (empty($project)) {
			$this->addError($attribute, 'Project with id  ' . $projectId . ' doesn\'t exists');
		}

		return !empty($project);
	}
}