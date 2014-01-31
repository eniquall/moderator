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
				Yii::app()->user->setFlash('success', 'Logging in successfully');
				$returnUrl = $this->getAfterLoginUrl()
					? $this->getAfterLoginUrl()
					: Yii::app()->user->returnUrl;
				$this->redirect($returnUrl);
			}
		}

		$this->render('application.views.common.login', array('model' => $model));
	}

	public function actionStatistics() {
		$modelId = Yii::app()->user->getId();

		$curMonthStat = $prevMonthStat = null;
		$beginningOfThePrevMonth = mktime(0, 0, 0, date("m", strtotime("-1 month")), 1, date("Y", strtotime("-1 month")));
		$beginningOfTheCurMonth  = mktime(0, 0, 0, date("m", time()), 1, date("Y", strtotime("-1 month")));

		$criteria = new EMongoCriteria();
		if (Yii::app()->user->isModerator()) {
			$criteria->addCond('stat.' . $modelId, 'exists', true);
		} else if (Yii::app()->user->isProject()) {
			$criteria->addCond('projectId', '==', $modelId);
		}

		$criteria->addCond('reasonDate', '>=' ,$beginningOfThePrevMonth);
		$prevAndCurMonthStat = ContentModel::model()->count($criteria);

		$criteria->cleanFieldConditions('reasonDate');
		$criteria->addCond('reasonDate', '>=' ,$beginningOfTheCurMonth);

		$curMonthStat = ContentModel::model()->count($criteria);

		$prevMonthStat = $prevAndCurMonthStat - $curMonthStat;

		$this->render('application.views.common.statistics', array(
			'curMonthStat'  => $curMonthStat,
			'prevMonthStat' => $prevMonthStat
		));
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

	protected function _checkPermissionForEditProfile($id) {
		if (Yii::app()->user->isAdmin()) {
			return true;
		}

		// if you are trying to do something with profile and you are logged in as this profile
		if (Yii::app()->user->role == $this->getLoginUserRole()) {
			$profileModel = Yii::app()->user->getModel();
			if ($profileModel->getId() === $id) {
				return true;
			}
		}

		throw new CHttpException(403, "You are trying to edit profile, but do not have permissions");
	}
} 