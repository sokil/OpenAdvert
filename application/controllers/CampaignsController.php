<?php

class CampaignsController extends Controller
{
    public function actionIndex()
    {
        // get advertiser
        /* @var $advertiser \MongoAdvertDb\Advertisers\Advertiser */

        // advertiser id
        $advertiserId = Yii::app()->user->getProfile()->advertiser;
        if(!$advertiserId) {
            $advertiserId = $this->request->getParam('advertiser');
            if(!$advertiserId) {
                throw new \Exception('Advertiser not specified');
            }
        }
        
        // advertiser
        $advertiser = Yii::app()->mongo->getCollection('advertisers')->getDocument($advertiserId);
        if(!$advertiser) {
            throw new \Exception('Advertiser not found');
        }
        
        // check if advertiser active
        if($advertiser->isDeleted()) {
            throw new \CHttpException(404, 'Advertioser not found');
        }
        
        // get campaigns
        $campaigns = Yii::app()->mongo->getCollection('campaigns')
            ->find()
            ->byAdvertiser($advertiser)
            ->notDeleted()
            ->sort(array(
                'name'  => 1
            ));
        
        // filter
        $filter = $this->request->getParam('filter');
        if($filter) {
            if(isset($filter['name'])) {
                $campaigns->byNameLike($filter['name']);
            }
        }

        // render
        if($this->request->isAjaxRequest) {
            $this->renderPartial('listPartial', array(
                'campaigns'     => $campaigns,
            ));
        }
        else {
            $this->render('list', array(
                'advertiser'    => $advertiser,
                'campaigns'     => $campaigns
            ));
        }
        
    }
    
    public function actionAdd()
    {
        $this->forward('edit');
    }
    
    public function actionEdit()
    {
        $campaignsCollection = Yii::app()->mongo->getCollection('campaigns');
        
        /**
         * Existed campaign
         */
        $campaignId = $this->request->getParam('id');

        if( $campaignId) {
            // get campaign
            $campaign = $campaignsCollection->getDocument($campaignId);
            if(!$campaign) {
                throw new \Exception('Campaign not found');
            }
            
            // check permissions
            if(!$campaign->canBeManagedBy(Yii::app()->user)) {
                Yii::app()->user->loginRequired();
            }
            
            $advertiser = Yii::app()->mongo
                ->getCollection('advertisers')
                ->getDocument($campaign->getAdvertiserId());
        }
        
        /**
         * New campaign
         */
        else {
            
            // check permissions
            if(!Yii::app()->user->checkAccess('manageCampaign.create')) {
                Yii::app()->user->loginRequired();
            }
            
            // get advertiser id
            $advertiserId = Yii::app()->user->getProfile()->advertiser;
            if(!$advertiserId) {
                $advertiserId = $this->request->getParam('advertiser');
                if(!$advertiserId) {
                    throw new \Exception('Advertiser not specified');
                }
            }
            
            // get advertiser
            $advertiser = Yii::app()->mongo
                ->getCollection('advertisers')
                ->getDocument($advertiserId);
            
            $campaign = $campaignsCollection->createDocument()
                ->setAdvertiser($advertiser);
        }
        
        // render
        Yii::app()->getClientScript()->registerScriptFile('/js/form.js');
        Yii::app()->getClientScript()->registerPackage('pickmeup');
        
        $this->render('edit', array(
            'advertiser'    => $advertiser,
            'campaign'      => $campaign,
        ));
    }
    
