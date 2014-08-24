<?php

class ResetStatCommand extends CConsoleCommand
{

    public function actionIndex()
    {
        $campaignsCollection = Yii::app()->mongo
            ->getCollection('campaigns');
        
        $bannersCollection = Yii::app()->mongo
            ->getCollection('banners');
        
        /**
         * Campaings
         */

        // remove aggregeted stat
        $campaignsCollection->updateMultiple(
            $campaignsCollection->expression(),
            $campaignsCollection->operator()
                ->unsetField('impressions')
                ->unsetField('clicks')
                ->unsetField('deactivated')
        );
        
        /**
         * Banners
         */
        
        // remove deactivation markers of banners
        $bannersCollection->updateMultiple(
            $bannersCollection->expression()->whereExists('deactivated'),
            $bannersCollection->operator()->unsetField('deactivated')
        );
        
        /**
         * Tracnking
         */
        Yii::app()->mongo->getCollection('track.impression')->delete();
        Yii::app()->mongo->getCollection('track.click')->delete();
        Yii::app()->mongo->getCollection('track.event')->delete();
    }

}
