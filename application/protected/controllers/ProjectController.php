<?php
/**
 * User: dna
 * Date: 1/11/14
 * Time: 1:54 AM
 */

class ProjectController extends Controller {
	public function actionRegistration() {
		$this->render('registration');
	}
	
	public function actionEditProfile() {
		$this->render('editProfile');
	}
}