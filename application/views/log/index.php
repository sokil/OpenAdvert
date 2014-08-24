<?php

Yii::app()->getClientScript()->registerPackage('pickmeup');

$this->breadcrumbs = array(
    _('Log')
);

$this->widget('GridView', array(
    'dataProvider' => $dataProvider,
    'filter' => $formModel,
    'afterAjaxUpdate' => 'function(id,data){initDatePickers()}',
    'columns' => array(
        array(
            'name' => 'logtime',
            'header' => _('Time'),
            'value' => 'date("d/m/Y H:i:s",$data["logtime"]->sec)',
            'filter' => $formModel->getDatesFilter(),
            'htmlOptions'=>array('width'=>'200px'),
        ),
        array(
            'name' => 'level',
            'header' => _('Level'),
            'filter' => $formModel->getLevels(),
            'htmlOptions'=>array('width'=>'200px'),
        ),
        array(
            'name' => 'category',
            'header' => _('Category'),
            'htmlOptions'=>array('width'=>'200px'),
        ),
        array(
            'name' => 'message',
            'header' => _('Message'),
            'value' => 'CHtml::link(parse_url($data["requestUri"], PHP_URL_PATH), $data["requestUri"], array("target"=>"_blank")) . CHtml::tag("pre", ["style"=>"max-height: 150px; overflow: auto;"], $data["message"]);',
            'type' => 'raw',
            'htmlOptions' => ["style"=>"width: 100%;"]
        ),
        array(
            'class' => 'TbButtonColumn',
            'template' => '{view}',
            'viewButtonUrl'   => 'Yii::app()->createUrl("log/view", array("id" =>  $data["_id"]))',
        ),
    )
));

?>

<script type="text/javascript">
    function initDatePickers() {
        // date-from datepicker
        $('#txtDateFrom').pickmeup({
            mode            : 'single',
            format          : 'Y-m-d',
            hide_on_select  : true,
            before_show		: function () {
                var $this = $(this);
                $this.pickmeup('set_date', $this.val());
            },
            change: function (formatted) {
                $(this).val(formatted);
            }
        });
        // date-to datepicker
        $('#txtDateTo').pickmeup({
            mode            : 'single',
            format          : 'Y-m-d',
            hide_on_select  : true,
            before_show		: function () {
                var $this = $(this);
                $this.pickmeup('set_date', $this.val());
            },
            change: function (formatted) {
                $(this).val(formatted);
            }
        });
    }
    initDatePickers();
</script>
