<?php

/**
 * AMQP extension wrapper to communicate with RabbitMQ server
 * For More documentation please see:
 * http://php.net/manual/en/book.amqp.php
 */

/**
 * @defgroup CAMQP
 * @ingroup AMQPModule
 * @version 1.0.1
 */

/**
 * @class CAMQP
 * @brief Use for comunicate with AMQP server
 * @details  Send and recieve messages. Implements Wrapper template.
 *
 * @author     Andrey Evsyukov <evsyukov@citypatrol.ru>
 *
 * Requirements:
 * --------------
 *  - Yii 1.1.x or above
 *  - AMQP PECL library
 *
 * Usage:
 * --------------
 *
 * To write a message into the MQ-Exchange:
 *
 *     Yii::App()->amqp->exchange('topic')->publish('some message','some.route');
 *
 *
 * To read a message from MQ-Queue:
 *
 *     Yii::App()->amqp->queue('some_listener')->get();
 *
 */
class CAMQP extends CApplicationComponent
{
    public $host      = 'localhost';
    public $port      = '5672';
    public $vhost     = '/';

    public $login     = 'guest';
    public $password  = 'guest';

    protected $channel = null;

    /**
     * @brief states if extension should work in fake mode
     * @details in case it is enabled - CAMQP will not perform real connection with 
     * @var boolean
     */
    public $isFakeMode = false;


    /**
     * @brief Initialize component.
     * @details in case fakeMode is enabled loading fake Queue and Exchange classes
     */
    public function init()
    {
    	Yii::trace('Initializating CAMQP', 'CEXT.CAMQP.Init');

        if ($this->isFakeMode) {
        	include_once(dirname(__FILE__) . "/fake/CAMQPQueue.php");
        	include_once(dirname(__FILE__) . "/fake/CAMQPExchange.php");
        } else {
        	include_once(dirname(__FILE__) . "/CAMQPQueue.php");
        	include_once(dirname(__FILE__) . "/CAMQPExchange.php");

        	// init connection
	        $connection = new AMQPConnection(array(
	            'host'     => $this->host,
	            'vhost'    => $this->vhost,
	            //'port'   => $this->port,
	            'login'    => $this->login,
	            'password' => $this->password,
	        ));
	        $connection->connect();
	        
            // init Channel
            $this->channel = new AMQPChannel($connection);
        }

        parent::init();
    }

    /**
     * @brief Declares a new Exchange on the broker
     * @param string $name
     * @param string $type
     * @param int $flags
     */
    public function declareExchange($name, $type = AMQP_EX_TYPE_DIRECT, $flags = NULL)
    {
    	$ex = new AMQPExchange($this->channel);

    	$ex->setName($name);
        $ex->setType($type);

        if ($flags !== null) {
            $ex->setFlags($flags);
        }

        return $ex->declare();
    }
    
    public function deleteExchange($name)
    {
        $ex = new AMQPExchange($this->channel);
        
        return $ex->delete($name);
    }

    /**
     * @brief Declares a new Queue on the broker
     * @param string $name
     * @param int $flags
     */
    public function declareQueue($name, $flags = NULL)
    {
        $queue = new AMQPQueue($this->channel);

        $queue->setName($name);

        if ($flags !== null) {
            $queue->setFlags($flags);
        }

        return $queue->declare();
    }

    /**
     * @brief binds one exchange to another exchange
     * @details Returns an instance of CAMQPExchange for exchange a queue is bind
     * @param $exchange
     * @param $queue
     * @param $routingKey
     */
    public function bindExchangeToExchange($from, $to, $routingKey = "")
    {
        $exchange = $this->exchange($to);
        $exchange->bind($from, $routingKey);
        return $exchange;
    }

    /**
     * @brief Binds a queue to specified exchange
     * @details Returns an instance of CAMQPQueue for queue an exchange is bind
     * @param $queue
     * @param $exchange
     * @param $routingKey
     */
    public function bindQueueToExchange($queue, $exchange, $routingKey = "")
    {
        $queue = $this->queue($queue);
        $queue->bind($exchange, $routingKey);
        return $queue;
    }

    /**
     * @brief Get exchange by name
     * @param $name  name of exchange
     * @return  object AMQPExchange
     */
    public function exchange($name)
    {
        $ex = new CAMQPExchange($this->channel);
        $ex->setName($name);
        return $ex; // so $exy =)
    }

    /**
     * @brief Get queue by name
     * @param $name  name of exchange
     * @return  object AMQPQueue
     */
    public function queue($name)
    {
        $queue = new CAMQPQueue($this->channel);
        $queue->setName($name);
        return $queue;
    }

    /**
     * Returns AMQPChannel instance
     *
     * @return AMQPChannel
     */
    public function getChannel()
    {
        return $this->channel;
    }
}