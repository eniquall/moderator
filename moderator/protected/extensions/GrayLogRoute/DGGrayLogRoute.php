<?php

/**
 * CGrayLogRoute class file.
 *
 * @version 1.0
 * @link http://www.graylog2.org/about/gelf
 * @link https://github.com/Graylog2/gelf-php
 */
require_once('GELF.php');

class DGGrayLogRoute extends CLogRoute
{

    /**
     * @var string GELF hostname 
     */
    public $host = 'localhost';

    /**
     * @var integer GELF port
     */
    public $port = 12201;

    /**
     * @var string application hostname
     */
    public $apphost = null;
    
    public $uid; 
    public $ip;
    
    public function init()
    {
        parent::init();
        
        if ($this->apphost === null)
            $this->apphost = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : 'defaultHost';
        
        $this->uid = uniqid(mt_rand(), true);
        $this->ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'localhost';
    }

    /**
     * Stores log messages into GELF
     * 
     * @param array $logs list of log messages
     */
    protected function processLogs($logs)
    {
        $message = new GELFMessage($this->host, $this->port, 'LAN');

        foreach ($logs as $log) {
            $message->setShortMessage($log[0]);
            $message->setFullMessage($log[0]);
            $message->setHost($this->apphost);
            $message->setFacility('yiiapp');
            $message->setAdditional('category', $log[2]);
            $message->setAdditional('ip', $this->ip);
            $message->setAdditional('uid', $this->uid);
            $message->setLevel($this->YiiLogLevel2SysLog($log[1]));
            $message->send();
        }

        unset($message);
    }

    protected function YiiLogLevel2SysLog($YiiLevel)
    {
        switch ($YiiLevel) {
            case CLogger::LEVEL_ERROR:
                $SysLogLevel = LOG_ERR;
                break;

            case CLogger::LEVEL_WARNING:
                $SysLogLevel = LOG_WARNING;
                break;

            case CLogger::LEVEL_TRACE:
                $SysLogLevel = LOG_DEBUG;
                break;

            case CLogger::LEVEL_INFO:
            case CLogger::LEVEL_PROFILE:
            default:
                $SysLogLevel = LOG_INFO;
        }

        return $SysLogLevel;
    }

}
