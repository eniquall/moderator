<?php

/**
 * @ingroup CAMQP
 */

/**
 * @class   CAMQPQueue
 * @brief   Represents an AMQP queue
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
class CAMQPQueue extends AMQPQueue
{
	/**
     * @param integer $flags A bitmask of any of the flags: AMQP_NOACK. 
	 * @brief Retrieve the next message from the queue.
	 * 
	 * @TODO: Refactor it after proper migration to CentOS!
	 * 
	 * @return AMQPEnvelope
	 */
    public function get($flags = null)
    {
        Yii::trace("Trying to get messages", "CEXT.CAMQP.CAMQPQueue.get");
        $envelope = parent::get($flags ^ AMQP_AUTOACK);
        if (!$envelope || !($envelope instanceof AMQPEnvelope)) {
            return false;
        }
        return array('msg' => $envelope->getBody());
    }
    
    /**
     * @brief Acknowledge the receipt of a message
     * @param integer $deliveryTag The message delivery tag of which to acknowledge receipt.
     * @param integer $flags The only valid flag that can be passed is AMQP_MULTIPLE.
     */
    public function ack($deliveryTag, $flags = null)
    {
        Yii::trace("Execute with params: " . print_r(func_get_args(), true), "CEXT.CAMQP.CAMQPQueue.ack");
        return parent::ack($deliveryTag, $flags);
    }

    /**
     * @brief Bind the given queue to a routing key on an exchange.
     * @param string $exchangeName The exchange name on which to bind.
     * @param string $routingKey   The routing key to which to bind.
     */
    public function bind($exchangeName, $routingKey = "")
    {
        Yii::trace("Execute with params: " . print_r(func_get_args(), true), "CEXT.CAMQP.CAMQPQueue.bind");
        return parent::bind($exchangeName, $routingKey);
    }

    /**
     * @brief Cancel a queue binding.
     * @param string $consumerTag The queue name to cancel, if the queue object is not already representative of a queue.
     */
    public function cancel($consumerTag)
    {
        Yii::trace("Execute with params: " . print_r(func_get_args(), true), "CEXT.CAMQP.CAMQPQueue.cancel");
        return parent::cancel($consumerTag);
    }

    /**
     * @brief The consume purpose
     * @param string $numMessages number of messages to fetch 
     * @param $flags $flags
     */
    public function consume($numMessages, $flags = null)
    {
        Yii::trace("Execute with params: " . print_r(func_get_args(), true), "CEXT.CAMQP.CAMQPQueue.consume");
        return parent::consume($numMessages, $flags);
    }

    /**
     * @brief Delete a queue and its contents.
     * @param string $queueName The name of the queue to deletes
     */
    public function delete($queueName)
    {
        Yii::trace("Delete queue '$queueName'", "CEXT.CAMQP.CAMQPQueue.delete");
        return parent::delete($queueName);
    }

    /**
     * @brief Purge the contents of a queue
     * @param string $queueName The name of the queue to purge
     */
    public function purge($queueName)
    {
        Yii::trace("Purge queue '$queueName'", "CEXT.CAMQP.CAMQPQueue.purge");
        return parent::purge($queueName);
    }

    /**
     * @brief Unbind the queue from a routing key.
     * @param string $exchangeName The name of the exchange on which the queue is bound.
     * @param string $routingKey   The binding routing key used by the queue.
     */
    public function unbind($exchangeName, $routingKey = "")
    {
        Yii::trace("Execute with params: " . print_r(func_get_args(), true), "CEXT.CAMQP.CAMQPQueue.unbind");
        return parent::unbind($exchangeName, $routingKey);
    }
}
