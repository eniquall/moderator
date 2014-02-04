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

	public static function getModerationRuleByProjectNameAndTypeName($projectName, $ruleTypeName) {
		$criteria = new EMongoCriteria();
		$criteria->name = new MongoRegex('/' . trim($projectName) . '/i');
		$project = ProjectModel::model()->find($criteria);
		if (empty($project)) {
			return null;
		}

		$criteria = new EMongoCriteria();
		$criteria->projectId = $project->getId();
		$criteria->type = new MongoRegex('/' . trim($ruleTypeName) . '/i');
		$rule = ModerationRuleModel::model()->find($criteria);

		return $rule;
	}

	public static function getContentForModeration($moderatorId) {
		Yii::beginProfile(__METHOD__ . "_" . $moderatorId);

		$moderator = ModeratorModel::model()->findByPk(new MongoId($moderatorId));

		$criteria = new EMongoCriteria();
		// if it's regular moderator - add langs list to criteria
		// supermoderator can moderate content with any language

		if ($moderator->isSuperModerator != "1") {
			$langs = [];
			$allowedLangsList = LanguagesHelper::getAllowedLanguagesList();

			foreach($moderator->langs as $lang) {
				$langs[] = $allowedLangsList[$lang];
			}

			$criteria->addCond('lang', 'in', $langs);
		}

		// content doesn't have final status
		$criteria->addCond('reason', 'notin', [0,1]); // YMDS set default value null for it somehow
		$criteria->addCond('checkedDate', '<', time() - 3 * 60); // last check attempt was more than 3 minutes ago

		// stat array doesn't contains our moderatorId - content wasn't moderated by current moderator
		$criteria->addCond('stat.' . $moderatorId, 'exists', false); // stat array doesn't contains our moderatorId
		$criteria->limit(20);
		//according to the task

		$content = ContentModel::model()->findAll($criteria);

		if (empty($content)) {
			return null;
		}

		// get one item from array (sort them by projects)
		if (count($content) > 1 && !empty($moderator->projects)) {
			$contentArray = [];
			foreach($content as $contentItem) {
				$contentArray[$contentItem->projectId] = $contentItem;
			}

			$projects = $moderator->projects;

			$contentOfFamiliarProjects = array_intersect_key($contentArray, $projects);

			if (empty($contentOfFamiliarProjects)) {
				// moderator didn't moderaterd content of any of those project - we can
				// not choose "familiar" project - get first
				$contentItem = reset($content);
			} else {
				//choose the most familiar project
				$familiarProjectsFromContentList = array_intersect_key($projects, $contentArray);
				arsort($familiarProjectsFromContentList); // sort by amount of marks
				reset($familiarProjectsFromContentList);

				$contentItem = $contentOfFamiliarProjects[key($familiarProjectsFromContentList)];
			}
		} else {
			$contentItem = reset($content);
		}

		// set check time - so other moderator will not take it at the same time
		$contentItem->checkedDate = time();
		$contentItem->save();

		Yii::endProfile(__METHOD__ . "_" . $moderatorId);
		return $contentItem;
	}
}