<?php

date_default_timezone_set('Europe/Moscow');
mb_internal_encoding("UTF-8");

// add real time logging
//Yii::getLogger()->autoDump = 1;
//Yii::getLogger()->autoFlush = 1;


Yii::setPathOfAlias('bootstrap', dirname(__FILE__).'/../extensions/bootstrap');

return array(
	'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
	'name' => 'ModeratorService',

	'sourceLanguage' => 'en',
	'language' => 'en',
	'defaultController' => 'static',
	// preloading 'log' component
	'preload' => array('log'),
	// autoloading model and component classes
	'import' => array(
		'application.components.*',
		'application.controllers.BaseProfileController',
		'application.models.forms.*',
		'application.models.*',
		'application.helpers.*',
		'ext.ymds.*',
		'ext.PRFLR.*'
	),

	'theme'=>'bootstrap',

	'modules' => array(
		'gii' => array(
			'class' => 'system.gii.GiiModule',
			'password' => '124',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters' => array('127.0.0.1', '::1'),
			'generatorPaths'=>array(
				'bootstrap.gii',
			),
		),
	),
	// application components
	'components' => array(
		'bootstrap'=>array(
			'class'=>'bootstrap.components.Bootstrap',
		),
		'user' => array(
			// enable cookie-based authentication
			'class' => 'WebUser',
			'allowAutoLogin' => true,
		),
		// uncomment the following to enable URLs in path-format
		'urlManager' => array(
//			'urlFormat' => 'path',
			'rules' => array(
				'' => 'static/welcome',
				'/api' => 'rest/index'
			),
			'showScriptName' => false,
		),
		'mongodb' => array(
			'class' => 'ext.ymds.EMongoDB',
			'connectionString' => 'mongodb://localhost/moderator',
			'dbName' => 'moderator',
			'fsyncFlag' => false,
			'safeFlag' => false,
			'useCursor' => false,
			'cacheId' => 'cache',
		),
		'db' => array(
			'connectionString' => 'mysql:host=127.0.0.1;dbname=moderator',
			'username' => 'root',
			'password' => '',
			'schemaCachingDuration' => 0,
			'enableProfiling' => true,
		),
		'log' => array(
			'class' => 'CLogRouter',
			'routes' => array(
/*
				array(
					'class' => 'application.extensions.PRFLR.PRFLRLogRoute',
					'enabled' => true,
					'levels'  => 'profile',
							'source' => 'moderator',
								'apikey' => '234fgrtnsdfk45309s5',
								//'email'  => 'info@moderator.com',
								//'pass'   => '1234567890'
				),
*/
				array(
					'class' => 'CWebLogRoute'
				),
				array(
					'class' => 'CFileLogRoute'
				)
			),
		),
	),
	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params' => require(dirname(__FILE__) . '/params.php'),
);
