<?php

/**
 * AccountRecoverForm class.
 * AccountRecoverForm is the data structure for keeping
 * account recovery form data.
 * It is used by the 'recover' action of 'AccountController'.
 */
class AccountRecoverForm extends CFormModel
{
        public $name; // or email address
        public $verifyCode;

        public $user = NULL;

        /**
         * Declares the validation rules.
         */
        public function rules()
        {
                return array(
                        array('name', 'required'),
                        array('name', 'validAccount'),
                        array('verifyCode', 'captcha', 'allowEmpty'=>!CCaptcha::checkRequirements()),
                );
        }

        public function validAccount()
        {
                if(strpos($this->name, '@') === false)
                        $this->user = User::model()->findByAttributes(array(
                                'username'=>$this->name,
                        ));
                else
                        $this->user = User::model()->findByAttributes(array(
                                'email'=>$this->name,
                        ));

                if(NULL==$this->user)
                        $this->addError('name','No account was found with the information you provided.');
        }

        /**
         * Declares customized attribute labels.
         * If not declared here, an attribute would have a label that is
         * the same as its name with the first letter in upper case.
         */
        public function attributeLabels()
        {
                return array(
                        'name'=>'Username or email address',
                        'verifyCode'=>'Verification Code',
                );
        }
}