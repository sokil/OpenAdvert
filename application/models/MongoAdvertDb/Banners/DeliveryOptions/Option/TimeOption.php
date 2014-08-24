<?php

namespace MongoAdvertDb\Banners\DeliveryOptions\Option;

use \MongoAdvertDb\Banners\DeliveryOptions\SingleValueOption;
use \Sokil\Mongo\QueryBuilder;

class TimeOption extends SingleValueOption
{   
    public function getCaption() {
        return _('Time');
    }
    
    public function getComparisons() {
        return array(
            self::COMPARISON_EQUALS             => _('equals'),
            self::COMPARISON_NOT_EQUALS         => _('not equals'),
        );
    }
    
    public function getTemplate()
    {
        return 'TimeTemplate';
    }
    
    public function setValue($value, $comparison) {
	if (!isset($this->getComparisons()[$comparison])) {
            throw new \Exception('Unknown comparison');
        }
        
        $this->set('comparison', $comparison);
        
        $value = array_map(function($val) {
            return str_pad($val, 2, '0', STR_PAD_LEFT);
        }, $value);
            
        $value = array(
            'min' => $value['minh'] . $value['minm'],
            'max' => $value['maxh'] . $value['maxm'],
            'mid' => $value['maxh'] < $value['minh'],
        );
        
        $value = array_map(function($val) {
            return intval($val);
        }, $value);
            
        $this->set('value', $value);
    }
    
    public function getValue($subKeyName = null)
    {
        $value = $this->get('value');
        if (empty($value)) {
            return;
        }
        
        $value = array_map(function($val) {
            return str_pad($val, 4, '0', STR_PAD_LEFT);
        }, $value);
        
        $value = array(
            'minh' => substr($value['min'], 0, 2),
            'minm' => substr($value['min'], 2, 2),
            'maxh' => substr($value['max'], 0, 2),
            'maxm' => substr($value['max'], 2, 2),
        );
        
        return $value;
    }
    
    public function getCompareExpressions(QueryBuilder $builder)
    {
        $value = (int) date('Hi');
        
        return array_map(function($comparisonRule) use($builder, $value) {
            switch ($comparisonRule) {
                case self::COMPARISON_NOT_EQUALS:
                    return $builder->expression()->optionTimeNotEquals($this->getType(), $value);
                    
                case self::COMPARISON_EQUALS:
                default:
                    return $builder->expression()->optionTimeEquals($this->getType(), $value);
            }
        }, array_keys($this->getComparisons()));
    }
}
