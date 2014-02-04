<?php

class BaseRestController extends Controller {
	protected $_errors;

	const ERROR_PROJECT_NOT_FOUND_BY_KEY = 1001;
	const ERROR_INVALID_CONTENT_DATA_FORMAT = 1002;
	const ERROR_APIKEY_DOESNT_BELONGS_TO_PROJECT = 1003;
	const ERROR_MODERATION_RULE_NOT_FOUND = 1004;
	const ERROR_DUPLICATED_CONTENT_ID = 1005;

	public function init()
	{
		parent::init();

		Yii::app()->attachEventHandler('onError',array($this,'handleError'));
		Yii::app()->attachEventHandler('onException',array($this,'handleError'));
	}

	public function handleError(CEvent $event) {
		$statusCode = 500;
		if ($event instanceof CExceptionEvent) {
			$statusCode = $event->exception->statusCode;
			$body = array(
				'code' => $event->exception->getCode(),
				'message' => $event->exception->getMessage(),
				'file' => YII_DEBUG ? $event->exception->getFile() : '*',
				'line' => YII_DEBUG ? $event->exception->getLine() : '*'
			);
		} elseif ($event instanceof CErrorEvent) {
			$body = array(
				'code' => $event->code,
				'message' => $event->message,
				'file' => YII_DEBUG ? $event->file : '*',
				'line' => YII_DEBUG ? $event->line : '*'
			);
		}

		$event->handled = TRUE;

		$this->_respond($statusCode, CJSON::encode($body));
	}

	protected function _respond($status = 200, $body, $content_type = 'application/json') {
		$status_header = 'HTTP/1.1 ' . $status . ' ' . $this->_getStatusCodeMessage($status);
		header($status_header);
		header('Content-type: ' . $content_type);

		echo $body;
		Yii::app()->end();
	}

	protected function _addError($code, $additionalData) {
		$this->_errors[$code][] = $this->getMessageByErrorCode($code) . ' ' . CJSON::encode($additionalData);
	}

	protected function _respondError($status, $code, $additionalData) {
		$this->_addError($code, $additionalData);
		$this->_respond($status, $this->_prepeareErrorData());
	}

	public function getApiErrorCodes() {
		return [
			self::ERROR_PROJECT_NOT_FOUND_BY_KEY => 'Project with this apiKey was not found. Project doesn\'t exist or inactive',
			self::ERROR_INVALID_CONTENT_DATA_FORMAT => 'Invalide format of user content.  Check your data request format. id, project, type, data are required, data - is an array',
			self::ERROR_APIKEY_DOESNT_BELONGS_TO_PROJECT => 'ApiKey doestn\'t belongs to project',
			self::ERROR_MODERATION_RULE_NOT_FOUND => 'Moderation rule was not found for content',
			self::ERROR_DUPLICATED_CONTENT_ID => 'Content with this id already was posted'
		];
	}

	public function getMessageByErrorCode($code) {
		$messages = $this->getApiErrorCodes();
		return isset($messages[$code]) ? $messages[$code] : '';
	}

	public function _getStatusCodeMessage($status) {
		$codes = Array(
			200 => 'OK',
			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
		);

		return (isset($codes[$status])) ? $codes[$status] : '';
	}

	protected function _prepeareErrorData() {
		return $this->_errors
			? CJSON::encode(['ApiErrors' => $this->_errors])
			: '';
	}
	public function hasErrors() {
		return !empty($this->_errors);
	}
}