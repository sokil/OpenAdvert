<?php

$this->breadcrumbs = array(
    _('Zones') => array('index'),
    $zone->getName() => array('edit', 'id' => $zone->getId()),
    _('Banners')
);

$this->widget('GridView', array(
    'id' => 'zones-grid',
    'dataProvider' => $dataProvider,
    'filter' => new CFormModel(),
    'columns' => array(
        array(
            'name' => 'campaign.advertiser.name',
            'header' => _('Advertiser'),
            'filter' => CHtml::textField('advertiser', Yii::app()->request->getParam('advertiser'))
        ),
        array(
            'name' => 'campaign.name',
            'header' => _('Campaign'),
            'filter' => CHtml::textField('campaign', Yii::app()->request->getParam('campaign'))
        ),
        array(
            'name' => 'name',
            'header' => _('Banner'),
            'value' => 'CHtml::link($data["name"], array("/banners/edit","id"=>$data["_id"]))',
            'type' => 'raw',
            'filter' => CHtml::textField('banner', Yii::app()->request->getParam('banner'))
        ),
        array(
            'class' => 'TbButtonColumn',
            'template' => '{delete}',
            'deleteButtonUrl' => 'Yii::app()->createUrl("zones/removeBanner", array("id" => $data["_id"], "zone" => "' . $zone->getId() . '"))',
            'buttons' => array(
                'delete' => array('click' => 'function(){return false;}')
            )
        ),
    )
));

?>

<script type="text/javascript">
    $('#zones-grid').tableButtons({
        deleteSignature: 'removeBanner',
        activateSignature: 'addBanner',
    });
</script>
