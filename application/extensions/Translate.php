<?php

class Translate extends CApplicationComponent
{    
    public $supportedLanguages = array();
    
    public $defaultLanguage;
    
    private $_language;
    
    private $_browserLanguages;
    
    /**
     * init translate engine if need
     */
    public function initGettext($domain, $path)
    {        
        // set locale
        $localeName = $this->supportedLanguages[$this->getLanguage()];
        //echo $localeName;
        putenv('LC_ALL=' . $localeName);
        setlocale(LC_ALL, $localeName);
        //die();
        
        // init gettext engine
       bindtextdomain($domain, $path);
       bind_textdomain_codeset($domain, 'UTF-8');
       textdomain($domain);
    }
    
    public function getSystemLanguages()
    {
        return $this->supportedLanguages;
    }
    
    /**
     * get system language
     * 
     * @staticvar string $_language
     * @return string language used in system
     */
    public function getLanguage()
    {
        if($this->_language !== null) {
            return $this->_language;
        }
        
        // get from cookie
        $cookieLanguage = $this->getCookieLanguage();
        if($cookieLanguage && $this->isAllowedLanguage($cookieLanguage)) {
            $this->_language = $cookieLanguage;
            return $this->_language;
        }
        
        // get from Accept_Language header      
        $browserSupportedLanguages = $this->getSupportedBrowserAcceptLanguages();
        if(!empty($browserSupportedLanguages))
        {
            $_language = key($browserSupportedLanguages);
            return $_language;
        }
        
        // get default language
        $this->_language = $this->defaultLanguage;
        return $this->_language;
    }
    
    public function setLanguage($lang)
    {
        if(!$this->isAllowedLanguage($lang)) {
            throw new Exception('Language not supported');
        }
        
        $this->_language = $lang;
        
        $this->setCookieLanguage($lang);
        
        return $this;
    }
    
    public function isAllowedLanguage($lang)
    {
        return array_key_exists($lang, $this->getSystemLanguages());
    }
    
    /**
     * get all accepted languages from browser
     * 
     * @license based on code of Zend Framework method Zend_Locale::getBrowser()
     * @staticvar array $_browserAacceptLanguages
     * @return array array of accepted languages with qualities
     */
    public function getBrowserAcceptLanguages()
    {
        if($this->_browserLanguages !== null) {
            return $this->_browserLanguages;
        }

        $this->_browserLanguages = array();
        if(empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $this->_browserLanguages = array();
            return $this->_browserLanguages;
        }
        
        $accepted = preg_split('/,\s*/', $_SERVER['HTTP_ACCEPT_LANGUAGE']);

        foreach ($accepted as $accept)
        {
            $result = preg_match('/^([a-z]{1,8}(?:[-_][a-z]{1,8})*)(?:;\s*q=(0(?:\.[0-9]{1,3})?|1(?:\.0{1,3})?))?$/i', $accept, $match);

            if ($result < 1) {
                continue;
            }

            if (isset($match[2]) === true) {
                $quality = (float) $match[2];
            } else {
                $quality = 1.0;
            }

            $countrys = explode('-', $match[1]);
            $region   = array_shift($countrys);

            $country2 = explode('_', $region);
            $region   = array_shift($country2);

            foreach ($countrys as $country) {
                $this->_browserLanguages[$region . '_' . strtoupper($country)] = $quality;
            }

            foreach ($country2 as $country) {
                $this->_browserLanguages[$region . '_' . strtoupper($country)] = $quality;
            }

            if ((isset($this->_browserLanguages[$region]) === false) || ($this->_browserLanguages[$region] < $quality)) {
                $this->_browserLanguages[$region] = $quality;
            }
        }
        
        return $this->_browserLanguages;
    }
    
    /**
     * get only supported langs from allowed by browser
     * @staticvar string $_languages
     * @return array list of languages
     */
    public function getSupportedBrowserAcceptLanguages()
    {
        return array_intersect_key(
            $this->getBrowserAcceptLanguages(),
            $this->supportedLanguages
        );
    }
    
    /**
     * get language stored into cookie
     */
    public function getCookieLanguage()
    {
        return  isset($_COOKIE['lang']) ? $_COOKIE['lang'] : null;
    }
    
    public function setCookieLanguage($lang)
    {
        setcookie('lang', $lang, time() + 60 * 60 * 24 * 365, '/');
    }
    
}