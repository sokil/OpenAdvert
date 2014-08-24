<?php

class PhpAuthManager extends CPhpAuthManager {

    public function init()
    {
        parent::init();
        
        if (!Yii::app()->user->isGuest) {
            $this->assign(Yii::app()->user->role, Yii::app()->user->id);
        }
    }

}