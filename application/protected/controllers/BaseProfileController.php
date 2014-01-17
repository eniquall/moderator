<?php
/**
 * User: eniquall
 * Date: 1/15/14
 * Time: 3:17 AM
 */

abstract class BaseProfileController extends Controller {
	public function actionLogin() {
		$model = new LoginForm($this->getLoginUserRole());
		if (isset($_POST['LoginForm'])) {
			$model->attributes = $_POST['LoginForm'];
			if ($model->validate() && $model->login()) {
				$returnUrl = !empty(Yii::app()->user->returnUrl)
					? Yii::app()->user->returnUrl
					: $this->getAfterLoginUrl();
				$this->redirect($returnUrl);
			}
		}

		$this->render('application.views.common.login', array('model' => $model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}

	abstract function getLoginUserRole();
	abstract function getAfterLoginUrl();

	public function filters()
	{
		return array(
			'accessControl',
		);
	}

} 