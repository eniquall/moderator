<?php /* @var $this Controller */ ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/styles.css" />
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
	<?php Yii::app()->bootstrap->register(); ?>
</head>

<body>
<?php $this->widget('bootstrap.widgets.TbNavbar',array(
	'items'=>array(
		array(
			'class'=>'bootstrap.widgets.TbMenu',
			'items'=>array(
				array('label'=>'Home', 'url'=>array('/static/welcome')),
				array('label'=>'About', 'url'=>array('/static/about')),

				// moderator
				array('label'=>'Moderator registration', 'url'=>array('/moderator/registration'), 'visible'=>Yii::app()->user->isGuest),
				array('label'=>'Login as moderator', 'url'=>array('/moderator/login'), 'visible'=>Yii::app()->user->isGuest),
				array('label'=>'Moderate content', 'url'=>$this->createUrl('/moderator/moderate'), 'visible'=>Yii::app()->user->isModerator()),
				array('label'=>'Edit moderator profile', 'url'=>$this->createUrl('/moderator/editProfile', array('id' => Yii::app()->user->getId())), 'visible'=>Yii::app()->user->isModerator()),

				// project
				array('label'=>'Project registration', 'url'=>array('/project/registration'), 'visible'=> (Yii::app()->user->isGuest || Yii::app()->user->isAdmin())),
				array('label'=>'Login as project', 'url'=>array('/project/login'), 'visible'=>Yii::app()->user->isGuest),
				array('label'=>'Edit project profile', 'url'=>$this->createUrl('/project/editProfile', array('id' => Yii::app()->user->getId())), 'visible'=>Yii::app()->user->isProject()),

				array('label'=>'Add moderation rule', 'url' => array('/project/addModerationRule'), 'visible'=>Yii::app()->user->isProject()),
				array('label'=>'Show moderation rules', 'url' => array('/project/showModerationRulesList'), 'visible'=>Yii::app()->user->isProject()),

				// admin
				array('label'=>'Show projects', 'url' => array('/admin/showProjectsList'), 'visible'=>Yii::app()->user->isAdmin()),
				array('label'=>'Show moderators', 'url' => array('/admin/showModeratorsList'), 'visible'=>Yii::app()->user->isAdmin()),

				array('label'=>'Logout (' . Yii::app()->user->name .')', 'url'=>array('/moderator/logout'), 'visible'=>!Yii::app()->user->isGuest)
			),
		),
	),
)); ?>

<div class="container" id="page">
	<?php $this->widget('bootstrap.widgets.TbAlert', array(
		'block'=>true, // display a larger alert block?
		'fade'=>true, // use transitions?
		'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
		'alerts'=>array( // configurations per alert type
			'success'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;')
		),
	)); ?>

	<?php if(isset($this->breadcrumbs)):?>
		<?php $this->widget('bootstrap.widgets.TbBreadcrumbs', array(
			'links'=>$this->breadcrumbs,
		)); ?><!-- breadcrumbs -->
	<?php endif?>

	<?php echo $content; ?>

	<div class="clear"></div>
	<div id="footer"></div><!-- footer -->
</div><!-- page -->

</body>
</html>
