<?php

namespace App\Http\Requests;

/**
 * Updated data from the form in renewal mode. Same normalization and rules as
 * a new application; the password gate does not apply — the renewal token in
 * the URL is verified by the Interessenten backend, which only accepts it for
 * the one application it was issued for.
 */
class RenewApplicationRequest extends SubmitApplicationRequest
{
	public function authorize(): bool
	{
		return true;
	}
}
