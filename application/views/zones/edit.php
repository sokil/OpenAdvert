<?php
$this->breadcrumbs = array(
    _('Zones') => array('index'),
    _('Edit')
);

$this->pageTitle = $model->name . ' :: ' . _('Zone');

$form = $this->beginWidget('CActiveForm', array(
    'errorMessageCssClass' => 'text-danger',
));

$bannerType = ZoneForm::getTypes()[$model->type];
?>

<?php echo $form->hiddenField($model, 'type'); ?>

<div class="form-group <?php echo $model->getError('name') ? 'has-error' : ''; ?>">
    <?php
    echo $form->labelEx($model, 'name', array('class' => 'control-label'));
    ?>
    <div class="input-group">
        <span class="input-group-addon">
            <span class="glyphicon <?php echo $bannerType['icon']; ?>"></span>
            <?php echo $bannerType['name']; ?>
        </span>
        <?php
        echo $form->textField($model, 'name', array('class' => 'form-control'));
        ?></div><?php
        echo $form->error($model, 'name');
        ?>
</div>

<?php echo CHtml::submitButton(_('Save'), array('class' => 'btn btn-primary')); ?>

<?php $this->endWidget(); ?>