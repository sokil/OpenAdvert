<?php

namespace Report\EventReport;

class CampaignsReport extends \Report\EventReport
{
    /**
     *
     * @var \MongoAdvertDb\Advertisers\Advertiser
     */
    private $_advertiser;
    
    private $_datasets = array();
    
    private $_calcs = array();
    
    private $_dataProvider;

    public function byAdvertiser(\MongoAdvertDb\Advertisers\Advertiser $advertiser)
    {
        $this->_advertiser = $advertiser;
        $this->_dataProvider = null;
        return $this;
    }
    
    /**
     * 
     * @return \MongoAdvertDb\Advertisers\Advertiser
     */
    public function getAdvertiser()
    {
        return $this->_advertiser;
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

    public function withCost()
    {
        $this->_calcs['cost'] = function ($campaignData) {
            return $campaignData['campaign']->calcCost(
                $campaignData['impressions'], $campaignData['uniqueImpressions'], $campaignData['clicks'], $campaignData['uniqueClicks']
            );
        };
        
        $this->_dataProvider = null;

        return $this;
    }

    public function withCtr()
    {
        $this->_calcs['ctr'] = function ($campaignData) {
            return $campaignData['campaign']->calcCtr(
                    $campaignData['impressions'], $campaignData['uniqueImpressions'], $campaignData['clicks'], $campaignData['uniqueClicks']
            );
        };
        
        $this->_dataProvider = null;

        return $this;
    }

    public function getDataProvider()
    {
        if($this->_dataProvider) {
            return $this->_dataProvider;
        }
        
        if (!$this->_advertiser) {
            throw new \Exception('Advertiser not specified');
        }

        $campaignsReport = array();
        
        // get aggregated data
        foreach ($this->_datasets as $datasetName => $dataset) {
            foreach ($dataset() as $row) {
                $campaignId = (string) $row['_id'];
                $campaignsReport[$campaignId][$datasetName] = $row['count'];
            }
        }
        
        if(!$campaignsReport) {
            return new \ArrayDataProvider([]);
        }
        
        // get related campaigns
        $campaigns = \Yii::app()->mongo
            ->getCollection('campaigns')
            ->find()
            ->byIdList(array_keys($campaignsReport))
            ->sort(array('name' => 1))
            ->findAll();
        
        foreach ($campaignsReport as $campaignId => &$campaignReport) {
            // define default values
            $campaignReport = array_merge(array(
                'impressions'       => 0,
                'uniqueImpressions' => 0,
                'clicks'            => 0,
                'uniqueClicks'      => 0,
                'cost'              => 0,
                'ctr'               => 0,
            ), $campaignReport);
            
            // add reference to campaign
            $campaignReport['campaignId'] = $campaignId;
            $campaignReport['campaign'] = $campaigns[$campaignId];
        }

        // get post-calculated data
        foreach ($this->_calcs as $name => $function) {
            foreach ($campaignsReport as $campaignId => $campaignData) {
                $campaignsReport[$campaignId][$name] = $function($campaignData);
            }
        }

        $this->_dataProvider = new \ArrayDataProvider(array_values($campaignsReport), array(
            'keyField' => 'campaignId',
            'sort'     => array('attributes' => array('name', 'impressions', 'uniqueImpressions', 'clicks', 'uniqueClicks', 'cost', 'ctr'))
        ));
        
        return $this->_dataProvider;
    }

    private function getTotalStat($event)
    {
        $collection = $this->getCollection($event);

        $pipeline = $collection->createPipeline();

        if (!in_array($event, array('impression', 'click'))) {
            $pipeline->match(array('event' => $event));
        }

        $pipeline
            ->match(array(
                'banner.advertiser' => $this->_advertiser->getId()
            ))
            ->match(array(
                'date' => array(
                    '$gt'  => new \MongoDate($this->_dateFrom),
                    '$lte' => new \MongoDate($this->_dateTo)
                )
            ))
            ->group(array(
                '_id'   => '$banner.campaign',
                'count' => array('$sum' => 1)
        ));
        
        return $collection->aggregate($pipeline);
    }

    private function getUniqueStat($event)
    {
        $collection = $this->getCollection($event);

        $pipeline = $collection->createPipeline();

        // match VAST events from "track.event" collection
        if (!in_array($event, array('impression', 'click'))) {
            $pipeline->match(array('event' => $event));
        }

        $pipeline
            ->match(array(
                'banner.advertiser' => $this->_advertiser->getId()
            ))
            ->match(array(
                'date' => array(
                    '$gt'  => new \MongoDate($this->_dateFrom),
                    '$lte' => new \MongoDate($this->_dateTo)
                )
            ))
            ->group(array(
                '_id'   => array(
                    // unique visitor
                    'visitor'  => '$visitor',
                    // unique in campaign
                    'campaign' => '$banner.campaign',
                    // unique by every banner
                    'banner'   => '$banner.id',
                    // unique per day
                    'y'        => ['$year' => '$date'],
                    'm'        => ['$month' => '$date'],
                    'd'        => ['$dayOfMonth' => '$date'],
                ),
                'count' => array('$sum' => 1),
            ))
            ->group(array(
                '_id'   => '$_id.campaign',
                'count' => array('$sum' => 1),
        ));

        return $collection->aggregate($pipeline);
    }

    /**
     * 
     * @param string $event event name
     * @return \Sokil\Mongo\Collection
     */
    private function getCollection($event)
    {
        switch ($event) {
            case 'impression':
                return \Yii::app()->mongo->getCollection('track.impression');

            case 'click':
                return \Yii::app()->mongo->getCollection('track.click');

            default:
                return \Yii::app()->mongo->getCollection('track.event');
        }
    }

}
