<?php

class MongoDataProvider extends CDataProvider {

    public $attributes;
    public $filter;
    
    /**
     *
     * @var \Sokil\Mongo\QueryBuilder
     */
    private $_queryBuilder;

    public function __construct($dataSource, $config = array()) {
        // name of collection
        if (is_string($dataSource)) {
            $collection = Yii::app()->mongo->getCollection($dataSource);
            $this->_queryBuilder = $collection->find();
        } 
        // query builder
        else if ($dataSource instanceof \Sokil\Mongo\QueryBuilder) {
            $this->_queryBuilder = $dataSource;
        }

        foreach ($config as $key => $value) {
            $this->$key = $value;
        }

        if (isset($this->filter)) {
            foreach ($this->filter as $a => $v) {
                if (!empty($v)) {
                    $this->_queryBuilder->where($a, array('$regex' => $v, '$options' => 'i'));
                }
            }
        }
        
        if (!isset($this->attributes) || !is_array($this->attributes)) {
            throw new \Exception('Attributes are required.');
        }
    }

    public function fetchData() {
        if (($pagination = $this->getPagination()) !== false) {
            $pagination->setItemCount($this->getTotalItemCount());
            $this->_queryBuilder
                ->skip($pagination->getOffset())
                ->limit($pagination->getLimit());
        }
        
        $sort = $this->getSort();
        if ($sort) {
            foreach ($sort->getDirections() as $a => $d) {
                if ($d == 0) {
                    $d = -1; // mongo desc
                }
                $this->_queryBuilder->sort(array($a => $d));
            }
        }
        
        return array_values($this->_queryBuilder->findAll());
    }

    public function calculateTotalItemCount()
    {
        return $this->_queryBuilder->count();
    }

    public function fetchKeys()
    {
        return array_keys($this->getData());
    }

    public function getSort($className = 'CSort')
    {
        if (($sort = parent::getSort($className)) !== false) {
            $sort->attributes = $this->attributes;
        }
        
        return $sort;
    }

}
