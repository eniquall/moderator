<?php

/**
 * @ingroup CAMQP
 */

/**
 * @class   CAMQPExchange
 * @brief   Represents an AMQP exchange.
 * @details
 *  
 * @author     Andrey Evsyukov <evsyukov@citypatrol.ru>
 * 
 * Requirements:
 * ---------------------
 *  - Yii 1.1.x or above
 *  - AMQP Pecl PHP library
 *
 */
class CAMQPExchange extends AMQPExchange
{
	/**
	 * @brief Publish a message to the exchange represented by the AMQPExchange object.
	 * @param string $message The message to publish.
	 * @param srting $routingKey The routing key to which to publish.
	 */
    public function  publish($message, $routingKey)
    {
        Yii::trace("Publish '$message' with key '$routingKey'", "CEXT.CAMQP.CAMQPExchange.publish");
        return parent::publish($message, $routingKey);
    }

    /**
     * @brief The bind purpose
     * @param string $queueName The name of the queue to which to bind.
     * @param string $routingKey The routing key to use as a binding.
     */
    public function bind($queueName, $routingKey)
    {
        Yii::trace("Bind '$queueName' with key '$routingKey'", "CEXT.CAMQP.CAMQPQueue.bind");
        return parent::bind($queueName, $routingKey);
    }

    /**
     * @brief Delete the exchange from the broker.
     * @param $exchangeName The name of the exchange to delete.
     */
    public function delete($exchangeName = null)
    {
        Yii::trace("Delete '$exchangeName'", "CEXT.CAMQP.CAMQPExchange.delete");
        return parent::delete($exchangeName);
    }
}