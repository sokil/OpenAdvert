<?php

namespace MongoAdvertDb\Banners;

use \MongoAdvertDb\Campaigns\Campaign;
use \MongoAdvertDb\Zones\Zone;

abstract class Banner extends \Sokil\Mongo\Document
{
    const STATUS_ACTIVE     = 'ACTIVE';
    const STATUS_SUSPENDED  = 'SUSPENDED';
    const STATUS_DELETED    = 'DELETED';
    
    protected $_data = array(
        'campaign'  => null,
        'name'      => null,
        'type'      => null,
        'url'       => null,
        'zones'     => array(),
        'status'    => self::STATUS_ACTIVE,
        'limit' => [
            'impression' => [
                'total' => ['limit' => null, 'counter' => 0],
                'day'   => ['limit' => null, 'counter' => 0, 'period' => null],
                'hour'  => ['limit' => null, 'counter' => 0, 'period' => null],
            ],
            'click' => [
                'total' => ['limit' => null, 'counter' => 0],
                'day'   => ['limit' => null, 'counter' => 0, 'period' => null],
                'hour'  => ['limit' => null, 'counter' => 0, 'period' => null],
            ],
        ],
    );

    public function rules()
    {
        return array(
            array('campaign,name,type,url,status', 'required', 'message' => _('This field required')),
            array('status', 'in', 'range' => array(self::STATUS_ACTIVE, self::STATUS_SUSPENDED, self::STATUS_DELETED), 'message' => _('Wrong value specified')),
        );
    }
    
    public function beforeConstruct()
    {
        $this->onBeforeSave(function() {
            if ($this->isModified('limit')) {
                if ($this->isLimited()) {
                    if (!$this->isLimitRequired()) {
                        $this->removeLimit();
                    }
                } else {
                    if ($this->isLimitRequired()) {
                        $this->limit();
                    }
                }
            }
        });
    }
    
    public function setCampaign(Campaign $campaign)
    {
        $this->set('campaign', $campaign->getId());
        return $this;
    }
    
    public function getCampaignId()
    {
        return $this->get('campaign');
    }
    
    /**
     * 
     * @param string $name
     * @return \MongoAdvertDb\Banners\Banner
     */
    public function setName($name)
    {
        return $this->set('name', $name);
    }
    
    public function getName()
    {
        return $this->get('name');
    }
    
    /**
     * 
     * @param string $type
     * @return \MongoAdvertDb\Banners\Banner
     */
    public function setType($type)
    {
        return $this->set('type', $type);
    }
    
    public function getType()
    {
        return $this->get('type');
    }
    
    /**
     * @return \MongoAdvertDb\Banners\Banner
     */
    public function setTotalImpressionLimit($limit)
    {
        $this->set('limit.impression.total.limit', (int) $limit);

        return $this;
    }
    
    public function getTotalImpressionLimit()
    {
        return (int) $this->get('limit.impression.total.limit');
    }
    
    public function getTotalImpressionCounter()
    {
        return (int) $this->get('limit.impression.total.counter');
    }
        
    /**
     * @return \MongoAdvertDb\Banners\Banner
     */
    public function setDailyImpressionLimit($limit)
    {
        $this->set('limit.impression.day.limit', (int) $limit);
        
        return $this;
    }
    
    public function resetDailyImpressionCounter()
    {
        $this->set('limit.impression.day.counter', 0);
        $this->set('limit.impression.day.period', null);
        
        return $this;
    }
    
    public function getDailyImpressionLimit()
    {
        return (int) $this->get('limit.impression.day.limit');
    }
    
    public function getDailyImpressionCounter()
    {
        return (int) $this->get('limit.impression.day.counter');
    }
    
    /**
     * @return \MongoAdvertDb\Banners\Banner
     */
    public function setHourlyImpressionLimit($limit)
    {
        $this->set('limit.impression.hour.limit', (int) $limit);
        
        return $this;
    }
    
    public function resetHourlyImpressionCounter()
    {
        $this->set('limit.impression.hour.counter', 0);
        $this->set('limit.impression.hour.period', null);
        
        return $this;
    }
    
    public function getHourlyImpressionLimit()
    {
        return (int) $this->get('limit.impression.hour.limit');
    }
    
    public function getHourlyImpressionCounter()
    {
        return (int) $this->get('limit.impression.hour.counter');
    }
    
    /**
     * @return \MongoAdvertDb\Banners\Banner
     */
    public function setTotalClickLimit($limit)
    {
        $this->set('limit.click.total.limit', (int) $limit);
        
        return $this;
    }
    
    public function getTotalClickLimit()
    {
        return (int) $this->get('limit.click.total.limit');
    }
    
    public function getTotalClickCounter()
    {
        return (int) $this->get('limit.click.total.counter');
    }
        
    /**
     * @return \MongoAdvertDb\Banners\Banner
     */
    public function setDailyClickLimit($limit)
    {
        $this->set('limit.click.day.limit', (int) $limit);
        
        return $this;
    }
    
    public function resetDailyClickCounter()
    {
        $this->set('limit.click.day.counter', 0);
        $this->set('limit.click.day.period', null);
        
        return $this;
    }
    
