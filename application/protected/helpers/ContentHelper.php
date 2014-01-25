<?php
/**
 * User: eniquall
 * Date: 1/14/14
 * Time: 11:39 PM
 */

class ContentHelper {
	const CONTENT_TYPE_TEXT  = 1;
	const CONTENT_TYPE_IMAGE = 2;
	const CONTENT_TYPE_VIDEO = 3;
	const CONTENT_TYPE_AUDIO = 3;

	public static function getTypesList() {
		return array(
			self::CONTENT_TYPE_TEXT  => 'text',
			self::CONTENT_TYPE_IMAGE => 'image',
			self::CONTENT_TYPE_VIDEO => 'video',
			self::CONTENT_TYPE_AUDIO => 'audio'
		);
	}

	public static function getTypeNameByType($type) {
		$list = self::getAllowedTypesList();
		return $list[$type];
	}

	public static function getProfileTypesListByProject($id) {
		$moderationRuleNames = array();
		$moderationRules = ModerationRuleModel::model()->findAllByAttributes(array('projectId' => $id));


		foreach($moderationRules as $moderationRule) {
			$moderationRuleNames[] = $moderationRule->type;
		}

		return $moderationRules;
	}

	public static function getModerationRuleByProjectAndTypeName($projectId, $ruleTypeName) {
		$rule = new ModerationRuleModel();
		$rule->projectId = $projectId;
		$rule->type = $ruleTypeName;
		$rule->search(false); // caseSensitivity = false;

		return $rule->getData();


		$rule = ModerationRuleModel::model()->findByAttributes(
			array(
				'projectId'	=> $projectId,
				'type'		=> $ruleTypeName
			)
		);
		return $rule;
	}
} 