<div class="form-group">
    <label class="col-sm-2 control-label"><?php echo _('Text'); ?></label>
    <div class=" col-sm-5">
        <textarea name="text" class="form-control" id="txtBannerText" rows="3"><?php echo $banner->getText(); ?></textarea>
        <div id="textLengthStatus"><?php echo _('Length'); ?>: <?php echo mb_strlen($banner->getText()); ?></div>
    </div>
</div>
<script type="text/javascript">
    $('#txtBannerText').keyup(function() {
        $('#textLengthStatus').text('<?php echo _('Length'); ?>: ' + $('#txtBannerText').val().length);
    }); 
</script>