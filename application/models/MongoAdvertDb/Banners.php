<?php 

namespace MongoAdvertDb;

use \MongoAdvertDb\Zones\Zone;
use \MongoAdvertDb\Campaigns\Campaign;
use \MongoAdvertDb\Banners\DeliveryOptions;

class Banners extends \Sokil\Mongo\Collection
{    
    protected $_queryExpressionClass = '\MongoAdvertDb\Banners\QueryExpression';
    
    /**
     *
     * @var \MongoAdvertDb\Banners\DeliveryOptions
     */
    private $_deliveryOptions;
    
    public function getDocumentClassName(array $documentData = null) {
        if(!isset($documentData['type'])) {
            throw new \Exception('Banner type must be defined');
        }
        
        return '\\MongoAdvertDb\\Banners\\Banner\\' . ucfirst($documentData['type']) . 'Banner';
    }
    
    public function createBanner($type, array $data = array())
    {
        $data['type'] = $type;
        
        return $this->createDocument($data);
    }
    
    public function getDeliveryOptions()
    {
        if(!$this->_deliveryOptions) {
            $this->_deliveryOptions = new DeliveryOptions;
        }
        
        return $this->_deliveryOptions;
    }
    
    public function getRandomBannerOfZone(Zone $zone)
    {
        // get banners, assigned to zone
        $bannerSearch = $this
            ->find()
            ->active()
            ->notLimited()
            ->byActiveCampaign()
            ->byZone($zone);
        
        if ($zone->getType() == 'video') {
            $bannerSearch->withMediaFiles();
        }

        // gather delivery option expressions
        $expressionList = array();
        foreach ($this->getDeliveryOptions()->getAvailableOptions() as $optionType) {
            
            $compareExpressions = $this->getDeliveryOptions()->create($optionType)->getCompareExpressions($bannerSearch);
            if ($compareExpressions) {
                $compareExpressions[] = $bannerSearch->expression()->withNoDeliveryOption(lcfirst($optionType));
                
                $expressionList[]= $bannerSearch->expression()->whereOr($compareExpressions);
            }
            
            else {
                $expressionList[] = $bannerSearch->expression()->withNoDeliveryOption(lcfirst($optionType));
            }
        }
        
        // filter
        $bannerSearch->whereOr(
            $bannerSearch->expression()->withNoDeliveryOptions(),
            $bannerSearch->expression()->whereAnd($expressionList)
        );

        return $bannerSearch->findRandom();
    }
    
    /**
     * Activate all banners of specified campaign
     * 
     * @param \MongoAdvertDb\Campaigns\Campaign $campaign
     * @return \MongoAdvertDb\Banners
     */
    public function activateByCampaign(Campaign $campaign)
    {
        $this->updateMultiple(
            $this->expression()->byCampaign($campaign),
            $this->operator()->unsetField('deactivated')
        );
        
        return $this;
    }
    
    /**
     * Dectivate all banners of specified campaign
     * 
     * @param \MongoAdvertDb\Campaigns\Campaign $campaign
     * @return \MongoAdvertDb\Banners
     */
    public function deactivateByCampaign(Campaign $campaign)
    {
        $this->updateMultiple(
            $this->expression()->byCampaign($campaign),
            $this->operator()->set('deactivated', true)
        );
        
        return $this;
    }
    
    /**
     * 
     * @param array<\MongoAdvertDb\Campaigns\Campaign> $campaigns
     * @return \MongoAdvertDb\Banners
     */
    public function deactivateByCampaigns(array $campaigns)
    {
        $this->updateMultiple(
            $this->expression()->byCampaigns($campaigns),
            $this->operator()->set('deactivated', true)
        );

        return $this;
    }

    public function deleteByCampaign(Campaign $campaign)
    {
        $this->updateMultiple(
            $this->expression()->byCampaign($campaign),
            $this->operator()->set('status', \MongoAdvertDb\Banners\Banner::STATUS_DELETED)
        );

        return $this;
    }

}
