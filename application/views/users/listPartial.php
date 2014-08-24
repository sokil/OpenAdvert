<div class="row-fluid bottom-space top-space">
    <div class="col-md-10">
        <?php if($filter['role'] === \MongoAdvertDb\Users\User::ROLE_MANAGER): ?>
        <a href="<?php echo $this->createUrl('new'); ?>" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-plus"></i> <?php echo _('Add Manager'); ?>
        </a>
        <?php endif; ?>
        <?php if(!empty($currentGroup)): ?>
        <a href="<?php echo $this->createUrl('new', array(
            $filter['role'] => (string) $currentGroup->getId()
        )); ?>" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-plus"></i> <?php echo _('Add User'); ?>
        </a>
        <?php endif; ?>
        
    </div>
    <div class="col-md-2">
        <form>
            <?php
            if (!empty($groupList)) {
                echo CHtml::dropDownList(
                    'filter[' . $filter['role'] . ']',
                    !empty($currentGroup) ? (string) $currentGroup->getId() : null,
                    $groupList,
                    array(
                        'class' => 'form-control input-sm pull-left',
                        'prompt' => _('Choose...'),
                        'onchange' => CHtml::ajax(array(
                            'type'=>'GET',
                            'url' => '/users/?filter[role]=' . $filter['role'],
                            'update'=>'#cnt',
                        ))
                    )
                );
            }
            ?>
        </form>
    </div>
</div>


<?php

$this->widget('GridView', array(
    'id' => 'users-grid',
    'dataProvider' => $users,
    'columns' => array(
        array(
            'name' => 'name',
            'header' => _('Name'),
        ),
        array(
            'name' => 'email',
            'header' => _('E-mail'),
        ),
        array(
            'name' => 'phone',
            'header' => _('Phone'),
        ),
        array(
            'class' => 'TbButtonColumn',
            'template' => '{update} {delete}',
            'deleteButtonUrl' => 'Yii::app()->createUrl("users/delete", array("id" =>  $data["_id"]))',
            'updateButtonUrl' => 'Yii::app()->createUrl("users/edit", array("id" =>  $data["_id"]))',
        ),
    )
));