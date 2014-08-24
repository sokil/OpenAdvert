<?php

namespace MongoAdvertDb\Log;

class Record extends \Sokil\Mongo\Document
{
    protected $_data = array(
        'level'         => null,
        'category'      => null,
        'logtime'       => null,
        'message'       => null,
        'requestUri'    => null,
        'userAgent'     => null,
    );
    
    public function beforeConstruct() {
        $this->onBeforeInsert(function() {
            $this->set('requestUri', isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null);
            $this->set('userAgent', isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null);
        });
    }
    
    public function setLevel($level)
    {
        return $this->set('level', $level);
    }
    
    public function setCategory($category)
    {
        return $this->set('category', $category);
    }
    
    public function setTime($time)
    {
        if(!is_numeric($time)) {
            $time = strtotime($time);
            if(!$time) {
                $time = time();
            }
        }
        
        return $this->set('logtime', new \MongoDate($time));
    }
    
    public function setMessage($message)
    {
        return $this->set('message', $message);
    }
}