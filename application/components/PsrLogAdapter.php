<?php

use Psr\Log\LogLevel;

class PsrLogAdapter extends \Psr\Log\AbstractLogger
{
    private $_levelMap = array(
        LogLevel::EMERGENCY => CLogger::LEVEL_ERROR,
        LogLevel::ALERT     => CLogger::LEVEL_ERROR,
        LogLevel::CRITICAL  => CLogger::LEVEL_ERROR,
        LogLevel::ERROR     => CLogger::LEVEL_ERROR,
        LogLevel::WARNING   => CLogger::LEVEL_WARNING,
        LogLevel::NOTICE    => CLogger::LEVEL_INFO,
        LogLevel::INFO      => CLogger::LEVEL_INFO,
        LogLevel::DEBUG     => CLogger::LEVEL_PROFILE,
    );
    
    public $category = 'DEFAULT';
    
    public function init()
    {
    }
    
    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null
     */
    public function log($level, $message, array $context = array())
    {            
        if($context) {
            $message .= PHP_EOL . PHP_EOL . json_encode($context);
        }
        
        Yii::log($message, $this->_levelMap[$level], $this->category);
    }
}