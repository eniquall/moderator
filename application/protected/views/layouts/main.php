<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />

	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print" />
	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" media="screen, projection" />
	<![endif]-->

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>

<div class="container" id="page">

	<div id="header">
		<div id="logo">
			<?php echo CHtml::encode(Yii::app()->name); ?>
			<?php echo (!Yii::app()->user->isGuest) ? Yii::app()->user->role : '' ;?>
		</div>
	</div><!-- header -->

	<div id="mainmenu">
		<?php $this->widget('zii.widgets.CMenu',array(
			'items'=>array(
				array('label'=>'Home', 'url'=>array('')),
				array('label'=>'About', 'url'=>array('/static/about')),

				// moderator
				array('label'=>'Moderator registration', 'url'=>array('/moderator/registration'), 'visible'=>Yii::app()->user->isGuest),
				array('label'=>'Login as moderator', 'url'=>array('/moderator/login'), 'visible'=>Yii::app()->user->isGuest),
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
		)); ?>
	</div><!-- mainmenu -->

	<?php if (Yii::app()->user->hasFlash('success')) {
		foreach ((array)Yii::app()->user->getFlash('success') as $message) { ?>
			<div class="flash-success">
				<?php echo $message; ?>
			</div>
		<?php }
	} ?>

	<?php $this->widget('zii.widgets.CBreadcrumbs', array(
		'links'=>$this->breadcrumbs,
	)); ?><!-- breadcrumbs -->

	<?php echo $content; ?>

	<div id="footer">
		Copyright &copy; <?php echo date('Y'); ?> by My Company.<br/>
		All Rights Reserved.<br/>
		<?php echo Yii::powered(); ?>
	</div><!-- footer -->

</div><!-- page -->

</body>
</html>
