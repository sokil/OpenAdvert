<h1><?php echo $user->getName(); ?></h1>
<p><?php echo  sprintf(_("You have successfully registered in %s"), '<a href="' . Yii::app()->getBaseUrl(true) .'">' . Yii::app()->name . '</a>'); ?>
<p><?php echo sprintf(_("Site: %s"), '<a href="' . Yii::app()->getBaseUrl(true) . '">' . Yii::app()->getBaseUrl(true) . '</a>'); ?>
<p><?php echo sprintf(_("Email: %s"), $user->getEmail()); ?>
<?php if($user->isPasswordChanged()): ?>
<p><?php echo sprintf(_("Password: %s"), $user->getChangedPassword()); ?>
<?php endif; ?>
