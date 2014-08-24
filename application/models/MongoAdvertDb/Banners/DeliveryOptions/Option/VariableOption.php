<?php

namespace MongoAdvertDb\Banners\DeliveryOptions\Option;

use \MongoAdvertDb\Banners\DeliveryOptions\SingleValueOption;
use \Sokil\Mongo\QueryBuilder;

class VariableOption extends SingleValueOption {
    
    public function getCaption() {
        return _('Variable');
    }
    
    public function getTemplate() {
        return 'VariableTemplate';
    }
    
    public function getComparisons() {
        return array(
            self::COMPARISON_EQUALS             => _('equals'),
            self::COMPARISON_NOT_EQUALS         => _('not equals'),
        );
    }
    
    public function getCompareExpressions(QueryBuilder $builder)
    {
        if(empty($_GET['var']) || !is_array($_GET['var'])) {
            return false;
        }
        
        $values = $_GET['var'];
        
        $expressionList = array();
        foreach ($values as $k => $v) {
            $value = array(
                'key' => $k,
                'value' => $v
            );
            
            $expressions = array_map(function($comparisonRule) use($builder, $value) {
                    switch ($comparisonRule) {
                        case self::COMPARISON_NOT_EQUALS:
                            return $builder->expression()->optionVarValueNotEquals($this->getType(), $value);

                        case self::COMPARISON_EQUALS:
                        default:
                            return $builder->expression()->optionValueEquals($this->getType(), $value);
                    }
                }, array_keys($this->getComparisons()));
            $expressionList[] = $builder->expression()->whereOr($expressions);
        }
        
        return $expressionList;
    }
}
