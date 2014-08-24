<div class="form-group row-fluid bordered">
    <input type="hidden" name="option[type][<?php echo $index; ?>]" value="<?php echo $option->getType(); ?>" />
    <label class="col-md-2 control-label">
        <?php echo $option->getCaption(); ?>
    </label>
    <div class="col-md-2">
        <select name="option[comparison][<?php echo $index; ?>]" class="form-control ">
            <?php foreach($option->getComparisons() as $comparisonValue => $comparisonCaption): ?>
            <option value="<?php echo $comparisonValue; ?>" <?php echo $comparisonValue==$option->getComparison()?'selected':''; ?> >
                <?php echo $comparisonCaption; ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-7">
        <input type="text" class="form-control txtDate" name="option[value][<?php echo $index; ?>]" value="<?php echo $option->getValue(); ?>" />
    </div>
    <div class="col-md-1">
        <a href="javascript:void(0);" class="delete">
            <span class="glyphicon glyphicon-trash"></span>
        </a>
    </div>
    <div class="clearfix"></div>
</div>

<script type="text/javascript">
    (function() {
        
        var $cnt = $('#deliveryOption<?php echo $index; ?>'),
            $txtDate = $('.txtDate', $cnt);
        $txtDate.pickmeup({
            mode            : 'single',
            format          : 'Y-m-d',
            hide_on_select  : true,
            before_show	    : function () {
                var $this = $(this);
                $this.pickmeup('set_date', $this.val());
            },
            change: function (formatted) {
                $(this).val(formatted);
            }
        });
    
    })();
</script>
