<?php

namespace Report\EventReport;

use \MongoAdvertDb\Campaigns\Campaign;

class CampaignByHourReport extends \Report\EventReport
{

    protected $_campaign;
    protected $_datasets = array();
    protected $_calcs = array();

    private $_dataProvider;

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

    public function byCampaign(Campaign $campaign)
    {
        $this->_campaign = $campaign;

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

    public function withUniqueImpressions()
    {
        $this->_datasets['uniqueImpressions'] = function() {
            return $this->getUniqueStat('impression');
        };

        $this->_dataProvider = null;

        return $this;
    }

    public function withClicks()
    {
        $this->_datasets['clicks'] = function() {
            return $this->getTotalStat('click');
        };

        $this->_dataProvider = null;

        return $this;
    }

    public function withUniqueClicks()
    {
        $this->_datasets['uniqueClicks'] = function() {
            return $this->getUniqueStat('click');
        };

        $this->_dataProvider = null;

        return $this;
    }

    public function withCtr()
    {
        $this->_calcs['ctr'] = function ($data) {
            return $this->_campaign->calcCtr(
                $data['impressions'],
                $data['uniqueImpressions'],
                $data['clicks'],
                $data['uniqueClicks']
            );
        };

        return $this;
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
        
        // fill hour column
        for ($hour = 0; $hour <= 23; $hour++) {
            $entityReport[$hour]['hour'] = str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00';
        }
        
        foreach ($this->_datasets as $eventName => $dataset) {
            
            // prepare result with null values
            for ($hour = 0; $hour <= 23; $hour++) {
                $entityReport[$hour][$eventName] = 0;
            }
            
            // fill results
            foreach ($dataset() as $row) {
                $entityReport[$row['_id']['h']][$eventName] = $row['count'];
            }
        }

        // count ctr
        foreach ($this->_calcs as $name => $function) {
            foreach ($entityReport as $key => $data) {
                $entityReport[$key][$name] = $function($data);
            }
        }
        
        $this->_dataProvider = new \ArrayDataProvider($entityReport, array(
            'keyField' => "hour",
            'pagination' => array('pageSize' => count($entityReport)),
        ));

        return $this->_dataProvider;
    }

    /**
     *
     * @return \MongoAdvertDb\Campaigns\Campaign
     */
    public function getCampaign()
    {
        return $this->_campaign;
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

    protected function getTotalStat($eventName)
    {
        $pipeline = $this->createPipeline($eventName);
        $pipeline
            // fix date for local timezone
            ->project(array(
                'date'  => [
                    '$add' => ['$date', date('Z') * 1000]
                ]
            ))
            ->group(array(
                '_id'   => array(
                    'h' => ['$hour' => '$date'],
                ),
                'count' => array('$sum' => 1)
            ));

        return $pipeline
            ->sort(array(
                '_id.h' => 1,
            ))
            ->aggregate();
    }

    protected function getUniqueStat($eventName)
    {
        $pipeline = $this->createPipeline($eventName);
        $pipeline
            // fix date for local timezone
            ->project(array(
                'visitor' => '$visitor',
                'date'  => [
                    '$add' => ['$date', date('Z') * 1000]
                ]
            ))
            ->group(array(
                '_id'   => array(
                    'visitor' => '$visitor',
                    'h' => ['$hour' => '$date'],
                )
            ))
            ->group(array(
                '_id'   => array(
                    'h' => '$_id.h',
                ),
                'count' => array('$sum' => 1)
            ));

        return $pipeline
            ->sort(array(
                '_id.h' => 1,
            ))
            ->aggregate();
    }

}