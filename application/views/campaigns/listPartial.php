<?php foreach($campaigns as $campaign): ?>
<tr class="<?php if($campaign->isModerationRequired()) echo ' danger'; ?>">
    <td>
        <a href="/banners?campaign=<?php echo $campaign->getId(); ?>">
            <?php echo $campaign->getName(); ?>
            <span class="text-danger"><?php if ($campaign->isDeactivated()) echo '(' . _('Deactivated') . ')'; ?></span>
        </a>
    </td>
    <td>
        <?php echo $campaign->getDateFrom('d.m.Y'); ?>
    </td>
    <td>
        <?php echo $campaign->getDateTo() ? $campaign->getDateTo('d.m.Y') : _('Unlimited'); ?>
    </td>
    <td>
            
            <?php if($campaign->getImpressionLimit()): ?>
                <?php echo $campaign->getImpressionLimit(); ?>
                <?php if($campaign->isCostCalculatedByUniqueEvents()) echo _('unique'); ?>
            <?php else: ?>
                <?php echo _('Unlimited'); ?>
            <?php endif; ?>
            (<span title="<?php echo _('unique'); ?>"><?php echo $campaign->getUniqueImpressions(); ?></span> 
            / <span title="<?php echo _('total'); ?>"><?php echo $campaign->getImpressions(); ?></span>)
    </td>
    <td>
        <?php if($campaign->getClickLimit()): ?>
            <?php echo $campaign->getClickLimit(); ?>
            <?php if($campaign->isCostCalculatedByUniqueEvents()) echo _('unique'); ?>
        <?php else: ?>
            <?php echo _('Unlimited'); ?>
        <?php endif; ?>
        (<span title="<?php echo _('unique'); ?>"><?php echo $campaign->getUniqueClicks(); ?></span>
        / <span title="<?php echo _('total'); ?>"><?php echo $campaign->getClicks(); ?></span>)
    </td>
    <td>
        <?php echo $campaign->getPricingModel(); ?>
        <?php echo $campaign->getCost(); ?>
    </td>
    <td>
        <?php if($campaign->canBeManagedBy(Yii::app()->user)): ?>
        <?php if($campaign->isActive()): ?>
            <a href="/campaigns/suspend/id/<?php echo $campaign->getId(); ?>" title="<?php echo _('Active'); ?>" class="status active"><span class="glyphicon glyphicon-play"></span></a>
        <?php endif; ?>
        <?php if($campaign->isSuspended()): ?>
            <a href="/campaigns/activate/id/<?php echo $campaign->getId(); ?>" title="<?php echo _('Suspended'); ?>" class="status suspended"><span class="glyphicon glyphicon-pause"></span></a>
        <?php endif; ?>
        <?php endif; ?>
    </td>
    <td>
        <?php if($campaign->canBeManagedBy(Yii::app()->user)): ?>
        <a href="/campaigns/edit/id/<?php echo $campaign->getId(); ?>" title="<?php echo _('Edit'); ?>"><span class="glyphicon glyphicon-pencil"></span></a>
        <?php endif; ?>
    </td>
    <td>
        <?php if($campaign->canBeManagedBy(Yii::app()->user)): ?>
        <a href="/campaigns/delete/id/<?php echo $campaign->getId(); ?>" title="<?php echo _('Delete'); ?>" class="delete"><span class="glyphicon glyphicon-trash"></span></a>
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>