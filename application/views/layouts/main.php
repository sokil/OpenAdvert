<?php /* @var $this Controller */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title><?php echo CHtml::encode($this->pageTitle); ?> :: <?php echo Yii::app()->name; ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="language" content="en" />
        <link href="/favicon.ico" rel="shortcut icon" type="image/x-icon" />
    </head>
    <body>
        <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
            <div class="container">
                <a class="navbar-brand" href="/">
                    <span class="glyphicon glyphicon-leaf" style="color: #47a447;"></span>
                    <?php echo Yii::app()->name; ?>
                </a>
                <ul class="nav navbar-nav">
                    <?php if (Yii::app()->user->checkAccess('manageZone')): ?>
                    <li><a href="/zones">
                        <span class="glyphicon glyphicon-th"></span>
                        <?php echo _('Zones'); ?>
                    </a></li>
                    <?php endif; ?>
                    <?php if (Yii::app()->user->checkAccess('manageAdvertiser')): ?>
                    <li><a href="/advertisers">
                        <span class="glyphicon glyphicon-picture"></span>
                        <?php echo _('Campaigns'); ?>
                    </a></li>
                    <?php endif; ?>
                    <?php if (Yii::app()->user->checkAccess('advertiser')): ?>
                    <li><a href="/campaigns">
                        <span class="glyphicon glyphicon-picture"></span>
                        <?php echo _('Campaigns'); ?>
                    </a></li>
                    <?php endif; ?>
                    
                    <?php if (Yii::app()->user->checkAccess('manageStat')): ?>
                    <li><a href="/stat">
                        <span class="glyphicon glyphicon-stats"></span>
                        <?php echo _('Statistics'); ?>
                    </a></li>
                    <?php elseif (Yii::app()->user->checkAccess('manageAdvertStat')): ?>
                    <li><a href="/stat/advertiser">
                        <span class="glyphicon glyphicon-stats"></span>
                        <?php echo _('Statistics'); ?>
                    </a></li>                    
                    <?php elseif (Yii::app()->user->checkAccess('managePartnerStat')): ?>
                    <li><a href="/stat/partner">
                        <span class="glyphicon glyphicon-stats"></span>
                        <?php echo _('Statistics'); ?>
                    </a></li>
                    <?php endif; ?>
                    
                    <?php if (Yii::app()->user->checkAccess('manageUser')): ?>
                    <li><a href="/users">
                        <span class="glyphicon glyphicon-user"></span>
                        <?php echo _('Users'); ?>
                    </a></li>
                    <?php endif; ?>
                    <?php if (Yii::app()->user->checkAccess('manageLog')): ?>
                    <li><a href="/log">
                        <span class="glyphicon glyphicon-stats"></span>
                        <?php echo _('Log'); ?>
                    </a></li>
                    <?php endif; ?>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <?php if (Yii::app()->user->isGuest): ?>
                        <li><a href="/login"><span class="glyphicon glyphicon-log-in"></span> <?php echo _('Sing in'); ?></a></li>
                    <?php else: ?>
                        <li><?=CHtml::link( Yii::app()->user->getProfile()->email, Yii::app()->createUrl('users/edit', array('id' => Yii::app()->user->getProfile()->getId())) )?></li>
                        <li><a href="/logout"><span class="glyphicon glyphicon-log-out"></span> <?php echo _('Sign out'); ?></a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
        <div class="container">
        <?php
        if (isset($this->breadcrumbs)) {
            $this->widget('zii.widgets.CBreadcrumbs', array(
                'tagName' => 'ul',
                'htmlOptions' => array('class' => 'breadcrumb'),
                'activeLinkTemplate' => '<li><a href="{url}">{label}</a></li>',
                'inactiveLinkTemplate' => '<li class="active">{label}</li>',
                'separator' => '',
                'homeLink' => false,
                'links' => $this->breadcrumbs
            ));
        }
        ?>
        </div>
        <div class="container">
            <?php echo $content; ?>
        </div>
    </body>
</html>
