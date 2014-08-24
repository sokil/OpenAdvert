<?php

$this->breadcrumbs = array(
    _('Zones')
);

?>
<div class="btn-toolbar bottom-space" role="toolbar">
    <div class="btn-group">
        <button type="button" class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown">
            <span class="glyphicon glyphicon-plus"></span>
            <?php echo _('Add Zone'); ?>
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu" role="menu">
            <?php foreach(ZoneForm::getTypes() as $bannerType => $bannerTypeMeta): ?>
            <li>
                <a href="<?php echo $this->createUrl('new', array('type'=>$bannerType)); ?>">
                    <span class="glyphicon <?php echo $bannerTypeMeta['icon']; ?>"></span>
                    <?php echo $bannerTypeMeta['name']; ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<?php

foreach (ZoneForm::getTypes() as $bannerType => $bannerTypeMeta) {
    $typeFilter[$bannerType] = $bannerTypeMeta['name'];
}

$this->widget('GridView', array(
    'id'            => 'zones-grid',
    'dataProvider'  => $dataProvider,
    'filter'        => $filter,
    'columns' => array(
        array(
            'name' => 'name',
            'header' => _('Name'),
        ),
        array(
            'name' => 'type',
            'header' => _('Type'),
            'value' => '"<span class=\"glyphicon ".ZoneForm::getTypes()[$data["type"]]["icon"]."\"></span> ".
                ZoneForm::getTypes()[$data["type"]]["name"]',
            'type' => 'raw',
            'filter' => $typeFilter,
        ),
        array(
            'class' => 'TbButtonColumn',
            'template' => '{status} {update} {delete} {vast} {dropVastCache} {banners}',
            'deleteButtonUrl'   => 'Yii::app()->createUrl("zones/delete", array("id" =>  $data["_id"]))',
            'updateButtonUrl'   => 'Yii::app()->createUrl("zones/edit", array("id" =>  $data["_id"]))',
            'buttons'   => array(
                'status'  => array(
                    'click'         => 'function(){chStatus($(this));return false;}',
                    'label'         => '',
                    'iconExp'          => '($data["status"]=="ACTIVE"?"play":"pause")',
                    'url'           => 'Yii::app()->createUrl("zones/togglestatus", array("id" =>  $data["_id"]))',
                ),
                'vast'  => array(
                    'label'         => _('Zone code'),
                    'iconWithText'  => true,
                    'icon'          => 'tasks',
                    'url'           => 'Yii::app()->createUrl("zones/code", array("id" =>  $data["_id"]))',
                    'options'       => array(
                        'class'     => 'btn btn-primary btn-xs'
                    ),
                ),
                'dropVastCache'  => array(
                    'label'         => _('Drop VAST cache'),
                    'icon'          => 'repeat',
                    'url'           => 'Yii::app()->createUrl("zones/dropVastCache", array("id" =>  $data["_id"]))',
                    'options'       => array(
                        'class'     => 'dropVastCache'
                    ),
                ),
                'banners'  => array(
                    'label'         => _('Banners'),
                    'iconWithText'  => true,
                    'icon'          => 'film',
                    'url'           => 'array("banners", "id" => $data["_id"])',
                    'options'       => array(
                        'class'     => 'btn btn-primary btn-xs'
                    ),
                )
            ),
            'htmlOptions'=>array('width'=>'250px'),
        ),
    )
));

?>

<script type="text/javascript">
function chStatus(button) {
    button.find('.glyphicon').removeClass('glyphicon-play glyphicon-pause').addClass('glyphicon-refresh');
    $('#zones-grid').yiiGridView('update', {
        type: 'POST',
        url: button.attr("href"),
        success: function(data) {
            if (data.status=='ACTIVE') {
                button.find('.glyphicon').removeClass('glyphicon-refresh').addClass('glyphicon-play');
            } else {
                button.find('.glyphicon').removeClass('glyphicon-refresh').addClass('glyphicon-pause');
            }
            //$('#zones-grid').yiiGridView('update');
        }
    });
}

$('#zones-grid a.dropVastCache').click(function(e) {
    e.preventDefault();
    var $a = $(this);
    $.get($a.attr('href'), function(response) {
        alert(response.errorMessage);
    });
});
</script>
