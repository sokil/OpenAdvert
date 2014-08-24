<?php

namespace MongoAdvertDb\Banners\Banner;

class TextPopupBanner extends \MongoAdvertDb\Banners\Banner
{
    public function beforeConstruct() {
        
        parent::beforeConstruct();
        
        $this->_data = array_merge($this->_data, array(
            'text' => null,
        ));
    }
    
    public function rules()
    {
        return array_merge(parent::rules(), array(
            array('text', 'required'),
        ));
    }
    
    public function getText()
    {
        return $this->get('text');
    }
    
    public function setText($text)
    {
        $this->set('text', trim($text));
        return $this;
    }
}