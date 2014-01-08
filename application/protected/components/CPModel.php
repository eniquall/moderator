<?php

abstract class CPModel extends EMongoDocument
{
    /**
     * @brief executes the validation rules as declared in {@link rules}.
     * @desc collects all validator's error messages and formats error output
     * 
     * @param array $attributes
     * @param boolean $clearErrors
     * @return boolean true if no errors
     * @throws ApiException in case of errors
     */
    public function validate($attributes = null, $clearErrors = true)
    {
        if (!parent::validate($attributes, $clearErrors)) {
            $msg = array();
            foreach ($this->getErrors() as $error) {
                $msg[] = reset($error);
            }
            throw new ApiException(implode('; ', $msg), ApiException::UNKNOWN);
        }

        return true;
    }
    
    /**
     * Updates the row represented by this active record.
     * All loaded attributes will be saved to the database.
     * Performs Validation!
     * @param array $attributes list of attributes that need to be saved. Defaults to null,
     * meaning all attributes that are loaded from DB will be saved.
     * @param boolean modify if set true only selected attributes will be replaced, and not the whole document
     * @return boolean whether the update is successful
     * @throws ApiException if validation is failed
     * @throws CDbException if the record is new
     * @throws CException if an error occured
     * @throws EMongoException on fail of update
     * @throws MongoCursorException on fail of update, when safe flag is set to true
     * @throws MongoCursorTimeoutException on timeout of db operation , when safe flag is set to true
     * @since v1.0
     */
    public function update(array $attributes = null, $modify = false)
    {
        $this->validate($attributes);

        return parent::update($attributes, $modify);
    }
    
    /**
     * Magic search method, provides basic search functionality.
     *
     * Returns EMongoDocument object ($this) with criteria set to
     * regexp: /$attributeValue/i
     * used for Data provider search functionality
     * @param boolean $caseSensitive whathever do a case-sensitive search, default to false
     * @return EMongoDocument
     * @since v1.2.2
     */
    public function search($caseSensitive = false)
    {
        $criteria = $this->getDbCriteria();
        $pkField  = $this->primaryKey();

        foreach ($this->getSafeAttributeNames() as $attribute) {
            if ($this->$attribute !== null && $this->$attribute !== '') {
                if (is_array($this->$attribute) || is_object($this->$attribute)) {
                    $criteria->$attribute = $this->$attribute;
                } elseif (preg_match('/^(?:\s*(<>|<=|>=|<|>|=|!=|==))?(.*)$/', $this->$attribute, $matches)) {
                    $op    = $matches[1];
                    $value = $matches[2];
                    
                    if ($attribute == $pkField) {
                        $criteria->{$pkField} = new MongoId($value);
                    } else if ($op || is_numeric($value)) {
                        if ($op === '=') {
                            $op = '==';
                        }
                        call_user_func(array($criteria, $attribute), $op ?: '==', is_numeric($value) ? floatval($value) : $value);
                    } else {
                        $criteria->$attribute = new MongoRegex($caseSensitive ? '/' . preg_quote($this->$attribute) . '/' : '/' . preg_quote($this->$attribute) . '/i');
                    }
                }
            }
        }
        
        // default sorting 
        $criteria->setSort(array('_id' => EMongoCriteria::SORT_DESC));
        $this->setDbCriteria($criteria);

        return new EMongoDocumentDataProvider($this);
    }

    /**
     * @brief returns Object identificator
     * 
     * @return string
     */
    public function getId()
    {
        if (!($this->_id instanceof MongoId)) {
            return null;
        }

        return $this->_id->{'$id'};
    }
    
    public function setId($id)
    {
        if ($id instanceof MongoId) {
            $this->_id = $id;
        }

        $this->_id = new MongoId($id);

        return $this;
    }
}