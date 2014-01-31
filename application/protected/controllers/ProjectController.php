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

	public function init() {
		Yii::app()->user->loginUrl = $this->createUrl('/project/login');
	}

	public function accessRules() {
		return array(
			array('deny',
				'actions'=>array('EditProfile', 'AddModerationRule', 'EditModerationRule', 'ShowModerationRulesList', 'Statistics'),
				'users'=>array('?'),
			),
			array('allow',
				'actions'=>array('Registration', 'EditProfile', 'AddModerationRule', 'EditModerationRule', 'Statistics'),
				'roles'=>array(UserIdentity::PROJECT_ROLE, UserIdentity::ADMIN_ROLE),
			),
		);
	}

	public function getAfterLoginUrl() {
		if (Yii::app()->user->isAdmin()) {
			return $this->createUrl('/admin/showProjectsList');
		}
		return $this->createUrl('/project/showModerationRulesList');
	}

	public function actionRegistration() {
		$model = new ProjectForm(BaseProfileForm::REGISTRATION_SCENARIO);
		if (isset($_POST['ProjectForm'])) {
			$model->attributes = $_POST['ProjectForm'];
			if ($model->validate()) {
				if ($this->_saveProjectProfile($model, new ProjectModel())) {
					$this->redirect($this->getAfterLoginUrl());
				}
			}
		}
		$this->render('registration', array('model' => $model));
	}

	public function actionEditProfile($id) {
		$formModel = new ProjectForm(BaseProfileForm::EDIT_PROFILE_SCENARIO);
		$this->_checkPermissionForEditProfile($id);
		$project = ProjectModel::model()->findByPk(new MongoId($id));

		if (empty($project)) {
			throw new CException(404, 'Profile with this id was not found');
		}

		if (isset($_POST['ProjectForm'])) {
			$formModel->attributes = $_POST['ProjectForm'];
			if ($formModel->validate()) {
				if ($this->_saveProjectProfile($formModel, $project)) {
					$this->redirect($this->getAfterLoginUrl());
				}
			}
		} else {
			$formModel->populateFromModel($project);
		}

		$this->render('editProfile', array('model' => $formModel));
	}

	public function actionAddModerationRule() {
		$model = new ModerationRuleForm(ModerationRuleForm::ADD_RULE_SCENARIO);

		$project = Yii::app()->user->getModel();
		$model->projectId = $project->getId();

		if (isset($_POST['ModerationRuleForm'])) {
			$model->attributes = $_POST['ModerationRuleForm'];
			if ($model->validate()) {
				if ($this->_saveModerationRuleProfile($model)) {
					$this->redirect($this->createUrl('/project/showModerationRulesList'));
				}
			}
		}

		$this->render('moderationRule/add', array('model' => $model));
	}

	public function actionEditModerationRule() {
		$model = new ModerationRuleForm(ModerationRuleForm::EDIT_RULE_SCENARIO);

		$project = Yii::app()->user->getModel();
		$model->projectId = $project->getId();

		if (isset($_POST['ModerationRuleForm'])) {
			$model->attributes = $_POST['ModerationRuleForm'];

			if ($model->validate()) {
				if ($this->_saveModerationRuleProfile($model)) {
					$this->redirect($this->createUrl('/project/showModerationRulesList'));
				}
			}
		} else {
			$moderationRuleId = Yii::app()->request->getParam('id');

			if (empty($moderationRuleId)) {
				$this->redirect($this->createUrl('/project/showModerationRulesList'));
			}
			$moderationRule = ModerationRuleModel::model()->findByPk(new MongoId($moderationRuleId));
			$model->populateFromModel($moderationRule);
		}

		$this->render('moderationRule/add', array('model' => $model));
	}

	public function actionShowModerationRulesList() {
		if (Yii::app()->user->role == UserIdentity::PROJECT_ROLE) {
			$project =  Yii::app()->user->getModel();
			$projectId = (string) $project->getId();
		} else {
			throw new CException("You are not logged in with project profile");
		}

		$rules = ModerationRuleModel::model()->findAll(
			array(
				'conditions' => array(
					'projectId' => array(
						'==' => $projectId
					)
				)
			)
		);
		$this->render('moderationRule/showList', array('rules' => $rules, 'project' => $project));
	}

	/**
	 * Method for edit project profile - checks for permissions to do that should be performed
	 * before that method! (in action)
	 * @param ProjectForm $formModel
	 * @param ProjectModel $project
	 * @return bool
	 */
	protected function _saveProjectProfile(ProjectForm $formModel, ProjectModel $project) {
		if ($formModel->getScenario() == BaseProfileForm::EDIT_PROFILE_SCENARIO) {
			// save new password if it was entered
			// additional validations were performed with ProjectForm validations (checkForNewPassword)
			if (!empty($formModel->newPassword)) {
				$project->password = SecurityHelper::generatePasswordHash($formModel->newPassword);
			}
		} else if ($formModel->getScenario() == BaseProfileForm::REGISTRATION_SCENARIO) {
			//there should be new ProjectModel
			// save md5 hash of password
			$project->password = SecurityHelper::generatePasswordHash($formModel->password);
		}

		$project->setScenario($formModel->getScenario());

		// only admin can change these vaues
		if (Yii::app()->user->isAdmin()) {
			$project->balance = $formModel->balance;
			$project->notes = $formModel->notes;
		}

		$project->name  = $formModel->name;
		$project->email = mb_strtolower($formModel->email);
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

			//enable validation rules for apiKey - as we already saved (registered project)
			//have to set EDIT_PROFILE_SCENARIO
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

	protected function _saveModerationRuleProfile($formModel) {
		$project = null;

		if (Yii::app()->user->role != UserIdentity::PROJECT_ROLE) {
			throw new CHttpException(403, "You are trying to edit moderation rule but not loggged in as profile administrator or this rule belongs to another project");
		}

		if ($formModel->getScenario() == ModerationRuleForm::EDIT_RULE_SCENARIO) {
			// if rule not belons to current logged in project profile
			$moderationRule = ModerationRuleModel::model()->findByPk(new MongoId($formModel->_id));

			if (empty($moderationRule)) {
				throw new CHttpException(500, "Moderation rule not found");
			}

			if ($moderationRule->projectId != Yii::app()->user->getId()) {
				throw new CHttpException(403, "This rule belongs to another project. Use another account to edit it.");
			}
		} else if ($formModel->getScenario() == ModerationRuleForm::ADD_RULE_SCENARIO) {
			$project = Yii::app()->user->getModel();

			$moderationRule = new ModerationRuleModel();
			$moderationRule->projectId = (string) $project->_id; //use string instead of mongoId object
		}

		// validate model with the same scenario as form model
		$moderationRule->setScenario($formModel->getScenario());

		$moderationRule->type = $formModel->type;
		$moderationRule->text = $formModel->text;
		$moderationRule->level = $formModel->level;

		$result = $moderationRule->save();
		if (!$result) {
			Yii::log(__CLASS__ . " " . __METHOD__ .  "Can't save moderation rule model (scenario: " . $formModel->getScenario() . "): " .
				CJSON::encode($project->getAttributes()) . " " . CJSON::encode($moderationRule->getErrors()));
			Yii::app()->user->setFlash('error', 'Error. Moderation rule was not saved.');
		} else {
			Yii::app()->user->setFlash('success', 'Moderation rule successfully saved');
		};

		return $result;
	}
}