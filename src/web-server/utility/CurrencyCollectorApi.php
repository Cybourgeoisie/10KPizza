<?php

declare(strict_types=1);

namespace Utility;

class CurrencyCollectorApi
{
	// Raw results
	protected $fiat_raw = "";
 	protected $crypto_raw = "";

	// Parsed rates
	protected $fiat_rates = array();
	protected $crypto_rates = array();

	public function getFiatRates()
	{
		// Use the cache
		if ($this->fiat_rates && count($this->fiat_rates) > 0) {
			return $this->fiat_rates;
		}

		// Call the API
		$client = new \GuzzleHttp\Client();
		$res = $client->request('GET', 'https://api.fixer.io/latest?base=USD');

		// Validate the API result
		$status_code = $res->getStatusCode();

		// Collect the result
		if ($status_code == 200) {
			$this->fiat_raw = \strval($res->getBody());
			if ($this->fiat_raw) {
				// Convert the fiat array into rates
				$fiat_array = json_decode($this->fiat_raw, true);
				$this->fiat_rates = $fiat_array['rates'];

				// Add the USD rate
				$this->fiat_rates['USD'] = 1.0;
			}
		} else {
			return false;
		}

		return $this->fiat_rates;
	}

	public function getCryptoRates()
	{
		// Use the cache
		if ($this->crypto_rates && count($this->crypto_rates) > 0) {
			return $this->crypto_rates;
		}

		// Call the API
		$client = new \GuzzleHttp\Client();
		$res = $client->request('GET', 'https://api.coinmarketcap.com/v1/ticker/');

		// Validate the API result
		$status_code = $res->getStatusCode();

		// Collect the result
		if ($status_code == 200) {
			$this->crypto_raw = \strval($res->getBody());
			if ($this->crypto_raw) {
				$crypto_array = json_decode($this->crypto_raw, true);

				// Format the rates
				foreach ($crypto_array as $crypto) {
					// Do not include duplicate symbols. The higher of the duplicates wins.
					if (array_key_exists($crypto['symbol'], $this->crypto_rates)) {
						continue;
					}

					$this->crypto_rates[$crypto['symbol']] = array(
						'price_btc' => $crypto['price_btc'],
						'price_usd' => $crypto['price_usd'],
						'name'      => $crypto['name']
					);
				}
			}
		} else {
			return false;
		}

		return $this->crypto_rates;
	}
}
