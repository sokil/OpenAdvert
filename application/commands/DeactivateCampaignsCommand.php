<?php

class DeactivateCampaignsCommand extends CConsoleCommand
{

    public function actionIndex($campaignsPerQuery = 50)
    {
        $campaignsCollection = Yii::app()->mongo->getCollection('campaigns');

        do {
            $campaigns = $campaignsCollection
                ->find()
                ->notDeactivated()
                ->endedByNow()
                ->limit($campaignsPerQuery)
                ->findAll();

            $campaignsCollection->deactivateByCampaigns($campaigns);
            
        } while (count($campaigns));
    }

}
