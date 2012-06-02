<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	/**
	 * Reasons a login can fail....
	 */
	const ERROR_ACCOUNT_NEW = 4;
	const ERROR_ACCOUNT_LOCKED = 5;
	const ERROR_ACCOUNT_INACTIVE = 6;
	const ERROR_ACCOUNT_TEMPLOCK = 7;
	const ERROR_ACCOUNT_STALE = 8;

	/**
	 * If a user has not logged in for over this amount of time, they
	 * will have to re-activate their account.
	 * 180 days = 180*24*60*60 = 15552000
	 */
	const TIME_ACCOUNT_STALE = 15552000;

	/**
	 * If a user fails authentication 3 time in a row, their account
	 * will be locked for this amount of time.
	 * 3 hours = 3*60*60 = 10800
	 */
	const TIME_ACCOUNT_LOCKOUT = 10800;

	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate()
	{
		$user = User::model()->findByAttributes(array(
			'username' => $this->username,
		));
		if (NULL == $user)
		{
			$this->errorCode = self::ERROR_PASSWORD_INVALID;
			return false;
		}

		// Convert timestamps to integers so we can calculate with them
		$now = time();
		// This is "3 hours from now"
		$newLockTime = time() + self::TIME_ACCOUNT_LOCKOUT;
		// This is "180 days ago"
		$staleTime = time() - self::TIME_ACCOUNT_STALE;

		// Expire lock
		if ((NULL != $user->locked_until) && (strtotime($user->locked_until) <= $now))
		{
			$user->login_failures = 0;
			$user->locked_until = NULL;
		}

		$isStale = (NULL != $user->lastlogin_time) && (strtotime($user->lastlogin_time) < $staleTime);
		$isLocked = (NULL != $user->locked_until);

		// A new account can not login. ever
		if (User::STATUS_NEW == $user->status_id)
			$this->errorCode = self::ERROR_ACCOUNT_NEW;

		// Same for banned account
		elseif (User::STATUS_LOCKED == $user->status_id)
			$this->errorCode = self::ERROR_ACCOUNT_LOCKED;

		// Same for otherwise inactive accounts
		elseif (User::STATUS_ACTIVE != $user->status_id)
			$this->errorCode = self::ERROR_ACCOUNT_INACTIVE;

		// If account is templocked, we reset the lock timer
		elseif ($isLocked)
		{
			$user->locked_until = date("Y-m-d H:i:s", $newLockTime);
			$this->errorCode = self::ERROR_ACCOUNT_TEMPLOCK;
		}

		// If password is invalid, we increase the counter.
		// when it reaches 3, we lock the account
		elseif (User::encrypt($user->salt . $this->password) !== $user->password)
		{
			$user->login_failures++;
			if ($user->login_failures >= 3)
			{
				$user->locked_until = date("Y-m-d H:i:s", $newLockTime);
			}
			$this->errorCode = self::ERROR_PASSWORD_INVALID;
		}

		// Ok, password is crrect. Now we only need to check if the
		// account has been stale for more than 180 days.
		elseif ($isStale)
		{
			$unsaltedkey = User::randomString();
			$user->activkey = User::encrypt($user->salt . $unsaltedkey);
			$user->status_id = User::STATUS_INACTIVE;
			// We also need to clear the login date to prevent an
			// "endless loop"
			$user->lastlogin_time = NULL;

			// Send activation email with the unsalted $activkey
			$act_url = Yii::app()->createAbsoluteUrl('/account/activate', array(
				'user' => $user->username,
				'key' => $unsaltedkey
					));

			$adminEmail = Yii::app()->params['adminEmail'];
			$headers = "MIME-Version: 1.0\r\nFrom: $adminEmail\r\nReply-To: $adminEmail\r\nContent-Type: text/html; charset=utf-8";
			$subject = 'Account re-activation for ' . Yii::app()->name;
			$message = "Please re-activate your account by clicking on the following link:\r\n$act_url";

			mail(
					$user->email, '=?UTF-8?B?' . base64_encode($subject) . '?=', str_replace("\n.", "\n..", wordwrap($message, 70)), $headers
			);

			$this->errorCode = self::ERROR_ACCOUNT_STALE;
		}

		// Rests nothing but to accept the login
		else
		{
			$user->login_failures = 0;
			$user->locked_until = NULL;
			$user->activkey = NULL;
			$user->lastlogin_time = new CDbExpression('NOW()');
			$this->errorCode = self::ERROR_NONE;
		}

		$user->save();
		return !$this->errorCode;
	}

}