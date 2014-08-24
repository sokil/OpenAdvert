<form id="frmNewMediaFile" action="/banners/addmediafile" method="post">
    <input type="hidden" name="banner" value="<?php echo $bannerId; ?>" />
    <input type="hidden" name="campaign" value="<?php echo $campaignId; ?>" />
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><?php echo _('New media file'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="input-group bottom-space">
                    <span class="input-group-addon" style="width: 150px;"><?php echo _('URL'); ?></span>
                    <input type="text" name="url" class="url form-control" style="width: 350px;" placeholder="<?php echo _('Upload file or type url directly'); ?>" />
                </div>
                <div class="input-group bottom-space">
                    <span class="input-group-addon" style="width: 150px;"><?php echo _('Delivery'); ?></span>
                    <select name="delivery" class="delivery form-control">
                        <option value="progressive"><?php echo _('Progressive delivery'); ?></option>
                        <option value="streaming"><?php echo _('Streaming delivery'); ?></option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <span class="status"></span>
                <button id="cmdAddMediaFile" type="submit" class="btn btn-success">
                    <span class="glyphicon glyphicon-floppy-disk"></span>
                    <?php echo _('Save'); ?>
                </button>
            </div>
        </div>
    </div>
</form>
<script type="text/javascript">
    // form handler
    $('#frmNewMediaFile').form({
        onSuccess: function(response) {
            var $modal = $('#frmNewMediaFile').closest('.modal');
            $modal
                .on('hidden.bs.modal', function() {
                    $modal.remove();
                })
                .modal('hide');
        
            // if in modal environment - add item to list of uploaded files
            if($('#mediaFileList').length) {
                
                // define banner id if banner created
                $('#parameters input[name=id]').val(response.banner_id)
                
                // add media file
                editor.addMediaFile(response.url, response.delivery, response.id, response.size, response.type);
            }
        }
    });
</script>