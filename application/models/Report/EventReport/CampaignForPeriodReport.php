<?php

namespace Report\EventReport;

use \MongoAdvertDb\Campaigns\Campaign;

/**
 * @method \Report\EventReport\CampaignsForPeriodReport fromDate($date) set left bound of date range
 * @method \Report\EventReport\CampaignsForPeriodReport toDate($date) set right bound of date range
 */
class CampaignForPeriodReport extends \Report\EventReport
{

    protected $_entity;
    protected $_datasets = array();
    protected $_calcs = array();
    private $_dataProvider;
    
    protected $_period = self::PERIOD_DAY;
    
    private $_periodMeta = array(
        self::PERIOD_HOUR   => array(3600, 'Y-n-d G:00'),
        self::PERIOD_DAY    => array(86400, 'Y-n-d'),
        self::PERIOD_WEEK   => array(604800, 'Y-W'),
        self::PERIOD_MONTH  => array(2419200, 'Y-n'),
    );

    public function byCampaign(Campaign $campaign)
    {
        $this->_campaign = $campaign;

        $this->_dataProvider = null;

        return $this;
    }
    
    public function getDatasetLocalization()
    {
        return array(
            'impressions'       => _('Impressions'),
            'uniqueImpressions' => _('Unique impressions'),
            'clicks'            => _('Clicks'),
            'uniqueClicks'      => _('Unique clicks'),
        );
    }

    public function byPeriod($period)
    {
        if(!$period) {
            return $this;
        }

        $this->_period = $period;
        $this->_dataProvider = null;

        return $this;
    }

    public function withImpressions()
    {
        $this->_datasets['impressions'] = function() {
            return $this->getTotalStat('impression');
        };

        $this->_dataProvider = null;

        return $this;
    }
    
    public function hasImpressions()
    {
        return isset($this->_datasets['impressions']);
    }

    public function withUniqueImpressions()
    {
        $this->_datasets['uniqueImpressions'] = function() {
            return $this->getUniqueStat('impression');
        };

        $this->_dataProvider = null;
        
        return $this;
    }
    
    public function hasUniqueImpressions()
    {
        return isset($this->_datasets['uniqueImpressions']);
    }

    public function withClicks()
    {
        $this->_datasets['clicks'] = function() {
            return $this->getTotalStat('click');
        };

        $this->_dataProvider = null;
        
        return $this;
    }
    
    public function hasClicks()
    {
        return isset($this->_datasets['clicks']);
    }

    public function withUniqueClicks()
    {
        $this->_datasets['uniqueClicks'] = function() {
            return $this->getUniqueStat('click');
        };

        $this->_dataProvider = null;
        
        return $this;
    }
    
    public function hasUniqueClicks()
    {
        return isset($this->_datasets['uniqueClicks']);
    }
    
    public function getDatasets()
    {
        return $this->_datasets;
    }
    
    private function _getStatKeyByAggregatedRowId($id)
    {
        switch($this->_period) {
            case self::PERIOD_MONTH:
                return sprintf('%04d-%d', $id['y'], $id['m']);
                
            case self::PERIOD_WEEK:
                return sprintf('%04d-%02d', $id['y'], $id['w']);
                
            case self::PERIOD_DAY:
                return sprintf('%04d-%d-%02d', $id['y'], $id['m'], $id['d']);
                
            case self::PERIOD_HOUR:
                return sprintf('%04d-%d-%02d %02d:00', $id['y'], $id['m'], $id['d'],$id['h']);
        }
    }

    public function getDataProvider()
    {
        if($this->_dataProvider) {
            return $this->_dataProvider;
        }
        
        if (!$this->_campaign) {
            return new \ArrayDataProvider(array());
        }
        
        

        $entityReport = array();
        foreach ($this->_datasets as $eventName => $dataset) {
            // prepare result with null values
            for($i = $this->_dateFrom; $i < $this->_dateTo; $i += $this->_periodMeta[$this->_period][0]) {
                $day = date($this->_periodMeta[$this->_period][1], $i);
                $entityReport[$day][$eventName] = 0;
            }
            
            // fill results
            foreach ($dataset() as $row) {
                $date = $this->_getStatKeyByAggregatedRowId($row['_id']);
                $entityReport[$date][$eventName] = $row['count'];
            }
        }

        $this->_dataProvider = new \ArrayDataProvider($entityReport, array(
            'keyField' => $this->_period
        ));
        
        return $this->_dataProvider;
    }
    
