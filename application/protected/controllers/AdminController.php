<?php
/**
 * User: dna
 * Date: 1/11/14
 * Time: 1:56 AM
 */

class AdminController extends Controller {
	public function actionShowModeratorsList() {
		$this->render('moderatorsList');
	}

	public function actionEditModeratorProfile() {
		$this->render('editModeratorProfile');
	}

	public function actionShowProjectsList() {
		$this->render('projectsList');
	}

	public function actionEditProjectProfile() {
		$this->render('editProjectProfile');
	}
}