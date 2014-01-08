<?php

/**
 * @defgroup ZabbixSender
 */

/**
 * @class ZabbixSender
 * @brief Class implement ZabbixSender protocol
 * @ingroup ZabbixSender
 */

class ZabbixSender extends CApplicationComponent {

    public $server = 'localhost';
    public $port = 10051;
    public $timeout = 30;
    
    private $protocolHeader = 'ZBXD';
    private $version = 1;
    private $response = null;
    private $socket;
    private $data;

    public function init() {
        parent::init();
        $this->setData();
    }

    private function setData() {
        $this->data = array(
            "request" => "sender data",
            "data" => array()
        );
    }

    public function addData($hostname = null, $key = null, $value = null, $clock = null) 
    {   
        $input = array("host" => $hostname, "value" => $value, "key" => $key);
        if (isset($clock)) {
            $input["clock"] = $clock;
        }
        array_push($this->data["data"], $input);
        return $this;
    }

    private function buildData() 
    {
        $json_data = json_encode($this->data);
        
        $json_length = strlen($json_data);
        $data_header = pack("aaaaCCCCCCCCC", substr($this->protocolHeader, 0, 1), substr($this->protocolHeader, 1, 1), substr($this->protocolHeader, 2, 1), substr($this->protocolHeader, 3, 1), intval($this->version), ($json_length & 0xFF), ($json_length & 0x00FF) >> 8, ($json_length & 0x0000FF) >> 16, ($json_length & 0x000000FF) >> 24, 0x00, 0x00, 0x00, 0x00
        );
        return ($data_header . $json_data);
    }

    public function send() {

        $error = '';
        $errormsg = '';

        $this->socket = @fsockopen($this->server, intval($this->port), $error, $errormsg, $this->timeout);

        if (!$this->socket) {
            throw new CException(sprintf('Cant connect to: %s, %s,%s', $this->server.':'.$this->port, $error, $errormsg));
        }
        
        Yii::trace('Set zabbix.model.' . time() . ' ' . print_r($this->data, true), 'ext.ZabbixReader.addData');
        $data = $sendData = $this->buildData();

        // write data to zabbix
        $totalWritten = 0;
        $length = strlen($sendData);
        while ($totalWritten < $length) {
            $writeSize = @fwrite($this->socket, $sendData);
            if ($writeSize === false) {
                throw new CException('Cant send data to zabbix');
            } else {
                $totalWritten += $writeSize;
                $sendData = substr($sendData, $writeSize);
            }
        }

        if ($writeSize != $length) {
            throw new CException('Cant send data to zabbix');
        }

        // read data from zabbix
        $zdata = "";
        while (!feof($this->socket)) {
            $buffer = fread($this->socket, 8192);
            if ($buffer === false) {
                return false;
            }
            $zdata .= $buffer;
        }
        if ($zdata === false) {
            throw new CException('Cant read response from zabbix');
        }

        //close socket
        fclose($this->socket);
        
        if (substr($zdata, 0, 4) == $this->protocolHeader) {
            $this->response = json_decode(substr($zdata, 13), true);
            if (is_null($this->response)) {
                throw new CException('Cant parse response:' . $zdata);
            } elseif ($this->response['response'] == "success") {
                $this->setData();
                return true;
            } else {
                $this->response = false;
                return false;
            }
        } else {
            $this->response = false;
            throw new CException('Invalid protocol header in received data / ' . print_r($this->data, true));
        }
    }

}
