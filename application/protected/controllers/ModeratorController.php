<?php
/**
 * User: eniquall
 * Date: 1/8/14
 * Time: 11:29 PM
 */

Yii::import('application.models.forms.ModeratorForm');
class ModeratorController extends Controller {

	public function actionRegistration() {
		$model = new ModeratorForm();
		if (isset($_POST['ModeratorForm'])) {
			$model->attributes = $_POST['ModeratorForm'];
			if ($model->validate()) {
				// register
			}
		}
		$this->render('registration', array('model' => $model));
	}
}
