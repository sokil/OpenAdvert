<?php

class StatController extends Controller
{

    public function actionIndex()
    {
        if (Yii::app()->user->checkAccess('manageStat')) {            
            $this->render('index');
            return;
        }

        if(Yii::app()->user->checkAccess('manageAdvertStat')) {
            $this->forward('advertiser');
        }
        
        if(Yii::app()->user->checkAccess('managePartnerStat')) {
            $this->forward('partner');
        }
    }
    
    public function actionPartners()
    {
        $partners = Yii::app()->mongo->getCollection('partners')
            ->findAsArray();
        
        $dataProvider = new MongoDataProvider($partners, array(
            'attributes'    => ['name', 'ref'],
            'pagination'    => array('pageSize' => 30)
        ));
        
        $this->render('partners', [
            'dataProvider'  => $dataProvider,
        ]);
    }
    
    public function actionPartner($id = null)
    {
        // get advertiser id
        if(!$id) {
            $id = Yii::app()->user->getProfile()->partner;
            if(!$id) {
                throw new Exception('Advertiser not specified');
            }   
        }
        
        $partner = Yii::app()->mongo
            ->getCollection('partners')
            ->getDocument($id);
        
        if(!$partner) {
            throw new Exception('Partner not found');
        }
        
        /**
         * Static files
         */
        
        // Google charts JSAPI
        ChartWidget::loadStatic();
        
        // Calendar
        Yii::app()->getClientScript()
            ->registerPackage('pickmeup');
        
        // Stat app
        Yii::app()->getClientScript()
            ->registerScriptFile('/js/stat.js');
        
        /**
         * Render
         */
        $this->render('partner', array(
            'partner' => $partner,
        ));
    }
    
    public function actionPartnerEvents($id = null)
    {
        // partner
        if(!$id) {
            $id = Yii::app()->user->getProfile()->partner;
            if(!$id) {
                $id = $this->request->getParam('partner');
                if(!$id) {
                    throw new Exception('Partner not specified');
                }
            }
        }
        
        $partner = Yii::app()->mongo
            ->getCollection('partners')
            ->getDocument($id);

        if(!$partner) {
            throw new \Exception('Partner not found');
        }

        $dateFrom = $this->request->getParam('dateFrom', date('Y-m-d'));
        $dateTo = $this->request->getParam('dateTo', date('Y-m-d'));

        // report
        $report = Yii::app()->report
            ->get('eventReport.partnerEvents')
            ->byPartner($partner)
            ->fromDate($dateFrom)
            ->toDate($dateTo);

        // period of report
        $period = $this->request->getParam('period');
        if ($period) {
            $report->setPeriod($period);
        }

        // render
        $this->renderPartial('partnerEvents', [
            'partner' => $partner,
            'report' => $report
        ]);
    }
    
    /**
     * Show list of advertisers
     */
    public function actionAdvertisers()
    {
        $this->pageTitle = _('Statistics');

        if (!Yii::app()->user->checkAccess('manageStat')) {
            $this->forward('campaigns');
        }

        $filter = new AdvertiserForm();
        if (isset($_GET['AdvertiserForm'])) {
            $filter->attributes = $_GET['AdvertiserForm'];
        }

        $advertisers = Yii::app()->mongo->getCollection('advertisers')
            ->findAsArray()
            ->notDeleted();

        $dataProvider = new MongoDataProvider($advertisers, array(
            'attributes' => array('name', 'phone', 'address', 'email'),
            'filter'     => $filter->attributes,
            'pagination' => array('pageSize' => 30)
        ));
        
        $this->renderPartial('advertisers', array(
            'dataProvider' => $dataProvider,
            'filter'       => $filter
        ));
    }
    
    public function actionAdvertiser($id = null)
    {
        // get advertiser id
        if(!$id) {
            $id = Yii::app()->user->getProfile()->advertiser;
            if(!$id) {
                throw new Exception('Advertiser not specified');
            }   
        }
        
        $advertiser = Yii::app()->mongo
            ->getCollection('advertisers')
            ->getDocument($id);
        
        if(!$advertiser) {
            throw new Exception('Advertiser not found');
        }
        
        /**
         * Static files
         */
        
        // Google charts JSAPI
        ChartWidget::loadStatic();
        
        // Calendar
        Yii::app()->getClientScript()
            ->registerPackage('pickmeup');
        
        // Stat app
        Yii::app()->getClientScript()
            ->registerScriptFile('/js/stat.js');
        
        /**
         * Render
         */
        $this->render('advertiser', array(
            'advertiser' => $advertiser,
        ));
    }
    
