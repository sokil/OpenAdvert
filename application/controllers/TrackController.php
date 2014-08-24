<?php

class TrackController extends Controller
{
    
    public function accessRules() {
        $rules = parent::accessRules();
        array_unshift($rules, array('allow',
            'actions' => array('impression', 'event', 'clickthrough'),
            'users' => array('*'),
        ));
        return $rules;
    }
    
    public function actionImpression()
    {
        $this->getBanner()->trackImpression();
    }
    
    public function actionEvent()
    {        
        $event = $this->request->getParam('e');
        if(!$event) {
            throw new \Exception('Event not specified');
        }
        
        $this->getBanner()->trackEvent($event);
    }
    
    public function actionClickthrough()
    {
        $banner = $this->getBanner();
    
        // track click
        try {
            $banner->trackClick();
        }
        catch(\Exception $e) {
            Yii::log($e, CLogger::LEVEL_ERROR, 'TRACK');
        }
        
        // redirect
        Header('Location: ' . $banner->getUrl());
    }
    
    /**
     * 
     * @return \MongoAdvertDb\Banners\Banner
     * @throws \Exception
     */
    private function getBanner()
    {
        // get banner
        $bannerId = $this->request->getParam('b');
        if(!$bannerId) {
            throw new \Exception('Banner not specified');
        }
        
        $banner = Yii::app()->mongo->getCollection('banners')->getDocument($bannerId);
        if(!$banner) {
            throw new \Exception('Banner not found');
        }
        
        return $banner;
    }
}