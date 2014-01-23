<?php

/**
 * To add admin profile - use insert into mongodb directly:
 * db.admin.insert({"email": "admin@gmail.com", "name": "admin", "password": "eedd5fa0b9d044905587b5f0873e8927"}) // password = md5("moderatorAdmin");
 */
class AdminController extends BaseProfileController {
	public function getLoginUserRole() {
		return UserIdentity::ADMIN_ROLE;
	}

	public function getAfterLoginUrl() {
		return $this->createUrl('/admin/showProjectsList');
	}

	public function actionShowModeratorsList() {
		$moderators = ModeratorModel::model()->findAll();
		$this->render('showModeratorsList', array('moderators' => $moderators));
	}

	public function actionEditModeratorProfile() {
		$this->render('editModeratorProfile');
	}

	public function actionShowProjectsList() {
		$projects = ProjectModel::model()->findAll();
		$this->render('showProjectsList', array('projects' => $projects));
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