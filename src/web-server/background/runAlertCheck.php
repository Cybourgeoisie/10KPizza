<?php

// Access the source gateway
require_once(dirname(realpath(__FILE__)) . '/../../gateway.php');

// Statistics
$start_time = microtime(true);
echo "Starting alert notifications at " . $start_time . "\r\n";

// Collect the current currency rates
$currency_collector_obj = new \Utility\CurrencyCollectorApi();
$fiat_rates = $currency_collector_obj->getFiatRates();
$crypto_rates = $currency_collector_obj->getCryptoRates();


// Prepare the email handler just in case
$alert_email = new \Email\AlertNotificationEmail();


// Get all alerts
$sql = '
	SELECT user_id, email, alerts FROM "user" WHERE alerts IS NOT NULL AND status AND verified;
';

// Get the results and validate that we have something
$res = \Geppetto\Utility::pgQueryParams($sql, array());
if (!$res || !$res[0] || !$res[0]['user_id']) {
	return false;
}

// Iterate over all of the alerts, parse them and test them
foreach ($res as $row) {
	// Parse the alerts
	try  {
		$alerts = json_decode($row['alerts'], true);
	} catch (\Exception $ex) {
		continue;
	}

	// Go through each one
	$b_any_alert_pass_validation = false;
	foreach ($alerts as $alert_key => $alert) {
		// Skip non-active alerts
		if (array_key_exists('active', $alert) && $alert['active'] != 1) { continue; }

		// Get the alert info
		$watch_coin    = $alert['watch'];
		$alert_type    = $alert['alert-type'];
		$compare_coin  = $alert['compare-to'];
		$compare_value = (float)($alert['compare-to-value']);
		$condition     = $alert['condition'];

		// Validate the watched coin
		if (!array_key_exists($watch_coin, $crypto_rates)) {
			continue;
		}

		// Convert the watched value into the compared value
		$conversion = 0;
		if (array_key_exists($compare_coin, $fiat_rates)) {
			// Fiat value
			$conversion = \Utility\CurrencyConverter::convertFiat($crypto_rates[$watch_coin]['price_usd'], $fiat_rates[$compare_coin]);
		} else if (array_key_exists($compare_coin, $crypto_rates)) {
			// Crypto value
			$conversion = \Utility\CurrencyConverter::convertCrypto($crypto_rates[$watch_coin]['price_btc'], $crypto_rates[$compare_coin]['price_btc']);
		} else {
			continue;
		}

		// Run the test
		$b_pass_validation = false;
		if ($condition == 'greater') {
			$b_pass_validation = ($conversion > $compare_value);
		} else if ($condition == 'less') {
			$b_pass_validation = ($conversion < $compare_value);
		}

		// If it passed, we update the alert
		if ($b_pass_validation) {
			// Make sure we're aware of this to resave the data at the end
			$b_any_alert_pass_validation = true;

			// Set the alert to false
			$alerts[$alert_key]['active'] = false;

			// Form the description statement
			$condition_symbol = ($condition == 'greater' ? ' > ' : ' < ');
			$watch_desc       = '1 ' . $watch_coin;
			$compare_desc     = $compare_value . ' ' . $compare_coin;

			// Final description
			$desc = $watch_desc . $condition_symbol . $compare_desc;

			echo $desc . " passed: " . $row['email'] . "\r\n";

			// Email the winner winner chicken dinner
			$alert_email->clearRecipients();
			$alert_email->setRecipient($row['email']);
			$alert_email->sendAlertEmail($desc);
		}
	}

	// Now save any changes to this user
	if ($b_any_alert_pass_validation) {
		$alerts_json = json_encode($alerts);

		// Get the user
		$user_obj = \Model\User::find($row['user_id']);
		$user_obj->alerts = $alerts_json;
		$user_obj->save();
	}
}

$end_time = microtime(true);
$duration = $end_time - $start_time;
echo "Finished alert notifications in " . $duration . " at " . $end_time . "\r\n";
