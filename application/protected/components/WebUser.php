<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dna
 * Date: 1/11/14
 * Time: 2:22 AM
 * To change this template use File | Settings | File Templates.
 */

class WebUser extends CWebUser {
	public function login($identity,$duration=0) {
		$this->setState('role', $identity->getRole());
		//$this->role = $identity->getRole();
		$result = parent::login($identity, $duration=0);

//		if ($result) {
//			$this->role = $identity->getRole();
//		}

		return $result;
	}

//	public function setRole($role) {
//		$this->role = $role;
//	}
//
//	public function getRole() {
//		return $this->role;
//	}

	public function isProject() {
		if ($this->isGuest) {
			return false;
		}

		return $this->getState('role') == UserIdentity::PROJECT_ROLE;
	}

	public function isModerator() {
		if ($this->isGuest) {
			return false;
		}

		return $this->getState('role') == UserIdentity::MODERATOR_ROLE;
	}

	/**
	 * Methods return model of the current user
	 * @return null|Moderator
	 */
	public function getModel() {
		if (Yii::app()->user->isGuest) {
			return null;
		}

		// can be Moderator or Project
		$modelClassName = $this->getState('role');
		$model = $modelClassName::model()->findByPk(new MongoID($this->getId()));
		return $model;
	}
}