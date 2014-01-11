<div class="form">
	<?php
	$form = $this->beginWidget('CActiveForm', array(
		'id' => 'moderatorForm',
		'action' =>
		($model->getScenario() == ModeratorForm::REGISTRATION_SCENARIO)
			? Yii::app()->createUrl("/moderator/registration/")
			: Yii::app()->createUrl("/moderator/editProfile/"),
		'enableClientValidation'=>true,
		'clientOptions' => array(
			'validateOnChange' => true,
		),
	));
	?>
	<div class="row">
		<?= $form->label($model, 'name', array('label' => $model->getAttributeLabel("name"))); ?>
		<?= $form->textField($model, 'name', array('class' => 'longField')); ?>
		<?= $form->error($model, 'name'); ?>
	</div>
	<div class="row">
		<?= $form->label($model, 'email', array('label' => $model->getAttributeLabel("email"))); ?>
		<?= $form->textField($model, 'email', array('class' => 'shortField')); ?>
		<?= $form->error($model, 'email'); ?>
	</div>

	<div class="row">
		<?= $form->label($model, 'password', array('label' => $model->getAttributeLabel("password"))); ?>
		<?= $form->passwordField($model, 'password', array('class' => 'shortField')); ?>
		<?= $form->error($model, 'password'); ?>
	</div>


<?php if ($model->getScenario() == ModeratorForm::REGISTRATION_SCENARIO) {?>
	<div class="row">
		<?= $form->label($model, 'password2', array('label' => $model->getAttributeLabel("password2"))); ?>
		<?= $form->passwordField($model, 'password2', array('class' => 'shortField')); ?>
		<?= $form->error($model, 'password2'); ?>
	</div>
<?php } else if ($model->getScenario() == ModeratorForm::EDIT_PROFILE_SCENARIO) {?>
	<div class="row">
		<?= $form->label($model, 'newPassword', array('label' => $model->getAttributeLabel("newPassword"))); ?>
		<?= $form->passwordField($model, 'newPassword', array('class' => 'shortField')); ?>
		<?= $form->error($model, 'newPassword'); ?>
	</div>

	<div class="row">
		<?= $form->label($model, 'newPassword2', array('label' => $model->getAttributeLabel("newPassword2"))); ?>
		<?= $form->passwordField($model, 'newPassword2', array('class' => 'shortField')); ?>
		<?= $form->error($model, 'newPassword2'); ?>
	</div>
<?php } ?>

	<div class="row">
		<?= $form->label($model, 'langs', array('label' => $model->getAttributeLabel("langs"))); ?>
		<?= $form->checkBoxList($model, 'langs', LanguagesHelper::getAllowedLanguagesList()); ?>
		<?= $form->error($model, 'langs'); ?>
	</div>

	<div class="row">
		<?= $form->label($model, 'paypal', array('label' => $model->getAttributeLabel("paypal"))); ?>
		<?= $form->textField($model, 'paypal', array('class' => 'shortField')); ?>
		<?= $form->error($model, 'paypal'); ?>
	</div>

	<div class="row">
		<input type="submit" value="Register">
	</div>
	<?php $this->endWidget(); ?>
</div>