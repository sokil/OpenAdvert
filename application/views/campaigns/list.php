<?php
if (Yii::app()->user->checkAccess('manager')) :
$this->breadcrumbs = array(
    _('Advertisers') => array('/advertisers'),
    sprintf(_('Campaigns of "%s"'), $advertiser->getName())
);
else:
$this->breadcrumbs = array(
    sprintf(_('Campaigns of "%s"'), $advertiser->getName())
);

endif;

$this->pageTitle = $advertiser->getName() . ' :: ' . _('Campaigns');
?>

<?php /* @var $campaigns \MongoAdvertDb\Campaigns */?>
<?php /* @var $advertiser \MongoAdvertDb\Advertisers\Advertiser */?>
<div class="row-fluid bottom-space">
    <div class="col-md-9 btn-toolbar" role="toolbar">
        <a href="/campaigns/add?advertiser=<?php echo $advertiser->getId(); ?>" class="btn btn-primary btn-xs">
            <span class="glyphicon glyphicon-plus"></span>
            <?php echo _('Add campaign'); ?>
        </a>
    </div>
    <div class="col-md-3">
        <form method="get" action="/campaigns?advertiser=<?php echo $advertiser->getId(); ?>" id="frmCampaignFilter">
            <div class="input-group">
                <input type="text" name="filter[name]" class="form-control" placeholder="<?php echo _('Campaign name'); ?>">
                <span class="input-group-btn">
                    <input type="submit" class="btn btn-default" value="<?php echo _('Find'); ?>" />
                </span>
            </div>
        </form>
    </div>
    <div class="clearfix"></div>
</div>
<table class="table table-hover table-condensed" id="tblCampaigns">
    <col/><col/><col/><col/><col/><col/><col width="30px" /><col width="30px" /><col width="30px" />
    <thead>
        <tr>
            <th><?php echo _('Name'); ?></th>
            <th><span class="glyphicon glyphicon-calendar"></span> <?php echo _('Start date'); ?></th>
            <th><span class="glyphicon glyphicon-calendar"></span> <?php echo _('Stop date'); ?></th>
            <th><span class="glyphicon glyphicon-eye-open"></span> <?php echo _('Impression limit'); ?></th>
            <th><span class="glyphicon glyphicon-hand-up"></span> <?php echo _('Click limit'); ?></th>
            <th><span class="glyphicon glyphicon-usd"></span> <?php echo _('Cost'); ?></th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php echo $this->renderPartial('listPartial', array(
            'campaigns' => $campaigns,
        )); ?>
    </tbody>
</table>

<script type="text/javascript">
    $('#tblCampaigns').tableButtons();
    
    // find
    $('#frmCampaignFilter').submit(function(e) {
        e.preventDefault();
        var $frm = $(this);
        $.get($frm.attr('href'), $frm.serialize(), function(response) {
            $('#tblCampaigns tbody').html(response);
        });
    });
</script>