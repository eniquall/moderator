<?php
/**
 * User: eniquall
 * Date: 1/23/14
 * Time: 10:08 PM
 */

class AdminForm extends BaseProfileForm {
	public $email;
	public $name;
	public $password;

	public function rules() {
		return array(
			array('email, password, name', 'required'),
		);
	}

	public function attributeLabels() {
		return array(
			'email'=>'Email',
			'password'=>'Password',
			'name'=>'Name',
		);
	}
}