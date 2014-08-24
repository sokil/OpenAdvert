<h1><?php echo $user->getName(); ?></h1>
<p><?php echo _("Your profile was changed."); ?>
<p><?php echo _('To sign in on site use this e-mail and password'); ?>:</p>
<p><?php echo sprintf(_("Email: %s"), $user->getEmail()); ?>
<?php if($user->isPasswordChanged()): ?>
<p><?php echo sprintf(_("Password: %s"), $user->getChangedPassword()); ?>
<?php endif; ?>
