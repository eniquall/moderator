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
			array('projectId, type, level', 'required'),
			array('projectId', 'length', 'is'=>24),
			array('level', 'numerical', 'integerOnly' => true),
			array('level', 'Odd'),

			array('type', 'TypeAllowed', 'message' => 'Content type is not allowed'),
			array('type', 'UniqueRule', 'You already have rule of this type'),
			array('text', 'length', 'max'=>1000),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('_id, projectId, type, text, level', 'safe', 'on'=>'search'),
		);
	}

	public function getModelClass() {
		return 'ModerationRule';
	}
}