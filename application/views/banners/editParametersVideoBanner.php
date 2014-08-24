<div class="form-group">
    <label class="col-sm-2 control-label"><?php echo _('Skip Offset'); ?></label>
    <div class="col-sm-2">
        <div class="input-group">
        <input type="text" name="skipoffset" class="form-control" value="<?php echo $banner->getSkipOffset(); ?>" />
        <span class="input-group-addon"><?php echo _('seconds'); ?></span>
        </div>
    </div>
</div>

<div class="form-group" id="frmVideoBannerMediaFiles">
    <label class="col-sm-2 control-label"><?php echo _('Media files'); ?></label>
    <div class="col-sm-9">
        
        <div class="btn-group">
            <a href="/banners/addmediafileform?banner=<?php echo $banner->getId(); ?>&campaign=<?php echo $banner->getCampaignId(); ?>" onclick="modal(this); return false;" class="btn btn-success" title="<?php echo _('New media file'); ?>">
                <span class="glyphicon glyphicon-plus"></span>
                <?php echo _('Add URL'); ?>
            </a>
            <div id="cmdUpload" class="upload btn btn-success">
                <span class="glyphicon glyphicon-upload"></span>
                <?php echo _('Upload'); ?>
                <input type="file" />
            </div>
        </div>
        <small class="text-muted">
            <?php echo _('Maximum file size') . ' ' . ini_get('upload_max_filesize'); ?>
        </small>
        <table class="table table-hover table-striped" id="mediaFileList">
            <thead>
                <tr>
                    <th><?php echo _('URL'); ?></th>
                    <th><?php echo _('Delivery'); ?></th>
                    <th><?php echo _('Size'); ?></th>
                    <th><?php echo _('Mime'); ?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

    </div>
</div>

<div class="form-group" id="frmVideoBannerEvents">
    <label class="col-sm-2 control-label"><?php echo _('Tracking Events'); ?></label>
    <div class="col-sm-5">

        <div class="input-group bottom-space">
            <select class="form-control disabled" id="selAvailableEventList">
                <?php foreach(Sokil\Vast\Ad\InLine\Creative\Linear::getEventList() as $event): ?>
                <option><?php echo $event; ?></option>
                <?php endforeach; ?>
            </select>
            <span class="input-group-btn">
                <button type="button" id="cmdAddEvent" class="btn btn-success" style="width: 180px;">
                    <span class="glyphicon glyphicon-plus"></span>
                    <?php echo _('Add event'); ?>
                </button>
            </span>
        </div>

    </div>
</div>

<script type="text/javascript">

    // define media files
    var mediaFileList = <?php echo json_encode(array_map(function($mediaFile) {
        return array(
            'id'        => (string) $mediaFile->getId(),
            'url'       => $mediaFile->getUrl(), 
            'delivery'  => $mediaFile->getDelivery(),
            'size'      => $mediaFile->getHeight() . 'x' . $mediaFile->getWidth(),
            'type'      => $mediaFile->getType(),
        );
    }, $banner->getMediaFiles()));
    ?>;

    editor.addMediaFileList(mediaFileList);
    
    editor.i18n.addMessages({
        "You need to attach media file": "<?php echo _("You need to attach media file"); ?>"
    });

    // define events
    var eventsList = <?php echo json_encode($banner->getEvents()); ?>;

    editor.addEventsList(eventsList);
</script>