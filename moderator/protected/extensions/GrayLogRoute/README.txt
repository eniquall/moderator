GrayLogRoute Extension
===============


 The 'GrayLogRoute' is a Yii Framework Plugin that avoids to write log-messages to Graylog-server.


Requirements
------------

- Yii 1.1.*
- Installed Graylog-server (http://www.graylog2.org/about/gelf)


Installation
------------

 - Unpack all files under your project 'extension' folder
 - Set up your log route in main.php configuration file:
 
      'components' => array(
        
        ...

        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(

        ...

                array(
                    'class' => 'application.extensions.GrayLogRoute.DGGrayLogRoute',
                    'enabled' => true,
                    'levels' => 'info,error,warning',
                    'host' => 'localhost', // the hostname of your graylog-server
                    'port' => 12201 // the UDP-portnumber
                ),
        
        ...
            ),
        ),
        
      )
      
 - Enjoy!
 
 
Usage:
-------

 Write log by standart Yii's method

    Yii::log('Writing to GrayLog!');


Changelog:
-------

1.0.0 - Initial release