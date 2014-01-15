<?php
/**
 * User: dna
 * Date: 1/11/14
 * Time: 1:54 AM
 */

class ProjectController extends BaseProfileController {
	public function getLoginUserRole() {
		return UserIdentity::PROJECT_ROLE;
	}

	public function getAfterLoginUrl() {
		return $this->createUrl('/project/addModerationRule');
	}

	public function actionRegistration() {
		$model = new ProjectForm(BaseProfileForm::REGISTRATION_SCENARIO);
		if (isset($_POST['ProjectForm'])) {
			$model->attributes = $_POST['ProjectForm'];
			if ($model->validate()) {
				if ($this->_saveProjectProfile($model)) {
					$this->redirect($this->getAfterLoginUrl());
				}
			}
		}
		$this->render('registration', array('model' => $model));
	}

	public function actionEditProfile() {
		$formModel = new ProjectForm(BaseProfileForm::EDIT_PROFILE_SCENARIO);

		if (isset($_POST['ProjectForm'])) {

			$formModel->attributes = $_POST['ProjectForm'];
			if ($formModel->validate()) {
				// register

				if ($this->_saveProjectProfile($formModel)) {
					$this->redirect($this->getAfterLoginUrl());
				}
			}
		} else {
			$moderator = Yii::app()->user->getModel();
			$formModel->populateFromModel($moderator);
		}

		$this->render('editProfile', array('model' => $formModel));
	}

	protected function _saveProjectProfile(ProjectForm $formModel) {
		$project = null;
		if ($formModel->getScenario() == BaseProfileForm::EDIT_PROFILE_SCENARIO) {
			if (Yii::app()->user->getId() != $formModel->_id) {
				throw new CHttpException(403, "You are trying to edit profile, but not loggged in");
			} else {
				$project = Yii::app()->user->getModel();
				// save new password if it was entered
				// additional validations were performed with ModeratorForm validations (checkForNewPassword)
				if (!empty($formModel->newPassword)) {
					$project->password = SecurityHelper::generatePasswordHash($formModel->newPassword);
				}
			}
		} else if ($formModel->getScenario() == BaseProfileForm::REGISTRATION_SCENARIO) {
			$project = new ProjectModel();
			// save md5 hash of password
			$project->password = SecurityHelper::generatePasswordHash($formModel->password);
		}

		$project->setScenario($formModel->getScenario());

		$project->name  = $formModel->name;
		$project->email = mb_strtolower($formModel->email);
		$project->balance = 0;
		$project->notes = '';
		// @TODO clarify if project should be active instantly after registration
		$project->isActive = $formModel->isActive;

		$result = $project->save();
		if (!$result) {
			Yii::log(__CLASS__ . " " . __METHOD__ .  "Can't save project profile (scenario: " . $formModel->getScenario() . "): " .
				CJSON::encode($project->getAttributes()) . " " . CJSON::encode($project->getErrors()));
			Yii::app()->user->setFlash('error', 'Error. Profile was not saved.');
		} else {
			Yii::app()->user->setFlash('success', 'Profile successfully saved');
		};

		if ($formModel->getScenario() == BaseProfileForm::REGISTRATION_SCENARIO) {

			//enable validation rules for apiKey
			$project->setScenario(BaseProfileForm::EDIT_PROFILE_SCENARIO);
			$project->apiKey = SecurityHelper::generateApiKey($project->email, $project->_id);
			$result = $project->save();

			if (!$result) {
				Yii::log(__CLASS__ . " " . __METHOD__ .  "Can't save project profile after generating apiKey (scenario: " . $formModel->getScenario() . "): " .
						CJSON::encode($project->getAttributes()) . " " . CJSON::encode($project->getErrors()));
				Yii::app()->user->setFlash('error', 'Error. Profile was not saved.');
			} else {
				Yii::app()->user->setFlash('success', 'Profile successfully saved');
			};
		}

		return $result;
	}


	public function actionAddModerationRule() {
		$this->render('moderationRule/add');
	}
}