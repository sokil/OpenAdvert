<?php
$this->breadcrumbs = array(
    _('Statistics') => array('/stat'),
    $partner->getName(),
);

$this->pageTitle = $partner->getName();

?>

<div class="row-fluid">
    <div class="col-md-2" id="menu">
        <div class="panel panel-default">
            <div class="panel-heading"><?php echo _('Statistics'); ?></div>
            <div class="list-group">
                <a href="/stat/partnerEvents/<?php echo $partner->getId(); ?>" class="list-group-item"><?php echo _('Events'); ?></a>
            </div>
        </div>
    </div>
    <div class="col-md-10">
            <span id="dateRange"><?php echo date('Y-m-d') . ' - ' . date('Y-m-d'); ?></span>
            <span class="caret"></span>
        </form>
        <ul class="nav nav-tabs top-space" id="subMenu"></ul>
        <div id="container"></div>
    </div>
</div>

<script type="text/javascript">
    statApp.init({
        calendar: {
            locale: {
                days: ["<?php echo _('Sunday'); ?>","<?php echo _('Monday'); ?>","<?php echo _('Tuesday'); ?>","<?php echo _('Wednesday'); ?>","<?php echo _('Thursday'); ?>","<?php echo _('Friday'); ?>","<?php echo _('Saturday'); ?>","<?php echo _('Sunday'); ?>"],
                daysShort:["<?php echo _('Sun'); ?>","<?php echo _('Mon'); ?>","<?php echo _('Tue'); ?>","<?php echo _('Wed'); ?>","<?php echo _('Thu'); ?>","<?php echo _('Fri'); ?>","<?php echo _('Sat'); ?>","<?php echo _('Sun'); ?>"],
                daysMin:["<?php echo _('Su'); ?>","<?php echo _('Mo'); ?>","<?php echo _('Tu'); ?>","<?php echo _('We'); ?>","<?php echo _('Th'); ?>","<?php echo _('Fr'); ?>","<?php echo _('Sa'); ?>","<?php echo _('Su'); ?>"],
                months:["<?php echo _('January'); ?>","<?php echo _('February'); ?>","<?php echo _('March'); ?>","<?php echo _('April'); ?>","<?php echo _('May'); ?>","<?php echo _('June'); ?>","<?php echo _('July'); ?>","<?php echo _('August'); ?>","<?php echo _('September'); ?>","<?php echo _('October'); ?>","<?php echo _('November'); ?>","<?php echo _('December'); ?>"],
                monthsShort:["<?php echo _('Jan'); ?>","<?php echo _('Feb'); ?>","<?php echo _('Mar'); ?>","<?php echo _('Apr'); ?>","<?php echo _('May'); ?>","<?php echo _('Jun'); ?>","<?php echo _('Jul'); ?>","<?php echo _('Aug'); ?>","<?php echo _('Sep'); ?>","<?php echo _('Oct'); ?>","<?php echo _('Nov'); ?>","<?php echo _('Dec'); ?>"]
            }
        }
    });
</script>