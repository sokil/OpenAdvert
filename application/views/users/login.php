<div class="row-fluid">
    <form method="post" action="/login" class="form col-md-3 col-md-offset-4" role="form">
        <div class="form-group<?php if(isset($errors['username'])) echo ' has-error' ?>">
            <div class="input-group">
                <span class="input-group-addon"><span class="glyphicon glyphicon glyphicon-user"></span></span>
                <input type="text" class="form-control" name="LoginForm[username]" placeholder="<?php echo _('E-mail'); ?>" autocomplete="off" />
                <?php if(isset($errors['username'])): ?>
                    <?php echo implode(',', $errors['username']); ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="form-group<?php if(isset($errors['password'])) echo ' has-error' ?>">
            <div class="input-group">
                <span class="input-group-addon"><span class="glyphicon glyphicon glyphicon-asterisk"></span></span>
                <input type="password" class="form-control" name="LoginForm[password]" placeholder="<?php echo _('Password'); ?>" autocomplete="off" />
                <?php if(isset($errors['password'])): ?>
                    <?php echo implode(',', $errors['password']); ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="form-group">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="LoginForm[rememberMe]" value="1"/> <?php echo _('Remember me?'); ?>
                </label>
            </div>
        </div>
        <div class="form-group text-center">
            <input type="submit" class="btn btn-default" value="<?php echo _('Sign in'); ?>" />
        </div>
    </form>
    
</div>