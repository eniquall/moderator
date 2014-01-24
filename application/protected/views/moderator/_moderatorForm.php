<div class="form">
	<?php
	$form = $this->beginWidget('CActiveForm', array(
		'id' => 'moderatorForm',
		'action' =>
		($model->getScenario() == BaseProfileForm::REGISTRATION_SCENARIO)
			? Yii::app()->createUrl("/moderator/registration/")
			: Yii::app()->createUrl("/moderator/editProfile/", array('id' => $model->_id)),
		'enableClientValidation'=>true,
		'clientOptions' => array(
			'validateOnChange' => true,
		),
	));
	?>

<?php echo $form->errorSummary($model); ?>

<?php if ($model->getScenario() == BaseProfileForm::EDIT_PROFILE_SCENARIO) {?>
	<div class="row">
		<?php echo $form->hiddenField($model, '_id'); ?>
	</div>
<?php } ?>
	<div class="row">
		<?php echo $form->label($model, 'name', array('label' => $model->getAttributeLabel("name"))); ?>
		<?php echo $form->textField($model, 'name', array('class' => 'longField')); ?>
		<?php echo $form->error($model, 'name'); ?>
	</div>
	<div class="row">
		<?php echo $form->label($model, 'email', array('label' => $model->getAttributeLabel("email"))); ?>
		<?php echo $form->textField($model, 'email', array('class' => 'shortField')); ?>
		<?php echo $form->error($model, 'email'); ?>
	</div>

<?php if (Yii::app()->user->isAdmin()) {?>
		<div class="row">
			<?php echo $form->label($model, 'notes', array('label' => $model->getAttributeLabel("notes"))); ?>
			<?php echo $form->textArea($model, 'notes', array('class' => 'longField')); ?>
			<?php echo $form->error($model, 'notes'); ?>
		</div>

	<div class="row">
		<?php echo $form->label($model, 'isActive', array('label' => $model->getAttributeLabel("isActive"))); ?>
		<?php echo $form->checkbox($model, 'isActive', array('class' => 'shortField')); ?>
		<?php echo $form->error($model, 'isActive'); ?>
	</div>

		<div class="row">
			<?php echo $form->label($model, 'isSuperModerator', array('label' => $model->getAttributeLabel("isSuperModerator"))); ?>
			<?php echo $form->checkbox($model, 'isSuperModerator', array('class' => 'shortField')); ?>
			<?php echo $form->error($model, 'isSuperModerator'); ?>
		</div>
<?php } ?>

	<div class="row">
		<?php echo $form->label($model, 'password', array('label' => $model->getAttributeLabel("password"))); ?>
		<?php echo $form->passwordField($model, 'password', array('class' => 'shortField')); ?>
		<?php echo $form->error($model, 'password'); ?>
	</div>


<?php if ($model->getScenario() == BaseProfileForm::REGISTRATION_SCENARIO) {?>
	<div class="row">
		<?php echo $form->label($model, 'password2', array('label' => $model->getAttributeLabel("password2"))); ?>
		<?php echo $form->passwordField($model, 'password2', array('class' => 'shortField')); ?>
		<?php echo $form->error($model, 'password2'); ?>
	</div>
<?php } else if ($model->getScenario() == BaseProfileForm::EDIT_PROFILE_SCENARIO) {?>
	<div class="row">
		<?php echo $form->label($model, 'newPassword', array('label' => $model->getAttributeLabel("newPassword"))); ?>
		<?php echo $form->passwordField($model, 'newPassword', array('class' => 'shortField')); ?>
		<?php echo $form->error($model, 'newPassword'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'newPassword2', array('label' => $model->getAttributeLabel("newPassword2"))); ?>
		<?php echo $form->passwordField($model, 'newPassword2', array('class' => 'shortField')); ?>
		<?php echo $form->error($model, 'newPassword2'); ?>
	</div>
<?php } ?>

	<div class="row">
		<?php echo $form->label($model, 'langs', array('label' => $model->getAttributeLabel("langs"))); ?>
		<?php echo $form->checkBoxList($model, 'langs', LanguagesHelper::getAllowedLanguagesList()); ?>
		<?php echo $form->error($model, 'langs'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'paypal', array('label' => $model->getAttributeLabel("paypal"))); ?>
		<?php echo $form->textField($model, 'paypal', array('class' => 'shortField')); ?>
		<?php echo $form->error($model, 'paypal'); ?>
	</div>

	<div class="row">
		<input type="submit" value="<?php echo  $model->getScenario() == BaseProfileForm::REGISTRATION_SCENARIO ? "Register" : "Edit"; ?>">
	</div>
	<?php $this->endWidget(); ?>
</div>