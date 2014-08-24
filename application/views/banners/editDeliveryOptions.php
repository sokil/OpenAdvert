<form action="/banners/deliveryOptions" id="frmDeliveryOptions" class="form-horizontal">
    <input type="hidden" name="id" value="<?php echo $banner->getId(); ?>" />
    
    <div class="form-group">
        <div class="row-fluid">
            <div class="col-md-5">
                <div class="input-group bottom-space">
                    <select id="comboOptionTypes" class="form-control">
                    <?php foreach($availableDeliveryOptions as $option): ?>
                        <option value="<?php echo $option->getType(); ?>">
                            <?php echo $option->getCaption(); ?>
                        </option>
                    <?php endforeach; ?>
                    </select>
                    <span class="input-group-btn">
                        <button type="button" id="cmdAddNewOption" class="btn btn-success" style="width: 180px;">
                            <span class="glyphicon glyphicon-plus"></span>
                            <?php echo _('Add'); ?>
                        </button>
                    </span>
                </div>
            </div>
            <div class="col-md-7"></div>
        </div>
    </div>
    

    <?php foreach($banner->getDeliveryOptions() as $position => $deliveryOption): ?>
        <?php echo $deliveryOption->render($position); ?>
    <?php endforeach; ?>
    
    <div class="top-space">
        <input type="submit" class="btn btn-success" value="<?php echo _('Save'); ?>" />
    </div>
</form>

<script type="text/javascript">editor.initDeliveryOptionsForm();</script>

