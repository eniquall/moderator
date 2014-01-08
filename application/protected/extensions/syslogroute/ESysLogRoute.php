<?php

// Not original version from yiiframework.com  Fixed by  a.spiridonov.

class ESysLogRoute extends CLogRoute
{

	/**
	 * @var array levels
	 */
	public $levels;

	/**
	 * @var array categories
	 */
	public $categories;

	/**
	 * @var string logName
	 */
	public $logName;

	/**
	 * @var string logFacility
	 */
	public $logFacility = LOG_LOCAL6;

	/**
	 * @var bool isWin
	 */
	private $isWin;

	/**
	 * @var integer 
	 */
    public $maxMessageLength = 1024;

	/**
	 * Initializes the route.
	 * This method is invoked after the route is created by the route manager.
	 */
	public function init()
	{
		parent::init();
		$this->isWin = (strtoupper( substr( PHP_OS, 0, 3 ) ) === 'WIN')? true : false;
		if(true !== openlog($this->logName, LOG_ODELAY | LOG_PID, $this->logFacility ))
			throw new CException('Failed to initiate the logging subsystem.');
	}

	/**
	 * Saves log messages in files.
	 * @param array list of log messages
	 */
	protected function processLogs($logs)
	{
		foreach($logs as $log) {
			switch($log[1]) {
				case 'trace':
					$pri = ($this->isWin)? LOG_INFO : LOG_INFO;
                    break;
				case 'info':
					$pri = ($this->isWin)? LOG_INFO : LOG_INFO;
                    break;
                case 'debug':
					$pri = ($this->isWin)? LOG_DEBUG : LOG_DEBUG;
                    break;
				case 'warning':
					$pri = ($this->isWin)? LOG_WARNING : LOG_WARNING;
                    break;
				case 'error':
					$pri = ($this->isWin)? LOG_EMERG : LOG_ERR;
                    break;
                default:
                    $pri = ($this->isWin)? LOG_INFO : LOG_INFO;
                    break;
			}
			syslog($pri, substr( $log[1] . ' - (' . $log[2] . ') - ' . $log[0], 0, $this->maxMessageLength));
		}

		closelog();
	}

}