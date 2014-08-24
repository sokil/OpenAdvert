<?php

namespace MongoAdvertDb\Banners\DeliveryOptions\Option;

use \MongoAdvertDb\Banners\DeliveryOptions\SingleValueOption;
use \Sokil\Mongo\QueryBuilder;

class DateOption extends SingleValueOption
{   
    public function getCaption() {
        return _('Date');
    }
    
    public function getComparisons() {
        return array(
            self::COMPARISON_EQUALS             => _('equals'),
            self::COMPARISON_NOT_EQUALS         => _('not equals'),
        );
    }

    public function getTemplate()
    {
        return 'DateTemplate';
    }
    
    public function registerStaticFiles() {
        \Yii::app()->getClientScript()->registerPackage('pickmeup');
    }
    
    public function getCompareExpressions(QueryBuilder $builder)
    {
        $value = date('Y-m-d');
        if(!$value) {
            return false;
        }
        
        return array_map(function($comparisonRule) use($builder, $value) {
            switch ($comparisonRule) {
                case self::COMPARISON_NOT_EQUALS:
                    return $builder->expression()->optionValueNotEquals($this->getType(), $value);
                    
                case self::COMPARISON_EQUALS:
                default:
                    return $builder->expression()->optionValueEquals($this->getType(), $value);
            }
        }, array_keys($this->getComparisons()));
    }
}
