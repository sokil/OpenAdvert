<?php

$this->widget('GridView', array(
    'dataProvider'  => $dataProvider,
    'columns'       => array(
        array(
            'name'      => 'name',
            'header'    => _('Partner'),
            'value'     => 'CHtml::link($data["name"], array("partner","id"=>$data["_id"]))',
            'type'      => 'raw',
        ),
        array(
            'name'      => 'code',
            'header'    => _('Code'),
            'value'     => '$data["ref"]',
            'type'      => 'raw',
        ),
    )
));
