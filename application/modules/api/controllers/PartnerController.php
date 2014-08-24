<?php

class PartnerController extends RestController
{    
    /**
     * Insert or update partner due to existance of "ref" param in request
     */
    public function actionSave()
    {
        try {
            $ref = Yii::app()->request->getParam('ref');

            $name = Yii::app()->request->getParam('name');

            $collection = Yii::app()->mongo->getCollection('partners');
            $partner = $collection->find()->byRef($ref)->findOne();

            if (!$partner) {
                $partner = $collection->createDocument();
                $partner->setRef($ref);
            }

            if ($name) {
                $partner->setName($name);
            }

            $collection->saveDocument($partner);
            $this->response->successMessage = _('Saved successfully');

        } catch(\Sokil\Mongo\Document\Exception\Validate $e) {
            $this->response->invalidated = $e->getDocument()->getErrors();
            $this->response->raiseError();
        } catch (\Exception $e) {
            $this->response->raiseError($e);
        }
        $this->response->sendJson();
    }

}