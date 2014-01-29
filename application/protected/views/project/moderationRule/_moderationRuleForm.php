<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id' => 'moderationRuleForm',
	'action' =>
		($model->getScenario() == ModerationRuleForm::ADD_RULE_SCENARIO)
			? Yii::app()->createUrl("/project/addModerationRule/")
			: Yii::app()->createUrl("/project/editModerationRule/"),
	'enableClientValidation'=>true,
	'clientOptions' => array(
		'validateOnChange' => true,
	),
	'htmlOptions'=>array('class'=>'well'),
));
?>

<?php echo $form->errorSummary($model); ?>

<?php if ($model->getScenario() == ModerationRuleForm::EDIT_RULE_SCENARIO) {?>
	<?php echo $form->hiddenField($model, '_id'); ?>
<?php } ?>

<?php echo $form->label($model, 'type', array('label' => $model->getAttributeLabel("type"))); ?>
<?php echo $form->textField($model, 'type'); ?>
<?php echo $form->error($model, 'type'); ?>

<?php echo $form->label($model, 'text', array('label' => $model->getAttributeLabel("text"))); ?>
<?php echo $form->textArea($model, 'text'); ?>
<?php echo $form->error($model, 'text'); ?>

<?php echo $form->label($model, 'level', array('level' => $model->getAttributeLabel("level"))); ?>
<?php echo $form->dropDownList($model, 'level', [1 => 1, 3 => 3, 5 => 5, 7 => 7, 9 => 9]); ?>
<?php echo $form->error($model, 'level'); ?>

<div>
	<?php $this->widget('bootstrap.widgets.TbButton',
		array(
			'buttonType'=>'submit',
			'label'=> ($model->getScenario() == ModerationRuleForm::ADD_RULE_SCENARIO ? "Register" : "Edit")
		)
	); ?>
</div>
<?php $this->endWidget(); ?>