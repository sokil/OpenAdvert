<?php
$this->breadcrumbs = array(
    _('Statistics'),
);
?>

<ul class="nav nav-tabs bottom-space" id="tab">
  <li><a href="/stat/advertisers"><?php echo _('Advertisers'); ?></a></li>
  <li><a href="/stat/partners"><?php echo _('Partners'); ?></a></li>
</ul>
<div id="cnt"></div>
<script type="text/javascript">
    $('#tab a').click(function(e) {
        e.preventDefault();
        var $a = $(this);
        
        $('#tab LI').removeClass('active');
        $a.closest('LI').addClass('active');
        
        $.get($a.attr('href'), function(response) {
            $('#cnt').html(response);
        }, 'html');
    });
    
    $('#tab LI:first A').click();
</script>