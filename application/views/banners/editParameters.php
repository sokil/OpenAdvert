<?php /* @var $banner \MongoAdvertDb\Banners\Banner */ ?>

<form method="post" action="/banners/save" class="form-horizontal tab-pane active">
    <input type="hidden" name="id" value="<?php echo $banner->getId(); ?>" />

    <?php if(!$banner->getId()): ?>
    <input type="hidden" name="campaign" value="<?php echo $banner->getCampaignId(); ?>" />
    <input type="hidden" name="type" value="<?php echo $banner->getType(); ?>" />
    <?php endif; ?>

    <div class="form-group">
        <label class="col-sm-2 control-label"><?php echo _('Banner type'); ?></label>
        <div class="col-sm-5">
            <div class="form-control" style="background: #efefef;">
                <span class="glyphicon <?php echo $bannerType['icon']; ?>"></span>
                <?php echo $bannerType['name']; ?>
                <?php if($banner->isLimited()): ?>
                <span class="label label-warning"><?php echo _('Limits reached'); ?></span>
                <?php endif; ?>
                <?php if($banner->isDeactivated()): ?>
                <span class="label label-warning"><?php echo _('Campaign deactivated'); ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="form-group">
        <label class="col-sm-2 control-label"><?php echo _('Banner name'); ?></label>
        <div class=" col-sm-5">
            <input type="text" name="name" class="form-control" value="<?php echo $banner->getName(); ?>" />
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label"><?php echo _('URL'); ?></label>
        <div class="col-sm-5">
            <input type="text" name="url" class="form-control" value="<?php echo $banner->getUrl(); ?>" />
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label"><?php echo _('Impressions Limits'); ?></label>
        <div class="col-sm-2">
            <div class="input-group <?php if($banner->isLimitRequiredByTotalImpression()) echo ' has-error'; ?>">
            <span class="input-group-addon"><?php echo _('total'); ?></span>
            <input type="text" name="limits[impressions][total]" class="form-control" value="<?php echo $banner->getTotalImpressionLimit(); ?>" />
            <span class="input-group-addon"><?php echo $banner->getTotalImpressionCounter(); ?></span>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="input-group <?php if($banner->isLimitRequiredByDayImpression()) echo ' has-error'; ?>">
            <span class="input-group-addon"><?php echo _('day'); ?></span>
            <input type="text" name="limits[impressions][day]" class="form-control" value="<?php echo $banner->getDailyImpressionLimit(); ?>" />
            <span class="input-group-addon"><?php echo $banner->getDailyImpressionCounter(); ?></span>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="input-group <?php if($banner->isLimitRequiredByHourImpression()) echo ' has-error'; ?>">
            <span class="input-group-addon"><?php echo _('hour'); ?></span>
            <input type="text" name="limits[impressions][hour]" class="form-control" value="<?php echo $banner->getHourlyImpressionLimit(); ?>" />
            <span class="input-group-addon"><?php echo $banner->getHourlyImpressionCounter(); ?></span>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label"><?php echo _('Clicks Limits'); ?></label>
        <div class="col-sm-2">
            <div class="input-group <?php if($banner->isLimitRequiredByTotalClick()) echo ' has-error'; ?>">
            <span class="input-group-addon"><?php echo _('total'); ?></span>
            <input type="text" name="limits[clicks][total]" class="form-control" value="<?php echo $banner->getTotalClickLimit(); ?>" />
            <span class="input-group-addon"><?php echo $banner->getTotalClickCounter(); ?></span>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="input-group <?php if($banner->isLimitRequiredByDayClick()) echo ' has-error'; ?>">
            <span class="input-group-addon"><?php echo _('day'); ?></span>
            <input type="text" name="limits[clicks][day]" class="form-control" value="<?php echo $banner->getDailyClickLimit(); ?>" />
            <span class="input-group-addon"><?php echo $banner->getDailyClickCounter(); ?></span>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="input-group <?php if($banner->isLimitRequiredByHourClick()) echo ' has-error'; ?>">
            <span class="input-group-addon"><?php echo _('hour'); ?></span>
            <input type="text" name="limits[clicks][hour]" class="form-control" value="<?php echo $banner->getHourlyClickLimit(); ?>" />
            <span class="input-group-addon"><?php echo $banner->getHourlyClickCounter(); ?></span>
            </div>
        </div>
    </div>

    <?php $this->renderPartial('editParameters' . ucfirst($banner->getType()) . 'Banner', array(
        'banner' => $banner
    )); ?>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <input type="submit" value="<?php echo _('Save'); ?>" class="btn btn-default">
            <span class="status" style="margin-left: 20px;"></span>
        </div>
    </div>
</form>