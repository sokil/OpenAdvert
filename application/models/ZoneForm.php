<?php

class ZoneForm extends CFormModel {

    public $name;
    public $type;
    public $status;

    public function rules() {
        return array(
            array('name, type', 'required', 'message' => _('This field required')),
            array('status', 'safe'),
        );
    }

    public function attributeLabels() {
        return array(
            'name' => _('Name'),
            'type' => _('Type'),
            'status' => _('Status'),
        );
    }

    public static function getTypes() {
        return array(
            'creepingLine' => array(
                'name' => _('Creeping Line'),
                'icon' => 'glyphicon-text-width'
            ),
            'textPopup' => array(
                'name' => _('Text Popup'),
                'icon' => 'glyphicon-comment'
            ),
            'image' => array(
                'name' => _('Image'),
                'icon' => 'glyphicon-picture',
            ),
            'video' => array(
                'name' => _('Video'),
                'icon' => 'glyphicon-film',
            )
        );
    }

}
