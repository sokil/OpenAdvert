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
        <input type="text" name="option[value][<?php echo $index; ?>][minh]" value="<?php echo $option->getValue()['minh']; ?>" size="2" maxlength="2" />
        :
        <input type="text" name="option[value][<?php echo $index; ?>][minm]" value="<?php echo $option->getValue()['minm']; ?>" size="2" maxlength="2"/>
        -
        <input type="text" name="option[value][<?php echo $index; ?>][maxh]" value="<?php echo $option->getValue()['maxh']; ?>" size="2" maxlength="2"/>
        :
        <input type="text" name="option[value][<?php echo $index; ?>][maxm]" value="<?php echo $option->getValue()['maxm']; ?>" size="2" maxlength="2"/>
    </div>
    <div class="col-md-1">
        <a href="javascript:void(0);" class="delete">
            <span class="glyphicon glyphicon-trash"></span>
        </a>
    </div>
    <div class="clearfix"></div>
</div>