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

	protected function prepareForValidation(): void
	{
		parent::prepareForValidation();

		$confirmation = $this->input('main_applicant.email_confirmation');
		if (is_string($confirmation)) {
			$this->merge(['main_applicant' => [
				...$this->input('main_applicant'),
				'email_confirmation' => strtolower(trim($confirmation)),
			]]);
		}
	}

	/**
	 * The form sends a confirmation only when the applicant changed the stored
	 * email address; then it has to match.
	 */
	public function rules(): array
	{
		return array_merge(parent::rules(), [
			'main_applicant.email_confirmation' => ['sometimes', 'required', 'same:main_applicant.email'],
		]);
	}

	public function messages(): array
	{
		return array_merge(parent::messages(), [
			'main_applicant.email_confirmation.required' => 'Bitte bestätigen Sie die neue E-Mail-Adresse.',
			'main_applicant.email_confirmation.same' => 'Die beiden E-Mail-Adressen stimmen nicht überein.',
		]);
	}

	/**
	 * Only for checking here; the backend gets the address itself.
	 */
	public function forwardable(): array
	{
		$data = $this->validated();
		unset($data['main_applicant']['email_confirmation']);

		return $data;
	}
}
