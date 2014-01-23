<div class="form">
	<?php
	$form = $this->beginWidget('CActiveForm', array(
		'id' => 'moderationRuleForm',
		'action' =>
			($model->getScenario() == ModerationRuleForm::ADD_RULE_SCENARIO)
				? Yii::app()->createUrl("/project/addModerationRule/")
				: Yii::app()->createUrl("/project/editModerationRule/"),
		'enableClientValidation'=>true,
		'clientOptions' => array(
			'validateOnChange' => true,
		),
	));
	?>

	<?php echo $form->errorSummary($model); ?>

	<?php if ($model->getScenario() == ModerationRuleForm::EDIT_RULE_SCENARIO) {?>
		<div class="row">
			<?= $form->hiddenField($model, '_id'); ?>
		</div>
	<?php } ?>

	<div class="row">
		<?= $form->label($model, 'type', array('label' => $model->getAttributeLabel("type"))); ?>
		<?= $form->dropdownList($model, 'type', ContentHelper::getAllowedTypesList()); ?>
		<?= $form->error($model, 'type'); ?>
	</div>

	<div class="row">
		<?= $form->label($model, 'text', array('label' => $model->getAttributeLabel("text"))); ?>
		<?= $form->textArea($model, 'text'); ?>
		<?= $form->error($model, 'text'); ?>
	</div>

	<div class="row">
		<?= $form->label($model, 'level', array('level' => $model->getAttributeLabel("level"))); ?>
		<?= $form->textField($model, 'level', ContentHelper::getAllowedTypesList()); ?>
		<?= $form->error($model, 'level'); ?>
	</div>

	<div class="row">
		<input type="submit" value="<?= $model->getScenario() == ModerationRuleForm::ADD_RULE_SCENARIO ? "Register" : "Edit"; ?>">
	</div>
	<?php $this->endWidget(); ?>
</div>
