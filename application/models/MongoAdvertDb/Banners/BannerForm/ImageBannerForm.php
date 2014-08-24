<?php

namespace MongoAdvertDb\Banners\BannerForm;

/**
 * @method \MongoAdvertDb\Banners\Banner\CreepingLineBanner getBanner() get banner, relative to this form
 */
class ImageBannerForm extends \MongoAdvertDb\Banners\BannerForm
{
    public $imageUrl;
    
    public function rules()
    {
        return array_merge(parent::rules(), array(
            array('imageUrl', 'required', 'message' => _('This field required')),
        ));
    }
    
    protected function applyAttributes()
    {
        $this->getBanner()->setImageUrl($this->imageUrl);
    }
}