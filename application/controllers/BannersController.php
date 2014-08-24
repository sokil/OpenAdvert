<?php

class BannersController extends Controller
{
    //banner type list
    private $_bannerTypes;
    
    public function init()
    {
        $this->_bannerTypes = array(
            'creepingLine' => array(
                'name'  => _('Creeping Line'),
                'icon'  => 'glyphicon-text-width'
            ),
            'textPopup' => array(
                'name'  => _('Text Popup'),
                'icon'  => 'glyphicon-comment'
            ),
            'image' => array(
                'name'  => _('Image'),
                'icon'  => 'glyphicon-picture',
            ),
            'video' => array(
                'name'  => _('Video'),
                'icon'  => 'glyphicon-film',
            )
        );
    }
        
    public function actionIndex()
    {        
        // get campaign
        $campaignId = $this->request->getParam('campaign');
        if(!$campaignId) {
            throw new \Exception('Campaign not specified');
        }
        
        $campaign = Yii::app()->mongo->getCollection('campaigns')
            ->getDocument($campaignId);
        
        if(!$campaign) {
            throw new \Exception('Campaign not found');
        }
        
        // get advertiser
        $advertiser = Yii::app()->mongo->getCollection('advertisers')
            ->getDocument($campaign->getAdvertiserId());
        
        // get banners list
        $banners = Yii::app()->mongo->getCollection('banners')
            ->find()
            ->byCampaign($campaign)
            ->notDeleted()
            ->sort(array(
                'name'  => 1
            ));
        
        // render
        if($this->request->isAjaxRequest) {
            $this->partialRender('listPartial', array(
                'banners' => $banners,
                'bannerTypes'   => $this->_bannerTypes,
            ));
        }
        else {
            $this->render('list', array(
                'campaign'      => $campaign,
                'advertiser'    => $advertiser,
                'banners'       => $banners,
                'bannerTypes'   => $this->_bannerTypes,
            ));
        }
    }
    
    public function actionAdd()
    {
        $this->forward('edit');
    }
    
    public function actionEdit()
    {        
        $bannerId = $this->request->getParam('id');
        
        $bannersCollection = Yii::app()->mongo->getCollection('banners');
        
        if($bannerId) {
            
            // get banner
            $banner = $bannersCollection->getDocument($bannerId);
            if(!$banner) {
                throw new \Exception('Banner not found');
            }
            
            // get campaign
            $campaignId = $banner->getCampaignId();
            $campaign = Yii::app()->mongo->getCollection('campaigns')->getDocument($campaignId);
            if(!$campaign) {
                throw new \Exception('Campaign not found');
            }
        }
        else {
            
            // get campaign
            $campaignId = $this->request->getParam('campaign');
            if(!$campaignId) {
                throw new \Exception('Campaign not specified');
            }

            $campaign = Yii::app()->mongo->getCollection('campaigns')->getDocument($campaignId);
            if(!$campaign) {
                throw new \Exception('Campaign not found');
            }

            // get banner type
            $bannerType = $this->request->getParam('type');
            if(!$bannerType) {
                throw new \Exception('Banner type not specified');
            }

            // create banner
            $banner = $bannersCollection
                ->createBanner($bannerType)
                ->setCampaign($campaign);
        }
        
        // get advertiser
        $advertiser = Yii::app()->mongo->getCollection('advertisers')
            ->getDocument($campaign->getAdvertiserId());

        if(!$advertiser) {
            throw new \Exception('Advertiser not found');
        }
        
        /**
         * Delivery options
         */
        $options = array_map(function($optionType) use($bannersCollection) {
            $option = $bannersCollection->getDeliveryOptions()->create($optionType);
            $option->registerStaticFiles();
            return $option;
        }, $bannersCollection->getDeliveryOptions()->getAvailableOptions());
        
        /**
         *  zones
         */
        $zones = Yii::app()->mongo->getCollection('zones')
                ->findAsArray()
                ->sortByName()
                ->byType($banner->getType())
                ->active()
                ->findAll();
        
        foreach (array_keys($zones) as $zoneId) {
            $zones[$zoneId]['checked']= in_array($zoneId, $banner->getZoneIdList());
        }
        
        $zonesDataProvider = new CArrayDataProvider(array_values($zones), array('keyField' => '_id'));

        /**
         * Render
         */
        
        // file uploader
        \Sokil\Uploader\Adapter\Yii\Factory::registerScripts();
        
        // banner editor
        Yii::app()->getClientScript()->registerScriptFile('/js/bannerEditor.js');
        Yii::app()->getClientScript()->registerScriptFile('/js/' . $banner->getType() . 'BannerEditor.js');
        
        // render page
        $this->render('edit', array(
            'advertiser'                => $advertiser,
            'campaign'                  => $campaign,
            'banner'                    => $banner,
            'bannerType'                => $this->_bannerTypes[$banner->getType()],
            'availableDeliveryOptions'  => $options,
            'zonesDataProvider'         => $zonesDataProvider,
        ));
    }
    
