<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dna
 * Date: 1/11/14
 * Time: 2:22 AM
 * To change this template use File | Settings | File Templates.
 */

class WebUser extends CWebUser {
	public $role;

	public function login($identity,$duration=0) {
		$this->role = $identity->getRole();
		$result = parent::login($identity,$duration=0);

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

		return $this->role == UserIdentity::PROJECT_ROLE;
	}

	public function isModerator() {
		if ($this->isGuest) {
			return false;
		}

		return $this->role == UserIdentity::MODERATOR_ROLE;
	}
}