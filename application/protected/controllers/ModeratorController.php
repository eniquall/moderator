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
		Yii::app()->user->loginUrl = '/moderator/login';
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
		return $this->createUrl('/moderator/moderate');
	}

	public function actionRegistration() {
		$model = new ModeratorForm(BaseProfileForm::REGISTRATION_SCENARIO);
		if (isset($_POST['ModeratorForm'])) {
			$model->attributes = $_POST['ModeratorForm'];
			if ($model->validate()) {
				if ($this->_saveModeratorProfile($model)) {
					$this->redirect($this->getAfterLoginUrl());
				}
			}
		}
		$this->render('registration', array('model' => $model));
	}

	/**
	 * Edit profile of the current moderator
	 */
	public function actionEditProfile() {
		$formModel = new ModeratorForm(BaseProfileForm::EDIT_PROFILE_SCENARIO);

		if (isset($_POST['ModeratorForm'])) {

			$formModel->attributes = $_POST['ModeratorForm'];
			if ($formModel->validate()) {
				// register

				if ($this->_saveModeratorProfile($formModel)) {
					$this->redirect('/moderator/moderate');
				}
			}
		} else {
			$moderator = Yii::app()->user->getModel();
			$formModel->populateFromModel($moderator);
		}

		$this->render('editProfile', array('model' => $formModel));
	}

	public function _saveModeratorProfile(ModeratorForm $formModel) {
		$moderator = null;
		if ($formModel->getScenario() == BaseProfileForm::EDIT_PROFILE_SCENARIO) {
			if ((Yii::app()->user->getId() != $formModel->_id) || (Yii::app()->user->getRole() != UserIdentity::MODERATOR_ROLE)) {
				throw new CHttpException(403, "You are trying to edit profile, but not loggged in");
			} else {
				$moderator = Yii::app()->user->getModel();
				// save new password if it was entered
				// additional validations were performed with ModeratorForm validations (checkForNewPassword)
				if (!empty($formModel->newPassword)) {
					$moderator->password = SecurityHelper::generatePasswordHash($formModel->newPassword);
				}
			}
		} else if ($formModel->getScenario() == BaseProfileForm::REGISTRATION_SCENARIO){
			$moderator = new ModeratorModel();
			// save md5 hash of password
			$moderator->password = SecurityHelper::generatePasswordHash($formModel->password);
		}

		$moderator->setScenario($formModel->getScenario());

		$moderator->name  = $formModel->name;
		$moderator->email = mb_strtolower($formModel->email);

		$moderator->langs  = $formModel->langs;
		$moderator->paypal = $formModel->paypal;


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
		$this->render('moderate');
	}
}
