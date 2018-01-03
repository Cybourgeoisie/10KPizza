<?php

declare(strict_types=1);

namespace Model;

class User extends \Scrollio\Model\AbstractModel
{

/**
 * Registration, Login, Logout
 **/
	public function register(string $email, string $password)
	{
		// Validate the email address
		if (!filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			throw new \Exception('Provided email address is invalid.');
		}

		// Validate that the email doesn't already exist
		if ($this->checkEmailExists($email))
		{
			throw new \Exception('Email address already exists.');
		}

		// Require that the password is at least 6 characters long
		if (strlen($password) < 6)
		{
			throw new \Exception('Password is too short - minimum 6 characters');
		}

		// Get a unique verification code
		$verification_code = strtoupper(\Scrollio\Utility\Utility::createObfuscatedString(24));
		while ($this->checkVerificationCodeExists($verification_code))
		{
			// Just run this shit again
			$verification_code = strtoupper(\Scrollio\Utility\Utility::createObfuscatedString(24));
		}

		// Create the new user
		$this->email             = $email;
		$this->original_email    = $email;
		$this->password          = $this->generatePassword($email, $password);
		$this->verification_code = $verification_code;
		$this->save();

		return true;
	}

	public function login(string $email, string $password)
	{
		// Validate the email address
		if (!filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			throw new \Exception('Provided email address is invalid.');
		}

		// Validate that the email exists
		if (!$this->checkEmailExists($email))
		{
			throw new \Exception('Email address was not found');
		}

		// Get the user by the email and password
		$result = $this->getByEmailPassword($email, $password);
		if (!$result)
		{
			throw new \Exception('Email and password do not match');
		}

		// Load this user
		$this->load($result[0]['user_id']);

		// Validate the user
		$this->validateUser();

		// Login the user
		\Session\Manager::login($this);

		return true;
	}

	public static function getByEmailSession($email, $session_id)
	{
		$sql = 'SELECT user_id FROM "user" WHERE email = $1 AND session = $2 AND status;';
		$res = \Geppetto\Utility::pgQueryParams($sql, array($email, $session_id));

		if (!$res || !$res[0] || !$res[0]['user_id'])
		{
			return false;
		}

		return \Model\User::find($res[0]['user_id']);
	}

/**
 * User Validation
 **/

	public function validateUser()
	{
		// Validate that this user is legitimate, and that it can be used
		if (!$this->user_id)
		{
			throw new \Exception('User is not valid');
		}

		if ($this->status != 't')
		{
			throw new \Exception('User account does not exist.');
		}

		if ($this->verified != 't')
		{
			throw new \Exception('Your user account has not been verified. Check your email to confirm your registration.');
		}

		return true;
	}

	public static function getByVerificationCode(string $code)
	{
		// Require that the user is status=true and verify=false
		$sql = 'SELECT user_id FROM "user" WHERE verification_code = $1 AND NOT verified AND status;';
		$res = \Geppetto\Utility::pgQueryParams($sql, array($code));

		if (!$res || !$res[0] || !$res[0]['user_id'])
		{
			return false;
		}

		return self::find($res[0]['user_id']);
	}

	private function checkVerificationCodeExists(string $code)
	{
		$sql = 'SELECT user_id FROM "user" WHERE verification_code = $1;';
		$res = \Geppetto\Utility::pgQueryParams($sql, array($code));

		if (!$res || !$res[0] || !$res[0]['user_id'])
		{
			return false;
		}

		return true;
	}

	public function verify(string $code)
	{
		// Verify that the user isn't logged in
		if (\Session\Manager::isActiveUserSession())
		{
			throw new \Exception('User account can not be verified while logged in.');
		}

		// Verify the user
		if (!empty($code) && $code === $this->verification_code)
		{
			$this->verified = 't';
			$this->save();
		}

		return true;
	}

	public static function getLoggedIn()
	{
		return \Session\Manager::getUser();
	}

	public static function isLoggedIn()
	{
		return !!\Session\Manager::getUser();
	}


/**
 * Generate the password
 */
	protected function generatePassword($email, $password)
	{
		return $this->hashPassword_v1($email, $password);
	}

	protected function getByEmailPassword($email, $password)
	{
		$hashed_password = $this->generatePassword($this->getOriginalEmailByEmail($email), $password);

		$sql = 'SELECT user_id FROM "user" WHERE email = $1 AND password = $2 AND status;';
		$res = \Geppetto\Utility::pgQueryParams($sql, array($email, $hashed_password));

		if (!$res || !$res[0] || !$res[0]['user_id'])
		{
			return false;
		}

		return $res;
	}

/**
 * Password Hash Generation - Version 1
 * sha512 generates 128 characters for the password
 * - Use a deployment-based salt from the config file
 * - Append an additional salt of the email address
 * - Throw on the password
 */
	private function hashPassword_v1($original_email, $password)
	{
		if (!defined('PASSWORD_SALT')) {
			define('PASSWORD_SALT', 'SCROLLIO_SALT_PAD');
		}

		return hash('sha512', PASSWORD_SALT . hash('md5', $original_email) . $password);
	}

	private function checkEmailExists($email)
	{
		$sql = 'SELECT user_id FROM "user" WHERE email = $1 AND status;';
		$res = \Geppetto\Utility::pgQueryParams($sql, array($email));
		return !!($res && $res[0] && $res[0]['user_id']);
	}

	private function getOriginalEmailByEmail($email)
	{
		$sql = 'SELECT original_email FROM "user" WHERE email = $1 AND status;';
		$res = \Geppetto\Utility::pgQueryParams($sql, array($email));

		if (!$res || !$res[0] || !$res[0]['original_email'])
		{
			return false;
		}

		return $res[0]['original_email'];
	}
}
