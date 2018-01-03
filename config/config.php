<?php

/** ENVIRONMENT **/

// To be used to distinguish between dev, staging, prod in one file and fewer docker crap
define('PROGRAM_ENVIRONMENT', getenv('PROGRAM_ENVIRONMENT'));

switch (PROGRAM_ENVIRONMENT)
{
	// Development
	case 'DEVELOPMENT':
		// Dev only - print all errors
		error_reporting(E_ALL);
		ini_set('display_errors', '1');

		// Site address
		define('SITE_ADDRESS', 'http://192.168.99.100/');

		// Email settings
		define('EMAIL_DEFAULT_ADDRESS', 'debug.register@10k.pizza');
		define('EMAIL_ALERT_ADDRESS',   'debug.alert@10k.pizza');
		define('EMAIL_CONTACT_ADDRESS', 'debug.contact@10k.pizza');

		break;

	case 'PRODUCTION':
		// Tell no secrets
		error_reporting(E_ALL);
		ini_set('display_errors', '0');

		// Site address
		define('SITE_ADDRESS', 'https://www.10k.pizza');

		// Email settings
		define('EMAIL_DEFAULT_ADDRESS', 'register@10k.pizza');
		define('EMAIL_ALERT_ADDRESS',   'alert@10k.pizza');
		define('EMAIL_CONTACT_ADDRESS', 'contact@10k.pizza');

		break;

	// Just die.
	default:
		die('Unauthorized Access.');
		break;
}

/** STANDARD **/

// Database Connection
define('DB_HOST', getenv('TENKPIZZA_DB_HOST'));
define('DB_PORT', 5432);
define('DB_NAME', getenv('TENKPIZZA_DB_NAME'));
define('DB_USER', getenv('TENKPIZZA_DB_USER'));
define('DB_PASS', getenv('TENKPIZZA_DB_PASS'));

// Scrollio: CORS
define('SCROLLIO_WEBSITE_ORIGINS', SITE_ADDRESS);
define('SCROLLIO_WEBSITE_ORIGINS_ACCEPT_ALL', false);

// Paths
define('ROOT_PATH', dirname(realpath(__FILE__)) . '/../');
define('APP_PATH',  ROOT_PATH . 'app/');
define('SERVER_PATH',  ROOT_PATH . 'src/web-server/');

// Common Files
define('GATEWAY_PATH', SERVER_PATH . 'gateway.php');

// Site Configuration
define('API_ADDRESS', SITE_ADDRESS . 'api/');
define('VERIFICATION_ADDRESS', SITE_ADDRESS . '?verify_user=');

// Site Meta
define('SITE_NAME', '10K Pizza');
define('SITE_ADDRESS_CLEAN', 'www.10k.pizza');
define('EMAIL_DEFAULT_SENDER', 'Ben @ 10K Pizza');
define('EMAIL_ALERT_SENDER', 'Alerts @ 10K Pizza');

// Security
define('PASSWORD_SALT', getenv('TENKPIZZA_PASSWORD_SALT'));
