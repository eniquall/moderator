<?php
/**
 * Created by PhpStorm.
 * User: eniquall
 * Date: 1/15/14
 * Time: 1:13 AM
 */

class ModerationRuleForm extends CFormModel {
	use ModrationRuleValidationTrait;

	public $_id;
	public $projectId;
	public $type;
	public $text;
	public $level;

	const ADD_RULE_SCENARIO = 'add';
	const EDIT_RULE_SCENARIO = 'edit';

	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('projectId, type, level, text', 'required'),
			array('projectId', 'length', 'is'=>24),

			array('_id', 'required', 'on' => ModerationRuleForm::EDIT_RULE_SCENARIO),

			array('level', 'numerical', 'integerOnly' => true),
			array('level', 'Odd'),
			array('level', 'in', 'range' => [1,3,5,7,9]),

			//array('type', 'TypeAllowed', 'message' => 'Content type is not allowed'),
			array('type', 'UniqueRule', 'You already have rule of this type'),
			array('text', 'length', 'max'=>1000),
			array('type', 'length', 'max'=>200),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('_id, projectId, type, text, level', 'safe', 'on'=>'search'),
		);
	}

	public function getModelClass() {
		return 'ModerationRule';
	}

	public function populateFromModel(ModerationRuleModel $model) {
		$this->level = $model->level;
		$this->text = $model->text;
		$this->type = $model->type;
		$this->projectId = $model->projectId;
		$this->_id = $model->_id;
	}
}