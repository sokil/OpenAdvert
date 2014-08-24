<?php

use MongoAdvertDb\Campaigns\Campaign;

class CampaignByHourChart extends ChartWidget
{
    private $_dateFrom;
    
    private $_dateTo;
    
    private $_campaign;
    
    private $_report;
    
    public function run()
    {
        parent::run();
        
        $this->render('campaignByHourChart');
    }
    
    public function setCampaign(Campaign $campaign)
    {
        $this->_campaign = $campaign;
        $this->_report = null;
        return $this;
    }
    
    public function getCampaign()
    {
        return $this->_campaign;
    }
    
    public function setDateFrom($date) 
    {
        $this->_dateFrom = $date;
        $this->_report = null;
        return $this;
    }
    
    public function getDateFrom()
    {
        return $this->_dateFrom;
    }

    public function setDateTo($date)
    {
        $this->_dateTo = $date;
        $this->_report = null;
        return $this;
    }
    
    public function getDateTo()
    {
        return $this->_dateTo;
    }

    public function getReport()
    {
        if($this->_report) {
            return $this->_report;
        }
        
        $this->_report = Yii::app()->report
            ->get('eventReport.campaignByHour')
            ->byCampaign($this->_campaign)
            ->fromDate($this->_dateFrom)
            ->toDate($this->_dateTo)
            ->withImpressions()
            ->withUniqueImpressions()
            ->withClicks()
            ->withUniqueClicks()
            ->withCtr();
        
        return $this->_report;
    }
    
    public function getChartData()
    {
        $chartData = array( array(_('Date'), _('Impressions'), _('Unique Impressions'), _('Clicks'), _('Unique Clicks')));
        foreach ($this->getReport()->getDataProvider()->rawData as $row) {
            unset($row['ctr']);
            $chartData[] = array_values($row);
        }
        
        return $chartData;
    }
}