    public function actionSave()
    {        
        try {
            
            // init form
            $bannerForm = \MongoAdvertDb\Banners\BannerForm::factory($_POST);
            
            if(!$bannerForm->validate()) {
                $this->response->invalidated = $bannerForm->getErrors();
                $this->response->raiseError();
            }
            else {
                $bannerForm->save();

                // response
                $this->response->bannerId = (string) $bannerForm->getBanner()->getId();
                $this->response->successMessage = _('Saved successfully');
            }
        }
        catch (\Sokil\Mongo\Document\Exception\Validate $e) {
            $this->response->invalidated = $bannerForm->getBanner()->getErrors();
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
            
            $bannersCollection = Yii::app()->mongo->getCollection('banners');
            
            $bannerId = $this->request->getParam('id');
            if(!$bannerId) {
                throw new \Exception('Banner not specified');
            }
            
            $banner = $bannersCollection->getDocument($bannerId);
            if(!$banner) {
                throw new \Exception('Banner not found');
            }
            
            $banner->setActive();
            
            $bannersCollection->saveDocument($banner);
        }
        catch (\Exception $e) {
            $this->response->raiseError($e);
        }
        
        $this->response->sendJson();
    }
    
    public function actionSuspend()
    {
        try {
            
            $bannersCollection = Yii::app()->mongo->getCollection('banners');
            
            $bannerId = $this->request->getParam('id');
            if(!$bannerId) {
                throw new \Exception('Banner not specified');
            }
            
            $banner = $bannersCollection->getDocument($bannerId);
            if(!$banner) {
                throw new \Exception('Banner not found');
            }
            
            $banner->setSuspended();
            
            $bannersCollection->saveDocument($banner);
        }
        catch (\Exception $e) {
            $this->response->raiseError($e);
        }
        
        $this->response->sendJson();
    }
    
    public function actionDelete()
    {
        try {
            
            $bannersCollection = Yii::app()->mongo->getCollection('banners');
            
            $bannerId = $this->request->getParam('id');
            if(!$bannerId) {
                throw new \Exception('Banner not specified');
            }
            
            $banner = $bannersCollection->getDocument($bannerId);
            if(!$banner) {
                throw new \Exception('Banner not found');
            }
            
            $banner->setDeleted();
            
            $bannersCollection->saveDocument($banner);
        }
        catch (\Exception $e) {
            $this->response->raiseError($e);
        }
        
        $this->response->sendJson();
    }
    
    public function actionUploadimage()
    {
        try {
            
            $bannersCollection = Yii::app()->mongo->getCollection('banners');
            
            $bannerId = $this->request->getParam('id');
            if($bannerId) {
                $banner = $bannersCollection->getDocument($bannerId);
                if(!$banner) {
                    throw new \Exception('Banner not found');
                }
            }
            else {
                $campaignId = $this->request->getParam('campaign');
                if(!$campaignId) {
                    throw new \Exception('Campaign must be specified');
                }
                
                $campaign = Yii::app()->mongo->getCollection('campaigns')->getDocument($campaignId);
                if(!$campaign) {
                    throw new \Exception('Campaign not found');
                }
                
                $banner = $bannersCollection
                    ->createBanner('image')
                    ->setCampaign($campaign);
                
                $bannersCollection->saveDocument($banner, false);
            }
            
            // upload local banner
            $banner->upload();
            $bannersCollection->saveDocument($banner, false);
            
            $this->response->id = (string) $banner->getId();
            $this->response->imageUrl = $banner->getImageUrl();
        }
        catch(\Exception $e) {
            $this->response->raiseError($e);
        }
        
        $this->response->sendJson();
    }
    
