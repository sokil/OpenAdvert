<?php

namespace MongoAdvertDb\Campaigns;

use MongoAdvertDb\Advertisers\Advertiser;
use MongoAdvertDb\Banners\Banner;

class Campaign extends \Sokil\Mongo\Document
{
    const STATUS_ACTIVE                 = 'ACTIVE';
    const STATUS_SUSPENDED              = 'SUSPENDED';  // by user
    const STATUS_DELETED                = 'DELETED';
    const STATUS_MODERATION_REQUIRED    = 'MODERATION_REQUIRED'; // by default for advertisers

    const PRICING_MODEL_CPM = 'CPM';
    const PRICING_MODEL_CPC = 'CPC';
    
    
    protected $_data = array(
        'advertiser'        => null,
        'name'              => null,
        'dateFrom'          => null,
        'dateTo'            => null,
        'impressionLimit'   => null,
        'impressions'       => array(
            'total'     => 0,
            'unique'    => 0,
        ),
        'clickLimit'        => null,
        'clicks'            => array(
            'total'     => 0,
            'unique'    => 0,
        ),
        'pricing'           => array(
            'model'         => null,
            'price'         => null,
        ),
        'status'            => self::STATUS_ACTIVE,
    );
    
    public function beforeConstruct() {
        /**
         * Before Save Hook
         */
        $this->onBeforeSave(function() {
            
            if ($this->mustDeactivated()) {
                if(!$this->isDeactivated()) {
                    $this->deactivate();
                }
            } else if ($this->isDeactivated()) {
                $this->activate();
                
            }
            
            if ($this->isModified('status')) {
                if ( $this->isSuspended() ) {
                    \Yii::app()->mongo->getCollection('banners')->deactivateByCampaign($this);
                } else if ( $this->isDeleted() ) {
                    \Yii::app()->mongo->getCollection('banners')->deleteByCampaign($this);
                } else if ($this->isActive()) {
                    \Yii::app()->mongo->getCollection('banners')->activateByCampaign($this);
                }
            }
            
            // if start date empty - add current date
            if(!$this->getDateFrom()) {
                $this->setDateFrom(new \MongoDate);
            }
            
        });

        $this->onAfterInsert(function(){
            if ($this->isModerationRequired()) {
                $mailer = \Yii::app()->mailer;
                $mailer->setSubject(sprintf('New campaign %s', $this->getName()));
                $mailer->addAddressList( $mailer->getAdminsAddressList() );
                $mailer->send('new_campaign', array(
                    'campaign' => $this
                ));
            }
        });
    }
    
    public function rules()
    {
        return array(
            array('advertiser,name,status', 'required', 'message' => _('This field required')),
            array('impressionLimit, clickLimit, pricing.price', 'numeric', 'message' => _('Field must be numeric')),
            array('pricing.model', 'in', 'range' => array(self::PRICING_MODEL_CPC, self::PRICING_MODEL_CPM), 'message' => _('Wrong value specified')),
            array('status', 'in', 'range' => array(self::STATUS_ACTIVE, self::STATUS_SUSPENDED, self::STATUS_DELETED, self::STATUS_MODERATION_REQUIRED), 'message' => _('Wrong value specified')),
        );
    }
    
    public function isDeactivated() {
        return (bool) $this->get('deactivated');
    }
    
    public function mustDeactivated() {
        if ($this->getImpressionLimit() && $this->getActualImpressions() >= $this->getImpressionLimit()) {
            return true;
        }
        if ($this->getClickLimit() && $this->getActualClicks() >= $this->getClickLimit()) {
            return true;
        }
        if ($this->getDateFrom() && $this->getDateFrom() > strtotime('midnight')) {
            return true;
        }
        if ($this->getDateTo() && $this->getDateTo() < strtotime('midnight')) {
            return true;
        }
        return false;
    }
    
    public function deactivate()
    {
        $this
            ->set('deactivated', true)
            ->save();
        
        \Yii::app()->mongo
            ->getCollection('banners')
            ->deactivateByCampaign($this);
        
        return $this;
    }
    
    public function activate()
    {
        $this
            ->unsetField('deactivated')
            ->save();
        
        \Yii::app()->mongo
            ->getCollection('banners')
            ->activateByCampaign($this);
        
        return $this;
    }

