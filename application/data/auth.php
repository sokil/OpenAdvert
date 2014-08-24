<?php

return array(
    /**
     * Roles
     */
    'guest' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Guest',
        'bizRule' => null,
        'data' => null
    ),
    'partner' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Partner',
        'bizRule' => NULL,
        'data' => NULL,
        'children' => array(
            'guest',
            'managePartnerUser',
            'managePartnerStat',
        ),
    ),
    'advertiser' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Advertiser',
        'bizRule' => NULL,
        'data' => NULL,
        'children' => array(
            'guest',
            'manageAdvertStat',
            'manageCampaign.create',
            'manageAdvertiserUser'
        ),
    ),
    'manager' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Manager',
        'bizRule' => NULL,
        'data' => NULL,
        'children' => array(
            'guest',
            'manageZone',
            'manageAdminUser',
            'manageAdvertiser',
            'manageStat',
            'manageCampaign',
            'manageLog',
        ),
    ),
    
    /**
     * Tasks
     */
    'manageZone' => array(
        'type' => CAuthItem::TYPE_TASK,
        'description' => 'Manage Zones',
        'bizRule' => NULL,
        'data' => NULL,
    ),

    'manageAdminUser' => array(
        'type' => CAuthItem::TYPE_TASK,
        'description' => 'Manage users',
        'bizRule' => NULL,
        'data' => NULL,
        'children'  => array(
            'manageUser.view',
            'manageUser.editManager',
            'manageUser.editPartner',
            'manageUser.editAdvertiser'
        ),
    ),

    'managePartnerUser' => array(
        'type' => CAuthItem::TYPE_TASK,
        'description' => 'Manage users',
        'bizRule' => NULL,
        'data' => NULL,
        'children'  => array(
            'manageUser.editPartner',
        ),
    ),

    'manageAdvertiserUser' => array(
        'type' => CAuthItem::TYPE_TASK,
        'description' => 'Manage users',
        'bizRule' => NULL,
        'data' => NULL,
        'children'  => array(
            'manageUser.editAdvertiser'
        ),
    ),

    'manageAdvertiser' => array(
        'type' => CAuthItem::TYPE_TASK,
        'description' => 'Manage advertisers',
        'bizRule' => NULL,
        'data' => NULL,
    ),
    'manageStat' => array(
        'type' => CAuthItem::TYPE_TASK,
        'description' => 'Manage statistics',
        'bizRule' => NULL,
        'data' => NULL,
        'children' => array(
            'manageAdvertStat',
            'managePartnerStat',
        ),
    ),
    'manageAdvertStat' => array(
        'type' => CAuthItem::TYPE_TASK,
        'description' => 'Manage statistics',
        'bizRule' => NULL,
        'data' => NULL,
    ),
    'managePartnerStat' => array(
        'type' => CAuthItem::TYPE_TASK,
        'description' => 'Manage statistics',
        'bizRule' => NULL,
        'data' => NULL,
    ),
    'manageCampaign' => array(
        'type' => CAuthItem::TYPE_TASK,
        'description' => 'Manage campaigns',
        'bizRule' => NULL,
        'data' => NULL,
        'children' => array(
            'manageCampaign.create',
            'manageCampaign.createWithoutModeration',
            'manageCampaign.edit',
            'manageCampaign.editWithoutModeration',
        ),
    ),
    'manageLog' => array(
        'type' => CAuthItem::TYPE_TASK,
        'description' => 'Manage log',
        'bizRule' => NULL,
        'data' => NULL,
    ),
    
    /**
     * Operations
     */

    'manageUser.view' => array(
        'type' => CAuthItem::TYPE_OPERATION,
        'description' => 'Edit user',
        'bizRule' => NULL,
        'data' => NULL,
    ),
    
    'manageUser.editManager' => array(
        'type' => CAuthItem::TYPE_OPERATION,
        'description' => 'Edit user',
        'bizRule' => NULL,
        'data' => NULL,
        'children' => array(
            'manageUser'
        ),
    ),

    'manageUser.editPartner'  => array(
        'type' => CAuthItem::TYPE_OPERATION,
        'description' => 'Manage Partner',
        'bizRule' => NULL,
        'data' => NULL,
        'children' => array(
            'manageUser'
        ),
    ),

    'manageUser.editAdvertiser'  => array(
        'type' => CAuthItem::TYPE_OPERATION,
        'description' => 'Manage Advertiser',
        'bizRule' => NULL,
        'data' => NULL,
        'children' => array(
            'manageUser'
        ),
    ),

    'manageUser'  => array(
        'type' => CAuthItem::TYPE_OPERATION,
        'description' => 'Manage Advertiser',
        'bizRule' => NULL,
        'data' => NULL
    ),

    'manageStat.viewAllAdvertisers' => array(
        'type' => CAuthItem::TYPE_OPERATION,
        'description' => 'Edit user',
        'bizRule' => NULL,
        'data' => NULL,
    ),
    
    'manageCampaign.create' => array(
        'type' => CAuthItem::TYPE_OPERATION,
        'description' => 'Edit user',
        'bizRule' => NULL,
        'data' => NULL,
    ),
    
    'manageCampaign.createWithoutModeration' => array(
        'type' => CAuthItem::TYPE_OPERATION,
        'description' => 'Edit user',
        'bizRule' => NULL,
        'data' => NULL,
        'children'  => array(
            'manageCampaign.create',
        )
    ),

    'manageCampaign.edit' => array(
        'type' => CAuthItem::TYPE_OPERATION,
        'description' => 'Edit user',
        'bizRule' => NULL,
        'data' => NULL,
        'children' => [
            'manageCampaign.create'
        ]
    ),
    
    'manageCampaign.editWithoutModeration' => array(
        'type' => CAuthItem::TYPE_OPERATION,
        'description' => 'Edit user',
        'bizRule' => NULL,
        'data' => NULL,
        'children' => [
            'manageCampaign.createWithoutModeration'
        ]
    )

);
