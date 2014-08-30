<?php

class LogForm extends CFormModel
{

    public $level;
    public $category;
    public $dateFrom;
    public $dateTo;
    public $requestUri;
    public $message;

    public function init()
    {
        if (!isset($_GET['sort'])) {
            $_GET['sort'] = 'logtime';
        }
        
        $formParams = \Yii::app()->request->getParam('LogForm');
        if (is_array($formParams)) {
            foreach ($formParams as $name => $value) {
                $this->$name = $value;
            }
        }
    }

    public static function getLevels()
    {
        $refl = new ReflectionClass('CLogger');
        $levels = array_values($refl->getConstants());
        return array_combine($levels, $levels);
    }

    public function getDatesFilter()
    {
        $from = CHtml::activeTextField($this, 'dateFrom', array('id' => 'txtDateFrom', 'placeholder' => _('from'), 'style' => 'width:90px'));
        $to = CHtml::activeTextField($this, 'dateTo', array('id' => 'txtDateTo', 'placeholder' => _('to'), 'style' => 'width:90px'));
        return $from . $to;
    }
    
    public function getDataSource() {
        $log = Yii::app()->mongo->getCollection('log')->findAsArray();
        
        $refl = new ReflectionClass($this);
        foreach ($refl->getProperties() as $property) {
            $name = $property->name;
            if (empty($this->$name)) {
                continue;
            }
            switch ($name) {
                case 'dateFrom':
                    $log->whereGreaterOrEqual('logtime', new \MongoDate(strtotime($this->$name . ' 00:00:00')));
                    break;
                case 'dateTo':
                    $this->whereLessOrEqual('logtime', new \MongoDate(strtotime($this->$name . ' 23:59:59')));
                    break;
                default:
                    $log->whereLike($name, $this->$name);
            }
        }
        
        return $log;
    }

}
