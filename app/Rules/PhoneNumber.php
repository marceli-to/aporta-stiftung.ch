<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;

/**
 * Validates a phone number. National-format numbers (e.g. "079 123 45 67") are
 * interpreted as Swiss; foreign numbers are accepted as long as they include a
 * country code (e.g. "+49 151 12345678").
 */
class PhoneNumber implements ValidationRule
{
	public function validate(string $attribute, mixed $value, Closure $fail): void
	{
		if (! is_string($value) || $value === '') {
			return;
		}

		try {
			$util = PhoneNumberUtil::getInstance();
			$proto = $util->parse($value, 'CH');
			if ($util->isValidNumber($proto)) {
				return;
			}
		} catch (NumberParseException $e) {
			// fall through to failure
		}

		$fail('Bitte eine gültige Telefonnummer angeben (ausländische Nummern mit Ländervorwahl, z. B. +49 …).');
	}
}
