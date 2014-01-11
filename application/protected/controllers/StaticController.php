<?php
/**
 * User: dna
 * Date: 1/11/14
 * Time: 1:47 AM
 */

class StaticController extends Controller {
	public function actionWelcome() {
		$this->render('welcome');
	}

	public function actionAbout() {
		$this->render('about');
	}
}