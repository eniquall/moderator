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
			<?php echo $form->hiddenField($model, '_id'); ?>
		</div>
	<?php } ?>

	<div class="row">
		<?php echo $form->label($model, 'type', array('label' => $model->getAttributeLabel("type"))); ?>
		<?php echo $form->textField($model, 'type'); ?>
		<?php echo $form->error($model, 'type'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'text', array('label' => $model->getAttributeLabel("text"))); ?>
		<?php echo $form->textArea($model, 'text'); ?>
		<?php echo $form->error($model, 'text'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'level', array('level' => $model->getAttributeLabel("level"))); ?>
		<?php echo $form->dropDownList($model, 'level', [1,3,5,7,9]); ?>
		<?php echo $form->error($model, 'level'); ?>
	</div>

	<div class="row">
		<input type="submit" value="<?php echo $model->getScenario() == ModerationRuleForm::ADD_RULE_SCENARIO ? "Register" : "Edit"; ?>">
	</div>
	<?php $this->endWidget(); ?>
</div>
