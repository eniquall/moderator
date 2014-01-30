<?php
/**
 * User: dna
 * Date: 1/11/14
 * Time: 1:58 AM
 */

class RestController extends Controller{
	// main action of the rest interface - adds new content and returns info about moderated content by project
	/**
	 * @param $apiKey
	 * @param $data - is JSON object in format
	 * {
		id: “1234567890”,   ­ уникальный идентификатор контента,  произвольный набор символов до 128знаков
		“projectId” : “123342”,   ­ идентификатор проекта 24 hex of the mondoId object
		type: “profile”,  ­ тип контента
		lang: “ru”,   ­ язык (опционально)
		data: [   ­ содержимое. Произвольный набор строк с указанием типа  text, img, audio, video,
			 {“img”: url },
			 {“text”: “я стою на утесе и машу писей ветру!!!”}
		],
		context: [	­ контекст в котором создан или показан контент, произвольный набор строк
			  {“text”: “Василий,25”},
			  {“text”: “12 жалоб”}
		]
	},
	{..}
	 *
	 * http://moderator.local/?r=rest/index/apiKey/4bfa4f88d767379953074aed37f140e4/data/{}
	 */
	public function actionIndex($apiKey) {
		$data = Yii::app()->request->getParam('data');
		// need to change to POST after testing

		$project = $this->getProjectByApiKey($apiKey);

		if (empty($project)) {
			$this->_generateError(403, 'Project with this apiKey (' . $apiKey . ') was not found. Project doesn\'t exist or inactive');
		}

		$decodedData = CJSON::decode($data);

		foreach($decodedData as $contentByUser) {
			// check content json format by each user
			if ($this->_checkData($contentByUser)) {
				//if everything ok - add it into storage
				$this->_addContent($contentByUser);
			}
		}
		$moderatedContent = $this->_getModeratedContentByProject($project->getId());
		$this->_respond(200, 'ok', $moderatedContent);
	}

	protected function _respond($code, $status, $moderatedContent) {
		echo CJSON::encode($moderatedContent);
	}

	public function getProjectByApiKey($apiKey) {
		$criteria = new EMongoCriteria();
		$criteria->apiKey = $apiKey;
		$criteria->addCond('isActive', 'in', [1, '1']);
		$project = ProjectModel::model()->find($criteria);

		return $project;
	}

	protected function _generateError($httpErrorCode, $message) {
		throw new CHttpException($httpErrorCode, $message);
		//$this->_respond();
	}

	protected function _checkData($contentByUser) {

		if ( !is_array($contentByUser) ||
			empty($contentByUser['id']) ||
			empty($contentByUser['projectId']) ||
			empty($contentByUser['type']) ||
			empty($contentByUser['data']) ||
			!is_array($contentByUser['data'])) {
			Yii::log("Invalide format of user content: " . CJSON::encode($contentByUser)
				. ' Check your data request format. id, projectId, type, data are required, data - is an array', CLogger::LEVEL_ERROR);
			return false;
			//$this->_generateError(500, 'Check your data request format. id, projectId, type, data are required, data - is an array');
		}

		$moderatorRule = ContentHelper::getModerationRuleByProjectIdAndTypeName($contentByUser['projectId'], $contentByUser['type']);
		if (empty($moderatorRule)) {
			Yii::log("Moderation rule was not found for content: " . CJSON::encode($contentByUser), CLogger::LEVEL_ERROR);
		}
		return true;
	}

	protected function _addContent($contentByUser) {
		Yii::beginProfile(__METHOD__);
		$contentModel = new ContentModel();

		$contentModel->data = $contentByUser['data'];
		$contentModel->id = $contentByUser['id'];
		$contentModel->projectId = $contentByUser['projectId'];
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

	protected function _getModeratedContentByProject($projectId) {
		$criteria = new EMongoCriteria();
		$criteria->addCond('projectId', '==', $projectId);
		$criteria->addCond('reason', 'in', [0,1]);

		$content = ContentModel::model()->findAll($criteria);


		$response = [];
		foreach($content as $contentItem) {
			$contentItemArray = [];
			$contentItemArray['id'] = $contentItem->getId();
			$contentItemArray['status'] = ((int) $contentItem->reason) ? 'allow' : 'disallow' ;

			$yes = array_sum($contentItem->stat);
			$no = count($contentItem->stat) - $yes;
			$contentItemArray['stat'] = array(
				'allow' => $yes,
				'disallow' => $no
			);
			$response[] = $contentItemArray;
		}

		return $response;
	}
}