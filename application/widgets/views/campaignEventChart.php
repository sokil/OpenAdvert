<form method="POST" id="frmCampaignEventChart" class="form-inline top-space bottom-space" role="form">
    <input type="hidden" name="campaign" value="<?php echo $this->campaign; ?>" />
    <div class="form-group">
        <div id="periods" class="btn-group" data-toggle="buttons">
            <label class="btn btn-info <?php if($period === 'hour') echo 'active'; ?>">
                <input type="radio" name="period" id="hour" value="hour" <?php if($period === 'hour') echo 'checked'; ?>> <?php echo _('Hours'); ?>
            </label>
            <label class="btn btn-info <?php if($period === 'day') echo 'active'; ?>">
                <input type="radio" name="period" id="day" value="day" <?php if($period === 'day') echo 'checked'; ?>> <?php echo _('Days'); ?>
            </label>
            <label class="btn btn-info <?php if($period === 'week') echo 'active'; ?>">
                <input type="radio" name="period" id="week" value="week" <?php if($period === 'week') echo 'checked'; ?>> <?php echo _('Weeks'); ?>
            </label>
            <label class="btn btn-info <?php if($period === 'month') echo 'active'; ?>">
                <input type="radio" name="period" id="month" value="month" <?php if($period === 'month') echo 'checked'; ?>> <?php echo _('Months'); ?>
            </label>
        </div>
    </div>
    <div class="form-group">
        <div id="actions" class="btn-group" data-toggle="buttons">
            <label class="btn btn-info <?php if($this->hasImpressions()) echo 'active'; ?>">
                <input type="checkbox" name="events[]" value="withImpressions" <?php if($this->hasImpressions()) echo 'checked'; ?>> <?=_('Impressions')?>
            </label>
            <label class="btn btn-info <?php if($this->hasUniqueImpressions()) echo 'active'; ?>">
                <input type="checkbox" name="events[]" value="withUniqueImpressions" <?php if($this->hasUniqueImpressions()) echo 'checked'; ?>> <?=_('Unique impressions')?>
            </label>
            <label class="btn btn-info" <?php if($this->hasClicks()) echo 'active'; ?>>
                <input type="checkbox" name="events[]" value="withClicks" <?php if($this->hasClicks()) echo 'checked'; ?>> <?=_('Clicks')?>
            </label>
            <label class="btn btn-info <?php if($this->hasUniqueClicks()) echo 'active'; ?>">
                <input type="checkbox" name="events[]" value="withUniqueClicks" <?php if($this->hasUniqueClicks()) echo 'checked'; ?>> <?=_('Unique clicks')?>
            </label>
        </div>
    </div>
    <div class="form-group">
        <img src="/images/prel.gif" id="preloader" style="display: none;" />
    </div>
</form>

<div id="chartContainer"></div>

<script type="text/javascript">
    (function() {
        // chart
        var options = {
            title: '<?php echo _('Campaign Events'); ?>',
            hAxis: {
                titleTextStyle: {color: '#333'},
                textStyle: {fontSize: 10},
                pointSize: 15,
                gridlines: {
                    color: '#cdcdcd',
                    count: 5
                }
            },
            vAxis: {
                minValue: 0, 
                gridlines: {
                    color: '#cdcdcd',
                    count: 5
                }
            },
            pointSize: 5,
            height: 500,
            width: 800,
            chartArea:{left: 50, width:900,height:"65%"},
            legend: {
                textStyle: {fontSize: 12},
                position: 'top'
            }
        };

        var chart = new google.visualization.AreaChart(document.getElementById('chartContainer'));

        // form
        var changeTimer;
        
        var changeHandler = function() {
            
            $('#preloader').show();
            
            // if action in progress - stop it and start new action
            if(changeTimer) {
                clearTimeout(changeTimer);
            }
            
            // start new timer
            changeTimer = setTimeout(function() {
                var formQuery = $('#frmCampaignEventChart').serialize(),
                    query = formQuery + '&' + statApp.calendar.getValue();

                // update hash
                location.hash = Url(location.hash).merge(formQuery).toString();

                // load new data
                $.post('/stat/updateCampaignEventChart', query, function(response) {
                    var data = new google.visualization.arrayToDataTable(response);
                    chart.draw(data, options);
                    
                    $('#preloader').hide();
                });
                
                // clear timer
                changeTimer = null;
            }, 500);
            
        };
        
        $('#frmCampaignEventChart input')
            .change(changeHandler)
            .change();
    })();
</script>