<div class="form-group">
    <label class="col-sm-2 control-label"><?php echo _('Image URL'); ?></label>
    <div class="col-sm-5">
        <div class="input-group">
            <input type="text" name="imageUrl" class="form-control" value="<?php echo $banner->getImageUrl(); ?>" />
            <span class="input-group-btn">
                <button class="btn btn-default" type="button"><?php echo _('Upload'); ?></button>
                <input type="file" id="cmdUploadBannerImage"/>
            </span>
        </div>
        <div class="progress top-space" style="display: none;">
            <div class="progress-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100">0%</div>
        </div>
        <div class="top-space" <?php if(!$banner->getImageUrl()) echo 'style="display: none;"'; ?> id="bannerImage">
            <img src="<?php echo $banner->getImageUrl(); ?>" />
        </div>
    </div>
</div>