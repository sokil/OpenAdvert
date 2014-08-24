<?php

$this->widget('GridView', array(
    'dataProvider'  => $dataProvider,
    'filter'        => $filter,
    'columns'       => array(
        array(
            'name'      => 'name',
            'header'    => _('Advertiser'),
            'value'     => 'CHtml::link($data["name"], array("advertiser","id"=>$data["_id"]))',
            'type'      => 'raw',
        ),
        array(
            'name'      => 'phone',
            'header'    => _('Phone'),
            'value'     => '$data["phone"]',
            'type'      => 'raw',
        ),
        array(
            'name'      => 'address',
            'header'    => _('Address'),
            'value'     => '$data["address"]',
            'type'      => 'raw',
        ),
        array(
            'name'      => 'email',
            'header'    => _('E-mail'),
            'value'     => 'CHtml::link($data["email"], \'mailto:\' . $data["email"])',
            'type'      => 'raw',
        ),
    )
));
