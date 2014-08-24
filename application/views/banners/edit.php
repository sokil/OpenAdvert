<?php
$this->breadcrumbs = array(
    _('Advertisers') => array('/advertisers'),
    sprintf(_('Campaigns of "%s"'), $advertiser->getName()) => array('/campaigns?advertiser=' . $advertiser->getId()),
    sprintf(_('Banners of "%s"'), $campaign->getName()) => array('/banners?campaign=' . $campaign->getId()),
    $banner->getName() ? $banner->getName() : _('New banner')
); 

$this->pageTitle = $banner->getName() . ' :: ' . _('Banners');

?>

<ul class="nav nav-tabs bottom-space" id="bannerEditorTabs">
  <li class="active"><a href="#parameters" data-toggle="tab">
      <span class="glyphicon glyphicon glyphicon-cog"></span> <?php echo _('Parameters'); ?>
  </a></li>
  <li <?php if(!$banner->getId()): ?>class="disabled"<?php endif; ?>><a href="#deliveryOptions" <?php if($banner->getId()): ?>data-toggle="tab"<?php endif; ?>>
      <span class="glyphicon glyphicon glyphicon-globe"></span> <?php echo _('Delivery options'); ?>
  </a></li>
  <li <?php if(!$banner->getId()): ?>class="disabled"<?php endif; ?>><a href="#linkedZones" <?php if($banner->getId()): ?>data-toggle="tab"<?php endif; ?>>
      <span class="glyphicon glyphicon glyphicon-link"></span> <?php echo _('Linked Zones'); ?>
  </a></li>
</ul>

<script type="text/javascript">
    var editor = new <?php echo $banner->getType(); ?>BannerEditor();
</script>

<div class="tab-content">
    <div id="parameters" class="tab-pane active" data-new-banner="<?php echo $banner->getId() ? 0 : 1; ?>">
        <?php $this->renderPartial('editParameters', array(
            'banner'        => $banner,
            'bannerType'    => $bannerType,
        )); ?>
    </div>
    <script type="text/javascript">
        editor.initParametersForm();
    </script>
    
    
    <div id="deliveryOptions" class="tab-pane">
        <?php $this->renderPartial('editDeliveryOptions', array(
            'banner'                    => $banner,
            'availableDeliveryOptions'  => $availableDeliveryOptions,
        )); ?>
    </div>
    
    <div id="linkedZones" class="tab-pane">
        <?php
            $this->renderPartial('editLinkedZones', array(
                'banner'            => $banner,
                'zonesDataProvider' => $zonesDataProvider,
            ));
        ?>
    </div>
</div>