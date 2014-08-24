<?php

namespace MongoAdvertDb\Banners\DeliveryOptions\Option;

use \MongoAdvertDb\Banners\DeliveryOptions\MultiValueOption;
use \Sokil\Mongo\QueryBuilder;

class LanguageOption extends MultiValueOption
{   
    public function getCaption() {
        return _('Language');
    }

    public function getAvailableValues() {
        $values = array(
            'en' => _('English'),
            'ru' => _('Russian'),
            'uk' => _('Ukrainian'),
        );
        
        asort($values, SORT_LOCALE_STRING);
        
        return $values;
    }
    
    public function getCompareExpressions(QueryBuilder $builder)
    {
        $value = \Yii::app()->getRequest()->getPreferredLanguages();
        if (!$value) {
            return false;
        }

        return array_map(function($comparisonRule) use($builder, $value) {
            switch ($comparisonRule) {
                case self::COMPARISON_NONE_OF:
                    return $builder->expression()->optionValueNoneOf($this->getType(), $value);
                    
                case self::COMPARISON_ANY_OF:
                default:
                    return $builder->expression()->optionValueAnyOf($this->getType(), $value);
            }
        }, array_keys($this->getComparisons()));
    }
}
