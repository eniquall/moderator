<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	public $email;
	public $role;
	private $_id;

	const MODERATOR_ROLE = 'Moderator';
	const PROJECT_ROLE   = 'Project';
	const ADMIN_ROLE     = 'Admin';

	/**
	 * Authenticates a user.
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate() {
		$userModelClass = $this->getModelClass();
		$userModel = new $userModelClass;

		$criteria = new EMongoCriteria();
		$criteria->email = $this->username;

		$user = $userModel::model()->find($criteria);

		if ($user === null) {
			$this->errorCode = self::ERROR_USERNAME_INVALID;
		}
		else if (!$user->validatePassword($this->password)) {
			$this->errorCode = self::ERROR_PASSWORD_INVALID;
		} else {
			$this->_id = $user->id;
			$this->username = $user->name;
			$this->email = $user->email;
			$this->errorCode = self::ERROR_NONE;
		}

		if ($this->getRole() == self::MODERATOR_ROLE && $user->isSuperModerator == "1") {
			$this->setRole(self::ADMIN_ROLE);
		}

		return $this->errorCode == self::ERROR_NONE;
	}

	/**
	 * @return integer the ID of the user record
	 */
	public function getId()
	{
		return $this->_id;
	}

	/**
	 * Method set role of the userIdentity - it can be Moderator or Project
	 * @param $role string
	 */
	public function setRole($role) {
		$this->role = $role;
	}

	public function getRole() {
		return $this->role;
	}

	public function getModelClass() {
		return $this->getRole() . 'Model';
	}
}