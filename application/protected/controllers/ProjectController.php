<?php
/**
 * User: dna
 * Date: 1/11/14
 * Time: 1:54 AM
 */

class ProjectController extends Controller {
	public function actionRegistration() {
		$model = new ProjectForm(ProjectForm::REGISTRATION_SCENARIO);
		if (isset($_POST['ProjectForm'])) {
			$model->attributes = $_POST['ProjectForm'];
			if ($model->validate()) {
				if ($this->_saveModeratorProfile($model)) {
					$this->redirect('/moderator/moderate');
				}
			}
		}
		$this->render('registration', array('model' => $model));
	}

	public function actionEditProfile() {
		$this->render('editProfile');
	}
}