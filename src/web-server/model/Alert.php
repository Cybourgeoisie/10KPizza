<?php

declare(strict_types=1);

namespace Model;

class Alert extends \Scrollio\Model\AbstractModel
{
	public function saveAlerts(\Model\User $user_obj, array $alerts)
	{
		// Get the user's ID
		$user_id = $user_obj->user_id;
		if ($user_id <= 0) {
			throw new \Exception('Invalid user for saving alerts.');
		}

		// Validate and save all of the alerts
		
	}

	public function getAlerts(\Model\User $user_obj)
	{
		// Get the user's ID
		$user_id = $user_obj->user_id;
		if ($user_id <= 0) {
			throw new \Exception('Invalid user for saving alerts.');
		}

		// Find all alerts under this user

	}

	public function getActiveAlerts()
	{
		throw new \Exception('To build: getActiveAlerts');
	}
}
