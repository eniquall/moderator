<?php

class NodeModel extends CPModel 
{   
    const STATUS_ENABLED = 'en';
    const STATUS_DISABLED = 'dis';
    
    public $ip;
    public $name;
    public $location;
    public $status = self::STATUS_ENABLED;
    public $weight = 1;

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function getCollectionStructure()
    {
        return array('name' => 'node');
    }
    
    public function getMongoDBComponent()
    {
        return Yii::app()->getComponent('dictionarydb');
    }
    
    public function getListWeight()
    {
        return range(0, 20);
    }
    
    public function getViewStatus()
    {
        $list = $this->getListStatus();
        if ($this->status && isset($list[$this->status])) {
            return $list[$this->status];
        }
    
        return NULL;
    }
    
    public function indexes()
    {
        return array(
            'location' => array(
                'key' => array(
                    'location' => '2d'
                )
            )
        );
    }
    
    public function getListStatus()
    {
        return array(
                self::STATUS_ENABLED  => 'enabled',
                self::STATUS_DISABLED => 'disabled'
        );
    }
    
    public function attributeLabels() 
    {
        return array(
                '_id'      => 'ID',
                'ip'       => 'IP',
                'name'     => 'Name',
                'location' => 'Location',
                'status'   => 'Status',
                'weight'   => 'Weight',
        );
    }
    
    public function rules() 
    {
        return array(
            array('ip, name, location', 'required'),
            array('name', 'uniqueName'),
            array('name', 'url'),
            array('location', 'LocationValidator'),
            array('location', 'filter', 'filter' => array('LocationValidator', 'normal')),
            array('status', 'in', 'range' => array_keys($this->getListStatus())),
        );
    }    
    
    /**
     * Check unique name in collection 
     * 
     * @param unknown_type $attribute
     * @param unknown_type $params
     */
    public function uniqueName($attribute, $params)
    {
        $name = $this->attributes[$attribute];
        
        $criteria = new EMongoCriteria();
        $criteria->name = $name;
        
        if (!$this->getIsNewRecord()) {  
            $criteria->_id('!=', $this->_id);
        }
        
        if ($this->count($criteria) ) {
            $this->addError($attribute, 'Name ' . $name . ' already exists');
        }
    }
}