    public function actionAddmediafileform()
    {
        $bannerId   = $this->request->getParam('banner');
        $campaignId = $this->request->getParam('campaign');
        
        if(Yii::app()->request->isAjaxRequest) {
            echo $this->renderPartial('addmediafileform', array(
                'bannerId'      => $bannerId,
                'campaignId'    => $campaignId,
            ));
        }
        else {
            // file uploader
            \Sokil\Uploader\Adapter\Yii\Factory::registerScripts();
        
            echo $this->render('addmediafileform', array(
                'bannerId'      => $bannerId,
                'campaignId'    => $campaignId,
            ));
        }
    }
    
    /**
     * Define URL manually
     * 
     * @throws \Exception
     */
    public function actionAddmediafile()
    {
        try {
            $bannerCollection = Yii::app()->mongo->getCollection('banners');

            // get banner
            $bannerId = $this->request->getParam('banner');
            if($bannerId) {
                $banner = $bannerCollection->getDocument($bannerId);
                if(!$banner) {
                    throw new \Exception('Banner not found');
                }
            }
            else {
                $campaignId = $this->request->getParam('campaign');
                if(!$campaignId) {
                    throw new \Exception('Campaign not specified');
                }
                
                $campaign = Yii::app()->mongo->getCollection('campaigns')->getDocument($campaignId);
                if(!$campaign) {
                    throw new \Exception('Campaign not found');
                }
                
                $banner = $bannerCollection
                    ->createBanner('video')
                    ->setCampaign($campaign);
            }
            
            // get url
            $url = $this->request->getParam('url');
            if(!$url) {
                throw new \Exception(_('URL not specified'));
            }
            
            // create media file
            $mediaFileId = new \MongoId;
            
            $mediaFile = MongoAdvertDb\Banners\Banner\VideoBanner\MediaFile::create()
                ->setId($mediaFileId)
                ->setUrl($this->request->getParam('url'))
                ->setDelivery($this->request->getParam('delivery'));
            
            // save banner
            $banner->addMediaFile($mediaFile);
            
            $bannerCollection->saveDocument($banner, false);
            
            $this->response->banner_id  = (string) $banner->getId();
            
            $this->response->id         = (string) $mediaFileId;
            $this->response->delivery   = $mediaFile->getDelivery();
            $this->response->url        = $mediaFile->getUrl();
            $this->response->type       = $mediaFile->getType();
            $this->response->size       = $mediaFile->getSize();
            
            $this->response->successMessage = _('Saved successfully');
            
        }
        catch(\Exception $e) {
            $this->response->raiseError($e);
        }
        
        $this->response->sendJson();
    }
    
    public function actionDeletemediafile()
    {
        $mediaFileId = $this->request->getParam('id');
        if(!$mediaFileId) {
            throw new \Exception('Media file not specified');
        }
        
        // get banner with defined media file
        $banner = Yii::app()->mongo->getCollection('banners')->find()
            ->byMediaFileId($mediaFileId)
            ->findOne();
        
        // pull media file from banner
        $banner->deleteMediaFile($mediaFileId);
        
        Yii::app()->mongo->getCollection('banners')->saveDocument($banner);
    }
    
    public function actionUploadmediafile()
    {
        try {
            $bannerCollection = Yii::app()->mongo->getCollection('banners');
            
            $bannerId = $this->request->getParam('id');
            
            // existed banner 
            if($bannerId) {
                $banner = $bannerCollection->getDocument($bannerId);
                if(!$banner) {
                    throw new \Exception('Banner not found');
                }
            }
            
            // new banner
            else {
                $campaignId = $this->request->getParam('campaign');
                if(!$campaignId) {
                    throw new \Exception('Campaign not specified');
                }
                
                $campaign = Yii::app()->mongo->getCollection('campaigns')
                    ->getDocument($campaignId);
                
                $banner = $bannerCollection->createBanner('video')
                    ->setCampaign($campaign);
                
                $bannerCollection->saveDocument($banner, false);
            }
            
            // create media file            
            $mediaFile = MongoAdvertDb\Banners\Banner\VideoBanner\MediaFile::create()
                ->setId(new \MongoId)
                ->setDelivery(\Sokil\Vast\Ad\InLine\Creative\Base\MediaFile::DELIVERY_PROGRESSIVE)
                ->upload();
            
            // save banner
            $banner->addMediaFile($mediaFile);
            
            $bannerCollection->saveDocument($banner, false);
            
            $this->response->bannerId   = (string) $banner->getId();
            
            $this->response->id         = (string) $mediaFile->getId();
            $this->response->delivery   = $mediaFile->getDelivery();
            $this->response->url        = $mediaFile->getUrl();
            $this->response->type       = $mediaFile->getType();
            $this->response->size       = $mediaFile->getSize();
        }
        catch(\Exception $e) {
            $this->response->raiseError($e);
        }
        
        $this->response->sendJson();
    }
    
