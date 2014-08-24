<?php

class ArrayDataProvider extends CArrayDataProvider
{

    private $_sum = [];

    public function getSum($attribute)
    {
        if (!isset($this->_sum[$attribute])) {
            $this->_sum[$attribute] = array_reduce($this->getData(), function($result, $item) use($attribute) {
                    return $result + $item[$attribute];
                });
        }
        return $this->_sum[$attribute];
    }

}