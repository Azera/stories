<?php

/**
 * PasswordForm class.
 * PasswordForm is the data structure for keeping
 * user change password form data. It is used by the 'changepass' action of 'AccountController'.
 */
class PassowrdForm extends CFormModel
{
	public $oldpass;
	public $newpass1;
	public $newpass2;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			// username and password are required
			array('oldpass, newpass1, newpass2', 'required'),

            array('newpass1', 'length', 'min'=>6),
            array('newpass2', 'compare', 'compareAttribute'=>'newpass1', 'message' => 'Passwords do not match'),

            // oldpass needs to be authenticated
			array('oldpass', 'authenticate'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'oldpass'=>'Current password',
			'newpass1'=>'New password',
			'newpass2'=>'Repeat',
		);
	}

	/**
	 * Authenticates the password.
	 * This is the 'authenticate' validator as declared in rules().
	 */
	public function authenticate($attribute,$params)
	{
		if(!$this->hasErrors())
		{
            $user = User::model()->findByPk(Yii::app()->user->id);
            $encPass = User::encrypt($user->salt . $this->oldpass);
            if($user->password != $encPass)
				$this->addError('oldpass','Current password is incorrect.');
		}
	}
   
    /**
     * Update current user record with new password.
     * No need to generate a new salt, though it won't do any harm...
     */
    public function save()
    {
        $user = User::model()->findByPk(Yii::app()->user->id);
        $user->password = User::encrypt($user->salt . $this->newpass1);
        return $user->save();
    }
}
