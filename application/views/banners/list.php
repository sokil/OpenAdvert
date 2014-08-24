<?php
$this->breadcrumbs = array(
    _('Advertisers') => array('/advertisers'),
    sprintf(_('Campaigns of "%s"'), $advertiser->getName()) => array('/campaigns?advertiser=' . $advertiser->getId()),
    sprintf(_('Banners of "%s"'), $campaign->getName())    
); 

$this->pageTitle = $campaign->getName() . ' :: ' . $advertiser->getName() . ' :: ' . _('Banners');

?>

<div class="btn-toolbar bottom-space" role="toolbar">
    <div class="btn-group">
        <button type="button" class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown">
            <span class="glyphicon glyphicon-plus"></span>
            <?php echo _('Add banner'); ?>
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu" role="menu">
            <?php foreach($bannerTypes as $bannerType => $bannerTypeMeta): ?>
            <li>
                <a href="/banners/add?type=<?php echo $bannerType; ?>&campaign=<?php echo $campaign->getId(); ?>">
                    <span class="glyphicon <?php echo $bannerTypeMeta['icon']; ?>"></span>
                    <?php echo $bannerTypeMeta['name']; ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<table class="table table-condensed table-striped" id="tblBanners">
    <col/><col/><col width="70px"/>
    <thead>
        <tr>
            <th><?php echo _('Name'); ?></th>
            <th><?php echo _('URL'); ?></th>
            <?php if(Yii::app()->user->checkAccess('manageCampaign.editWithoutModeration')): ?>
            <th></th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php echo $this->renderPartial('listPartial', array(
            'banners'       => $banners,
            'bannerTypes'   => $bannerTypes,
        )); ?>
    </tbody>
</table>

<script type="text/javascript">
    // status
    $('#tblBanners').tableButtons();
</script>