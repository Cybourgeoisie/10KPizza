<?php

/**
 * Program Gateway - kickstart the program
 */

// Include the vendor autoloader
require_once(__DIR__ . '/../vendor/autoload.php');

// Include the config to get us started
require_once(dirname(realpath(__FILE__)) . '/../config/config.php');

// For now, just require certain files directly
require_once(SERVER_PATH . 'session/Manager.php');
require_once(SERVER_PATH . 'api/RestApi.php');
require_once(SERVER_PATH . 'model/User.php');
require_once(SERVER_PATH . 'service/User.php');
require_once(SERVER_PATH . 'email/RegistrationEmail.php');
require_once(SERVER_PATH . 'email/AlertNotificationEmail.php');
require_once(SERVER_PATH . 'utility/CurrencyCollectorApi.php');
require_once(SERVER_PATH . 'utility/CurrencyConverter.php');
