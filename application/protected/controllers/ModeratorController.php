<?php
/**
 * User: eniquall
 * Date: 1/8/14
 * Time: 11:29 PM
 */

class ModeratorController extends BaseProfileController {
	public function getLoginUserRole() {
		return UserIdentity::MODERATOR_ROLE;
	}

	public function init() {
		Yii::app()->user->loginUrl = $this->createUrl('/moderator/login');
	}

	public function accessRules() {
		return array(
			array('deny',
				'actions'=>array('EditProfile', 'Moderate', 'Statistics', 'AddModerationRule', 'EditModerationRule'),
				'users'=>array('?'),
			),
			array('deny',
				'actions'=>array('Registration'),
				'users'=>array('@'),
			),
			array('allow',
				'actions'=>array('EditProfile', 'AddModerationRule', 'EditModerationRule', 'Statistics'),
				'roles'=>array(UserIdentity::PROJECT_ROLE, UserIdentity::ADMIN_ROLE),
			),
		);
	}

	public function getAfterLoginUrl(){
		if (Yii::app()->user->isAdmin()) {
			return $this->createUrl('/admin/showModeratorsList');
		}
		return $this->createUrl('/moderator/moderate');
	}

	public function actionRegistration() {
		$formModel = new ModeratorForm(BaseProfileForm::REGISTRATION_SCENARIO);

		if (isset($_POST['ModeratorForm'])) {
			$formModel->attributes = $_POST['ModeratorForm'];
			if ($formModel->validate()) {
				if ($this->_saveModeratorProfile($formModel, new ModeratorModel())) {
					$this->redirect($this->getAfterLoginUrl());
				}
			}
		}
		$this->render('registration', array('model' => $formModel));
	}

	/**
	 * Edit profile of the current moderator
	 */
	public function actionEditProfile($id) {
		$formModel = new ModeratorForm(BaseProfileForm::EDIT_PROFILE_SCENARIO);
		$this->_checkMongoIdParameter($id, 'id');
		$this->_checkPermissionForEditProfile($id);

		$moderator = ModeratorModel::model()->findByPk(new MongoId($id));

		if (empty($moderator)) {
			throw new CException(404, 'Profile with this id was not found');
		}

		if (isset($_POST['ModeratorForm'])) {

			$formModel->attributes = $_POST['ModeratorForm'];
			if ($formModel->validate()) {
				if ($this->_saveModeratorProfile($formModel, $moderator)) {
					$this->redirect($this->getAfterLoginUrl());
				}
			}
		} else {
			$formModel->populateFromModel($moderator);
		}

		$this->render('editProfile', array('model' => $formModel));
	}

