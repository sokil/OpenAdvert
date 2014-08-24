<?php


$form = $this->beginWidget('CActiveForm', array('id'=>'frmBannerZones', 'action' => array('zones')));

echo CHtml::hiddenField('id', $banner->getId(), array('id' => 'zone-banner-id'));

$this->widget('GridView', array(
    'id' => 'zones-grid',
    'dataProvider' => $zonesDataProvider,
    'columns' => array(
        array(
            'class'=>'CCheckBoxColumn', 
            'id' => 'zones', 
            'selectableRows' => 2,
            'checked'=>'$data["checked"]',
            ),
        array(
            'name' => 'name',
            'header' => _('All'),
            'value' => 'CHtml::label($data["name"], "zones_".$row, ["class"=>"checkbox-inline", "style"=>"padding:0"])',
            'type'=>'raw'
        ),
    )
));

echo CHtml::submitButton(_('Save'), array('class' => 'btn btn-primary'));
$this->endWidget();

?>

<script type="text/javascript">editor.initBannerZonesForm();</script>