    public function actionCampaigns()
    {
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
        
        if (!$advertiser) {
            throw new \Exception('Advertiser not found');
        }

        $dateFrom = $this->request->getParam('dateFrom', date('Y-m-d'));
        $dateTo = $this->request->getParam('dateTo', date('Y-m-d'));

        $campaignReport = Yii::app()->report->get('eventReport.campaigns');

        $report = $campaignReport
            ->byAdvertiser($advertiser)
            ->fromDate($dateFrom)
            ->toDate($dateTo)
            ->withImpressions()
            ->withUniqueImpressions()
            ->withClicks()
            ->withUniqueClicks()
            ->withCost()
            ->withCtr();
        
        $this->renderPartial('campaigns', array(
            'report' => $report,
        ));
    }
    
    public function actionCampaign($id)
    {
        try {
            $campaign = Yii::app()->mongo
                ->getCollection('campaigns')
                ->getDocument($id);

            if($this->request->isAjaxRequest) {
                $this->layout = false;
            }
            
            $this->response->subMenu = array(
                array(
                    'url'       => '/stat/campaignEventChart?campaign=' . $campaign->getId(),
                    'caption'   => _('Event chart'),
                ),
                array(
                    'url'       => '/stat/campaignBanners?campaign=' . $campaign->getId(),
                    'caption'   => _('Banners'),
                ),
                array(
                    'url'       => '/stat/campaignByHour?campaign=' . $campaign->getId(),
                    'caption'   => _('Timing'),
                ),
            );
            
        } catch (Exception $e) {
            $this->response->raiseError($e);
        }
        
        $this->response->sendJson();
    }

    public function actionCampaignBanners()
    {
        $campaignId = $this->request->getParam('campaign');
        
        $campaign = Yii::app()->mongo->getCollection('campaigns')->getDocument($campaignId);
        if (!$campaign) {
            throw new \Exception('Campaign not found');
        }

        $dateFrom = $this->request->getParam('dateFrom', date('Y-m-d'));
        $dateTo = $this->request->getParam('dateTo', date('Y-m-d'));

        // table
        $report = Yii::app()->report
            ->get('eventReport.banners')
            ->byCampaign($campaign)
            ->fromDate($dateFrom)
            ->toDate($dateTo)
            ->withImpressions()
            ->withUniqueImpressions()
            ->withClicks()
            ->withUniqueClicks()
            ->withCost()
            ->withCtr();
        
        $this->renderPartial('campaignBanners', array(
            'report'        => $report,
            'advertiser'    => $campaign->getAdvertiser(),
        ));
    }

    public function actionCampaignEventChart()
    {
        $campaignId = $this->request->getParam('campaign');
        
        $campaign = Yii::app()->mongo->getCollection('campaigns')->getDocument($campaignId);
        if (!$campaign) {
            throw new \Exception('Campaign not found');
        }

        $dateFrom = $this->request->getParam('dateFrom', date('Y-m-d'));
        $dateTo = $this->request->getParam('dateTo', date('Y-m-d'));
        
        $this->renderPartial('campaignEventChart', [
            'campaign'  => $campaign,
            'dateFrom'  => $dateFrom,
            'dateTo'    => $dateTo,
            'period'    => $this->request->getParam('period'),
            'events'    => $this->request->getParam('events'),
        ]);
    }
    
    public function actionUpdateCampaignEventChart()
    {
        $campaignId = $this->request->getParam('campaign');
        if (!$campaignId) {
            throw new \Exception('Campaign not specified');
        }
        
        $campaign = Yii::app()->mongo
            ->getCollection('campaigns')
            ->getDocument($campaignId);

        if (!$campaign) {
            throw new \Exception('Campaign not found');
        }

        $dateFrom = $this->request->getParam('dateFrom', date('Y-m-d'));
        $dateTo = $this->request->getParam('dateTo', date('Y-m-d'));
        
        $widget = new \CampaignEventChart;
        
        $widget
            ->setDateFrom($dateFrom)
            ->setDateTo($dateTo)
            ->setCampaign($campaign)
            ->setPeriod($this->request->getParam('period'));

        $events = $this->request->getParam('events');
        if($events) {
            foreach ($events as $eventMethod) {
                if (method_exists($widget, $eventMethod)) {
                    $widget->$eventMethod();
                }
            }
        }

        Header('Content-type: application/json');
        echo json_encode($widget->getChartData());
    }

    public function actionCampaignByHour()
    {
        $campaignId = $this->request->getParam('campaign');

        $campaign = Yii::app()->mongo->getCollection('campaigns')->getDocument($campaignId);
        if (!$campaign) {
            throw new \Exception('Campaign not found');
        }

        $dateFrom = $this->request->getParam('dateFrom', date('Y-m-d'));
        $dateTo = $this->request->getParam('dateTo', date('Y-m-d'));

        $this->renderPartial('campaignByHour', array(
            'campaign'  => $campaign,
            'dateFrom'  => $dateFrom,
            'dateTo'    => $dateTo,
        ));
    }

}
