<?php
$this->breadcrumbs = array(
    _('Advertisers') => array('index'),
    _('Edit')
);


$form = $this->beginWidget('CActiveForm', array(
    'errorMessageCssClass'=>'text-danger',
//    'htmlOptions' => array('role' => 'form')
));

//echo $form->errorSummary($model);
?>

<div class="form-group <?php echo $model->getError('name')?'has-error':''; ?>">
    <?php
    echo $form->labelEx($model, 'name', array('class' => 'control-label'));
    echo $form->textField($model, 'name', array('class' => 'form-control'));
    echo $form->error($model, 'name');
    ?>
</div>

<div class="form-group <?php echo $model->getError('phone')?'has-error':''; ?>">
    <?php
    echo $form->labelEx($model, 'phone', array('class' => 'control-label'));
    echo $form->textField($model, 'phone', array('class' => 'form-control'));
    echo $form->error($model, 'phone');
    ?>
</div>

<div class="form-group <?php echo $model->getError('email')?'has-error':''; ?>">
    <?php
    echo $form->labelEx($model, 'email', array('class' => 'control-label'));
    echo $form->textField($model, 'email', array('class' => 'form-control'));
    echo $form->error($model, 'email');
    ?>
</div>

<div class="form-group <?php echo $model->getError('address')?'has-error':''; ?>">
    <?php
    echo $form->labelEx($model, 'address', array('class' => 'control-label'));
    echo $form->textArea($model, 'address', array('class' => 'form-control', 'rows' => 3));
    echo $form->error($model, 'address');
    ?>
</div>

<?php
echo CHtml::submitButton(_('Save'), array('class' => 'btn btn-primary'));
?>

<?php $this->endWidget(); ?>
