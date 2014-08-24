<?php

namespace MongoAdvertDb;

use \MongoAdvertDb\Visitors\Visitor;

class Visitors extends \Sokil\Mongo\Collection
{
    const STORE_COOKIE_DAYS = 60;
    
    protected $_queryExpressionClass = '\MongoAdvertDb\Visitors\QueryExpression';

    /**
     *
     * @var \MongoAdvertDb\Visitors\Visitor
     */
    private $_current;

    public function getDocumentClassName(array $documentData = null)
    {
        return '\\MongoAdvertDb\\Visitors\\Visitor';
    }

    public function getCurrent()
    {
        if ($this->_current) {
            return $this->_current;
        }

        if (isset($_COOKIE['v'])) {
            $visitor = $this->getDocument($_COOKIE['v']);
        }
        
        if(empty($visitor)) {
            $visitor = $this
                ->createDocument()
                ->save();
        }
        
        $this->setCurrent($visitor);

        return $this->_current;
    }
    
    public function setCurrent(Visitor $visitor)
    {
        $this->_current = $visitor;
        
        $visitor->markLastActive();
        
        setcookie('v', $visitor->getId(), time() + 90 * 24 * 60 * 60, '/');
        
        return $this;
    }
    
    /**
     * Delete visitors with last active time older then self::STORE_COOKIE_DAYS
     * @return \MongoAdvertDb\Visitors
     */
    public function clearUnused()
    {
        $this->deleteDocuments($this->expression()->byLastActiveLessThen(time() - self::STORE_COOKIE_DAYS * 24 * 60 * 60));
        return $this;
    }

}
