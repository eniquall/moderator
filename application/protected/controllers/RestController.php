<?php
/**
 * User: dna
 * Date: 1/11/14
 * Time: 1:58 AM
 */

class RestController extends BaseRestController {
	/**
	 * The main action of the rest interface - adds new content and returns info about moderated content by project
	* /* See /?r=static/apiInstruction page for details
	 *
	 * @param $apikey
	 * @param $data - is JSON object
	 */
	public function actionIndex() {
		$apikey = Yii::app()->request->getParam('apiKey');
		$data = !empty($_POST['data']) ? $_POST['data'] : null;

		Yii::log('API Request from ' . $apikey, CLogger::LEVEL_TRACE, 'RestController.API');

		$project = $this->getProjectByApiKey($apikey);

		if (empty($project)) {
			Yii::log("ApiKey {$apikey} is not found!", CLogger::LEVEL_ERROR, 'RestController.API');
			$this->_respondError(403, self::ERROR_PROJECT_NOT_FOUND_BY_KEY, $apikey);
		}

		if (!empty($data)) {
			$decodedData = CJSON::decode($data);
			foreach($decodedData as $contentByUser) {
				// check content json format by each user
				if ($this->_checkData($contentByUser, $project, $apikey)) {
					//if everything ok - add it into storage
					$this->_addContent($contentByUser, $project);
				}
			}
		}

		// send errors information or moderated content
		if ($this->hasErrors()) {
			$status = 500;
			$responseBody = $this->_prepeareErrorData();
		} else if (empty($responseBody)) {
			$status = 200;
			$responseBody = $this->_prepareResponseBody($project->getId());
		}
		$this->_respond($status, $responseBody);
	}

	public function getProjectByApiKey($apiKey) {
		$criteria = new EMongoCriteria();
		$criteria->apiKey = $apiKey;
		$criteria->addCond('isActive', 'in', [1, '1']);
		$project = ProjectModel::model()->find($criteria);

		return $project;
	}

	protected function _checkData($contentByUser, $project, $apiKey) {

		if ( !is_array($contentByUser) ||
			empty($contentByUser['id']) ||
			empty($contentByUser['project']) ||
			empty($contentByUser['type']) ||
			empty($contentByUser['data']) ||
			!is_array($contentByUser['data'])) {

			Yii::log("Content is not well formatted: " . print_r($contentByUser, true), CLogger::LEVEL_ERROR, 'RestController._checkData');
			$this->_addError(self::ERROR_INVALID_CONTENT_DATA_FORMAT, ['id' => $contentByUser['id']]);
			return false;
		}

		// check uniqueContentId
		if (ContentModel::model()->findByAttributes(['id' => $contentByUser['id']])) {
			Yii::log("Content with such ID already exists: " . print_r($contentByUser, true), CLogger::LEVEL_ERROR, 'RestController._checkData');
			$this->_addError(self::ERROR_DUPLICATED_CONTENT_ID, ['content' => $contentByUser]);
		}

		if (mb_strtolower($project->name) !== mb_strtolower($contentByUser['project'])) {
			Yii::log("ApiKey mismatched: {$project->name} " . print_r($contentByUser, true), CLogger::LEVEL_ERROR, 'RestController._checkData');
			$this->_addError(self::ERROR_APIKEY_DOESNT_BELONGS_TO_PROJECT, ['id' => $apiKey, 'project' => $contentByUser['project']]);
		}

		$moderatorRule = ContentHelper::getModerationRuleByProjectIdAndTypeName($project->getId(), $contentByUser['type']);
		if (empty($moderatorRule)) {
			Yii::log("No Moderation Rule is provided for this Content: " . print_r($contentByUser, true), CLogger::LEVEL_ERROR, 'RestController._checkData');
			$this->_addError(self::ERROR_MODERATION_RULE_NOT_FOUND, $contentByUser);
			return false;
		}
		return true;
	}

	protected function _addContent($contentByUser, $project) {
		Yii::beginProfile(__METHOD__);
		$contentModel = new ContentModel();

		$contentModel->data = $contentByUser['data'];
		$contentModel->id = $contentByUser['id'];
		$contentModel->projectId = $project->getId();
		$contentModel->type = $contentByUser['type']; // check type ?
		$contentModel->lang = mb_strtolower($contentByUser['lang']); // check
		$contentModel->context = $contentByUser['context']; // check
		$contentModel->lang = $contentByUser['lang']; // check
		$contentModel->isDelivered = 0; // check
		$contentModel->addedDate = time(); // check
		$contentModel->checkedDate = 0; // should be set as we will try to find content with cond: > time() - 3 * 60
		// reason shouldn't be set here

		$result = $contentModel->save();
		Yii::endProfile(__METHOD__);
		return $result;
	}

	protected function _getModeratedContentByProjectForResponse($projectId) {
		Yii::beginProfile(__METHOD__);
		$criteria = new EMongoCriteria();
		$criteria->addCond('projectId', '==', $projectId);
		$criteria->addCond('reason', 'in', [0,1]);
		$criteria->addCond('isDelivered', 'notin', [1,'1']);

		$content = ContentModel::model()->findAll($criteria);

		$response = [];
		foreach($content as $contentItem) {
			$contentItemArray = [];
			$contentItemArray['id'] = $contentItem->getId();
			$contentItemArray['status'] = ((int) $contentItem->reason) ? 'allow' : 'disallow' ;
			$contentItemArray['type'] = $contentItem->type;

			$yes = 0;//array_sum($contentItem->stat);
			$no = 1;//count($contentItem->stat) - $yes;
			$contentItemArray['stat'] = array(
				'allow' => $yes,
				'disallow' => $no
			);
			$response[] = $contentItemArray;
		}

		ContentModel::model()->updateAll(
			new EMongoModifier(
				[
					'isDelivered' => ['set' => 1]
				]
			),
			$criteria
		);

		Yii::endProfile(__METHOD__);
		return $response;
	}

	protected function _prepareResponseBody($projectId) {
		$moderatedContent = $this->_getModeratedContentByProjectForResponse($projectId);

		Yii::log('Preparing Response Body for ' . $projectId . ', count: ' . count($moderatedContent), CLogger::LEVEL_TRACE, 'RestController._prepareResponseBody');

		return CJSON::encode($moderatedContent);
	}
}