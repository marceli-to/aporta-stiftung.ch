<?php

namespace App\Http\Concerns;

trait MatchesMasterPassword
{
	/**
	 * Constant-time comparison against FORM_MASTER_PASSWORD. Returns false
	 * when no master password is configured.
	 */
	protected function matchesMasterPassword($value): bool
	{
		$master = config('form.master_password');
		if (!is_string($master) || $master === '' || !is_string($value)) {
			return false;
		}
		return hash_equals($master, $value);
	}
}
