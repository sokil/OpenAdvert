<?php

class Response extends CApplicationComponent
{
    private $_params = array(
        'error' => 0,
    );
    
    public function __set($name, $value)
    {
        $this->setParam($name, $value);
    }
    
    public function __get($name)
    {
        return $this->getParam($name);
    }
    
    public function setParam($name, $value)
    {
        $this->_params[$name] = $value;
        
        return $this;
    }
    
    public function addParams(array $params)
    {
        $this->_params = array_merge($this->_params, $params);
        
        return $this;
    }
    
    public function getParam($name)
    {
        return isset($this->_params[$name]) ? $this->_params[$name] : null;
    }
    
    public function getParams($names = null)
    {
        if(!$names)
            return $this->_params;
        
        return array_intersect_key($this->_params, array_flip($names));
    }
    
    public function getErrorMessage()
    {
        return $this->getParam('errorMessage');
    }
    
    public function getSuccessMessage()
    {
        return $this->getParam('successMessage');
    }
    
    public function hasError()
    {
        return $this->getParam('error') == 1;
    }
    
    public function raiseError($errorMessage = null)
    {
        if(!$errorMessage)
        {
            $errorMessage = _('Error occured');
        }
        
        else if($errorMessage instanceof Exception)
        {
            $errorMessage = $errorMessage->getMessage();
        }
        
        return $this
            ->setParam('error', 1)
            ->setParam('errorMessage', $errorMessage);
    }
    
    public function raiseSuccess($successMessage = null)
    {
        if(!$successMessage)
        {
            $successMessage = _('Operation successful');
        }
        
        else if($successMessage instanceof Exception)
        {
            $successMessage = $successMessage->getMessage();
        }
        
        return $this
            ->setParam('error', 0)
            ->setParam('successMessage', $successMessage);
    }
    
    public function sendJson()
    {
        // set json header
        Header('Content-type: application/json');
        
        // send data
        echo json_encode($this->_params);
    }
}