    public function getDailyClickLimit()
    {
        return (int) $this->get('limit.click.day.limit');
    }
    
    public function getDailyClickCounter()
    {
        return (int) $this->get('limit.click.day.counter');
    }
    
    /**
     * @return \MongoAdvertDb\Banners\Banner
     */
    public function setHourlyClickLimit($limit)
    {
        $this->set('limit.click.hour.limit', (int) $limit);
        
        return $this; 
    }
    
    public function resetHourlyClickCounter()
    {
        $this->set('limit.click.hour.counter', 0);
        $this->set('limit.click.hour.period', null);
        
        return $this; 
    }
    
    public function getHourlyClickLimit()
    {
        return (int) $this->get('limit.click.hour.limit');
    }
    
    public function getHourlyClickCounter()
    {
        return (int) $this->get('limit.click.hour.counter');
    }
    
    public function resetRequiredEventCounters()
    {
        $now = time();
        
        // day impression
        $impressionDayPeriodStartTime = $this->get('limit.impression.day.period');
        if($impressionDayPeriodStartTime && $now > $impressionDayPeriodStartTime->sec + 24 * 60 * 60) {
            $this->resetDailyImpressionCounter();
        }
        
        // hour impression
        $impressionHourPeriodStartTime = $this->get('limit.impression.hour.period');
        if($impressionDayPeriodStartTime && $now > $impressionHourPeriodStartTime->sec + 3600) {
            $this->resetHourlyImpressionCounter();
        }
        
        // day click
        $clickDayPeriodStartTime = $this->get('limit.click.day.period');
        if($clickDayPeriodStartTime && $now > $clickDayPeriodStartTime->sec + 24 * 60 * 60) {
            $this->resetDailyClickCounter();
        }
        
        // hour click
        $clickHourPeriodStartTime = $this->get('limit.click.hour.period');
        if($clickHourPeriodStartTime && $now > $clickHourPeriodStartTime->sec + 3600) {
            $this->resetHourlyClickCounter();
        }
        
        $this->save();
        
        return $this;
    }
    
    private function _incrementEventCounter($event)
    {
        // check if banner already limited
        if($this->isLimited()) {
            return;
        }
        
        // check if limits specified
        $limit = $this->get('limit');
        if(!$limit || empty($limit[$event])) {
            return;
        }
        
        $period = array(
            'total' => null,
            'day'   => new \MongoDate(strtotime(date('Y-m-d 00:00:00'))),
            'hour'  => new \MongoDate(strtotime(date('Y-m-d H:00:00'))),
        );
        
        // increment limits
        foreach ($limit[$event] as $interval => $meta) {
            if(empty($meta['limit'])) {
                continue;
            }
            
            $counterKey = 'limit.' . $event . '.' . $interval . '.counter';
            $periodKey  = 'limit.' . $event . '.' . $interval . '.period';

            // total
            if($interval === 'total') {
                // increment counter
                $this->increment($counterKey);
            }
            //save data
            else if(!empty($meta['period']) && $meta['period']->sec === $period[$interval]->sec) {
                // increment counter
                $this->increment($counterKey);
            }
            // diff date
            else {
                // update period and reset counter
                $this->set($counterKey, 1);
                if($interval !== 'total') {
                    $this->set($periodKey, $period[$interval]);
                }
            }
            
            // remove banner from chosing id limits occured
            if($this->get($counterKey) > $meta['limit']) {
                $this->limit();
                break;
            }
            
        }
        
        $this->save();
    }
    
    /**
     * Check if banner must be limited due to current limit and counter values
     * @return boolean
     */
    public function isLimitRequiredByTotalImpression()
    {
        return $this->get('limit.impression.total.counter') > $this->get('limit.impression.total.limit');
    }
    
    /**
     * Check if banner must be limited due to current limit and counter values
     * @return boolean
     */
    public function isLimitRequiredByTotalClick()
    {
        return $this->get('limit.click.total.counter') > $this->get('limit.click.total.limit');
    }   
    
