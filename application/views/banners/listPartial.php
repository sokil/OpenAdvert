<?php foreach($banners as $banner): ?>
<tr>
    <td>
        <span class="glyphicon <?php echo $bannerTypes[$banner->getType()]['icon']; ?>"></span>
        <?php if(Yii::app()->user->checkAccess('manageCampaign.editWithoutModeration')): ?>
        <a href="/banners/edit/id/<?php echo $banner->getId(); ?>"><?php echo $banner->getName(); ?></a>
        <?php else: ?>
        <?php echo $banner->getName(); ?>
        <?php endif; ?>
    </td>
    <td>
        <a href="<?php echo $banner->getUrl(); ?>"><?php echo $banner->getUrl(); ?></a>
    </td>
    <?php if(Yii::app()->user->checkAccess('manageCampaign.editWithoutModeration')): ?>
    <td>
        <?php if($banner->isActive()): ?>
        <a href="/banners/suspend/id/<?php echo $banner->getId(); ?>" title="<?php echo _('Active'); ?>" class="status active"><span class="glyphicon glyphicon-play"></span></a>
        <?php endif; ?>
        <?php if($banner->isSuspended()): ?>
        <a href="/banners/activate/id/<?php echo $banner->getId(); ?>" title="<?php echo _('Suspended'); ?>" class="status suspended"><span class="glyphicon glyphicon-pause"></span></a>
        <?php endif; ?>
        <a href="/banners/edit/id/<?php echo $banner->getId(); ?>" title="<?php echo _('Edit'); ?>"><span class="glyphicon glyphicon-pencil"></span></a>
        <a href="/banners/delete/id/<?php echo $banner->getId(); ?>" title="<?php echo _('Delete'); ?>" class="delete"><span class="glyphicon glyphicon-trash"></span></a>
    </td>
    <?php endif; ?>
</tr>
<?php endforeach; ?>