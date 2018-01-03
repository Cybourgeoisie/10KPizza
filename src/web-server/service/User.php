<?php

declare(strict_types=1);

namespace Service;

class User extends \Scrollio\Service\AbstractService
{
	public function register(string $email, string $password)
	{
		// Create the new user
		$user_obj = new \Model\User();
		$user_obj->register($email, $password);

		// Send a confirmation email
		$reg_email = new \Email\RegistrationEmail($email);
		$reg_email->sendRegistrationEmail($user_obj->verification_code, $email);

		return true;
	}

	public function login(string $email, string $password)
	{
		$user_obj = new \Model\User();
		$user_obj->login($email, $password);

		return true;
	}

	public function verify(string $verification_code)
	{
		// Verify that the user isn't logged in
		if (\Session\Manager::isActiveUserSession())
		{
			throw new \Exception('You can\'t verify an account while logged in.');
		}

		// Find the user by the code
		$user_obj = \Model\User::getByVerificationCode($verification_code);
		if (!$user_obj)
		{
			throw new \Exception('Your verification code was not found. Please contact us for more information.');
		}

		// Validate the user
		$user_obj->verify($verification_code);

		return true;
	}

	public function logout()
	{
		\Session\Manager::logout();
		return true;
	}

	public function getPortfolio()
	{
		$user_obj = \Session\Manager::getUser();
		if (!$user_obj) {
			return false;
		}

		return array(
			'portfolio' => $user_obj->portfolio,
			'alerts'    => $user_obj->alerts,
			'settings'  => $user_obj->settings
		);
	}

	public function savePortfolio(string $portfolio)
	{
		$user_obj = \Session\Manager::getUser();
		if (!$user_obj) {
			return false;
		}

		$user_obj->portfolio = $portfolio;
		$user_obj->save();

		return true;
	}

	public function getAlerts()
	{
		$user_obj = \Session\Manager::getUser();
		if (!$user_obj) {
			return false;
		}

		return array(
			'alerts' => $user_obj->alerts
		);
	}

	public function saveAlerts(string $alerts)
	{
		$user_obj = \Session\Manager::getUser();
		if (!$user_obj) {
			return false;
		}

		// Deconstruct the JSON blob & save them all
		// Leaving this here even without full implementation because it's a good sanity check
		try
		{
			// Deconstruct
			$alerts_array = json_decode($alerts, true);
		}
		catch (\Exception $ex)
		{
			return false;
		}

		// Save the alerts
		//\Model\Alert::saveAlerts($user_obj, $alerts_array);

		// Old method
		$user_obj->alerts = $alerts;
		$user_obj->save();

		return true;
	}

	public function getSettings()
	{
		$user_obj = \Session\Manager::getUser();
		if (!$user_obj) {
			return false;
		}

		return array(
			'settings' => $user_obj->settings
		);
	}

	public function saveSettings(string $settings)
	{
		$user_obj = \Session\Manager::getUser();
		if (!$user_obj) {
			return false;
		}

		$user_obj->settings = $settings;
		$user_obj->save();

		return true;
	}
}
