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
				'actions'=>array('EditProfile', 'Moderate'),
				'users'=>array('?'),
			),
			array('deny',
				'actions'=>array('Registration'),
				'users'=>array('@'),
			),
			array('allow',
				'actions'=>array('EditProfile', 'AddModerationRule', 'EditModerationRule'),
				'roles'=>array(UserIdentity::PROJECT_ROLE, UserIdentity::ADMIN_ROLE),
			),
		);
	}

	public function getAfterLoginUrl(){
		if (Yii::app()->user->isAdmin()) {
			return $this->createUrl('/admin/showModeratorsList');
		}
		return $this->createUrl('/static/about');
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
		$moderatorId = Yii::app()->user->getId();
		$moderationRule = null;
		$content = ContentHelper::getContentForModeration($moderatorId);

		if ($content) {
			$moderationRule = ContentHelper::getModerationRuleByProjectAndTypeName($content->projectId, $content->type);
		}
		$project = ProjectModel::model()->findByPk(new MongoId($content->projectId));

		$this->render('moderate',
			array(
				'content' => $content,
				'project' => $project,
				'moderationRule' => $moderationRule
			)
		);
	}
}
