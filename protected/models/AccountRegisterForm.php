<?php

/**
 * AccountRegisterForm class.
 * The data structure for keeping registration form data
 * It is used by the 'register' action of 'AccountController'.
 */
class AccountRegisterForm extends User
{
        public $password1;
        public $password2;
        public $verifyCode;

        public $unsaltedkey;

        /**
         * Declares the validation rules.
         * The rules state that username and password are required,
         * and password needs to be authenticated.
         */
        public function rules()
        {
                return array_merge(parent::rules(), array(
                        array('password1', 'length', 'min'=>6),
                        array('password1, password2', 'required'),
                        array('password2', 'compare', 'compareAttribute'=>'password1', 'message' => 'Passwords do not match'),
                        array('verifyCode', 'captcha', 'allowEmpty'=>!CCaptcha::checkRequirements()),
                ));
        }

        /**
         * Declares attribute labels.
         */
        public function attributeLabels()
        {
                return array_merge(parent::attributeLabels(), array(
                        'password1' => 'Password',
                        'password2' => 'Re-enter password',
                ));
        }

        /**
         * Override save method to a new account record
         * returns the number of records actually saved
         */
        public function save($runValidation=true,$attributes=NULL)
        {
                // Generate new salt and activation key
                $this->salt = self::randomString();
                $this->unsaltedkey = self::randomString();
                $this->activkey = self::encrypt($this->salt . $this->unsaltedkey);
                $this->password = self::encrypt($this->salt . $this->password1);
                return parent::save($runValidation, $attributes);
        }
}