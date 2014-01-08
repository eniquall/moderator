<?php

/**
 * @ingroup CAMQP
 */

/**
 * @class   CAMQPExchange
 * @brief   Fake Exchange
 * @details Stubs all methods to support fake mode for CAMQP
 * 
 * "A" - Team:
 * @author     Andrey Evsyukov <thaheless@gmail.com>
 * @author     Alexey Spiridonov <a.spiridonov@2gis.ru>
 * @author     Alexey Papulovskiy <a.papulovskiyv@2gis.ru>
 * @author     Alexander Biryukov <a.biryukov@2gis.ru>
 * @author     Alexander Radionov <alex.radionov@gmail.com>
 * @author     Andrey Trofimenko <a.trofimenko@2gis.ru>
 * @author     Artem Kudzev <a.kiudzev@2gis.ru>
 * @author     Alexey Ashurok <a.ashurok@2gis.ru>
 *   
 * @link       http://www.2gis.ru
 * @copyright  2GIS
 * @license http://www.yiiframework.com/license/
 * 
 * Requirements:
 * ---------------------
 *  - Yii 1.1.x or above
 *  - AMQP PHP library
 * 
 */
class CAMQPExchange
{
    private static $funct_list = array('bind', 'declare', 'delete', 'publish', 'existingExchange');

	/**
	 * @brief Create an instance of AMQPExchange
	 * @param null | AMQPConnect $connection
	 * @param string $exchangeName
	 */
    public function __construct ($connection, $exchangeName = "")
    {
    	Yii::trace("FAKE MODE: Initiated for exchange '$exchangeName'", "CEXT.CAMQP.CAMQPExchange");
    }

    public function  __call($name, $arguments)
    {
        if (!in_array($name, self::$funct_list))
            throw new BadMethodCallException('Unknown method: '.$name);
        Yii::trace("FAKE MODE: Execute with params: " . print_r($arguments, true), "CEXT.CAMQP.CAMQPQueue.$name");
        return true;
    }
    
}