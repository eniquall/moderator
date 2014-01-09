<?php

//date_default_timezone_set('@@TIMEZONE@@');
mb_internal_encoding("UTF-8");

// add real time logging
Yii::getLogger()->autoDump = 1;
Yii::getLogger()->autoFlush = 1;



return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => '@@NAME@@',
    
    'sourceLanguage' => 'en', 
    'language' => 'en',
        
    // preloading 'log' component
    'preload' => array('log'),
    // autoloading model and component classes
    'import' => array(
        'application.components.*',
        'application.helpers.*',
    ),
    'modules' => array(
    ),
    // application components
    'components' => array(
        
        'user' => array(
            // enable cookie-based authentication
            'allowAutoLogin' => true,
        ),
        // uncomment the following to enable URLs in path-format
        'urlManager' => array(
            'urlFormat' => 'path',
            'rules' => array(
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ),
            'showScriptName' => false,
        ),

        'db' => array(
            'class' => 'application.extensions.ymds.EMongoDB',
            'connectionString' => 'mongodb://@@MONGO_HOST@@/@@MONGO_DB@@',
            'dbName' => '@@MONGO_DB@@',
            'fsyncFlag' => false,
            'safeFlag' => false,
            'useCursor' => false,
            'cacheId' => 'cache',
        ),
                
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'application.extensions.GrayLogRoute.DGGrayLogRoute',
                    'enabled' => false,
                    'levels'  => 'error, warning, trace, info',
                    'host' => '@@GRAYLOG_HOST@@',
                    'port' => '@@GRAYLOG_PORT@@',
                    'apphost' => '@@URL@@',
                ),
                array(
                    'class' => 'application.extensions.PRFLR.PRFLRLogRoute',
                    'enabled' => false,
                    'levels'  => 'profile',
                    'host'   => '@@PROFILER_HOST@@',
                    'source' => '@@URL@@',
                ),
            ),
        ),        
    ),
    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params' => require(dirname(__FILE__) . '/params.php'), 
);
