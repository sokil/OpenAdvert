<?php

class AdvertisersController extends Controller {

    public function actionIndex()
    {
        if(!Yii::app()->user->checkAccess('manageAdvertiser')) {
            Yii::app()->user->loginRequired();
        }
        
        $advertisers = Yii::app()->mongo->getCollection('advertisers');
        
        $this->render('index', array(
            'advertisers' => $advertisers
                ->find()
                ->notDeleted()
                ->sort(array(
                    'name'  => 1,
                ))
        ));
    }

    public function actionNew() {
        $model = new AdvertiserForm();
        if (isset($_POST['AdvertiserForm'])) {
            $model->attributes = $_POST['AdvertiserForm'];
            if ($model->validate()) {
                $advertisers = Yii::app()->mongo->getCollection('advertisers');
                $advertiser = $advertisers->createDocument($_POST['AdvertiserForm']);
                $advertisers->saveDocument($advertiser);
                $this->redirect(array('index'));
            }
        }
        $this->render('form', array('model' => $model));
    }

    public function actionEdit($id) {
        $advertisers = Yii::app()->mongo->getCollection('advertisers');
        $advertiser = $advertisers->getDocument($id);

        $model = new AdvertiserForm();

        if (isset($_POST['AdvertiserForm'])) {
            $model->attributes = $_POST['AdvertiserForm'];
            if ($model->validate()) {
                $advertiser
                        ->setName($model->name)
                        ->setPhone($model->phone)
                        ->setEmail($model->email)
                        ->setAddress($model->address);
                $advertisers->saveDocument($advertiser);
                $this->redirect(array('index'));
            }
        }

        $model->attributes = array(
            'name' => $advertiser->getName(),
            'phone' => $advertiser->getPhone(),
            'email' => $advertiser->getEmail(),
            'address' => $advertiser->getAddress(),
        );
        $this->render('form', array('model' => $model));
    }

    public function actionDelete($id) {
        try {
            $advertiser = Yii::app()->mongo->getCollection('advertisers')->getDocument($id);
            if (!$advertiser) {
                throw new \Exception('Advertiser not found');
            }

            $advertiser->setDeleted()->save();

        } catch (Exception $e) {
            $this->response->raiseError($e);
        }
        
        $this->response->sendJson();
    }
    
    public function actionActivate($id) {
        
        try {
            $advertiser = Yii::app()->mongo->getCollection('advertisers')->getDocument($id);
            if (!$advertiser) {
                throw new \Exception('Advertiser not found');
            }

            $advertiser->setActive()->save();

        } catch (Exception $e) {
            $this->response->raiseError($e);
        }
        
        $this->response->sendJson();
    }

}
