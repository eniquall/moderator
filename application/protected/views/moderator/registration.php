<div class="form">
	<?php
	$form = $this->beginWidget('CActiveForm', array(
		'id' => 'moderatorForm',
		'action' => Yii::app()->createUrl("/moderator/registration/"),
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

		<div class="row">
			<?= $form->label($model, 'password2', array('label' => $model->getAttributeLabel("password2"))); ?>
			<?= $form->passwordField($model, 'password2', array('class' => 'shortField')); ?>
			<?= $form->error($model, 'password2'); ?>
		</div>

		<div class="row">
			<?= $form->label($model, 'languages', array('label' => $model->getAttributeLabel("languages"))); ?>
			<?= $form->checkBoxList($model, 'languages', LanguagesHelper::getAllowedLanguagesList()); ?>
			<?= $form->error($model, 'languages'); ?>
		</div>

		<div class="row">
			<input type="submit" value="Register">
		</div>
	<?php $this->endWidget(); ?>
</div>