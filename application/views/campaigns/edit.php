<?php
if (Yii::app()->user->checkAccess('manager')):
$this->breadcrumbs = array(
    _('Advertisers') => array('/advertisers'),
    sprintf(_('Campaigns of "%s"'), $advertiser->getName()) => array('/campaigns?advertiser=' . $advertiser->getId()),
    $campaign->getName() ? $campaign->getName() : _('New campaign')
);
else:
$this->breadcrumbs = array(
    sprintf(_('Campaigns of "%s"'), $advertiser->getName()) => array('/campaigns?advertiser=' . $advertiser->getId()),
    $campaign->getName() ? $campaign->getName() : _('New campaign')
);
endif
?>

<form id="frmCampaign" method="POST" action="/campaigns/save?advertiser=<?php echo $campaign->getAdvertiserId(); ?>" class="form-horizontal" />
    
    <input type="hidden" name="id" value="<?php echo $campaign->getId(); ?>" />

    <div class="form-group">
        <label class="col-sm-2 control-label"><?php echo _('Campaign name'); ?></label>
        <div class=" col-sm-5">
            <input type="text" name="campaign[name]" class="form-control" value="<?php echo $campaign->getName(); ?>" />
        </div>
    </div>
    
    <div class="form-group">
        <label class="col-sm-2 control-label"><?php echo _('Start date'); ?></label>
        <div class=" col-sm-2">
            <input type="text" name="campaign[dateFrom]" class="form-control" id="txtDateFrom" value="<?php echo $campaign->getDateFrom('Y-m-d'); ?>" />
        </div>
    </div>
    
    <div class="form-group">
        <label class="col-sm-2 control-label"><?php echo _('Stop date'); ?></label>
        <div class=" col-sm-2">
            <input type="text" name="campaign[dateTo]" class="form-control" id="txtDateTo" value="<?php echo $campaign->getDateTo('Y-m-d'); ?>" />
        </div>
    </div>
    
    <div class="form-group">
        <label class="col-sm-2 control-label"><?php echo _('Limit views'); ?></label>
        <div class=" col-sm-2">
            <input type="text" name="campaign[impressionLimit]" class="form-control"  value="<?php echo $campaign->getImpressionLimit(); ?>" />
        </div>
    </div>
    
    <div class="form-group">
        <label class="col-sm-2 control-label"><?php echo _('Limit clicks'); ?></label>
        <div class="col-sm-2">
            <input type="text" name="campaign[clickLimit]" class="form-control" value="<?php echo $campaign->getClickLimit(); ?>" />
        </div>
    </div>

    <?php if(Yii::app()->user->checkAccess('manageCampaign.editWithoutModeration')): ?>
    <div class="form-group">
        <label class="col-sm-2 control-label"><?php echo _('Pricing model'); ?></label>
        <div class="col-sm-5">
            <select name="campaign[pricing][model]" class="form-control">
                <option <?php if($campaign->isCPMPricingModel()) echo ' selected'; ?> value="<?php echo \MongoAdvertDb\Campaigns\Campaign::PRICING_MODEL_CPM; ?>"><?php echo _('Cost per thousand impressions'); ?></option>
                <option <?php if($campaign->isCPCPricingModel()) echo ' selected'; ?> value="<?php echo \MongoAdvertDb\Campaigns\Campaign::PRICING_MODEL_CPC; ?>"><?php echo _('Cost per clicks'); ?></option>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label"><?php echo _('Price'); ?></label>
        <div class="col-sm-2">
            <input type="text" name="campaign[pricing][price]" class="form-control" value="<?php echo $campaign->getPrice(); ?>" />
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <input type="checkbox" name="campaign[pricing][unique]" value="1" <?php if($campaign->isCostCalculatedByUniqueEvents()) echo 'checked'; ?>>
            <?php echo _('Limit and pay by unique events only'); ?>
        </div>
    </div>
    <?php endif;?>
    
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?php if($campaign->isModerationRequired()): ?>
            <input type="submit" value="<?php echo _('Publish'); ?>" class="btn btn-success">
            <?php else : ?>
            <input type="submit" value="<?php echo _('Save'); ?>" class="btn btn-default">
            <?php endif; ?>
            <span class="status" style="margin-left: 20px;"></span>
        </div>
    </div>
</form>

