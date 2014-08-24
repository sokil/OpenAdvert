<?php

namespace MongoAdvertDb\Banners;

abstract class BannerForm extends \CFormModel
{
    public $id;
    public $name;
    public $url;
    public $limits;
    
    protected $_banner;
    
    public function init() {}
    
    public static function factory(array $data) {
        
        // get banner
        if(empty($data['id'])) {
            $strategy = 'create';
            
            $campaign = \Yii::app()->mongo
                ->getCollection('campaigns')
                ->getDocument($data['campaign']);

            $banner = \Yii::app()->mongo
                ->getCollection('banners')
                ->createBanner($data['type'])
                ->setCampaign($campaign);
        }
        else {
            $strategy = 'update';
            
            $banner = \Yii::app()->mongo
                ->getCollection('banners')
                ->getDocument($data['id']);

            if(!$banner) {
                throw new \Exception('Banner ' . $this->id . ' not found');
            }
        }
        
        $formClassName = '\\MongoAdvertDb\\Banners\\BannerForm\\' . ucfirst($banner->getType()) . 'BannerForm';
        $form = new $formClassName($strategy);
        $form->setAttributes($data);
        $form->setBanner($banner);
        
        return $form;
    }
    
    public function rules()
    {
        return array(
            array('id', 'required', 'on' => 'update'),
            array('name,url', 'required', 'message' => _('This field required')),
            array('limits', 'safe'),
        );
    }
    
    public function setBanner(Banner $banner)
    {
        $this->_banner = $banner;
    }
    
    /**
     * 
     * @return \MongoAdvertDb\Banners\Banner
     */
    public function getBanner()
    {
        return $this->_banner;
    }
    
    abstract protected function applyAttributes();
    
    public function save()
    {        
        $banner = $this->getBanner();
        
        $banner
            ->setName($this->name)
            ->setUrl($this->url);
        
        if( isset($this->limits['impressions']['total']) ) {
            $banner->setTotalImpressionLimit((int) $this->limits['impressions']['total']);
        }
        
        if( isset($this->limits['impressions']['day']) ) {
            $banner->setDailyImpressionLimit((int) $this->limits['impressions']['day']);
        }
        
        if( isset($this->limits['impressions']['hour']) ) {
            $banner->setHourlyImpressionLimit((int) $this->limits['impressions']['hour']);
        }
        
        if( isset($this->limits['clicks']['total']) ) {
            $banner->setTotalClickLimit((int) $this->limits['clicks']['total']);
        }
        
        if( isset($this->limits['clicks']['day']) ) {
            $banner->setDailyClickLimit((int) $this->limits['clicks']['day']);
        }
        
        if( isset($this->limits['clicks']['hour']) ) {
            $banner->setHourlyClickLimit((int) $this->limits['clicks']['hour']);            
        }
                
        $this->applyAttributes();
        
        $banner->save();
    }
}
