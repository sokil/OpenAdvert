<?php

class UsersController extends Controller
{

    public function accessRules()
    {
        $rules = parent::accessRules();
        array_unshift($rules, array('allow',
            'actions' => array('login'),
            'users'   => array('*'),
        ));
        return $rules;
    }

    public function actionIndex()
    {
        if (!Yii::app()->user->checkAccess('manageUser')) {
            Yii::app()->user->loginRequired();
        }

        $response = array();

        $userSearch = Yii::app()->mongo
            ->getCollection('users')
            ->findAsArray()
            ->notDeleted()
            ->sort(array('name' => 1));

        /**
         * Users of advertiser
         */
        if ( Yii::app()->user->getProfile()->isAdvertiserManager() ) {

            $advertiserId = Yii::app()->user
                ->getProfile()->advertiser;
            
            $advertiser = Yii::app()->mongo
                ->getCollection('advertisers')
                ->getDocument($advertiserId);
            
            $userSearch->byAdvertiser($advertiser);

        /**
         * Users of partner
         */
        } elseif ( Yii::app()->user->getProfile()->isPartnerManager() ) {

            $partnerId = Yii::app()->user
                ->getProfile()->partner;
            
            $partner = Yii::app()->mongo
                ->getCollection('partners')
                ->getDocument($partnerId);
            
            $userSearch->byPartner($partner);

        /**
         * All users
         */
        } elseif ( Yii::app()->user->getProfile()->isManager() ) {

            // get tab
            $filter = (array) $this->request->getParam('filter');
            if(empty($filter['role'])) {
                $filter['role'] = \MongoAdvertDb\Users\User::ROLE_MANAGER;
            }
            
            $response['filter'] = $filter;
            
            // advertisers tab
            if (\MongoAdvertDb\Users\User::ROLE_ADVERTISER == $filter['role']) {
                
                $response['groupList'] = iterator_to_array(Yii::app()->mongo->getCollection('advertisers')->getActiveAdvertisersList());
                
                if(isset($filter['advertiser'])) {
                    $advertiser = Yii::app()->mongo
                        ->getCollection('advertisers')
                        ->getDocument($filter['advertiser']);

                    $response['currentGroup'] = $advertiser;
                    
                    $userSearch->byAdvertiser($advertiser);
                } else {
                    $userSearch->byAdvertiserRole();
                }
                

            // partner tab
            } elseif(\MongoAdvertDb\Users\User::ROLE_PARTNER == $filter['role']) {
                
                $response['groupList'] = iterator_to_array(Yii::app()->mongo->getCollection('partners')->getActivePartnersList());
                
                if(isset($filter['partner'])) {
                    $partner = Yii::app()->mongo
                        ->getCollection('partners')
                        ->getDocument($filter['partner']);

                    $response['currentGroup'] = $partner;
                    
                    $userSearch->byPartner($partner);
                }
                else {
                    $userSearch->byPartnerRole();
                }
                
            
            // manager tab
            } else {
                $userSearch->byManagerRole();
            }
        }

        /**
         * Render
         */        
        $response['users'] = new \Sokil\Mongo\Yii\DataProvider($userSearch, array(
            'attributes' => array('name', 'email'),
        ));

        if (Yii::app()->request->isAjaxRequest ) {
            $this->renderPartial('listPartial', $response);
        } else {
            $this->render('list', $response);
        }
    }

    public function actionNew()
    {
        $this->forward('edit');
    }

    public function actionEdit($id = null)
    {
        if($id) {
            $user = \Yii::app()->mongo
                ->getCollection('users')
                ->getDocument($id);

            if(!$user) {
                throw new \Exception('User not found');
            }
            
            if(!$user->canBeManagedBy(Yii::app()->user)) {
                Yii::app()->user->loginRequired();
            }
        
        } else {
            $user = \Yii::app()->mongo
                ->getCollection('users')
                ->createDocument();
        }
        
        // advertiser
        if ( Yii::app()->user->getProfile()->isAdvertiserManager() ) {
            $advertiserId = Yii::app()->user->getProfile()->advertiser;
        } else {
            $advertiserId = $this->request->getParam('advertiser');
        }

        // partner
        if ( Yii::app()->user->getProfile()->isPartnerManager() ) {
            $partnerId = Yii::app()->user->getProfile()->partner;
        } else {
            $partnerId = $this->request->getParam('partner');
        }

        $this->render('edit', array(
            'user' => $user,
            'advertiser' => $advertiserId ? $advertiserId : null,
            'partner' => $partnerId ? $partnerId : null,
        ));
    }
    
