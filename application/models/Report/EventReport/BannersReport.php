<?php

namespace Report\EventReport;

use MongoAdvertDb\Banners\Banner;

class BannersReport extends \Report\EventReport 
{
    /**
     *
     * @var \MongoAdvertDb\Campaigns\Campaign
     */
    private $_campaign;
    
    private $_datasets = array();
    
    private $_calcs = array();
    
    private $_dataProvider;

    public function byCampaign(\MongoAdvertDb\Campaigns\Campaign $campaign) {
        $this->_campaign = $campaign;
        return $this;
    }
    
    /**
     * 
     * @return \MongoAdvertDb\Campaigns\Campaign
     */
    public function getCampaign()
    {
        return $this->_campaign;
    }

    public function withImpressions() {
        $this->_datasets['impressions'] = function() {
            return $this->getTotalStat('impression');
        };
        
        return $this;
    }

    public function withUniqueImpressions() {
        $this->_datasets['uniqueImpressions'] = function() {
            return $this->getUniqueStat('impression');
        };
        
        return $this;
    }

    public function withClicks() {
        $this->_datasets['clicks'] = function() {
            return $this->getTotalStat('click');
        };
        
        return $this;
    }

    public function withUniqueClicks() {
        $this->_datasets['uniqueClicks'] = function() {
            return $this->getUniqueStat('click');
        };
        
        return $this;
    }

    public function withCost() {
        $this->_calcs['cost'] = function ($bannerData) {
            return $this->_campaign->calcCost(
                $bannerData['impressions'], 
                $bannerData['uniqueImpressions'], 
                $bannerData['clicks'],
                $bannerData['uniqueClicks']
            );
        };
        
        return $this;
    }
    
    public function withCtr() 
    {
        $this->_calcs['ctr'] = function ($bannerData) {
            return $this->_campaign->calcCtr(
                $bannerData['impressions'], 
                $bannerData['uniqueImpressions'], 
                $bannerData['clicks'],
                $bannerData['uniqueClicks']
            );
        };
        
        return $this;
    }

    public function getDataProvider() 
    {
        if($this->_dataProvider) {
            return $this->_dataProvider;
        }
        
        // get related baners
        $banners = \Yii::app()->mongo->getCollection('banners')
            ->find()
            ->byCampaign($this->_campaign)
            ->sort(array('name' => 1))
            ->findAll();

        if(!$banners) {
            return new \ArrayDataProvider(array());
        }
        
        $bannersReport = array_map(function(Banner $banner) {
            return array(
                'bannerId'          => $banner->getId(),
                'banner'            => $banner,
                'clicks'            => 0,
                'uniqueClicks'      => 0,
                'uniqueImpressions' => 0,
                'impressions'       => 0,
                'cost'              => 0,
                'ctr'               => 0,
            );
        }, $banners);
        
        // get aggregated data
        foreach ($this->_datasets as $datasetName => $dataset) {            
            foreach ($dataset() as $row) {
                $bannerId = (string) $row['_id'];
                $bannersReport[$bannerId][$datasetName] = $row['count'];
            }
        }
        
        // get post-calculated data
        foreach ($this->_calcs as $name => $function) {
            foreach ($bannersReport as $bannerId => $bannerData) {
                $bannersReport[$bannerId][$name] = $function($bannerData);
            }
        }
        
        // remove deleted empty campaigns
        foreach ($bannersReport as $bannerId => $bannerData) {
            if ($bannerData['banner']->isDeleted() && $bannerData['impressions']==0) {
                unset($bannersReport[$bannerId]);
            }
        }

        $this->_dataProvider = new \ArrayDataProvider(array_values($bannersReport), array(
            'keyField' => 'bannerId',
            'sort' => array('attributes' => array('name', 'impressions', 'uniqueImpressions', 'clicks', 'uniqueClicks', 'cost', 'ctr'))
        ));
        
        return $this->_dataProvider;
    }

    private function getTotalStat($event) {
        $collection = $this->getCollection($event);

        $pipeline = $collection->createPipeline();

        if (!in_array($event, array('impression', 'click'))) {
            $pipeline->match(array('event' => $event));
        }

        $pipeline
            ->match(array(
                'banner.campaign' => $this->_campaign->getId()
            ))
            ->match(array(
                'date' => array(
                    '$gt' => new \MongoDate($this->_dateFrom),
                    '$lte' => new \MongoDate($this->_dateTo)
                )
            ))
            ->group(array(
                '_id' => '$banner.id',
                'count' => array('$sum' => 1)
        ));

        return $collection->aggregate($pipeline);
    }

    private function getUniqueStat($event) {
        $collection = $this->getCollection($event);

        $pipeline = $collection->createPipeline();

        if (!in_array($event, array('impression', 'click'))) {
            $pipeline->match(array('event' => $event));
        }

        $pipeline
            ->match(array(
                'banner.campaign' => $this->_campaign->getId()
            ))
            ->match(array(
                'date' => array(
                    '$gt' => new \MongoDate($this->_dateFrom),
                    '$lte' => new \MongoDate($this->_dateTo)
                )
            ))
            ->group(array(
                '_id' => array(
                    'visitor' => '$visitor',
                    'banner' => '$banner.id',
                    'y' => ['$year' => '$date'],
                    'm' => ['$month' => '$date'],
                    'd' => ['$dayOfMonth' => '$date'],
                ) ,
                'count' => array('$sum' => 1),
            ))
            ->group(array(
                '_id' => '$_id.banner',
                'count' => array('$sum' => 1),
        ));

        return $collection->aggregate($pipeline);
    }

    private function getCollection($event) {
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
