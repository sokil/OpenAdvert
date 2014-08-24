<?php

class MongoAdapter extends CApplicationComponent
{
    private $_dsn;
    
    private $_defaultDatabaseName;
    
    private $_mapping;
    
    private $_mongoClient;
    
    private $_databasePool = array();
    
    private $_options = array('connect' => true);
    
    private $_loggerSectionName;
    
    public function setDsn($dsn)
    {
        $this->_dsn = $dsn;
    }
    
    public function setLogger($name)
    {
        $this->_loggerSectionName = $name;
        
    }
    
    public function setOptions(array $options)
    {
        $this->_options = $options;
        return $this;
    }
    
    public function setDefaultDatabase($databaseName)
    {
        $this->_defaultDatabaseName = $databaseName;
    }
    
    public function setMap(array $mapping) 
    {
        $this->_mapping = $mapping;
    }
    
    public function getClient()
    {
        if($this->_mongoClient) {
            return $this->_mongoClient;
        }
        
        $this->_mongoClient = new \Sokil\Mongo\Client($this->_dsn, $this->_options);
        
        if($this->_loggerSectionName) {
            $this->_mongoClient->setLogger(Yii::app()->{$this->_loggerSectionName});
        }
        
        return $this->_mongoClient;
    }
    
    public function getDatabase($databaseName = null)
    {
        if(isset($this->_databasePool[$databaseName])) {
            return $this->_databasePool[$databaseName];
        }
        
        if(!$databaseName) {
            $databaseName = $this->_defaultDatabaseName;
        }
        
        $database =  $this->getClient()->getDatabase($databaseName);
        
        // map
        while(is_string($this->_mapping[$databaseName])) {
            $databaseName = $this->_mapping[$databaseName];
        }
        
        $database->map($this->_mapping[$databaseName]);
        
        $this->_databasePool[$databaseName] = $database;
        
        return $database;
    }
    
    public function getCollection($collectionName, $databaseName = null)
    {
        return $this->getDatabase($databaseName)->getCollection($collectionName);
    }
}