<?php

namespace App\Http\Requests;

use App\Http\Concerns\MatchesMasterPassword;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class SubmitApplicationRequest extends FormRequest
{
	use MatchesMasterPassword;

	/**
	 * Authorize the submit by verifying the X-Form-Token header against the
	 * master password or any token from key.json. Tokens are not consumed
	 * (the file is not mutated) — the backend forwarding job is the source
	 * of truth for submissions in the new flow.
	 */
	public function authorize(): bool
	{
		$token = $this->header('X-Form-Token');

		if (!is_string($token) || $token === '') {
			return false;
		}

		if ($this->matchesMasterPassword($token)) {
			return true;
		}

		$key = json_decode(\Storage::disk('local')->get('key.json'), true);
		if (!is_array($key)) {
			return false;
		}

		foreach ($key as $v) {
			if (isset($v['token']) && hash_equals((string) $v['token'], $token)) {
				return true;
			}
		}

		return false;
	}

	protected function failedAuthorization()
	{
		throw new HttpResponseException(response()->json([
			'error' => 'Unauthorized',
		], 401));
	}

	protected function prepareForValidation(): void
	{
		$payload = $this->all();

		$normalize = function (array $applicant): array {
			foreach (['mobile_phone', 'landline_phone'] as $field) {
				if (isset($applicant[$field]) && is_string($applicant[$field]) && $applicant[$field] !== '') {
					$applicant[$field] = $this->toE164($applicant[$field]);
				}
			}
			if (isset($applicant['current_housing']['landlord_phone']) && is_string($applicant['current_housing']['landlord_phone']) && $applicant['current_housing']['landlord_phone'] !== '') {
				$applicant['current_housing']['landlord_phone'] = $this->toE164($applicant['current_housing']['landlord_phone']);
			}
			if (isset($applicant['email']) && is_string($applicant['email'])) {
				$applicant['email'] = strtolower(trim($applicant['email']));
			}

			return $applicant;
		};

		if (isset($payload['main_applicant']) && is_array($payload['main_applicant'])) {
			$payload['main_applicant'] = $normalize($payload['main_applicant']);
		}
		if (isset($payload['co_applicant']) && is_array($payload['co_applicant'])) {
			$payload['co_applicant'] = $normalize($payload['co_applicant']);
		}

		$this->replace($payload);
	}

	public function rules(): array
	{
		return array_merge(
			[
				'shares_apartment' => ['required', 'boolean'],

				'housing_wish' => ['required', 'array'],
				'housing_wish.earliest_move_in' => ['required', 'date', 'after_or_equal:today'],
				'housing_wish.max_gross_rent' => ['required', 'numeric', 'min:1200', 'max:20000'],
				'housing_wish.districts' => ['required', 'array', 'min:1'],
				'housing_wish.districts.*' => ['string'],
				'housing_wish.floors' => ['required', 'array', 'min:1'],
				'housing_wish.floors.*' => ['string'],

				'household_info' => ['required', 'array'],
				'household_info.total_persons' => ['required', 'integer', 'min:1'],
				'household_info.adults_count' => ['required', 'integer', 'min:1'],
				'household_info.children_count' => ['required', 'integer', 'min:0'],
				'household_info.all_children_live_constantly' => [
					Rule::requiredIf(fn () => (int) ($this->input('household_info.children_count') ?? 0) > 0),
					'nullable',
					'boolean',
				],
				'household_info.has_pets' => ['required', 'boolean'],
				'household_info.pets_description' => [
					'nullable',
					'string',
					'max:200',
					'required_if:household_info.has_pets,true',
					'not_regex:/\b(Hund|Hunde|Dog)\b/i',
				],
				'household_info.remarks' => ['nullable', 'string', 'max:5000'],

				'children' => ['present', 'array'],
				'children.*.position' => ['required', 'integer', 'min:1'],
				'children.*.birth_year' => ['required', 'integer', 'min:1900', 'max:'.((int) date('Y'))],

				'co_applicant' => ['nullable', 'array'],
			],
			$this->applicantRules('main_applicant', isMain: true),
			$this->applicantRules('co_applicant', isMain: false),
		);
	}

	public function messages(): array
	{
		return [
			'household_info.pets_description.not_regex' => 'Hunde sind in den Liegenschaften nicht erlaubt.',
		];
	}

	public function withValidator(ValidatorContract $validator): void
	{
		$validator->after(function (ValidatorContract $validator) {
			$data = $validator->getData();

			$total = $data['household_info']['total_persons'] ?? null;
			$adults = $data['household_info']['adults_count'] ?? null;
			$childrenCount = $data['household_info']['children_count'] ?? null;
			$children = $data['children'] ?? [];

			if (is_int($total) && is_int($adults) && is_int($childrenCount) && $total !== $adults + $childrenCount) {
				$validator->errors()->add(
					'household_info.total_persons',
					'Die Gesamtzahl muss der Summe von Erwachsenen und Kindern entsprechen.'
				);
			}

			if (is_int($childrenCount) && is_array($children) && count($children) !== $childrenCount) {
				$validator->errors()->add(
					'children',
					"Es müssen genau {$childrenCount} Kindereinträge vorhanden sein."
				);
			}
		});
	}

	protected function failedValidation(ValidatorContract $validator)
	{
		throw new HttpResponseException(response()->json([
			'message' => 'Validation failed',
			'errors' => $validator->errors(),
		], 422));
	}

	/**
	 * @return array<string, array<int, mixed>>
	 */
	private function applicantRules(string $key, bool $isMain): array
	{
		$prefix = $key;
		$required = $isMain ? 'required' : 'required_with:'.$key;
		$nationality = $this->input("$prefix.nationality");
		$nonSwissRequired = ($this->input($prefix) !== null && is_string($nationality) && $nationality !== 'CH')
			? ['required']
			: [];

		$rules = [
			$prefix => [$isMain ? 'required' : 'nullable', 'array'],

			"$prefix.salutation" => [$required, 'string'],
			"$prefix.first_name" => [$required, 'string', 'max:100'],
			"$prefix.last_name" => [$required, 'string', 'max:100'],
			"$prefix.birth_date" => [$required, 'date', 'before_or_equal:'.now()->subYears(16)->toDateString()],
			"$prefix.marital_status" => [$required, 'string'],
			"$prefix.nationality" => [$required, 'string', 'size:2'],
			"$prefix.place_of_origin" => ['nullable', 'string', 'max:100', "required_if:$prefix.nationality,CH"],
			"$prefix.residence_permit" => array_merge($nonSwissRequired, ['nullable', 'string']),
			"$prefix.swiss_residence_since" => array_merge($nonSwissRequired, ['nullable', 'date']),
			"$prefix.mobile_phone" => [$required, 'string', 'max:30'],
			"$prefix.landline_phone" => ['nullable', 'string', 'max:30'],
			"$prefix.email" => [$required, 'email', 'max:255'],
			"$prefix.occupation" => [$required, 'string', 'max:200'],
			"$prefix.employment_status" => [$required, 'string'],
			"$prefix.debt_enforcement_last_2y" => [$required, 'boolean'],

			"$prefix.employer" => ['nullable', 'array', "required_if:$prefix.employment_status,employed"],
			"$prefix.employer.name" => ['required_with:'.$prefix.'.employer', 'string', 'max:200'],
			"$prefix.employer.workload_percent" => ['required_with:'.$prefix.'.employer', 'integer', 'min:1', 'max:100'],
			"$prefix.employer.annual_income_bracket" => ['required_with:'.$prefix.'.employer', 'string'],

			"$prefix.current_housing" => [$required, 'array'],
			"$prefix.current_housing.tenant_role" => [$required, 'string'],
			"$prefix.current_housing.terminated_by_landlord" => [$required, 'boolean'],
			"$prefix.current_housing.termination_reason" => ['nullable', 'string', 'max:1000', "required_if:$prefix.current_housing.terminated_by_landlord,true"],
			"$prefix.current_housing.landlord_name" => [$required, 'string', 'max:200'],
			"$prefix.current_housing.landlord_contact_person" => ['nullable', 'string', 'max:200'],
			"$prefix.current_housing.landlord_phone" => ['nullable', 'string', 'max:30'],
		];

		if ($isMain) {
			$rules["$prefix.street"] = ['required', 'string', 'max:200'];
			$rules["$prefix.street_number"] = ['nullable', 'string', 'max:20'];
			$rules["$prefix.postal_code"] = ['required', 'string', 'max:10'];
			$rules["$prefix.city"] = ['required', 'string', 'max:100'];
		} else {
			$rules["$prefix.relationship_to_main"] = [$required, 'string'];
			$rules["$prefix.same_address_as_main"] = [$required, 'boolean'];
			$rules["$prefix.street"] = ['nullable', 'string', 'max:200', "required_if:$prefix.same_address_as_main,false"];
			$rules["$prefix.street_number"] = ['nullable', 'string', 'max:20'];
			$rules["$prefix.postal_code"] = ['nullable', 'string', 'max:10', "required_if:$prefix.same_address_as_main,false"];
			$rules["$prefix.city"] = ['nullable', 'string', 'max:100', "required_if:$prefix.same_address_as_main,false"];
		}

		return $rules;
	}

	private function toE164(string $raw): string
	{
		try {
			$util = PhoneNumberUtil::getInstance();
			$proto = $util->parse($raw, 'CH');
			if ($util->isValidNumber($proto)) {
				return $util->format($proto, PhoneNumberFormat::E164);
			}
		} catch (NumberParseException $e) {
			// fall through
		}

		return $raw;
	}
}
