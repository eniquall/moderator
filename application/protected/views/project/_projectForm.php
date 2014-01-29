<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id' => 'projectForm',
	'action' =>
	($model->getScenario() == BaseProfileForm::REGISTRATION_SCENARIO)
		? Yii::app()->createUrl("/project/registration/")
		: Yii::app()->createUrl("/project/editProfile/", array('id' => $model->_id)),
	'enableClientValidation'=>true,
	'clientOptions' => array(
		'validateOnChange' => true,
	),
	'htmlOptions'=>array('class'=>'well'),
));
?>

<?php echo $form->errorSummary($model); ?>

<?php if ($model->getScenario() == BaseProfileForm::EDIT_PROFILE_SCENARIO) {?>
		<?php echo $form->hiddenField($model, '_id'); ?>
<?php } ?>

	<?php echo $form->label($model, 'name', array('label' => $model->getAttributeLabel("name"))); ?>
	<?php echo $form->textField($model, 'name', array('class' => 'longField')); ?>
	<?php echo $form->error($model, 'name'); ?>

	<?php echo $form->label($model, 'email', array('label' => $model->getAttributeLabel("email"))); ?>
	<?php echo $form->textField($model, 'email', array('class' => 'shortField')); ?>
	<?php echo $form->error($model, 'email'); ?>

	<?php echo $form->checkboxRow($model, 'isActive'); ?>

<?php if (Yii::app()->user->isAdmin()) {?>
	<?php echo $form->label($model, 'notes', array('label' => $model->getAttributeLabel("notes"))); ?>
	<?php echo $form->textArea($model, 'notes', array('class' => 'longField')); ?>
	<?php echo $form->error($model, 'notes'); ?>

	<?php echo $form->label($model, 'balance', array('label' => $model->getAttributeLabel("balance"))); ?>
	<?php echo $form->textField($model, 'balance', array('class' => 'longField')); ?>
	<?php echo $form->error($model, 'balance'); ?>
<?php } ?>

	<?php echo $form->label($model, 'password', array('label' => $model->getAttributeLabel("password"))); ?>
	<?php echo $form->passwordField($model, 'password', array('class' => 'shortField')); ?>
	<?php echo $form->error($model, 'password'); ?>

<?php if ($model->getScenario() == BaseProfileForm::REGISTRATION_SCENARIO) {?>
		<?php echo $form->label($model, 'password2', array('label' => $model->getAttributeLabel("password2"))); ?>
		<?php echo $form->passwordField($model, 'password2', array('class' => 'shortField')); ?>
		<?php echo $form->error($model, 'password2'); ?>

<?php } else if ($model->getScenario() == BaseProfileForm::EDIT_PROFILE_SCENARIO) {?>
		<?php echo $form->label($model, 'newPassword', array('label' => $model->getAttributeLabel("newPassword"))); ?>
		<?php echo $form->passwordField($model, 'newPassword', array('class' => 'shortField')); ?>
		<?php echo $form->error($model, 'newPassword'); ?>

		<?php echo $form->label($model, 'newPassword2', array('label' => $model->getAttributeLabel("newPassword2"))); ?>
		<?php echo $form->passwordField($model, 'newPassword2', array('class' => 'shortField')); ?>
		<?php echo $form->error($model, 'newPassword2'); ?>
<?php } ?>

<div>
	<?php $this->widget('bootstrap.widgets.TbButton',
		array(
			'buttonType'=>'submit',
			'label'=> ($model->getScenario() == BaseProfileForm::REGISTRATION_SCENARIO ? "Register" : "Edit")
		)
	); ?>
</div>
<?php $this->endWidget(); ?>