    public function actionSave()
    {
        try {
            
            /* @var $campaignsCollection \MongoAdvertDb\Campaigns */
            /* @var $campaign \MongoAdvertDb\Campaigns\Campaign */
            
            $campaignsCollection = Yii::app()->mongo->getCollection('campaigns');
            
            /**
             * Existed campaign
             */
            $campaignId = $this->request->getParam('id');
            if($campaignId) {
                $campaign = $campaignsCollection->getDocument($campaignId);
                if(!$campaign) {
                    throw new \Exception('Campaign not found');
                }
                
                // check permissions
                if(!$campaign->canBeManagedBy(Yii::app()->user)) {
                    Yii::app()->user->loginRequired();
                }
                
                // status
                if($campaign->isModerationRequired()) {
                    if(Yii::app()->user->checkAccess('manageCampaign.editWithoutModeration')) {
                        $campaign->setActive();
                    }
                }
            }

            /**
             * New campaign
             */
            else {
                
                // check permissions
                if(!Yii::app()->user->checkAccess('manageCampaign.create')) {
                    Yii::app()->user->loginRequired();
                }
            
                // get advertiser id
                $advertiserId = Yii::app()->user->getProfile()->advertiser;
                if(!$advertiserId) {
                    $advertiserId = $this->request->getParam('advertiser');
                    if(!$advertiserId) {
                        throw new \Exception('Advertiser not specified');
                    }
                }

                // advertiser
                $advertiser = Yii::app()->mongo
                    ->getCollection('advertisers')
                    ->getDocument($advertiserId);

                $campaign = $campaignsCollection
                    ->createDocument()
                    ->setAdvertiser($advertiser);
                
                if (!Yii::app()->user->checkAccess('manageCampaign.createWithoutModeration')) {
                    $campaign->setModerationRequired();
                }
            }
            
            /**
             * Update
             */
            $campaignData = $this->request->getParam('campaign');
            
            if(!empty($campaignData['dateFrom'])) {
                $campaign->setDateFrom($campaignData['dateFrom']);
                unset($campaignData['dateFrom']);
            }
            
            if(!empty($campaignData['dateTo'])) {
                $campaign->setDateTo($campaignData['dateTo']);
                unset($campaignData['dateTo']);
            }
            
            if(!empty($campaignData['impressionLimit'])) {
                $campaign->setImpressionLimit($campaignData['impressionLimit']);
                unset($campaignData['impressionLimit']);
            }
            
            if(!empty($campaignData['clickLimit'])) {
                $campaign->setClickLimit($campaignData['clickLimit']);
                unset($campaignData['clickLimit']);
            }
            
            if(!Yii::app()->user->checkAccess('manageCampaign.editWithoutModeration')) {
                unset($campaignData['pricing']);
            }
            
            $campaign->fromArray(array_intersect_key($campaignData, array_flip([
                'name'
            ])));
            
            // save
            $campaign->save();
            
            $this->response->campaignId = (string) $campaign->getId();
            
            $this->response->successMessage = _('Saved successfully');
            
        }
        catch (\Sokil\Mongo\Document\Exception\Validate $e) {
            $this->response->invalidated = $e->getDocument()->getErrors();
            $this->response->raiseError();
        }
        catch (\Exception $e) {
            $this->response->raiseError($e);
        }

        $this->response->sendJson();
    }
    
    public function actionActivate()
    {
        try {
            
            $campaignsCollection = Yii::app()->mongo->getCollection('campaigns');
            
            $campaignId = $this->request->getParam('id');
            if(!$campaignId) {
                throw new \Exception('Campaign not specified');
            }
            
            $campaign = $campaignsCollection->getDocument($campaignId);
            if(!$campaign) {
                throw new \Exception('Campaign not found');
            }
            
            $campaign->setActive();
            
            $campaignsCollection->saveDocument($campaign);
        }
        catch (\Exception $e) {
            $this->response->raiseError($e);
        }
        
        $this->response->sendJson();
    }
    
    public function actionSuspend()
    {
        try {
            
            $campaignsCollection = Yii::app()->mongo->getCollection('campaigns');
            
            $campaignId = $this->request->getParam('id');
            if(!$campaignId) {
                throw new \Exception('Campaign not specified');
            }
            
            $campaign = $campaignsCollection->getDocument($campaignId);
            if(!$campaign) {
                throw new \Exception('Campaign not found');
            }
            
            $campaign->setSuspended();
            
            $campaignsCollection->saveDocument($campaign);
        }
        catch (\Exception $e) {
            $this->response->raiseError($e);
        }
        
        $this->response->sendJson();
    }
    
    public function actionDelete()
    {
        try {
            
            $campaignsCollection = Yii::app()->mongo->getCollection('campaigns');
            
            $campaignId = $this->request->getParam('id');
            if(!$campaignId) {
                throw new \Exception('Campaign not specified');
            }
            
            $campaign = $campaignsCollection->getDocument($campaignId);
            if(!$campaign) {
                throw new \Exception('Campaign not found');
            }
            
            $campaign->setDeleted();
            
            $campaignsCollection->saveDocument($campaign);
        }
        catch (\Exception $e) {
            $this->response->raiseError($e);
        }
        
        $this->response->sendJson();
    }
}