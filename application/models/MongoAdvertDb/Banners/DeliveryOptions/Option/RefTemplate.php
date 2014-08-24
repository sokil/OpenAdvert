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
        <input type="hidden" id="txtPartnerId" name="option[value][<?php echo $index; ?>]" value="<?php echo $option->getValue(); ?>" />
        <input type="text" id="txtPartnerName" name="option[meta][<?php echo $index; ?>][name]" value="<?php echo $option->getMeta('name'); ?>" class="form-control" />
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

        var engine = new Bloodhound({
            name: 'partners',
            datumTokenizer: function(d) {
                return Bloodhound.tokenizers.whitespace(d.name);
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: '/partners/partnersList?query=%QUERY',
            limit: 10
        });

        engine.initialize();

        $('#txtPartnerName').typeahead({
            highlight: true
        },{
            source: engine.ttAdapter(),
            displayKey: function (data){
                return data.name + ' (' + data.ref + ')';
            },
            templates: {
                empty: '<span class="empty">no data</span>',
                suggestion: function (data){
                    return data.name + ' (' + data.ref + ')';
                }
            }
        });

        $('#txtPartnerName').bind('typeahead:selected', function(e, datum) {
            $('#txtPartnerId').val(datum.ref);
        });

    })();
</script>