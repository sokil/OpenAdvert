<?php

class UnlimitBannersCommand extends CConsoleCommand
{
    public function actionIndex()
    {
        try {
            // get list of limitted banners
            /* @var $banners \MongoAdvertDb\Banners */
            $banners = Yii::app()->mongo
                ->getCollection('banners')
                ->find()
                ->active()
                ->limited()
                ->byActiveCampaign();

            if(!$banners) {
                return;
            }

            // check if limit still required and unlimit
            /* @var $banner \MongoAdvertDb\Banners\Banner */
            $tick = 0;
            foreach($banners as $banner) {
                if($banner->isLimitRequired()) {
                    continue;
                }
                
                $banner->removeLimit()->save();
                
                // garbage collector
                if(0 === $tick % 20) {
                    gc_collect_cycles();
                }
            }
            
        } catch (Exception $e) {
            Yii::app()->log($e, CLogger::LEVEL_ERROR);
        }

    }

}
