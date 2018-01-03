<?php

declare(strict_types=1);

namespace Utility;

class CurrencyConverter
{
	// Assuming that from_unit is already USD, to_unit is X / USD
	public static function convertFiat($from_unit, $to_unit) {
		return ($from_unit * $to_unit);
	}

	// Assuming that both from_unit and to_unit are in X / BTC
	public static function convertCrypto($from_unit, $to_unit) {
		return ($from_unit / $to_unit);
	}
}
