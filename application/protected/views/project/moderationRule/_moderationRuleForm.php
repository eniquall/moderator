<div class="form">
	<?php
	$form = $this->beginWidget('CActiveForm', array(
		'id' => 'projectForm',
		'action' =>
			($model->getScenario() == ProjectForm::REGISTRATION_SCENARIO)
				? Yii::app()->createUrl("/project/addModerationRule/")
				: Yii::app()->createUrl("/project/editModerationRule/"),
		'enableClientValidation'=>true,
		'clientOptions' => array(
			'validateOnChange' => true,
		),
	));
	?>
	<?php if ($model->getScenario() == ProjectForm::EDIT_PROFILE_SCENARIO) {?>
		<div class="row">
			<?= $form->hiddenField($model, '_id'); ?>
		</div>
	<? } ?>

	<div class="row">
		<?= $form->label($model, 'name', array('label' => $model->getAttributeLabel("name"))); ?>
		<?= $form->textField($model, 'name', array('class' => 'longField')); ?>
		<?= $form->error($model, 'name'); ?>
	</div>

	<div class="row">
		<?= $form->label($model, 'type', array('label' => $model->getAttributeLabel("type"))); ?>
		<?= $form->dropdownList($model, 'type', ContentHelper::getAllowedTypesList()); ?>
		<?= $form->error($model, 'type'); ?>
	</div>

	<div class="row">
		<input type="submit" value="<?= $model->getScenario() == ProjectForm::REGISTRATION_SCENARIO ? "Register" : "Edit"; ?>">
	</div>
	<?php $this->endWidget(); ?>
</div>
