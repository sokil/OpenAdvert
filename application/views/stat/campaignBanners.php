<?php

$this->breadcrumbs = array(
    _('Statistics') => array('/stat'),
    $advertiser->getName() => array('/stat/campaigns', 'advertiser'=>$advertiser->getId(), 'dateFrom'=>$report->getDateFrom(), 'dateTo'=>$report->getDateTo()),
    $report->getCampaign()->getName()
);

$this->pageTitle = $report->getCampaign()->getName() . ' :: ' . _('Banner statistics');

?>

<?php

$this->widget('GridView', array(
    'dataProvider'  => $report->getDataProvider(),
    'emptyText'     => _('No banners'),
    'columns'       => array(
        array(
            'name'              => 'name',
            'value'             => '$data["banner"]->getName()',
            'header'            => _('Banner'),
            'footer'            => _('Total'),
            'footerHtmlOptions' => ['style' => 'text-align:right;'],
        ),
        array(
            'name'              => 'impressions',
            'header'            => _('Impressions'),
            'footer'            => $report->getDataProvider()->getSum('impressions'),
            'headerHtmlOptions' => ['colspan' => '2', 'style' => 'text-align:center;'],
            'htmlOptions'       => ['style' => 'width:80px; text-align:right; border-left: 1px solid #DDDDDD;'],
            'footerHtmlOptions' => ['style' => 'text-align:right;'],
        ),
        array(
            'class'             => 'ShareDataColumn',
            'type'              => 'percent',
            'name'              => 'impressions',
            'headerHtmlOptions' => ['style' => 'display:none'],
            'htmlOptions'       => ['style' => 'width:80px; text-align:right;'],
            'footerHtmlOptions' => ['style' => 'text-align:right;'],
        ),
        array(
            'name'              => 'uniqueImpressions',
            'header'            => _('Unique Impressions'),
            'footer'            => $report->getDataProvider()->getSum('uniqueImpressions'),
            'headerHtmlOptions' => ['colspan' => '2', 'style' => 'text-align:center;'],
            'htmlOptions'       => ['style' => 'width:80px; text-align:right; border-left: 1px solid #DDDDDD;'],
            'footerHtmlOptions' => ['style' => 'text-align:right;'],
        ),
        array(
            'class'             => 'ShareDataColumn',
            'type'              => 'percent',
            'name'              => 'uniqueImpressions',
            'headerHtmlOptions' => ['style' => 'display:none'],
            'htmlOptions'       => ['style' => 'width:80px; text-align:right;'],
            'footerHtmlOptions' => ['style' => 'text-align:right;'],
        ),
        array(
            'name'              => 'clicks',
            'header'            => _('Clicks'),
            'footer'            => $report->getDataProvider()->getSum('clicks'),
            'headerHtmlOptions' => ['colspan' => '2', 'style' => 'text-align:center;'],
            'htmlOptions'       => ['style' => 'width:80px; text-align:right; border-left: 1px solid #DDDDDD;'],
            'footerHtmlOptions' => ['style' => 'text-align:right;'],
        ),
        array(
            'class'             => 'ShareDataColumn',
            'type'              => 'percent',
            'name'              => 'clicks',
            'headerHtmlOptions' => ['style' => 'display:none'],
            'htmlOptions'       => ['style' => 'width:80px; text-align:right;'],
            'footerHtmlOptions' => ['style' => 'text-align:right;'],
        ),
        array(
            'name'              => 'uniqueClicks',
            'header'            => _('Unique Clicks'),
            'footer'            => $report->getDataProvider()->getSum('uniqueClicks'),
            'headerHtmlOptions' => ['colspan' => '2', 'style' => 'text-align:center;'],
            'htmlOptions'       => ['style' => 'width:80px; text-align:right; border-left: 1px solid #DDDDDD;'],
            'footerHtmlOptions' => ['style' => 'text-align:right;'],
        ),
        array(
            'class'             => 'ShareDataColumn',
            'type'              => 'percent',
            'name'              => 'uniqueClicks',
            'headerHtmlOptions' => ['style' => 'display:none'],
            'htmlOptions'       => ['style' => 'width:80px; text-align:right;'],
            'footerHtmlOptions' => ['style' => 'text-align:right;'],
        ),
        array(
            'class'             => 'DataColumn',
            'header'            => _('Cost'),
            'name'              => 'cost',
            'type'              => 'money',
            'footer'            => $report->getDataProvider()->getSum('cost'),
            'headerHtmlOptions' => ['style' => 'text-align:center;'],
            'htmlOptions'       => ['style' => 'text-align:right; border-left: 1px solid #DDDDDD;'],
            'footerHtmlOptions' => ['style' => 'text-align:right;'],
        ),
        array(
            'name'              => 'ctr',
            'header'            => _('CTR'),
            'type'              => 'percent',
            'headerHtmlOptions' => ['style' => 'text-align:center;'],
            'htmlOptions'       => ['style' => 'text-align:right; border-left: 1px solid #DDDDDD;'],
            'footerHtmlOptions' => ['style' => 'text-align:right;'],
        ),
    )
));