	public function _saveModeratorProfile(ModeratorForm $formModel, ModeratorModel $moderator) {
		if ($formModel->getScenario() == BaseProfileForm::EDIT_PROFILE_SCENARIO) {
			// save new password if it was entered
			// additional validations were performed with ModeratorForm validations (checkForNewPassword)
			if (!empty($formModel->newPassword)) {
				$moderator->password = SecurityHelper::generatePasswordHash($formModel->newPassword);
			}
			$moderator->paypal = $formModel->paypal;

		} else if ($formModel->getScenario() == BaseProfileForm::REGISTRATION_SCENARIO){
			// save md5 hash of password
			$moderator->password = SecurityHelper::generatePasswordHash($formModel->password);
			$moderator->createDate = time();
		}

		$moderator->setScenario($formModel->getScenario());

		$moderator->name  = $formModel->name;
		$moderator->email = mb_strtolower($formModel->email);

		$moderator->langs  = $formModel->langs;

		//fields can be changed by admin only
		if (Yii::app()->user->isAdmin()) {
			$moderator->notes = $formModel->notes;
			$moderator->isActive = $formModel->isActive;
			$moderator->isSuperModerator = $formModel->isSuperModerator;
		}

		$result = $moderator->save();
		if (!$result) {
			Yii::log(__CLASS__ . " " . __METHOD__ .  "Can't save moderator profile (scenario: " . $formModel->getScenario() . "): " .
				CJSON::encode($moderator->getAttributes()) . " " . CJSON::encode($moderator->getErrors()));
		} else {
			Yii::app()->user->setFlash('success', 'Profile successfully saved');
		};

		return $result;
	}
	/**
	 * The main action for moderating content
	 */
	public function actionModerate() {
		// approving
		if (isset($_POST['moderateForm'], $_POST['moderateForm']['contentId'], $_POST['moderateForm']['approveResult'])) {
			$contentId = $_POST['moderateForm']['contentId'];
			$approveResult = (int) $_POST['moderateForm']['approveResult'];

			$content = ContentModel::model()->findByPk(new MongoId($contentId));
			if (empty($content)) {
				Yii::log(__METHOD__ . ' Content with id ' . $contentId . 'was not found' , CLogger::LEVEL_ERROR);
				$this->redirect(array('moderator/moderate')); // redirect to page without POST params to avoid multi approve actions
			}

			//check if moderator can work with this content
			$moderator = Yii::app()->user->getModel();
			$moderationRule = ContentHelper::getModerationRuleByProjectIdAndTypeName($content->projectId, $content->type);

			if (empty($moderationRule)) {
				throw new CHttpException(500, __METHOD__ . 'Can\'t find moderation rule (contentId = ' . $contentId . ' type = ' . $content->type . ')' );
			}

			$canModerate = $this->_checkIfModeratorCanModerate($content, $moderator, $moderationRule);
			if ($canModerate) {
				$this->_saveApproveMark($content, $moderator, $moderationRule, $approveResult);
			}

			$this->redirect(array('moderator/moderate'));
		}

		// get Next Content
		$moderatorId = Yii::app()->user->getId();
		$project = $moderationRule = null;
		$content = ContentHelper::getContentForModeration($moderatorId);

		if ($content) {
			$moderationRule = ContentHelper::getModerationRuleByProjectIdAndTypeName($content->projectId, $content->type);
			if (empty($moderationRule)) {
				Yii::log(__METHOD__ . ' Moderation rule was not found for content ' . CJSON::encode($content->getAttributes()), CLogger::LEVEL_ERROR);
			}
			$project = ProjectModel::model()->findByPk(new MongoId($content->projectId));
		}

		$this->render('moderate',
			array(
				'content' => $content,
				'project' => $project,
				'moderationRule' => $moderationRule
			)
		);
	}

	protected function _checkIfModeratorCanModerate(ContentModel $content, ModeratorModel $moderator, ModerationRuleModel $moderationRule) {
		Yii::beginProfile(__METHOD__);

		$errorMessage = '';

		$level = $moderationRule->level;
		if (count($content->stat) >= $level) {
			$errorMessage = 'because amount of marks for content reached the level of moderationRule ' . $level;
		}

		if ($moderator->isSuperModerator != "1") {
			$moderatorLangs = [];
			$allowedLangsList = LanguagesHelper::getAllowedLanguagesList();
			foreach($moderator->langs as $lang) {
				$moderatorLangs[] = $allowedLangsList[$lang];
			}

			if (!in_array(mb_strtolower($content->lang), $moderatorLangs)) {
				$errorMessage = 'moderator cannot moderate content on the ' . $content->lang . ' language';
			}
		}

		$stat = array_keys((array)$content->stat); // array with statistics
		if (in_array($moderator->getId(), $stat)) {
			$errorMessage = 'because he/she have moderated this content previously';
		}

		Yii::endProfile(__METHOD__);
		if (empty($errorMessage)) {
			return true;
		} else {
			$errorMessage = __METHOD__ . ' Moderator ' . $moderator->getId() . ' cant moderate content '
				. $content->getId() . ' ' . $errorMessage;
			Yii::log($errorMessage, CLogger::LEVEL_ERROR);
			return false;
		}
	}

	protected function _saveApproveMark(ContentModel $content, ModeratorModel $moderator, ModerationRuleModel $moderationRule, $approveResult) {
		$content->stat[$moderator->getId()] = $approveResult; // add statistics to content

		if (count($content->stat) >= $moderationRule->level) {
			// enough votes - calculate final result
			$yesVotes = array_sum($content->stat);
			$allVotes = count($content->stat);

			if ($yesVotes > ($allVotes / 2)) {
				// final result - yes
				$content->reason = 1;
			} else {
				// final result - no
				$content->reason = 0;
			}
			$content->reasonDate = time();
		}
		$result = $content->save();

		//save statistics for moderator
		if ($result) {
			$projects = !empty($moderator->projects) ? $moderator->projects : [];
			$countOfMarks = !empty($projects[$content->projectId]) ? $projects[$content->projectId] : 0;
			$countOfMarks++;

			$projects[$content->projectId] = $countOfMarks;
			$moderator->projects = $projects;
			$moderator->lastActivity = time();

			$moderator->save();
		}

		return $result;
	}
}