    public function hitImpressions(Banner $banner)
    {        
        // hit total impressions
        $this->increment('impressions.total');
        
        // hit unique impressions
        $bannerVisitorImpressions = \Yii::app()->mongo->getCollection('track.impression')
            ->find()
            ->byVisitor(\Yii::app()->mongo->getCollection('visitors')->getCurrent())
            ->byBanner($banner)
            ->byDateGreaterThan(strtotime('midnight'))
            ->count();

        if ($bannerVisitorImpressions == 0) {
            $this->increment('impressions.unique');
        }
        
        return $this;
    }
    
    public function hitClicks(Banner $banner)
    {        
        // hit total clicks
        $this->increment('clicks.total');
        
        // hit unique clicks
        $bannerVisitorClicks = \Yii::app()->mongo->getCollection('track.click')
            ->find()
            ->byVisitor(\Yii::app()->mongo->getCollection('visitors')->getCurrent())
            ->byBanner($banner)
            ->byDateGreaterThan(strtotime('midnight'))
            ->count();

        if ($bannerVisitorClicks == 0) {
            $this->increment('clicks.unique');
        }
        
        return $this;
    }
    
    public function setAdvertiser(Advertiser $advertiser)
    {
        $this->set('advertiser', $advertiser->getId());
        return $this;
    }
    
    public function getAdvertiserId()
    {
        return $this->get('advertiser');
    }
    
    /**
     * Get instance of related advertiser
     * @return \MongoAdvertDb\Advertisers\Advertiser
     */
    public function getAdvertiser()
    {
        $advertiserId = $this->getAdvertiserId();
        if(!$advertiserId) {
            return null;
        }
        
        $advertiser = $this
            ->getCollection()
            ->getDatabase()
            ->getCollection('advertisers')
            ->getDocument($advertiserId);
        
        if(!$advertiser) {
            return null;
        }
        
        return $advertiser;
        
        
    }
    
    public function setName($name)
    {
        return $this->set('name', $name);
    }
    
    public function getName()
    {
        return $this->get('name');
    }
    
    public function setDateFrom($date)
    {
        if(!($date instanceof \MongoDate)) {
            if(!is_numeric($date)) {
                $date = strtotime($date);
                if(!$date) {
                    $this->triggerError('dateFrom', 'date', _('Wrong date specified'));
                    return;
                }
            }
            
            $date = new \MongoDate($date);
        }
        
        $this->set('dateFrom', $date);
        return $this;
    }
    
    public function getDateFrom($format = null)
    {
        $date = $this->get('dateFrom');
        
        if(!$date) {
            return null;
        }
            
        if(!$format) {
            return $date->sec;
        }
        
        return date($format, $date->sec);
    }
    
    public function setDateTo($date)
    {
        if(!($date instanceof \MongoDate)) {
            if(!is_numeric($date)) {
                $date = strtotime($date);
                if(!$date) {
                    $this->triggerError('dateTo', 'date', _('Wrong date specified'));
                    return;
                }
            }
            
            $date = new \MongoDate($date);
        }

        $this->set('dateTo', $date);
        return $this;
    }
    
    public function getDateTo($format = null)
    {
        $date = $this->get('dateTo');
        if(!$date) {
            return null;
        }
        
        if(!$format) {
            return $date->sec;
        }
        
        return date($format, $date->sec);
    }
    
    public function setImpressionLimit($impressionLimit)
    {
        $this->set('impressionLimit', $impressionLimit);
        return $this;
    }

    public function getImpressionLimit()
    {
        return (int) $this->get('impressionLimit');
    }
    
    public function getImpressions()
    {
        return (int) $this->get('impressions.total');
    }
    
    public function getUniqueImpressions()
    {
        return (int) $this->get('impressions.unique');
    }
    
    public function getActualImpressions() {
        if ($this->isCostCalculatedByUniqueEvents()) {
            return $this->getUniqueImpressions();
        } else {
            return $this->getImpressions();
        }
    }

    public function setClickLimit($clickLimit)
    {
        $this->set('clickLimit', $clickLimit);
        return $this;
    }
    
