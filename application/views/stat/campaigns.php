<?php

/* @var $report \Report\EventReport\CampaignsReport */

$this->breadcrumbs = array(
    _('Statistics') => array('/stat'),
    $report->getAdvertiser()->getName()
);

$this->pageTitle = $report->getAdvertiser()->getName() . ' :: ' . _('Campaign statistics');
?>

<?php

$this->widget('GridView', array(
    'dataProvider' => $report->getDataProvider(),
    'emptyText' => _('No campaigns'),
    'htmlOptions' => ['id' => 'tblCampaigns'],
    'columns' => array(
        array(
            'name' => 'name',
            'header' => _('Campaign'),
            'value' => 'CHtml::link($data["campaign"]->getName(), "#", [\'class\' => \'campaign-link\', \'data-url\' => "/stat/campaign/" . $data["campaign"]->getId()])',
            'type' => 'raw',
            'footer' => _('Total'),
            'footerHtmlOptions' => ['style' => 'text-align:right;'],
        ),
        array(
            'name' => 'impressions',
            'header' => _('Impressions'),
            'footer' => $report->getDataProvider()->getSum('impressions'),
            'headerHtmlOptions' => ['colspan' => '2', 'style' => 'text-align:center;'],
            'htmlOptions' => ['style' => 'width:80px; text-align:right; border-left: 1px solid #DDDDDD;'],
            'footerHtmlOptions' => ['style' => 'text-align:right;'],
        ),
        array(
            'class' => 'ShareDataColumn',
            'type' => 'percent',
            'name' => 'impressions',
            'headerHtmlOptions' => ['style' => 'display:none'],
            'htmlOptions' => ['style' => 'width:80px; text-align:right;'],
            'footerHtmlOptions' => ['style' => 'text-align:right;'],
        ),
        array(
            'name' => 'uniqueImpressions',
            'header' => _('Unique Impressions'),
            'footer' => $report->getDataProvider()->getSum('uniqueImpressions'),
            'headerHtmlOptions' => ['colspan' => '2', 'style' => 'text-align:center;'],
            'htmlOptions' => ['style' => 'width:80px; text-align:right; border-left: 1px solid #DDDDDD;'],
            'footerHtmlOptions' => ['style' => 'text-align:right;'],
        ),
        array(
            'class' => 'ShareDataColumn',
            'type' => 'percent',
            'name' => 'uniqueImpressions',
            'headerHtmlOptions' => ['style' => 'display:none'],
            'htmlOptions' => ['style' => 'width:80px; text-align:right;'],
            'footerHtmlOptions' => ['style' => 'text-align:right;'],
        ),
        array(
            'name' => 'clicks',
            'header' => _('Clicks'),
            'footer' => $report->getDataProvider()->getSum('clicks'),
            'headerHtmlOptions' => ['colspan' => '2', 'style' => 'text-align:center;'],
            'htmlOptions' => ['style' => 'width:80px; text-align:right; border-left: 1px solid #DDDDDD;'],
            'footerHtmlOptions' => ['style' => 'text-align:right;'],
        ),
        array(
            'class' => 'ShareDataColumn',
            'type' => 'percent',
            'name' => 'clicks',
            'headerHtmlOptions' => ['style' => 'display:none'],
            'htmlOptions' => ['style' => 'width:80px; text-align:right;'],
            'footerHtmlOptions' => ['style' => 'text-align:right;'],
        ),
        array(
            'name' => 'uniqueClicks',
            'header' => _('Unique Clicks'),
            'footer' => $report->getDataProvider()->getSum('uniqueClicks'),
            'headerHtmlOptions' => ['colspan' => '2', 'style' => 'text-align:center;'],
            'htmlOptions' => ['style' => 'width:80px; text-align:right; border-left: 1px solid #DDDDDD;'],
            'footerHtmlOptions' => ['style' => 'text-align:right;'],
        ),
        array(
            'class' => 'ShareDataColumn',
            'type' => 'percent',
            'name' => 'uniqueClicks',
            'headerHtmlOptions' => ['style' => 'display:none'],
            'htmlOptions' => ['style' => 'width:80px; text-align:right;'],
            'footerHtmlOptions' => ['style' => 'text-align:right;'],
        ),
        array(
            'name' => 'cost',
            'header' => _('Cost'),
            'value' => '$data["campaign"]->getPricingModel()',
            'headerHtmlOptions' => ['colspan' => '2', 'style' => 'text-align:center;'],
            'htmlOptions' => ['style' => 'text-align:right; border-left: 1px solid #DDDDDD;'],
        ),
        array(
            'class' => 'DataColumn',
            'name' => 'cost',
            'type' => 'money',
            'footer' => $report->getDataProvider()->getSum('cost'),
            'headerHtmlOptions' => ['style' => 'display:none'],
            'htmlOptions' => ['style' => 'text-align:right;'],
            'footerHtmlOptions' => ['style' => 'text-align:right;'],
        ),
        array(
            'name' => 'ctr',
            'header' => _('CTR'),
            'type' => 'percent',
            'headerHtmlOptions' => ['style' => 'text-align:center;'],
            'htmlOptions' => ['style' => 'text-align:right; border-left: 1px solid #DDDDDD;'],
            'footerHtmlOptions' => ['style' => 'text-align:right;'],
        ),
    )
));
?>

<script type="text/javascript">
$('#tblCampaigns .campaign-link').click(function(e) {
    e.preventDefault();
    var $a = $(this);
    statApp.subMenu.load($a.data('url'));
});
</script>