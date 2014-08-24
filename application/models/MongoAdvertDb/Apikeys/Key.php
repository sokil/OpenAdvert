<?php

namespace MongoAdvertDb\Apikeys;

class Key extends \Sokil\Mongo\Document
{
    protected $_data = [
        'name'  => null,
        'key'   => null,
    ];
    
    private static $_keyAlphabet = 'qwertyuiopasdfghjklzxcvbnm1234567890!@#$%&*?';
    
    public function beforeConstruct()
    {
        $this->onBeforeSave(function() {
            if(!$this->key) {
                $this->generate();
            }
        });
    }
    
    public function generate()
    {
        $alphabetLength = strlen(self::$_keyAlphabet) - 1;
        
        $key = '';
        for($i = 0; $i < 32; $i++) {
            $key .= self::$_keyAlphabet[mt_rand(0, $alphabetLength)];
        }
        
        $this->key = $key;
        
        return $this;
    }
    
    public function __toString()
    {
        return $this->key;
    }
}