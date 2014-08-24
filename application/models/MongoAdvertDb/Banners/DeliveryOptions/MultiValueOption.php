<?php

namespace MongoAdvertDb\Banners\DeliveryOptions;

abstract class MultiValueOption extends Option {
    
    const COMPARISON_ANY_OF     = '=~';
    const COMPARISON_NONE_OF    = '!~';
    
    const TYPE_CHECKBOX = 'Checkbox';
    const TYPE_SELECT   = 'Select';
    
    protected $_type = self::TYPE_CHECKBOX;
    
    public function getComparisons() {
        return array(
            self::COMPARISON_ANY_OF     => _('any of'),
            self::COMPARISON_NONE_OF    => _('none of'),
        );
    }

    public function getTemplate() {
        return 'MultiValue' . $this->_type . 'Template';
    }

    protected function addValue($value, $comparison) {
        $value = (array) $value;
        
	if (count(array_diff($value, array_keys($this->getAvailableValues())))) {
            throw new \Exception('Invalid values');
        }
        
        parent::addValue($value, $comparison);
        return $this;
    }
    
    public function getValue($subKeyName = null)
    {
        $value = parent::getValue($subKeyName);
        if(!$value) {
            return $subKeyName ? null : array();
        }
        
        return $value;
    }

    public function anyOf(array $values) {
        $this->setValue($values, self::COMPARISON_ANY_OF);
    }

    public function noneOf(array $values) {
        $this->setValue($values, self::COMPARISON_NONE_OF);
    }
}
