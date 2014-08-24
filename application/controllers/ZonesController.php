<?php

class ZonesController extends Controller
{

    public function accessRules()
    {
        $rules = parent::accessRules();
        array_unshift($rules, array('allow',
            'actions' => array('code'),
            'users' => array('*'),
        ));
        return $rules;
    }

    public function actionIndex()
    {
        $this->pageTitle = _('Zones');
        
        $filter = new ZoneForm();
        if (isset($_GET['ZoneForm'])) {
            $filter->attributes = $_GET['ZoneForm'];
        }

        $zones = Yii::app()->mongo->getCollection('zones')
            ->findAsArray()
            ->notDeleted();

        $dataProvider = new MongoDataProvider($zones, array(
            'attributes' => array('name', 'type'),
            'filter' => $filter->attributes,
            'pagination' => array('pageSize' => 30)
        ));

        $this->render('index', array(
            'dataProvider' => $dataProvider,
            'filter' => $filter,
        ));
    }

    public function actionNew()
    {
        $mongo = Yii::app()->mongo;
        $model = new ZoneForm();
        $model->type = $this->request->getParam('type');
        if (isset($_POST['ZoneForm'])) {
            $model->attributes = $_POST['ZoneForm'];
            if ($model->validate()) {
                $zones = $mongo->getCollection('zones');
                $zone = $zones->createDocument($_POST['ZoneForm']);
                $zones->saveDocument($zone);
                $this->redirect(array('index'));
            }
        }
        $this->render('edit', array('model' => $model));
    }

    public function actionEdit($id)
    {
        $mongo = Yii::app()->mongo;
        $zones = $mongo->getCollection('zones');
        $zone = $zones->getDocument($id);

        $model = new ZoneForm();

        if (isset($_POST['ZoneForm'])) {
            $model->attributes = $_POST['ZoneForm'];
            if ($model->validate()) {
                $zone->setName($model->name)
                    ->setType($model->type);
                $zones->saveDocument($zone);
                $this->redirect(array('index'));
            }
        }

        $model->attributes = $zone->toArray();
        $this->render('edit', array('model' => $model));
    }

    public function actionTogglestatus()
    {
        try {
            $zoneId = $this->request->getParam('id');
            if (!$zoneId) {
                throw new \Exception('Zone not specified');
            }

            $zonesCollection = Yii::app()->mongo->getCollection('zones');

            $zone = $zonesCollection->getDocument($zoneId);
            if (!$zone) {
                throw new \Exception('Zone not found');
            }

            if ($zone->isActive()) {
                $zone->setSuspended();
            } else if ($zone->isSuspended()) {
                $zone->setActive();
            }
            $zonesCollection->saveDocument($zone);

            $this->response->status = $zone->getStatus();
        } catch (\Exception $e) {
            $this->response->raiseError($e);
        }

        $this->response->sendJson();
    }

    public function actionActivate()
    {
        try {
            $zoneId = $this->request->getParam('id');
            if (!$zoneId) {
                throw new \Exception('Zone not specified');
            }

            $zonesCollection = Yii::app()->mongo->getCollection('zones');

            $zone = $zonesCollection->getDocument($zoneId);
            if (!$zone) {
                throw new \Exception('Zone not found');
            }

            $zone->setActive();

            $zonesCollection->saveDocument($zone);

            $this->response->status = $zone->getStatus();
        } catch (\Exception $e) {
            $this->response->raiseError($e);
        }

        $this->response->sendJson();
    }

    public function actionSuspend()
    {
        try {
            $zoneId = $this->request->getParam('id');
            if (!$zoneId) {
                throw new \Exception('Zone not specified');
            }

            $zonesCollection = Yii::app()->mongo->getCollection('zones');

            $zone = $zonesCollection->getDocument($zoneId);
            if (!$zone) {
                throw new \Exception('Zone not found');
            }

            $zone->setSuspended();

            $zonesCollection->saveDocument($zone);

            $this->response->status = $zone->getStatus();
        } catch (\Exception $e) {
            $this->response->raiseError($e);
        }

        $this->response->sendJson();
    }

    public function actionDelete($id)
    {
        $zones = Yii::app()->mongo->getCollection('zones');
        $zone = $zones->getDocument($id);
        if (!$zone) {
            throw new \Exception('Zone not found');
        }
        $zone->setDeleted();
        $zones->saveDocument($zone);
        $this->redirect(array('index'));
    }

