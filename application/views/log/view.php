<?php

$this->breadcrumbs = array(
    _('Log') => array('/log')
);

$this->widget('zii.widgets.CDetailView', array(
    'htmlOptions' => array('class' => 'table table-hover table-striped table-bordered table-condensed'),
//    'type' => 'striped bordered condensed',
    'data' => $logDoc,
    'attributes' => array(
        array(
            'label' => _('Level'),
            'value' => $logDoc->level,
        ),
        array(
            'label' => _('Category'),
            'value' => $logDoc->category,
        ),
        array(
            'label' => _('Time'),
            'value' => date("d/m/Y H:i:s",$logDoc->logtime->sec),
        ),
        array(
            'label' => _('Message'),
            'value' => '<pre>'.$logDoc->message.'</pre>',
            'type' => 'raw'
        ),
        array(
            'label' => _('Request'),
            'value' => CHtml::link($logDoc->requestUri, $logDoc->requestUri, array("target"=>"_blank")),
            'type' => 'raw'
        ),
        array(
            'label' => _('Browser'),
            'value' => $logDoc->userAgent,
        ),
//        ($model->image ? array('value' => $model->image, 'type' => 'raw') : 'content'),
//        array(
//            'header' => 'Code',
//            'name' => 'code',
//            'value' => CHtml::textArea('code', $model->code, array('readonly' => 'readonly', 'style' => 'width:90%;height:100px;cursor:pointer;')),
//            'type' => 'raw'
//        ),
    ),
));
