<?php

declare(strict_types=1);

namespace Session;

class Manager
{
	protected static $this_user = null;

	public static function isActiveUserSession()
	{
		return !!(self::$this_user);
	}

	public static function getUser()
	{
		if (!self::$this_user)
		{
			throw new \Exception('User is not logged in.');
		}

		return self::$this_user;
	}

	public static function start()
	{
		// Initialize the session.
		session_start();

		// See if the user is logged in
		if ($_SESSION && $_SESSION['email'])
		{
			// Refresh the user credentials
			self::refresh($_SESSION['email']);
		}
	}

	public static function login(\Model\User $user_obj)
	{
		// Set this user
		self::$this_user = $user_obj;

		// Update the user's session ID
		$user_obj->session = session_id();
		$user_obj->save();

		// Store session data, write and close
		$_SESSION['email'] = $user_obj->email;

		self::setCookie(session_name(), session_id(), 86400);
		self::setCookie('logged_in', '1', 86400);

		session_write_close();
	}

	public static function refresh(string $email)
	{
		// Match the user to the session ID
		$user_obj = \Model\User::getByEmailSession($email, session_id());

		// If not found, throw a shit fit
		if (!$user_obj)
		{
			// Assume a hijacking attempt
			self::logout();

			throw new \Exception('User not found by name and session ID');
		}

		// Set the user found by name and the session ID
		self::$this_user = $user_obj;

		self::setCookie(session_name(), session_id(), 86400);
		self::setCookie('logged_in', '1', 86400);

		session_write_close();
	}

	public static function logout()
	{
		self::$this_user = null;

		self::destroy();
	}

	protected static function destroy()
	{
		// Unset all of the session variables.
		$_SESSION = array();

		// Reset the cookie
		self::setCookie(session_name(), '', -42000);
		self::setCookie('logged_in', '', -42000);

		// Finally, destroy the session.
		if (session_status() == PHP_SESSION_ACTIVE)
		{
			session_destroy();
		}
	}

	protected static function setCookie($name, $data, $timeout)
	{
		$params = session_get_cookie_params();

		setcookie($name,
			$data,
			time() + $timeout,
			$params["path"],
			$params["domain"],
			$params["secure"],
			$params["httponly"]
		);
	}
}
