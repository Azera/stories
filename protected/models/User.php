<?php

/**
 * This is the model class for table "user".
 *
 * The followings are the available columns in table 'user':
 * @property string $id
 * @property string $username
 * @property string $email
 * @property integer $status_id
 * @property string $salt
 * @property string $password
 * @property string $activkey
 * @property integer $login_failures
 * @property string $locked_until
 * @property string $create_time
 * @property string $lastlogin_time
 * @property integer $is_admin
 */
class User extends CActiveRecord
{

    // This holds the unsalted key that is required to activate a
    // new account
    protected $unsaltedkey = NULL;

    const STATUS_NEW        = 1; // New account
    const STATUS_LOCKED     = 2; // Banned account
    const STATUS_INACTIVE   = 3; // Stale account to be re-activated
    const STATUS_ACTIVE     = 9; // Active account

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return User the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'user';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('username, email', 'required'),
            array('username', 'match', 'pattern' => '/^[A-Za-z][A-Za-z0-9_]+$/u', 'message' => 'Invalid username. Username must start with a letter, and only contain letters and numbers.'),
            array('username', 'length', 'min' => 5, 'max' => 20),
            array('username, email', 'unique'),
            array('email', 'email'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'username' => 'Username',
            'email' => 'Email',
            'status_id' => 'Status',
            'salt' => 'Salt',
            'password' => 'Password',
            'activkey' => 'Activkey',
            'login_failures' => 'Login failures',
            'locked_until' => 'Locked until',
            'create_time' => 'Create Time',
            'lastlogin_time' => 'Lastlogin Time',
            'is_admin' => 'Is Admin',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('username', $this->username, true);
        $criteria->compare('email', $this->email, true);

        return new CActiveDataProvider($this, array(
                    'criteria' => $criteria,
                ));
    }

    public function scopes()
    {
        return array(
            'active' => array(
                'condition' => 'status_id=' . self::STATUS_ACTIVE,
            ),
            'administrators' => array(
                'condition' => 'is_admin=1',
            ),
        );
    }

    public static function getOptions($id, $value = NULL)
    {
        $options = array(
            'is_admin' => array(
                '0' => 'No',
                '1' => 'Yes',
            ),
            'status_id' => array(
                self::STATUS_NEW        => 'New',
                self::STATUS_LOCKED     => 'Locked',
                self::TATUS_INACTIVE    => 'Inactive',
                self::STATUS_ACTIVE     => 'Active',
            ),
        );

        if (!isset($options[$id]))
            throw new CException('Invalid option id "' . $id . '" specified');
        if ($value == NULL)
            return $options[$id];
        if (!isset($options[$id][$value]))
            throw new CException('Invalid value "' . $value . '" specified for option "' . $id . '"');
        return $options[$id][$value];
    }

    /**
     * Instead of inheriting from AuditActiveRecord, we implement our own,
     * because only have one field to take care of, instead of 4
     */
    protected function beforeValidate()
    {
        if ($this->isNewRecord)
        {
            $this->create_time = new CDbExpression('NOW()');
            $this->status_id = self::STATUS_NEW;
        }
        return parent::beforeValidate();
    }

    /**
     * @return encrypted string.
     */
    public static function encrypt($string = '')
    {
        return md5($string);
    }

    // A function to generate a salt value and activation code
    public static function randomString($length = 32)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $return = '';
        $totalChars = strlen($characters) - 1;
        for ($i = 0; $i < 32; ++$i)
        {
            $return .= $characters[rand(0, $totalChars)];
        }
        return $return;
    }

}