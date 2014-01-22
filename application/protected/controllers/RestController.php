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
	 *
	 * http://moderator.local/api/?apiKey=12344567890&data={}
	 */
	public function actionIndex($apiKey, $data) {
		$data = <<<DATA
[{
"id": "123",
"projectId": "70fdf952f7fb67e5177121b9d1530d73",
"type": "profile",
"lang": "ru",
"data": [
    {"img": "url" },
    {"text": "я стою на утесе и машу писей ветру!!!"}
],
"context": [
    {"text": "Василий,25"},
    {"text": "12 жалоб"}
]
}]
DATA;

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
		$moderatedContent = $this->_getModeratedContentByProject();
		$this->_respond(200, 'ok', $moderatedContent);
	}

	protected function _respond() {

	}

	public function getProjectByApiKey($apiKey) {
		$criteria = new EMongoCriteria();
		$criteria->apiKey = $apiKey;
		$criteria->isActive = '1';
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
	}

	protected function _addContent(stdClass $contentByUser) {

		foreach($contentByUser->data as $contentType => $contentValue) {
			//img, text ...
//			switch ($contentType) {
//				case ContentHelper::CONTENT_TYPE_TEXT:
//					break;
//
//				case ContentHelper::CONTENT_TYPE_IMAGE:
//					break;
//
//				case ContentHelper::CONTENT_TYPE_IMAGE_AND_TEXT:
//					break;
//
//				default:
//			}

			// in_array()
		}
	}

	protected function _getModeratedContentByProject() {
		return true;
	}
}