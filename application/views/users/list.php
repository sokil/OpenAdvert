<?php
$this->breadcrumbs = array(
    _('Users')
);

$this->pageTitle = _('Users');
?>

<?php if (Yii::app()->user->checkAccess('manageUser.editManager')): ?>
    <ul class="nav nav-tabs" id="tabs">
    <li<?php echo $filter['role'] == 'manager' ? ' class="active"' : null; ?>><a href="/users/?filter[role]=manager"><?php echo _('Managers'); ?></a></li>
    <li<?php echo $filter['role'] == 'advertiser' ? ' class="active"' : null; ?>><a href="/users/?filter[role]=advertiser"><?php echo _('Advertisers'); ?></a></li>
    <li<?php echo $filter['role'] == 'partner' ? ' class="active"' : null; ?>><a href="/users/?filter[role]=partner"><?php echo _('Partners'); ?></a></li>
    </ul>
<?php endif; ?>

<script type="text/javascript">
    $("#tabs a").click(function (e) {
        e.preventDefault();
        var $a = $(this);
        $("#tabs li").removeClass("active");
        $a.closest("li").addClass("active");

         $.get($a.attr("href"), function(response) {
            $("#cnt").html(response);
        }, "html");
    });
</script>

<div id="cnt">
    <?php $this->renderPartial('listPartial', array(
        'users'         => $users,
        'groupList'     => isset($groupList) ? $groupList : null,
        'currentGroup'  => isset($currentGroup) ? $currentGroup : null,
        'filter'        => isset($filter) ? $filter : null,
    )); ?>
</div>
