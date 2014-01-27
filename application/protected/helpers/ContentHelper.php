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

	public static function getContentForModeration($moderatorId) {
		$moderator = ModeratorModel::model()->findByPk(new MongoId($moderatorId));

		$criteria = new EMongoCriteria();
		// if it's regular moderator - add langs list to criteria
		// supermoderator can moderate content with any language

		if ($moderator->isSuperModerator != "1") {
			$criteria->lang = ['in' => $moderator->langs];
		}

		// content doesn't have final status
		$criteria->reason = ['notExists'];
		$criteria->checkDate = ['>' => time() + 3 * 60]; // last check attempt was more than 3 minutes ago
		$criteria->offset(20);
		//according to task

		$content = ContentModel::model()->find($criteria);


		// get one item from array (sort them by projects)
		if (count($content) > 1) {
			$contentItem = reset($content);
		} else {
			$contentItem = $content;
		}

		// set check time - so other moderator will not take it at the same time
		$contentItem->checkDate = time();
		$contentItem->save();

		return $contentItem;
	}
}