<?php

namespace Report;

class CampaignsReportTest extends \PHPUnit_Framework_TestCase
{
    public function testGetDataProvider()
    {
        $campaignReport = \Yii::app()->report->get('eventReport.campaigns');
        $this->assertInstanceOf('\Report\EventReport\CampaignsReport', $campaignReport);

        $advertiser = \Yii::app()->mongo->getCollection('advertisers')
            ->getDocument('52ba6dae41a88f9062ff1bed');

        $dataProvider = $campaignReport
            ->byAdvertiser($advertiser)
            ->fromDate('2014-01-01')
            ->toDate('2014-01-31')
            ->withImpressions()
            ->withUniqueImpressions()
            ->withClicks()
            ->withUniqueClicks()
            ->getDataProvider();
        
        $this->assertNotEmpty($dataProvider);
    }

}
