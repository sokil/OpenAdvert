<?php

namespace MongoAdvertDb\Banners\DeliveryOptions;

abstract class SingleValueOption extends Option {
    
    const COMPARISON_EQUALS             = '==';
    const COMPARISON_NOT_EQUALS         = '!=';
    const COMPARISON_CONTAINS           = '=~';
    const COMPARISON_NOT_CONTAINS       = '!~';
    const COMPARISON_REGEX_MATCH        = '=x';
    const COMPARISON_REGEX_NOT_MATCH    = '!x';
    
    public function getComparisons() {
        return array(
            self::COMPARISON_EQUALS             => _('equals'),
            self::COMPARISON_NOT_EQUALS         => _('not equals'),
            self::COMPARISON_CONTAINS           => _('contains'),
            self::COMPARISON_NOT_CONTAINS       => _('does not contains'),
            self::COMPARISON_REGEX_MATCH        => _('regex match'),
            self::COMPARISON_REGEX_NOT_MATCH    => _('regex does not match'),
        );
    }
    
    public function getTemplate() {
        return 'SingleValueTemplate';
    }

    public function equals($value) {
        $this->setValue($value, self::COMPARISON_EQUALS);
    }

    public function notEquals($value) {
        $this->setValue($value, self::COMPARISON_NOT_EQUALS);
    }

}
