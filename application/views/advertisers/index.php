<?php
$this->breadcrumbs = array(
    _('Advertisers')
);

$this->pageTitle = _('Advertisers');
?>
<div class="row-fluid bottom-space">
    <div class="col-md-9 btn-toolbar" role="toolbar">
        <a href="/advertisers/new" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-plus"></i> <?php echo _('Add Advertiser'); ?></a>
    </div>
    <div class="col-md-3">
        <!-- search form -->
    </div>
    <div class="clearfix"></div>
</div>
<table class="table table-hover table-striped table-bordered table-condensed" id="tblAdvertisers">
    <col/><col/><col/><col/><col width="250px"/>
    <thead>
    <tr>
        <th><?php echo _('Name'); ?></th>
        <th><?php echo _('Address'); ?></th>
        <th><?php echo _('Phone'); ?></th>
        <th><?php echo _('E-mail'); ?></th>
        <th>&nbsp;</th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($advertisers as $advertiser) {
        ?>
        <tr>
            <td>
                <a href="/campaigns/?advertiser=<?php echo $advertiser->getId(); ?>">
                    <?php echo $advertiser->getName(); ?>
                </a>
            </td>
            <td><?php echo $advertiser->getAddress(); ?></td>
            <td><?php echo $advertiser->getPhone(); ?></td>
            <td>
                <?php if($advertiser->getEmail()): ?>
                <a href="mailto:<?php echo $advertiser->getEmail(); ?>"><?php echo $advertiser->getEmail(); ?></a>
                <?php endif; ?>
            </td>
            <td>
                <a href="/advertisers/edit/?id=<?php echo $advertiser->getId(); ?>"><span class="glyphicon glyphicon-pencil"></span></a>
                <a href="/advertisers/delete/?id=<?php echo $advertiser->getId(); ?>" class="delete"><span class="glyphicon glyphicon-trash"></span></a>
                <a href="/campaigns/?advertiser=<?php echo $advertiser->getId(); ?>" class="btn btn-primary btn-xs">
                    <span class="glyphicon glyphicon-folder-open"></span>&nbsp;
                    <?php echo _('Campaigns'); ?>
                </a>
                <a href="/users/?advertiser=<?php echo $advertiser->getId(); ?>" class="btn btn-primary btn-xs">
                    <span class="glyphicon glyphicon-user"></span> 
                    <?php echo _('Users'); ?>
                </a>
            </td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>

<script type="text/javascript">
    $('#tblAdvertisers').tableButtons();
</script>
