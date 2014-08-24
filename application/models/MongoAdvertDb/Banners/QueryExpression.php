<?php

namespace MongoAdvertDb\Banners;

use \MongoAdvertDb\Campaigns\Campaign;
use \MongoAdvertDb\Banners\DeliveryOptions\MultiValueOption;
use \MongoAdvertDb\Banners\DeliveryOptions\SingleValueOption;

class QueryExpression extends \Sokil\Mongo\Expression
{
    public function byCampaign(Campaign $campaign)
    {
        $this->where('campaign', $campaign->getId());
        return $this;
    }
    
    public function byActiveCampaign() {
        $this->whereNotExists('deactivated');
        return $this;
    }
    
    public function notLimited() {
        $this->whereNotExists('limited');
        return $this;
    }
    
    public function limited() {
        $this->whereExists('limited');
        return $this;
    }

    public function byCampaigns(array $campaigns)
    {
        $this->whereIn('campaign', array_map(function($campaign) {
            return $campaign->getId();
        }, $campaigns));
        
        return $this;
    }
    
    public function active()
    {
        $this->where('status', Banner::STATUS_ACTIVE);
        return $this;
    }
    
    public function notDeleted()
    {
        $this->where('status', array('$ne' => Banner::STATUS_DELETED));
        return $this;
    }
    
    public function byNameLike($name) 
    {
        $this->where('name', array('$regex' => $name, '$options' => 'i'));
        return $this;
    }
    
    public function byZone($zone) 
    {
        $this->where('zones', $zone->getId());
        return $this;
    }
    
    public function byType($type)
    {
        $this->where('type', $type);
        return $this;
    }
    
    public function byMediaFileId($id)
    {
        return $this->whereElemMatch('mediaFiles', $this->expression()->where('id', new \Mongoid($id)));
    }
    
    public function withMediaFiles()
    {
        return $this->whereNotEmpty('mediaFiles');
    }
    
    public function withNoDeliveryOptions() {
        return $this->whereEmpty('deliveryOptions');
    }
    
    public function withNoDeliveryOption($option) {
        return $this->whereElemNotMatch(
            'deliveryOptions',
            $this->expression()->where('option', $option)
        );
    }
    
    public function optionValueNoneOf($option, $value) {
        $expressions = array();
        if (is_array($value)) {
            $expressions = array_map(function($val) {
                    return $this->expression()->whereNotEqual('value', $val);
                }, $value);
        } else {
            $expressions[] = $this->expression()->whereNotEqual('value', $value);
        }

        return $this->whereElemMatch(
                'deliveryOptions',
                $this->expression()
                    ->whereAnd($expressions)
                    ->where('option', $option)
                    ->where('comparison', MultiValueOption::COMPARISON_NONE_OF)
            );
    }
    
    public function optionValueAnyOf($option, $value) {
        $expressions = array();
        if (is_array($value)) {
            $expressions = array_map(function($val) {
                    return $this->expression()->where('value', $val);
                }, $value);
        } else {
            $expressions[] = $this->expression()->where('value', $value);
        }

        return $this->whereElemMatch(
                'deliveryOptions', 
                $this->expression()
                    ->whereOr($expressions)
                    ->where('option', $option)
                    ->where('comparison', MultiValueOption::COMPARISON_ANY_OF)
            );
    }
    
    public function optionVarValueNotEquals($option, $value) {
        return $this->whereElemMatch(
                'deliveryOptions',
                $this->expression()
                    ->where('value.key', $value['key'])
                    ->whereNotEqual('value.value', $value['value'])
                    ->where('option', $option)
                    ->where('comparison', SingleValueOption::COMPARISON_NOT_EQUALS)
            );
    }
    
    public function optionValueNotEqualsAnyOf($option, $value) {
        return $this->whereElemMatch(
                'deliveryOptions',
                $this->expression()
                    ->whereNotIn('value', $value)
                    ->where('option', $option)
                    ->where('comparison', SingleValueOption::COMPARISON_NOT_EQUALS)
            );
    }
    
    public function optionValueEqualsAnyOf($option, $value) {
        return $this->whereElemMatch(
                'deliveryOptions', 
                $this->expression()
                    ->whereIn('value', $value)
                    ->where('option', $option)
                    ->where('comparison', SingleValueOption::COMPARISON_EQUALS)
            );
    }
    
    public function optionValueNotEquals($option, $value) {
        return $this->whereElemMatch(
                'deliveryOptions',
                $this->expression()
                    ->whereNotEqual('value', $value)
                    ->where('option', $option)
                    ->where('comparison', SingleValueOption::COMPARISON_NOT_EQUALS)
            );
    }
    
    public function optionValueEquals($option, $value) {
        return $this->whereElemMatch(
                'deliveryOptions', 
                $this->expression()
                    ->where('value', $value)
                    ->where('option', $option)
                    ->where('comparison', SingleValueOption::COMPARISON_EQUALS)
            );
    }
    
    public function optionTimeNotEquals($option, $value) {
        return $this->whereElemMatch(
                'deliveryOptions', 
                $this->expression()
                    ->where('option', $option)
                    ->where('comparison', SingleValueOption::COMPARISON_NOT_EQUALS)
                    ->whereOr(
                        $this->expression()->where('value.mid', 1)
                            ->whereGreaterOrEqual('value.min', $value)
                            ->whereLessOrEqual('value.max', $value),
                        $this->expression()->where('value.mid', 0)
                            ->whereOr(
                                $this->expression()->whereGreaterOrEqual('value.min', $value),
                                $this->expression()->whereLessOrEqual('value.max', $value)
                                )
                        )
            );
    }
    
    public function optionTimeEquals($option, $value) {
        return $this->whereElemMatch(
                'deliveryOptions', 
                $this->expression()
                    ->where('option', $option)
                    ->where('comparison', SingleValueOption::COMPARISON_EQUALS)
                    ->whereOr(
                        $this->expression()->where('value.mid', 1)
                            ->whereOr(
                                $this->expression()->whereGreaterOrEqual('value.max', $value),
                                $this->expression()->whereLessOrEqual('value.min', $value)
                                ),
                        $this->expression()->where('value.mid', 0)
                            ->whereGreaterOrEqual('value.max', $value)
                            ->whereLessOrEqual('value.min', $value)
                        )
            );
    }
}
