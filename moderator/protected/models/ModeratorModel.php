<?php

class ModeratorModel extends CPModel 
{   
    
    public $name;
    public $active;
    public $email;
 

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function getCollectionStructure()
    {
        return array('name' => 'moderator');
    }
    
    public function getMongoDBComponent()
    {
        return Yii::app()->getComponent('db');
    }
    
    
    public function indexes()
    {
        return array(
            //'location' => array(
            //    'key' => array(
            //        'location' => '2d'
            //    )
            )
        );
    }
    
   
    
    public function attributeLabels() 
    {
        return array(
                '_id'      => 'ID',
                'name'     => 'Name',
        );
    }
    
    public function rules() 
    {
        return array(
            array('name', 'required'),
            array('email', 'uniqueEmail'),
            array('name', 'url'),
            array('active', 'in', 'range' => array[0,1]),
        );
    }    
    
    /**
     * Check unique name in collection 
     * 
     * @param unknown_type $attribute
     * @param unknown_type $params
     */
    public function uniqueEmail($attribute, $params)
    {
        $email = $this->attributes[$attribute];
        
        $criteria = new EMongoCriteria();
        $criteria->email = $email;
        
        if (!$this->getIsNewRecord()) {  
            $criteria->_id('!=', $this->_id);
        }
        
        if ($this->count($criteria) ) {
            $this->addError($attribute, 'Email ' . $email . ' already exists');
        }
    }
}