<script type="text/javascript">
    // date-from datepicker
    $('#txtDateFrom').pickmeup({
        mode            : 'single',
        format          : 'Y-m-d',
        hide_on_select  : true,
        locale: {
            days: ["<?php echo _('Sunday'); ?>","<?php echo _('Monday'); ?>","<?php echo _('Tuesday'); ?>","<?php echo _('Wednesday'); ?>","<?php echo _('Thursday'); ?>","<?php echo _('Friday'); ?>","<?php echo _('Saturday'); ?>","<?php echo _('Sunday'); ?>"],
            daysShort:["<?php echo _('Sun'); ?>","<?php echo _('Mon'); ?>","<?php echo _('Tue'); ?>","<?php echo _('Wed'); ?>","<?php echo _('Thu'); ?>","<?php echo _('Fri'); ?>","<?php echo _('Sat'); ?>","<?php echo _('Sun'); ?>"],
            daysMin:["<?php echo _('Su'); ?>","<?php echo _('Mo'); ?>","<?php echo _('Tu'); ?>","<?php echo _('We'); ?>","<?php echo _('Th'); ?>","<?php echo _('Fr'); ?>","<?php echo _('Sa'); ?>","<?php echo _('Su'); ?>"],
            months:["<?php echo _('January'); ?>","<?php echo _('February'); ?>","<?php echo _('March'); ?>","<?php echo _('April'); ?>","<?php echo _('May'); ?>","<?php echo _('June'); ?>","<?php echo _('July'); ?>","<?php echo _('August'); ?>","<?php echo _('September'); ?>","<?php echo _('October'); ?>","<?php echo _('November'); ?>","<?php echo _('December'); ?>"],
            monthsShort:["<?php echo _('Jan'); ?>","<?php echo _('Feb'); ?>","<?php echo _('Mar'); ?>","<?php echo _('Apr'); ?>","<?php echo _('May'); ?>","<?php echo _('Jun'); ?>","<?php echo _('Jul'); ?>","<?php echo _('Aug'); ?>","<?php echo _('Sep'); ?>","<?php echo _('Oct'); ?>","<?php echo _('Nov'); ?>","<?php echo _('Dec'); ?>"]
        },
        before_show		: function () {
            var $this = $(this);
            $this.pickmeup('set_date', $this.val());
        },
        change: function (formatted) {
            $(this).val(formatted);
        }
    });
    
    // date-to datepicker
    $('#txtDateTo').pickmeup({
        mode            : 'single',
        format          : 'Y-m-d',
        hide_on_select  : true,
        locale: {
            days: ["<?php echo _('Sunday'); ?>","<?php echo _('Monday'); ?>","<?php echo _('Tuesday'); ?>","<?php echo _('Wednesday'); ?>","<?php echo _('Thursday'); ?>","<?php echo _('Friday'); ?>","<?php echo _('Saturday'); ?>","<?php echo _('Sunday'); ?>"],
            daysShort:["<?php echo _('Sun'); ?>","<?php echo _('Mon'); ?>","<?php echo _('Tue'); ?>","<?php echo _('Wed'); ?>","<?php echo _('Thu'); ?>","<?php echo _('Fri'); ?>","<?php echo _('Sat'); ?>","<?php echo _('Sun'); ?>"],
            daysMin:["<?php echo _('Su'); ?>","<?php echo _('Mo'); ?>","<?php echo _('Tu'); ?>","<?php echo _('We'); ?>","<?php echo _('Th'); ?>","<?php echo _('Fr'); ?>","<?php echo _('Sa'); ?>","<?php echo _('Su'); ?>"],
            months:["<?php echo _('January'); ?>","<?php echo _('February'); ?>","<?php echo _('March'); ?>","<?php echo _('April'); ?>","<?php echo _('May'); ?>","<?php echo _('June'); ?>","<?php echo _('July'); ?>","<?php echo _('August'); ?>","<?php echo _('September'); ?>","<?php echo _('October'); ?>","<?php echo _('November'); ?>","<?php echo _('December'); ?>"],
            monthsShort:["<?php echo _('Jan'); ?>","<?php echo _('Feb'); ?>","<?php echo _('Mar'); ?>","<?php echo _('Apr'); ?>","<?php echo _('May'); ?>","<?php echo _('Jun'); ?>","<?php echo _('Jul'); ?>","<?php echo _('Aug'); ?>","<?php echo _('Sep'); ?>","<?php echo _('Oct'); ?>","<?php echo _('Nov'); ?>","<?php echo _('Dec'); ?>"]
        },
        before_show		: function () {
            var $this = $(this);
            $this.pickmeup('set_date', $this.val());
        },
        change: function (formatted) {
            $(this).val(formatted);
        }
    });
    
    // form handler
    $('#frmCampaign').form({
        onSuccess: function(response) {
            $('#frmCampaign input[name=id]').val(response.campaignId);
            if(history.pushState) {
                history.pushState(null, null, '/campaigns/edit/id/' + response.campaignId);
            }
        },
        modelToFormFieldName: function(fieldName) {
            return 'campaign\\[' + fieldName.replace('.', '\\]\\[') + '\\]';
        }
    });
</script>
