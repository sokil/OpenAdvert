<?php

class ApiModule extends CWebModule
{
    public $layout = false;
    
    public function __construct()
    {
        $this->_checkSign();
    }
    
    /**
     * Auth by signing every request
     */
    private function _checkSign()
    {
        /* @var $request CHttpRequest */
        $request = Yii::app()->request;
        
        // Get crypt key
        $applicationId = $request->getParam('app_id');
        if(!$applicationId) {
            Header('HTTP/1.0 403 Forbidden');
            exit;
        }
        
        // get crypt key from app
        try {
            $cryptKey = Yii::app()->mongo
                ->getCollection('apikeys')
                ->getDocument($applicationId);
            
            if(!$cryptKey) {
                Header('HTTP/1.0 403 Forbidden');
                exit;
            }
            
        } catch (\Exception $e) {
            Header('HTTP/1.0 403 Forbidden');
            exit;
        }
        
        
        // Get message sign
        $sign = $request->getParam('sign');
        if(!$sign) {
            Header('HTTP/1.0 403 Forbidden');
            exit;
        }
        
        // Prepare body to hash
        if($request->getIsPostRequest()) {
            $body = $request->getRawBody();
        } else {
            $body = $_GET;
            unset($body['sign']);
            ksort($body);
            $body = http_build_query($body);
        }
        
        // calculate sign
        if($sign !== hash_hmac('sha1', $body, $cryptKey)) {
            Header('HTTP/1.0 403 Forbidden');
            exit;
        }
    }
}