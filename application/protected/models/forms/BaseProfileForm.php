<?php
/**
 * User: eniquall
 * Date: 1/15/14
 * Time: 12:51 AM
 */

class BaseProfileForm extends CFormModel {
	// use common checks for models and form models of moderator and project
	use BaseProfileValidationTrait;

	const REGISTRATION_SCENARIO = 'registration';
	const EDIT_PROFILE_SCENARIO = 'edit';
}