    public function actionZones() 
    {
        try {
            
            $id = $this->request->getParam('id');
            if(!$id) {
                throw new \Exception('Banner not specified');
            }
            
            $mongo = Yii::app()->mongo;

            // get banner
            $banner = $mongo->getCollection('banners')->getDocument($id);

            // get zones
            $zoneIdList = $this->request->getParam('zones');
            if($zoneIdList) {
                
                $zones = $mongo->getCollection('zones')->getDocuments($zoneIdList);

                // apply zones to banner
                $banner->setZones($zones);
            }
            else {
                $banner->clearZones();
            }

            $mongo->getCollection('banners')->saveDocument($banner);

            $this->response->successMessage = _('Saved successfully');
            
        }
        catch(\Exception $e) {
            $this->response->raiseError($e);
        }
        
        $this->response->sendJson();
    }
    
    public function actionDeliveryOption()
    {
        $bannerId = $this->request->getParam('id');
        
        // existed banner's delivery option
        if($bannerId) {
            $index = (int) $this->request->getParam('index');
            
            $banner = Yii::app()->mongo->getCollection('banners')->getDocument($bannerId);
            if(!$banner) {
                throw new \Exception('Banner not specified');
            }
            
            $option = $banner->getDeliveryOptions()[$index];
        }
        
        // new delivery option
        else {
            $optionType = $this->request->getParam('type');
            if(!$optionType) {
                throw new \Exception('Option type not specified');
            }
            
            $index = $this->request->getParam('index');
            if(!$index) {
                $index = uniqid();
            }
            
            $option = Yii::app()->mongo->getCollection('banners')
                ->getDeliveryOptions()
                ->create($optionType);
        }
        
        echo $option->render($index);
    }
    
    public function actionDeliveryOptions()
    {
        try {
            /**
             * UPDATE
             */
            if($this->request->getIsPostRequest()) {

                // get banner
                $bannerId = $this->request->getParam('id');
                if(!$bannerId) {
                    throw new \Exception('Banner not specified');
                }

                /* @var $banner \MongoAdvertDb\Banners\Banner */
                $banner = Yii::app()->mongo->getCollection('banners')->getDocument($bannerId);
                if(!$banner) {
                    throw new \Exception('Banner not specified');
                }

                // get existed options
                $optionsData = $this->request->getParam('option');
                if(!$optionsData) {
                    $banner->clearDeliveryOptions();
                }
                else {
                    $options = array();
                    foreach($optionsData['value'] as $i => $value) {

                        // value
                        if(!$value) {
                            continue;
                        }

                        // type
                        if(!isset($optionsData['type'][$i])) {
                            continue;
                        }
                        $type = $optionsData['type'][$i];

                        // comparison
                        if(!isset($optionsData['comparison'][$i])) {
                            continue;
                        }
                        $comparison = $optionsData['comparison'][$i];

                        // option
                        $option = Yii::app()->mongo->getCollection('banners')->getDeliveryOptions()->create($type);
                        $option->setValue($value, $comparison);
                        
                        // meta
                        if(isset($optionsData['meta'][$i])) {
                            $option->setMeta($optionsData['meta'][$i]);
                        }

                        $options[] = $option;
                    }

                    $banner->setDeliveryOptions($options);
                }

                Yii::app()->mongo->getCollection('banners')->saveDocument($banner);
            }
            
            $this->response->successMessage = _('Saved successfully');
        }
        catch(Exception $e)
        {
            $this->response->raiseError($e);
        }
        
        $this->response->sendJson();
    }

}