    private function createPipeline($eventName)
    {
        $pipeline = $this
            ->getCollection($eventName)
            ->createPipeline();

        if (!in_array($eventName, array('impression', 'click'))) {
            $pipeline->match(array('event' => $eventName));
        }

        // filter by campaign and date range
        $pipeline
            ->match(array(
                'banner.campaign' => $this->_campaign->getId()
            ))
            ->match(array(
                'date' => array(
                    '$gt'  => new \MongoDate($this->_dateFrom),
                    '$lte' => new \MongoDate($this->_dateTo),
                )
            ));
        
        return $pipeline;
    }
    
    /**
     * @param $event
     * @return mixed
     */
    protected function getTotalStat($eventName)
    {
        $pipeline = $this
            ->createPipeline($eventName)
            // fix date for local timezone
            ->project(array(
                'date'  => [
                    '$add' => ['$date', date('Z') * 1000]
                ]
            ));
        
        switch ($this->_period) {

            case self::PERIOD_MONTH:
                $pipeline
                    ->group(array(
                        '_id'   => array(
                            'y' => ['$year' => '$date'],
                            'm'  => ['$month' => '$date'],
                        ),
                        'count' => array('$sum' => 1)
                    ));
                break;
            
            case self::PERIOD_WEEK :
                $pipeline
                    ->group(array(
                        '_id'   => array(
                            'y' => ['$year'=> '$date'],
                            'w' => ['$week' => '$date'],
                        ),
                        'count' => array('$sum' => 1)
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

    /**
     * @param $eventName
     * @return mixed
     */
    protected function getUniqueStat($eventName)
    {
        $pipeline = $this
            ->createPipeline($eventName)
            // fix date for local timezone
            ->project(array(
                'visitor'   => 1,
                'date'  => [
                    '$add' => ['$date', date('Z') * 1000]
                ]
            ));

        // group by period
        switch ($this->_period) {

            case self::PERIOD_MONTH:
                $pipeline
                    ->group(array(
                        '_id'   => array(
                            'visitor' => '$visitor',
                            'y' => ['$year' => '$date'],
                            'm' => ['$month' => '$date'],
                            'd' => ['$dayOfMonth' => '$date'], // unique calculates per day
                        ),
                        'count' => array('$sum' => 1)
                    ))
                    ->group(array(
                        '_id'   => array(
                            'y' => '$_id.y',
                            'm'  => '$_id.m',
                        ),
                        'count' => array('$sum' => 1)
                    ));
                break;
            
            case self::PERIOD_WEEK :
                $pipeline
                    ->group(array(
                        '_id'   => array(
                            'visitor' => '$visitor',
                            'y' => ['$year' => '$date'],
                            'w' => ['$week' => '$date'],
                            'd' => ['$dayOfMonth' => '$date'], // unique calculates per day
                        ),
                        'count' => array('$sum' => 1)
                    ))
                    ->group(array(
                        '_id'   => array(
                            'y' => '$_id.y',
                            'w' => '$_id.w',
                        ),
                        'count' => array('$sum' => 1)
                    ));
                break;
            
            case self::PERIOD_DAY:
                $pipeline
                    ->group(array(
                        '_id'   => array(
                            'visitor' => '$visitor',
                            'y' => ['$year' => '$date'],
                            'm' => ['$month' => '$date'],
                            'd' => ['$dayOfMonth' => '$date'],
                        ),
                        'count' => array('$sum' => 1)
                    ))
                    ->group(array(
                        '_id'   => array(
                            'y' => '$_id.y',
                            'm' => '$_id.m',
                            'd' => '$_id.d',
                        ),
                        'count' => array('$sum' => 1)
                    ));
                break;
            
            case self::PERIOD_HOUR:
                $pipeline
                    ->group(array(
                        '_id' => array(
                            'visitor' => '$visitor',
                            'y' => ['$year' => '$date'],
                            'm' => ['$month' => '$date'],
                            'd' => ['$dayOfMonth' => '$date'],
                            'h' => ['$hour' => '$date'],
                        ),
                        'count' => array('$sum' => 1)
                    ))
                    ->group(array(
                        '_id'   => array(
                            'y' => '$_id.y',
                            'm' => '$_id.m',
                            'd' => '$_id.d',
                            'h' => '$_id.h',
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

    /**
     * 
     * @param type $eventName
     * @return \Sokil\Mongo\Collection
     */
    protected function getCollection($eventName)
    {
        switch ($eventName) {
            case 'impression':
                return \Yii::app()->mongo->getCollection('track.impression');
            case 'click':
                return \Yii::app()->mongo->getCollection('track.click');
            default:
                return \Yii::app()->mongo->getCollection('track.event');
        }
    }

}