    public function actionSave()
    {
        try {
            $id = $this->request->getParam('id');

            if($id) {
                $user = \Yii::app()->mongo
                    ->getCollection('users')
                    ->getDocument($id);

                if(!$user) {
                    throw new \Exception('User not found');
                }
            } else {
                $user = \Yii::app()->mongo
                    ->getCollection('users')
                    ->createDocument();
                
                // set as advertiser manager
                if ( $this->request->getParam('advertiser') ) {
                    $advertiser = Yii::app()->mongo
                        ->getCollection('advertisers')
                        ->getDocument($this->request->getParam('advertiser'));

                    if(!$advertiser) {
                        throw new \Exception('Advertiser not found');
                    }

                    $user->setAdvertiser($advertiser);
                // set as partner manager
                } elseif ( $this->request->getParam('partner') ) {
                    $partner = Yii::app()->mongo
                        ->getCollection('partners')
                        ->getDocument($this->request->getParam('partner'));

                    if(!$partner) {
                        throw new \Exception('Partner not found');
                    }

                    $user->setPartner($partner);
                // set as manager
                } else {
                    $user->setRole('manager');                
                }
            }
            
            $user
                ->setName($this->request->getParam('name'))
                ->setPhone($this->request->getParam('phone'))
                ->setEmail($this->request->getParam('email'));
            
            
                
            // password
            if ($this->request->getParam('password')) {
                $user->setPassword($this->request->getParam('password'));
            }
            
            $user->save();
            $this->response->id = (string) $user->getId();
            $this->response->successMessage = _('Saved successfully');            
        } catch(\Sokil\Mongo\Document\Exception\Validate $e) {            
            $this->response->invalidated = $e->getDocument()->getErrors();
            $this->response->raiseError();
        } catch (\Exception $e) {
            $this->response->raiseError($e);
        }
        
        $this->response->sendJson();
    }

    public function actionDelete($id)
    {
        try {
            $user = Yii::app()->mongo
                ->getCollection('users')
                ->getDocument($id);
            
            if(!$user || !$user->canBeManagedBy(Yii::app()->user)) {
                throw new \Exception('User not found');
            }
            
            $user
                ->setDeleted()
                ->save();
            
        } catch (Exception $e) {
            $this->response->raiseError($e);
        }

        if ($this->request->getIsAjaxRequest()) {
            $this->response->sendJson();
        } else {
            $this->redirect(array('index'));
        }
    }

    public function actionLogin()
    {
        if (empty($_POST['LoginForm'])) {
            $this->render('login');
            exit;
        }

        $loginForm = new \LoginForm;
        $loginForm->attributes = $_POST['LoginForm'];
        
        if ($loginForm->validate() && $loginForm->login()) {
            $this->redirect('/');
        } else {
            $this->render('login', array(
                'errors' => $loginForm->getErrors(),
            ));
        }
    }

    public function actionLogout()
    {
        Yii::app()->user->logout();
        $this->redirect('/login');
    }
    
    public function actionHash()
    {
        $password = $this->request->getParam('p');
        if (!$password) {
            throw new \Exception('Password not specified');
        }

        $salt = $this->request->getParam('s');
        if (!$salt) {
            $salt = uniqid();
        }

        $hash = \MongoAdvertDb\Users\User::getPasswordHash($password, $salt);

        echo json_encode([
            'password' => $password,
            'salt'     => $salt,
            'hash'     => $hash,
        ]);
    }
}
