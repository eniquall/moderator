<?php

//date_default_timezone_set('@@TIMEZONE@@');
mb_internal_encoding("UTF-8");

// add real time logging
Yii::getLogger()->autoDump = 1;
Yii::getLogger()->autoFlush = 1;



return array(
	'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
	'name' => 'ModeratorService',

	'sourceLanguage' => 'en',
	'language' => 'en',

	// preloading 'log' component
	'preload' => array('log'),
	// autoloading model and component classes
	'import' => array(
		'application.components.*',
		'application.models.forms.*',
		'application.models.*',
		'application.helpers.*',
		'ext.ymds.*'
	),
	'modules' => array(
		'gii' => array(
			'class' => 'system.gii.GiiModule',
			'password' => '124',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters' => array('127.0.0.1', '::1'),
		),
	),
	// application components
	'components' => array(
		'user' => array(
			// enable cookie-based authentication
			'class' => 'WebUser',
			'allowAutoLogin' => true,
		),
		// uncomment the following to enable URLs in path-format
		'urlManager' => array(
			'urlFormat' => 'path',
			'rules' => array(
				'' => 'static/welcome',
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
				array(
					'class' => 'application.extensions.PRFLR.PRFLRLogRoute',
					'enabled' => true,
					'levels'  => 'profile',
                	'source' => 'moderator',
                    'apikey' => '234fgrtnsdfk453409s5',
				),
			),
		),
	),
	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params' => require(dirname(__FILE__) . '/params.php'),
);
