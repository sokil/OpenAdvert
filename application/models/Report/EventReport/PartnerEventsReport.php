<?php
namespace Report\EventReport;

use MongoAdvertDb\Partners\Partner;

class PartnerEventsReport extends \Report\EventReport
{
    /**
     *
     * @var \MongoAdvertDb\Campaigns\Campaign
     */
    private $_partner;

    private $_dataProvider;

    private $_period = self::PERIOD_DAY;
    
    /**
     * @param \MongoAdvertDb\Partners\Partner $partner
     * @return $this
     */
    public function byPartner(Partner $partner)
    {
        $this->_partner = $partner;
        $this->_dataProvider = null;
        return $this;
    }

    /**
     *
     * @return \MongoAdvertDb\Partners\Partner
     */
    public function getPartner()
    {
        return $this->_partner;
    }

    public function setPeriod($period)
    {
        $this->_period = $period;

        return $this;
    }

    public function getPeriod()
    {
        return $this->_period;
    }

    public function getDataProvider()
    {
        if($this->_dataProvider) {
            return $this->_dataProvider;
        }

        if (!$this->_partner) {
            return new \ArrayDataProvider(array());
        }

        $entityReport = [];
        
        foreach ($this->getTotalStat() as $row) {
            $entityReport[] = [
                'date'  => $this->_getStatKeyByAggregatedRowId($row['_id']),
                'count' => $row['count'],
            ];
        }

        $this->_dataProvider = new \ArrayDataProvider($entityReport, array(
            'keyField' => 'date'
        ));

        return $this->_dataProvider;
    }

    private function getTotalStat()
    {
        $collection = \Yii::app()->mongo->getCollection('track.impression');

        $pipeline = $collection->createPipeline();

        $pipeline
            ->match(array(
                'partner' => $this->_partner->getId(),
            ))
            ->match(array(
                'date' => array(
                    '$gt'  => new \MongoDate($this->_dateFrom),
                    '$lte' => new \MongoDate($this->_dateTo)
                )
            ))
            ->project(array(
                'date'  => [
                    '$add' => ['$date', date('Z') * 1000]
                ]
            ));
        
        switch ($this->_period) {
            case self::PERIOD_MONTH:
                $pipeline
                    ->group([
                        '_id'   => [
                            'y' => ['$year' => '$date'],
                            'm'  => ['$month' => '$date'],
                        ],
                        'count' => array('$sum' => 1),
                    ]);
                break;
            case self::PERIOD_WEEK :
                $pipeline
                    ->group(array(
                        '_id'   => array(
                            'y' => ['$year' => '$date'],
                            'm' => ['$month' => '$date'],
                            'w' => ['$week' => '$date'],
                        ),
                        'count' => array('$sum' => 1),
                    ));
                break;

            case self::PERIOD_DAY:
                $pipeline
                    ->group(array(
                        '_id'   => array(
                            'y' => ['$year' => '$date'],
                            'm' => ['$month' => '$date'],
                            'd' => ['$dayOfMonth' => '$date'],
                        ),
                        'count' => array('$sum' => 1)
                    ));
                break;

            case self::PERIOD_HOUR:
                $pipeline
                    ->group(array(
                        '_id'   => array(
                            'y' => ['$year' => '$date'],
                            'm' => ['$month' => '$date'],
                            'd' => ['$dayOfMonth' => '$date'],
                            'h' => ['$hour' => '$date'],
                        ),
                        'count' => array('$sum' => 1)
                    ));
                break;

        }

        return $pipeline
            ->sort(array(
                '_id.y' => 1,
                '_id.w' => 1,
                '_id.m' => 1,
                '_id.d' => 1,
                '_id.h' => 1,
            ))
            ->aggregate();
    }

    private function _getStatKeyByAggregatedRowId($id)
    {
        switch($this->_period) {
            case self::PERIOD_MONTH:
                return sprintf('%04d-%02d', $id['y'], $id['m']);

            case self::PERIOD_WEEK:
                $date = $id['y'] . 'W' . sprintf('%02d', 1 + $id['w']);
                return date('Y-m-d', strtotime($date)) . ' - ' . date('Y-m-d', strtotime($date . '7'));

            case self::PERIOD_DAY:
                return sprintf('%04d-%02d-%02d', $id['y'], $id['m'], $id['d']);

            case self::PERIOD_HOUR:
                return sprintf('%04d-%02d-%02d %02d:00', $id['y'], $id['m'], $id['d'],$id['h']);
        }
    }
}