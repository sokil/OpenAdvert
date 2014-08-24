<div id="partnereventsreport">
    <form method="POST" action="/stat/partnerEvents/<?php echo $partner->getId(); ?>" id="frmPartnerReport" class="form-inline top-space bottom-space" role="form">
        <div class="form-group">
            <div id="periods" class="btn-group" data-toggle="buttons">
                <label class="btn btn-info <?php if ($report->getPeriod() === 'hour') echo 'active'; ?>">
                    <input type="radio" name="period" id="hour" value="hour" <?php if ($report->getPeriod() === 'hour') echo 'checked'; ?>> <?php echo _('Hours'); ?>
                </label>
                <label class="btn btn-info <?php if ($report->getPeriod() === 'day') echo 'active'; ?>">
                    <input type="radio" name="period" id="day" value="day" <?php if ($report->getPeriod() === 'day') echo 'checked'; ?>> <?php echo _('Days'); ?>
                </label>
                <label class="btn btn-info <?php if ($report->getPeriod() === 'week') echo 'active'; ?>">
                    <input type="radio" name="period" id="week" value="week" <?php if ($report->getPeriod() === 'week') echo 'checked'; ?>> <?php echo _('Weeks'); ?>
                </label>
                <label class="btn btn-info <?php if ($report->getPeriod() === 'month') echo 'active'; ?>">
                    <input type="radio" name="period" id="month" value="month" <?php if ($report->getPeriod() === 'month') echo 'checked'; ?>> <?php echo _('Months'); ?>
                </label>
            </div>
        </div>
    </form>
    <script type="text/javascript">
        // form
        $('#frmPartnerReport input').change(function() {
            var query = $('#frmPartnerReport').serialize() + '&' + statApp.calendar.getValue();

            // update hash
            location.hash = Url(location.hash).merge(query).toString();

            // get
            $.post($('#frmPartnerReport').attr('action'), query, function(response) {
                $('#partnereventsreport').replaceWith(response);
            });
        });
    </script>

    <?php
    $this->widget('GridView', array(
        'dataProvider' => $report->getDataProvider(),
        'emptyText'    => _('No data'),
        'columns'      => array(
            array(
                'name'   => 'date',
                'header' => _('Date'),
                'htmlOptions'   => ['width' => '190px']
            ),
            array(
                'name'   => 'count',
                'header' => _('Impressions'),
            )
        )
    ));
    ?>
</div>