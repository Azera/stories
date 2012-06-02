<?php

class AccountController extends Controller
{

    /**
     * Declares class-based actions.
     */
    public function actions()
    {
        return array(
            // captcha action renders the CAPTCHA image displayed on the contact page
            'captcha' => array(
                'class' => 'CCaptchaAction',
                'backColor' => 0xFFFFFF,
            ),
        );
    }

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            // ALL users
            array('allow',
                'actions' => array('captcha', 'register', 'activate', 'recover', 'login'),
                'users' => array('*'),
            ),
            // authenticated users
            array('allow',
                'actions' => array('profile', 'logout'),
                'users' => array('@'),
            ),
            // deny all users
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    /**
     * Register a new account.
     */
    public function actionRegister()
    {
        $model = new AccountRegisterForm;

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['AccountRegisterForm']))
        {
            // The only fields that are supposed to be set safe
            // are username and email
            $model->attributes = $_POST['AccountRegisterForm'];
            if ($model->validate() && $model->save())
            {

                // Send activation email with the unsalted $activkey
                $act_url = $this->createAbsoluteUrl('/account/activate', array(
                    'user' => $model->username,
                    'key' => $model->unsaltedkey
                        ));

                // TODO: Get email from template, and use PHPmailer or something
                $adminEmail = Yii::app()->params['adminEmail'];
                $headers = "MIME-Version: 1.0\r\nFrom: $adminEmail\r\nReply-To: $adminEmail\r\nContent-Type: text/html; charset=utf-8";
                $subject = 'Account activation for ' . Yii::app()->name;
                $message = "Please activate your account by clicking on the following link:\r\n$act_url";

                mail(
                        $model->email, '=?UTF-8?B?' . base64_encode($subject) . '?=', str_replace("\n.", "\n..", wordwrap($message, 70)), $headers
                );

                Yii::app()->user->setFlash('success', 'Thank you for your registration. Please check your email for instructions to activate your account.');
                $this->redirect(array('/site/index'));
            }
        }

        $this->render('register', array(
            'model' => $model,
        ));
    }

    /**
     * Activate a new account
     * @param string $username the username of the account to activate
     * @param string $key the activation key for the account
     */
    public function actionActivate($user, $key)
    {
        // user must exist
        $user = User::model()->findByAttributes(array(
            'username' => $user,
        ));

        // Request is invalid if:
        //   - User does not exist
        //   - status_id is ACTIVE
        //   - status_id is LOCKED
        //   - activationcode mismatch
        // echo User::encrypt($user->salt . $key) .' vs '. $user->activkey; exit;
        if (
                (NULL == $user) or
                (User::STATUS_LOCKED == $user->status_id) or
                (User::STATUS_ACTIVE == $user->status_id) or
                (User::encrypt($user->salt . $key) !== $user->activkey)
        )
        {
            $this->render('activateerror');
            return;
        }

        $user->status_id = User::STATUS_ACTIVE;
        $user->activkey = NULL;
        // We also need to clear the login date to prevent an
        // "endless loop" when activation was triggered by a stale
        // account
        $user->lastlogin_time = NULL;
        $user->save();

        Yii::app()->user->setFlash('success', 'Your account has been activated. You can now login.');
        $this->redirect(array('/site/index'));
    }

    /**
     * Displays the login page
     */
    public function actionLogin()
    {
        $model = new LoginForm;

        // if it is ajax validation request
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        // collect user input data
        if (isset($_POST['LoginForm']))
        {
            $model->attributes = $_POST['LoginForm'];
            // validate user input and redirect to the previous page if valid
            if ($model->validate() && $model->login())
                $this->redirect(Yii::app()->user->returnUrl);
        }
        // display the login form
        $this->render('login', array('model' => $model));
    }

    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout()
    {
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->homeUrl);
    }

    /**
     * Change password.
     * If user is logged in, ask for current, and new password (twice)
     * if user is not logged in, and a validation code is given,
     *     if code is valid, ask for new password (twice)
     * Else
     *     Ask for username or email, and send validation code to it
     *     Respect banned ccounts etc.
     * End
     */
    // Returns boolean
    private function validateKey($username, $key)
    {
        $user = User::findByAttributes(array(
            'username' => $username,
        ));
        if (NULL == $user)
            return false;

        // Only accounts that are currently active can reset password
        if (User::STATUS_ACTIVE != $user->status_id)
            return false;

        // Validate the key
        if (User::encrypt($user->salt . $key) != $user->activkey)
            return false;
    }

    public function actionRecover()
    {
        $username = Yii::app()->request->getQuery('user', NULL);
        $key = Yii::app()->request->getQuery('key', NULL);
        if (NULL != $key && NULL != $username)
        {
            // Validate key
            if (!$this->validateKey($username, $key))
                throw new CHttpException(400, 'Invalid request');

            $model = new RecoverPasswordForm;

            // collect user input data
            if (isset($_POST['RecoverPasswordForm']))
            {
                $model->attributes = $_POST['RecoverPasswordForm'];
                // validate user input and redirect to the previous page if valid
                if ($model->validate() && $model->storePassword())
                {
                    Yii::app()->user->setFlash('success', 'Your password has been saved. You can now login.');
                    $this->redirect(Yii::app()->homeUrl);
                }
            }

            // display the recoverpassword form
            $this->render('recoverpassword', array('model' => $model));
        } else
        {
            // display the recover form
            $model = new AccountRecoverForm;

            // collect user input data
            if (isset($_POST['AccountRecoverForm']))
            {
                $model->attributes = $_POST['AccountRecoverForm'];
                // validate user input and redirect to the previous page if valid
                if ($model->validate())
                {

                    $unsaltedkey = User::randomString();
                    $model->user->activkey = User::encrypt($model->user->salt . $unsaltedkey);
                    $model->user->save();

                    $this->sendEmail(
                            $model->user->username, $model->user->email, 'Password recovery', 'mail/pwrecovery', array(
                        'link' => $this->createAbsoluteUrl('/account/recoverpass', array(
                            'user' => $model->user->username,
                            'key' => $unsaltedkey
                        )),
                            )
                    );

                    Yii::app()->user->setFlash('success', 'Instructions to reset your password have been sent to your email address.');
                    $this->redirect(Yii::app()->homeUrl);
                }
            }

            // display the recover form
            $this->render('recover', array('model' => $model));
        }
    }

}