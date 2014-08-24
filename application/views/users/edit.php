<?php
$this->breadcrumbs = array(
    _('Users') => array('index'),
    _('Edit')
);
?>

<form method="post" action="/users/save" class="form" id="frmUser">
    
    <input type="hidden" name="id" value="<?php echo $user->getId(); ?>" />
    
    <?php if($advertiser): ?>
    <input type="hidden" name="advertiser" value="<?php echo $advertiser; ?>" />
    <?php endif; ?>

    <?php if($partner): ?>
        <input type="hidden" name="partner" value="<?php echo $partner; ?>" />
    <?php endif; ?>
    
    <div class="form-group">
        <label class="control-label required"><?php echo _('Name'); ?><span class="required">*</span></label>
        <input type="text" class="form-control" name="name" value="<?php echo $user->name; ?>" autocomplete="off" />
    </div>

    <div class="form-group">
        <label class="control-label required"><?php echo _('Phone'); ?></label>
        <input type="text" class="form-control" name="phone" value="<?php echo $user->phone; ?>" autocomplete="off" />
    </div>

    <div class="form-group">
        <label class="control-label required"><?php echo _('E-mail'); ?><span class="required">*</span></label>
        <input type="text" class="form-control" name="email" value="<?php echo $user->email; ?>" autocomplete="off" />
    </div>

    <div class="form-group">
        <label class="control-label required"><?php echo _('Password'); ?><span class="required">*</span></label>
        <input type="password" class="form-control" name="password" autocomplete="off" />
    </div>

    <input type="submit" value="<?php echo _('Save'); ?>" class="btn btn-primary" />
</form>

<script type="text/javascript">
    $('#frmUser').form({
        onSuccess: function(response) {
            $('#frmUser input[name=id]').val(response.id);
            if(history.pushState) {
                history.pushState(null, null, '/users/edit/id/' + response.id);
            }
        }
    });
</script>
