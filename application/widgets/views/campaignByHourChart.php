<div class="row-fluid">
    <div class="col-md-7">
        <?php $this->widget('GridView', array(
            'dataProvider'  => $this->getReport()->getDataProvider(),
            'emptyText'     => _('No data'),
            'columns'       => array(
                array(
                    'name'              => 'hour',
                    'header'            => _('Hour'),
                ),
                array(
                    'name'              => 'impressions',
                    'header'            => _('Impressions'),
                ),
                array(
                    'name'              => 'uniqueImpressions',
                    'header'            => _('Unique impressions'),
                    'value'             => '$data[\'uniqueImpressions\']',
                ),
                array(
                    'name'              => 'clicks',
                    'header'            => _('Clicks'),
                    'headerHtmlOptions' => ['style' => 'text-align:center;'],
                ),
                array(
                    'name'              => 'uniqueClicks',
                    'header'            => _('Unique clicks'),
                    'value'             => '$data[\'uniqueClicks\']',
                ),
                array(
                    'name'              => 'ctr',
                    'header'            => _('CTR'),
                    'type'              => 'percent',
                    'headerHtmlOptions' => ['style' => 'text-align:center;'],
                ),
            )
        )); ?>
    </div>
    <div class="col-md-5">
        <script type="text/javascript">
            (function() {
                var options = {
                    hAxis:{
                        textPosition:'in'
                    },
                    legend: {
                        position: 'top'
                    },
                    bar: {
                        groupWidth: '95%'
                    },
                    height: 900,
                    width: '100%',
                    fontSize: 12,
                    tooltip: {textStyle:{fontSize:14}}
                };

                var chart = new google.visualization.BarChart(document.getElementById('chart'));

                var data = google.visualization.arrayToDataTable(<?php echo json_encode($this->getChartData()); ?>);
                chart.draw(data, options);
            })();
        </script>

        <div id="chart"></div>
    </div>
</div>