    public function getClickLimit()
    {
        return (int) $this->get('clickLimit');
    }
    
    public function getClicks()
    {
        return (int) $this->get('clicks.total');
    }
    
    public function getUniqueClicks()
    {
        return (int) $this->get('clicks.unique');
    }
    
    public function getActualClicks() {
        if ($this->isCostCalculatedByUniqueEvents()) {
            return $this->getUniqueClicks();
        } else {
            return $this->getClicks();
        }
    }
    
    public function isCPMPricingModel()
    {
        return $this->getPricingModel() == self::PRICING_MODEL_CPM;
    }
    
    public function isCPCPricingModel()
    {
        return $this->getPricingModel() == self::PRICING_MODEL_CPC;
    }
    
    public function getPricingModel()
    {
        return $this->get('pricing.model');
    }
    
    public function getPrice()
    {
        return $this->get('pricing.price');
    }
    
    public function isCostCalculatedByUniqueEvents()
    {
        return (bool) $this->get('pricing.unique');
    }
    
    public function getCost()
    {
        return $this->calcCost(
            $this->getImpressions(), 
            $this->getUniqueImpressions(), 
            $this->getClicks(), 
            $this->getUniqueClicks()
        );
    }
    
    public function calcCost($totalImpressions, $uniqueImpressions, $totalClicks, $uniqueClicks)
    {
        if ($this->isCPMPricingModel()) {
            $impressions = $this->isCostCalculatedByUniqueEvents()
                ? $uniqueImpressions
                : $totalImpressions;
            
            $cost = $this->getPrice() * $impressions / 1000;
        } elseif ($this->isCPCPricingModel()) {
            $clicks = $this->isCostCalculatedByUniqueEvents()
                ? $uniqueClicks
                : $totalClicks;
            
            $cost = $this->getPrice() * $clicks;
        }
        else {
            $cost = 0;
        }
        
        return $cost;
    }
    
    public function calcCtr($totalImpressions, $uniqueImpressions, $totalClicks, $uniqueClicks)
    {
        if ($this->isCostCalculatedByUniqueEvents()) {
            if ($uniqueImpressions > 0) {
                $ctr = $uniqueClicks / $uniqueImpressions;
            } else {
                $ctr = 0;
            }
        } else {
            if ($totalImpressions > 0) {
                $ctr = $totalClicks / $totalImpressions;
            } else {
                $ctr = 0;
            }
        }

        return $ctr;
    }
    
    public function setActive()
    {
        if($this->isDeactivated()) {
            throw new \Exception('Campaign is deactivated');
        }
        $this->set('status', self::STATUS_ACTIVE);
        return $this;
    }
    
    public function isActive()
    {
        return self::STATUS_ACTIVE === $this->get('status');
    }
    
    public function setSuspended()
    {
        if($this->isDeactivated()) {
            throw new \Exception('Campaign is deactivated');
        }
        $this->set('status', self::STATUS_SUSPENDED);
        return $this;
    }
    
    public function isSuspended()
    {
        return self::STATUS_SUSPENDED === $this->get('status');
    }
    
    public function setDeleted()
    {
        $this->set('status', self::STATUS_DELETED);
        return $this;
    }
    
    public function isDeleted()
    {
        return self::STATUS_DELETED === $this->get('status');
    }

    public function setModerationRequired()
    {
        $this->set('status', self::STATUS_MODERATION_REQUIRED);
        return $this;
    }

    public function isModerationRequired()
    {
        return self::STATUS_MODERATION_REQUIRED === $this->get('status');
    }
    
    public function canBeManagedBy($user)
    {
        if($user instanceof \User) {
            $user = $user->getProfile();
        } elseif (!($user instanceof \MongoAdvertDb\Users\User)) {
            throw new \Excepption('Wrong user passed');
        }
        
        // chack if campaign can be edited by user 
        if(\Yii::app()->user->checkAccess('manageCampaign.editWithoutModeration')) {
            return true;
        }
        
        // is campaign has modaration status
        if(!$this->isModerationRequired()) {
            return false;
        }
        
        // check if user belongs to same advertiser as campaign
        return (string) $this->getAdvertiserId() === (string) $user->getAdvertiserId();
    }
}
