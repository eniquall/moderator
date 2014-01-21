<?php
class AdminController extends Controller {
	public function getLoginUserRole() {
		return UserIdentity::ADMIN_ROLE;
	}

	public function getAfterLoginUrl() {
		return '';
	}

	public function actionShowModeratorsList() {
		$this->render('showModeratorsList');
	}

	public function actionEditModeratorProfile() {
		$this->render('editModeratorProfile');
	}

	public function actionShowProjectsList() {
		$this->render('showProjectsList');
	}

	public function actionEditProjectProfile() {
		$this->render('editProjectProfile');
	}

	public function actionShowModerationRulesList() {
		$this->render('showProjectsList');
	}

	public function actionEditModerationRule() {
		$this->render('editModerationRule');
	}
}