<h1><?php echo  sprintf(_('New campaign "%s"'), $campaign->name); ?></h1>
<p></p>
<p><?php echo  sprintf(_("Status: %s"), $campaign->status); ?></p>
<p><?php echo  sprintf(_("Start date: %s"), $campaign->getDateFrom('d.m.Y')); ?></p>
<p><?php echo  sprintf(_("Stop date: %s"), $campaign->getDateTo('d.m.Y')); ?></p>
<p><?php echo  sprintf(_("Impression limit: %s"), $campaign->getImpressionLimit()); ?></p>
<p><?php echo  sprintf(_("Click limit: %s"), $campaign->getClickLimit()); ?></p>
<p><?php echo  sprintf(_("Cost: %s"), $campaign->getCost()); ?></p>
<?php
$linkUrl = Yii::app()->createAbsoluteUrl('campaigns/edit', array('id' => $campaign->_id));
$linkText = sprintf('Campaign link: %s', $linkUrl);
?>
<p><?php echo CHtml::link($linkText, $linkUrl);?>
