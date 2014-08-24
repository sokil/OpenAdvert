<?php
class AddUserCommand extends CConsoleCommand
{

    /**
     * @param $name
     * @param $email
     * @param $password
     * @param $role
     * @param string $phone
     */
    public function actionIndex($name, $email, $password, $role = 'manager', $phone = '')
    {
        $mongo = Yii::app()->mongo;

        $data = array(
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'phone' => $phone
        );
        $model = new UserForm('register');
        $model->attributes = $data;

        if ($model->validate()) {
            $users = $mongo->getCollection('users');
            $user = $users->createDocument($data)
                ->setPassword($password)
                ->setRole($role);
            $users->saveDocument($user);
            echo "New user $email has been added.\n\r";
        } else {
            foreach ($model->errors as $field => $errors) {
                echo "error $field : ";
                foreach ($errors as $error) {
                    echo $error."\n\r";
                }
            }
        }
    }
    public function getHelp()
    {
        echo "Params for AddUser Command:\n\r";
        echo "name*\n\r";
        echo "email*\n\r";
        echo "password*\n\r";
        echo "role [ manager* | advertiser | partner ]\n\r";
        echo "phone (not required)\n\r";
    }
}