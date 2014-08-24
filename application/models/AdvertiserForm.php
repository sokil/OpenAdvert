<?php

class AdvertiserForm extends CFormModel {

    public $name;
    public $phone;
    public $email;
    public $address;

    public function rules() {
        return array(
            array('name', 'required', 'message' => _('This field required')),
            array('email', 'email', 'message' => _('Wrong E-mail')),
            array('address, phone', 'safe'),
        );
    }
    
    public function attributeLabels() {
        return array(
            'name'      => _('Name'),
            'phone'     => _('Phone'),
            'email'     => _('E-mail'),
            'address'   => _('Address'),
        );
    }

}
