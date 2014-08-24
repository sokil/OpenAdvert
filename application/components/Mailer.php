<?php

class Mailer extends CApplicationComponent
{
    public $template;
    public $host;
    public $port;
    public $SMTPSecure;
    public $username;
    public $password;
    public $fromEmail;
    public $fromName;
    public $reply;
    public $SMTPAuth = false;
    public $debug = 0;

    private $_mailer;

    public function init()
    {
        $this->_mailer = new PHPMailer();
        $this->_mailer->isSMTP();
        
        // connection params
        $this->_mailer->Host = $this->host;
        $this->_mailer->Port = $this->port;
        $this->_mailer->SMTPSecure = $this->SMTPSecure;
        
        // mail params
        $this->_mailer->CharSet = 'UTF-8';
        
        // auth
        if($this->SMTPAuth) {
            $this->_mailer->SMTPAuth = true;
            $this->_mailer->Username = $this->username;
            $this->_mailer->Password = $this->password;
        }
        
        // recipient and sender params
        $this->_mailer->setFrom($this->fromEmail, $this->fromName);
        $this->_mailer->addReplyTo($this->reply);
        
        // debug
        $this->_mailer->SMTPDebug = $this->debug;
        $this->_mailer->Debugoutput = 'error_log';
    }

    public function send($view, array $dataProvider = array())
    {
        $this->_mailer->MsgHTML( Yii::app()->controller->renderPartial($this->template.".".$view, $dataProvider, true) );
        if (!$this->_mailer->send()) {
            Yii::log($this->_mailer->ErrorInfo, CLogger::LEVEL_ERROR, 'Mailer');
        }
        
        return $this;
    }

    public function addAddress($address, $name = '')
    {
        $this->_mailer->addAddress($address, $name);
        return $this;
    }

    public function setSubject($subject)
    {
        $this->_mailer->Subject = $subject;
        return $this;
    }

    /**
     * @return array
     */
    public function getAdminsAddressList()
    {
        $users = Yii::app()->mongo
            ->getCollection('users')
            ->findAsArray()
            ->notDeleted()
            ->noAdvertiser()
            ->sort(array('name' => 1));

        $result = CHtml::listData($users, 'email', 'name');

        return $result;
    }


    /**
     * @param array $data list of user's data: array(email => name)
     */
    public function addAddressList(array $data)
    {
        foreach ($data as $email => $name ) {
            $this->addAddress($email, $name );
        }
    }

}