<?php
/**
 * A AR class that will automatically manage these fields in tables that
 * decend from it:
 *   - create_time: TIMESTAMP, Time record was created
 *   - create_user: INT, User id who created the record
 *   - update_time: TIMESTAMP, Time record was last updated
 *   - update_user: INT, User id who updated the record
 */
abstract class AuditActiveRecord extends CActiveRecord
{
    /**
     * Prepares create_time, create_user_id, update_time and update_user_id
     * attributes before performing validation.
     */
    protected function beforeValidate()
    {
        $now = new CDbExpression('NOW()');
        $id = Yii::app()->user->id;

        if($this->isNewRecord)
        {
            // set the create date, last updated date and the user doing the creating
            $this->create_time = $now;
            $this->create_user = $id;
        }

        // Either way, set the last updated time and last updated user id
        $this->update_time = $now;
        $this->update_user = $id;

        return parent::beforeValidate();
    }
}