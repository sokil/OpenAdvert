<?php

namespace MongoAdvertDb\Banners\BannerForm;

/**
 * @method \MongoAdvertDb\Banners\Banner\CreepingLineBanner getBanner() get banner, relative to this form
 */
class CreepingLineBannerForm extends \MongoAdvertDb\Banners\BannerForm
{
    public $text;
    
    public function rules()
    {
        return array_merge(parent::rules(), array(
            array('text', 'required', 'message' => _('This field required')),
        ));
    }
    
    protected function applyAttributes()
    {
        $this->getBanner()->setText($this->text);
    }
}