    /**
     * Check if banner must be limited due to current limit and counter values
     * @return boolean
     */
    public function isLimitRequiredByDayImpression()
    {
        $now = time();
        
        $impressionDayPeriodStartTime = $this->get('limit.impression.day.period');
        if(!$impressionDayPeriodStartTime) {
            return false;
        }
        
        if($now > $impressionDayPeriodStartTime->sec && $now < $impressionDayPeriodStartTime->sec + 24 * 60 * 60) {
            if($this->get('limit.impression.day.counter') > $this->get('limit.impression.day.limit')) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check if banner must be limited due to current limit and counter values
     * @return boolean
     */
    public function isLimitRequiredByDayClick()
    {
        $now = time();
        
        $clickDayPeriodStartTime = $this->get('limit.click.day.period');
        if(!$clickDayPeriodStartTime) {
            return false;
        }
        
        if($now > $clickDayPeriodStartTime->sec && $now < $clickDayPeriodStartTime->sec + 24 * 60 * 60) {
            if($this->get('limit.click.day.counter') > $this->get('limit.click.day.limit')) {
                return true;
            }
        }
        
        return false;
    }
        
    /**
     * Check if banner must be limited due to current limit and counter values
     * @return boolean
     */
    public function isLimitRequiredByHourImpression()
    {
        $now = time();
        
        $impressionHourPeriodStartTime = $this->get('limit.impression.hour.period');
        if(!$impressionHourPeriodStartTime) {
            return false;
        }
        
        if($now > $impressionHourPeriodStartTime->sec && $now < $impressionHourPeriodStartTime->sec + 3600) {
            if($this->get('limit.impression.hour.counter') > $this->get('limit.impression.hour.limit')) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check if banner must be limited due to current limit and counter values
     * @return boolean
     */
    public function isLimitRequiredByHourClick()
    {
        $now = time();
        
        $clickHourPeriodStartTime = $this->get('limit.click.hour.period');
        if(!$clickHourPeriodStartTime) {
            return false;
        }

        if($now > $clickHourPeriodStartTime->sec && $now < $clickHourPeriodStartTime->sec + 3600) {
            if($this->get('limit.click.hour.counter') > $this->get('limit.click.hour.limit')) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check if banner limit required
     * 
     * @return bool
     */
    public function isLimitRequired()
    {
        return 
            $this->isLimitRequiredByHourImpression()
            || $this->isLimitRequiredByHourClick()
            || $this->isLimitRequiredByDayImpression()
            || $this->isLimitRequiredByDayClick()
            || $this->isLimitRequiredByTotalImpression()
            || $this->isLimitRequiredByTotalClick();
    }

    public function isLimited()
    {
        return $this->get('limited');
    }
    
    private function limit()
    {
        if(!$this->isLimited()) {
            $this->set('limited', true);
        }
        
        return $this;
    }
    
    public function removeLimit()
    {
        if($this->isLimited()) {
            $this->unsetField('limited');
            $this->resetRequiredEventCounters();
        }
        
        return $this;
    }
    
    public function setUrl($url)
    {
        $this->set('url', $url);
        return $this;
    }
    
    public function getUrl()
    {
        return $this->get('url');
    }
    
    public function setZones(array $zones)
    {        
        $zoneIdList = array_map(function($zone) {
            if($zone instanceof \MongoId) {
                return $zone;
            }
            
            if($zone instanceof Zone) {
                return $zone->getId();
            }
            
            return new \MongoId($zone);
        }, array_values($zones));
        
        $this->set('zones', $zoneIdList);
        return $this;
    }
    
    public function clearZones()
    {
        $this->set('zones', array());
        return $this;
    }
    
    public function attachZone(Zone $zone)
    {
        $this->push('zones', $zone->getId());
        return $this;
    }
    
    public function detachZone(Zone $zone)
    {
        $zones = $this->getZoneIdList();
        
        $deletedZoneIndex = array_search($zone->getId(), $zones);
        unset($zones[$deletedZoneIndex]);

        $this->setZones($zones);
        
        return $this;
    }
    
    public function getZoneIdList()
    {
        return $this->get('zones');
    }
    
    public function setActive()
    {
        $this->set('status', self::STATUS_ACTIVE);
        return $this;
    }
    
    public function isActive()
    {
        return self::STATUS_ACTIVE === $this->get('status');
    }
    
    public function setSuspended()
    {
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
    
    public function isDeactivated()
    {
        return (bool) $this->get('deactivated');
    }
    
    public function clearDeliveryOptions()
    {
        return $this->set('deliveryOptions', array());
    }
    
    public function setDeliveryOptions(array $options) 
    {
        $this->set('deliveryOptions', array_map(function($option) {
            return $option->toArray();
        }, $options));
        
        return $this;
    }
    
    public function getDeliveryOptions()
    {
        return $this->getObjectList('deliveryOptions', function($data) {
            return '\\MongoAdvertDb\\Banners\\DeliveryOptions\\Option\\' . ucfirst($data['option']) . 'Option';
        });
    }
    
    public function getTrackImpressionUrl()
    {
        return 'http://' . $_SERVER['HTTP_HOST'] . '/track/impression?b=' . $this->getId();
    }
    
    public function getTrackEventUrl($event)
    {
        return 'http://' . $_SERVER['HTTP_HOST'] . '/track/event?b=' . $this->getId() . '&e=' . $event;
    }
    
    public function geClickThroughUrl()
    {
        return 'http://' . $_SERVER['HTTP_HOST'] . '/track/clickthrough?b=' . $this->getId();
    }
    
    public function trackImpression()
    {
        // register event
        \Yii::app()->mongo->getCollection('track.impression')
            ->createDocument()
            ->setBanner($this)
            ->save();
        
        $this->_incrementEventCounter('impression');

        return $this;
    }
    
    public function trackEvent($event)
    {
        // register event
        \Yii::app()->mongo->getCollection('track.event')
            ->createDocument()
            ->setBanner($this)
            ->setEvent($event)
            ->save();
        
        return $this;
    }
    
    public function trackClick()
    {        
        // register event
        \Yii::app()->mongo->getCollection('track.click')
            ->createDocument()
            ->setBanner($this)
            ->save();
        
        $this->_incrementEventCounter('click');
        
        return $this;
    }
}
