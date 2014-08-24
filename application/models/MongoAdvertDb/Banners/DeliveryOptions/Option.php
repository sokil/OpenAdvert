<?php

namespace MongoAdvertDb\Banners\DeliveryOptions;

use \Sokil\Mongo\QueryBuilder;

abstract class Option extends \Sokil\Mongo\Structure {
    
    protected $_data = array(
        'option'        => null,
        'comparison'    => null,
        'value'         => null,
    );

    public function __construct() {
        $this->_data['option'] = self::getType();
    }
    
    public function getType()
    {
        $path = explode('\\', get_called_class());
        return lcfirst(str_replace('Option', '', array_pop($path)));
    }
    
    public function getAvailableValues() {
        return array();
    }
    
    abstract public function getCaption();
    
    abstract public function getComparisons();

    abstract public function getTemplate();
    
    /**
     * @return array list of expressions
     */
    abstract public function getCompareExpressions(QueryBuilder $builder);
    
    public function setValue($value, $comparison) {
	if (!isset($this->getComparisons()[$comparison])) {
            throw new \Exception('Unknown comparison');
        }
        
        $this->set('comparison', $comparison);
        $this->set('value', $value);
    }
    
    public function getValue($subKeyName = null)
    {
        $keyName = 'value';
        if($subKeyName) {
            $keyName .= '.' . $subKeyName;
        }
        
        return $this->get($keyName);
    }
    
    public function setMeta($meta)
    {
        $this->set('meta', $meta);
        return $this;
    }
    
    public function getMeta($subKeyName = null)
    {
        $keyName = 'meta';
        if($subKeyName) {
            $keyName .= '.' . $subKeyName;
        }
        
        return $this->get($keyName);
    }
    
    public function getComparison()
    {
        return $this->get('comparison');
    }
    
    public function render($index)
    {
        return \Yii::app()->mongo->getCollection('banners')->getDeliveryOptions()->getRenderer()->render($this, $index);
    }
    
    public function registerStaticFiles() {}
}