    public function actionCode($id)
    {
        // generate VAST document
        try {
            
            // create or find visitor instance
            $visitor = \Yii::app()->mongo
                ->getCollection('visitors')
                ->getCurrent();
            
            // assign agent id to visitor
            if($this->request->getParam('ref')) {
                $partner = Yii::app()->mongo
                    ->getCollection('partners')
                    ->find()
                    ->byRef($this->request->getParam('ref'))
                    ->findOne();
                
                if($partner) {
                    $visitor->setPartner($partner)->save();
                }
            }
            
            /* @var $zone \MongoAdvertDb\Zones\Zone */
            $zone = Yii::app()->mongo->getCollection('zones')->getDocument($id);
            if (!$zone || !$zone->isActive()) {
                throw new \Exception('Zone not found');
            }

            $banner = Yii::app()->mongo->getCollection('banners')->getRandomBannerOfZone($zone);
            if (!$banner) {
                throw new \Exception('Banner not found');
            }

            // render
            $vastDocument = $zone->getVASTRenderer()
                ->setBanner($banner)
                ->render();
            
        } catch (\Exception $e) {
            $vastDocument = \Sokil\Vast\Document::create();
        }

        // return
        header('Content-type: text/xml');
        echo (string) $vastDocument;
    }

    public function actionDropVastCache()
    {
        
    }

    public function actionBanners($id)
    {
        $mongo = Yii::app()->mongo;

        $zone = $mongo->getCollection('zones')->getDocument($id);

        $bannerSearch = $mongo->getCollection('banners')->findAsArray()
            ->active()
            ->byZone($zone);

        /**
         * Filters
         */
        $advertiserName = $this->request->getParam('advertiser');
        $campaignName = $this->request->getParam('campaign');

        if ($advertiserName || $campaignName) {

            $campaigns = $mongo->getCollection('campaigns')->find();

            // by advertoser
            if ($advertiserName) {
                $advertisers = $mongo->getCollection('advertisers')->find()
                    ->byNameLike($advertiserName)
                    ->findAll();

                $campaigns = $campaigns->byAdvertisers($advertisers);
            }

            // by campaign
            if ($campaignName) {
                $campaigns = $campaigns->byNameLike($campaignName);
            }

            $bannerSearch->byCampaigns($campaigns->findAll());
        }

        // by banner
        $bannerName = $this->request->getParam('banner');
        if ($bannerName) {
            $bannerSearch->byNameLike($bannerName);
        }

        // get banners
        $banners = $bannerSearch->findAll();

        // get campaign ids
        $campaignIdList = array_unique(array_map(function($banner) {
                    return (string) $banner['campaign'];
                }, $banners));

        // get campaigns relative to banners
        $campaigns = $mongo->getCollection('campaigns')->findAsArray()
            ->byIdList($campaignIdList)
            ->findAll();

        // get advertiser ids relative to campaigns
        $advertiserIdList = array_unique(array_map(function($campaign) {
            return (string) $campaign['advertiser'];
        }, $campaigns));

        // get advertisers
        $advertisers = $mongo->getCollection('advertisers')->findAsArray()
            ->byIdList($advertiserIdList)
            ->findAll();

        // pass advertisers and campaigns to related banners
        foreach ($campaigns as $id => $campaign) {
            $campaigns[$id]['advertiser'] = $advertisers[(string) $campaign['advertiser']];
        }

        foreach ($banners as $id => $banner) {
            $banners[$id]['campaign'] = $campaigns[(string) $banner['campaign']];
        }

        // render
        $this->render('banners', array(
            'zone' => $zone,
            'dataProvider' => new CArrayDataProvider(array_values($banners), array('keyField' => '_id')),
        ));
    }

    public function actionRemoveBanner($id)
    {
        try {
            $mongo = Yii::app()->mongo;

            $zoneId = $this->request->getParam('zone');
            $zone = $mongo->getCollection('zones')->getDocument($zoneId);
            if (!$zone) {
                throw new \Exception('Zone not specified');
            }

            $banner = $mongo->getCollection('banners')->getDocument($id);
            if (!$banner) {
                throw new \Exception('Banner not specified');
            }

            $banner->detachZone($zone)->save();

            $this->response->successMessage = _('Operation successful');
        } catch (\Exception $e) {
            $this->response->raiseError($e);
        }

        $this->response->sendJson();
    }
    
    public function actionAddBanner($id)
    {
        try {
            $mongo = Yii::app()->mongo;

            // get zone
            $zoneId = $this->request->getParam('zone');
            $zone = $mongo->getCollection('zones')->getDocument($zoneId);
            if (!$zone) {
                throw new \Exception('Zone not specified');
            }

            // get banner
            $banner = $mongo->getCollection('banners')->getDocument($id);
            if (!$banner) {
                throw new \Exception('Banner not specified');
            }

            $banner->attachZone($zone)->save();

            $this->response->successMessage = _('Operation successful');
        } catch (\Exception $e) {
            $this->response->raiseError($e);
        }

        $this->response->sendJson();
    }

}
