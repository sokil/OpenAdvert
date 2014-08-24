<?php

use MongoAdvertDb\Campaigns\Campaign;
use Report\EventReport\CampaignForPeriodReport;

class CampaignEventChart extends ChartWidget
{
    /**
     *
     * @var \Report\EventReport\CampaignsForPeriodReport
     */
    private $_report;
    
    private $_campaign;
    
    private $_dateFrom;
    
    private $_dateTo;
    
    private $_period = CampaignForPeriodReport::PERIOD_DAY;
    
    public function __construct()
    {
        $this->_report = Yii::app()->report
            ->get('eventReport.campaignForPeriod');
    }
    
    public function setCampaign(Campaign $campaign)
    {
        $this->_campaign = $campaign;
        return $this;
    }
    
    public function getCampaign()
    {
        return $this->_campaign;
    }
    
    public function setPeriod($period = null)
    {
        $allowedPeriods = [
            CampaignForPeriodReport::PERIOD_HOUR, 
            CampaignForPeriodReport::PERIOD_DAY, 
            CampaignForPeriodReport::PERIOD_WEEK, 
            CampaignForPeriodReport::PERIOD_MONTH];
        
        if(!in_array($period, $allowedPeriods)) {
            return $this;
        }
        
        $this->_period = $period;
        
        return $this;
    }
    
    public function getPeriod()
    {
        return $this->_period;
    }
    
    public function setDateFrom($date) 
    {
        $this->_dateFrom = $date;
        return $this;
    }
    
    public function getDateFrom()
    {
        return $this->_dateFrom;
    }

    public function setDateTo($date)
    {
        $this->_dateTo = $date;
        return $this;
    }
    
    public function getDateTo()
    {
        return $this->_dateTo;
    }
    
    public function setEvents(array $events = null)
    {
        if(!$events) {
            return $this;
        }
        
        foreach($events as $event) {
            if(method_exists($this, $event)) {
                $this->{$event}();
            }
        }
        
        return $this;
    }
    
    public function withImpressions()
    {
        $this->_report->withImpressions();
        return $this;
    }
    
    public function hasImpressions()
    {
        return $this->_report->hasImpressions();
    }

    public function withUniqueImpressions()
    {
        $this->_report->withUniqueImpressions();
        return $this;
    }
    
    public function hasUniqueImpressions()
    {
        return $this->_report->hasUniqueImpressions();
    }

    public function withClicks()
    {
        $this->_report->withClicks();
        return $this;
    }
    
    public function hasClicks()
    {
        return $this->_report->hasClicks();
    }

    public function withUniqueClicks()
    {
        $this->_report->withUniqueClicks();
        return $this;
    }
    
    public function hasUniqueClicks()
    {
        return $this->_report->hasUniqueClicks();
    }
    
    public function run()
    {
        parent::run();
        
        // define event if none specified
        if(!$this->_report->getDatasets()) {
            $this->_report->withImpressions();
        }
        
        $this->render('campaignEventChart', array(
            'campaign'  => $this->_campaign,
            'period'    => $this->_period,
            'dateFrom'  => $this->_dateFrom,
            'dateTo'    => $this->_dateTo,
        ));
    }
    
    public function getChartData()
    {
        $this->_report
            ->fromDate($this->_dateFrom)
            ->toDate($this->_dateTo)
            ->byCampaign($this->_campaign)
            ->byPeriod($this->_period);
        
        $chartData = array();
        
        // get data provider
        $dataProvider = $this->_report->getDataProvider();
        if(!$dataProvider->rawData) {
            return array();
        }
        
        // prepend cols
        $localisedColumns = array_map(function($column) {
            return $this->_report->getDatasetLocalization()[$column];
        }, array_keys(current($dataProvider->rawData)));
        
        $chartData[] = array_merge(array(_($dataProvider->keyField)), $localisedColumns);
        
        // prepare chart data
        foreach ($dataProvider->rawData as $date => $stat) {
            $row = array_values($stat);
            array_unshift($row, $date);
            $chartData[] = $row;
        }

        return $chartData;
    }
}