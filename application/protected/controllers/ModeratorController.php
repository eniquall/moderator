<?php
/**
 * User: eniquall
 * Date: 1/8/14
 * Time: 11:29 PM
 */

Yii::import('application.models.forms.ModeratorForm');
Yii::import('application.models.forms.LoginForm');
Yii::import('application.models.Moderator');
class ModeratorController extends Controller {

	public function actionRegistration() {
		$model = new ModeratorForm(ModeratorForm::REGISTRATION_SCENARIO);
		if (isset($_POST['ModeratorForm'])) {
			$model->attributes = $_POST['ModeratorForm'];
			if ($model->validate()) {
				if ($this->_saveModeratorProfile($model)) {
					$this->redirect('/moderator/moderate');
				}
			}
		}
		$this->render('registration', array('model' => $model));
	}

	public function actionLogin() {
		$model = new LoginForm();
		if (isset($_POST['LoginForm'])) {
			$model->attributes = $_POST['LoginForm'];
			if ($model->validate() && $model->login()) {
				$returnUrl = !empty(Yii::app()->user->returnUrl)
					? Yii::app()->user->returnUrl
					: $this->createUrl('/moderator/moderate');
				$this->redirect($returnUrl);
			}
		}

		$this->render('login', array('model' => $model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}

	/**
	 * Edit profile of the current moderator
	 */
	public function actionEditProfile() {
		$formModel = new ModeratorForm(ModeratorForm::EDIT_PROFILE_SCENARIO);

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
		if ($formModel->getScenario() == ModeratorForm::EDIT_PROFILE_SCENARIO) {
			if (Yii::app()->user->getId() != $formModel->_id) {
				throw new CHttpException(403, "You are trying to edit profile, but not loggged in");
			} else {
				$moderator = Yii::app()->user->getModel();
				// save new password if it was entered
				// additional validations were performed with ModeratorForm validations (checkForNewPassword)
				if (!empty($formModel->newPassword)) {
					$moderator->password = md5($formModel->newPassword);
				}
			}
		} else if ($formModel->getScenario() == ModeratorForm::REGISTRATION_SCENARIO){
			$moderator = new Moderator();
			// save md5 hash of password
			$moderator->password = md5($formModel->password);
		}

		$moderator->name  = $formModel->name;
		$moderator->email = mb_strtolower($formModel->email);

		$moderator->langs  = $formModel->langs;
		$moderator->paypal = $formModel->paypal;


		$result = $moderator->save();
		if (!$result) {
			Yii::log(__CLASS__ . " " . __METHOD__ .  "Can't save moderator profile (scenario: " . $scenario . "): " . CJSON::encode($moderator->getErrors()));
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
