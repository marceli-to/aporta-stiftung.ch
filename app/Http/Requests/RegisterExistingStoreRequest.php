<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class RegisterExistingStoreRequest extends FormRequest
{
  public function authorize()
  {
    return true;
  }

  public function rules()
  {
    return [
      'token' => 'required',
      'main_tenant_salutation' => 'required',
      'main_tenant_lastname' => 'required',
      'main_tenant_firstname' => 'required',
      'main_tenant_birthdate' => 'required|date',
      'main_tenant_marital_status' => 'required',
      'main_tenant_nationality' => 'required',
      'main_tenant_residence_permit' => 'required_if:main_tenant_nationality,Andere',
      'main_tenant_swiss_residence_since' => 'required_if:main_tenant_nationality,Andere',
      'main_tenant_home_town' => 'required_if:main_tenant_nationality,CH',
      'main_tenant_email' => 'required|email',
      'main_tenant_private_phone' => 'required|string|min:10',
      'main_tenant_occupation' => 'required',
      'main_tenant_employment_status' => 'required',
      'main_tenant_current_employer_name' => 'required_if:main_tenant_employment_status,1',
      'sub_tenant_yn' => 'required',
      'sub_tenant_type' => 'required_if:sub_tenant_yn,1',
      'sub_tenant_salutation' => Rule::when(fn($input) => in_array($input->sub_tenant_type, [1, 2, 3, 4]), ['required'] ),
      'sub_tenant_lastname' => Rule::when(fn($input) => in_array($input->sub_tenant_type, [1, 2, 3, 4]), ['required'] ),
      'sub_tenant_firstname' => Rule::when(fn($input) => in_array($input->sub_tenant_type, [1, 2, 3, 4]), ['required'] ),
      'sub_tenant_birthdate' => Rule::when(fn($input) => in_array($input->sub_tenant_type, [1, 2, 3, 4]), ['required'] ),
      'sub_tenant_marital_status' => Rule::when(fn($input) => in_array($input->sub_tenant_type, [1, 2, 3, 4]), ['required'] ),
      'sub_tenant_nationality' => Rule::when(fn($input) => in_array($input->sub_tenant_type, [1, 2, 3, 4]), ['required'] ),
      'sub_tenant_residence_permit' => 'required_if:sub_tenant_nationality,Andere',
      'sub_tenant_swiss_residence_since' => 'required_if:sub_tenant_nationality,Andere',
      'sub_tenant_home_town' => [
        Rule::when(
          fn($input) => in_array($input->sub_tenant_type, [1, 2, 3, 4]) && $input->sub_tenant_nationality === 'CH',
          ['required']
        )
      ],
      'sub_tenant_email' => Rule::when(fn($input) => in_array($input->sub_tenant_type, [1, 2, 3, 4]), ['required'] ),
      'sub_tenant_private_phone' => Rule::when(fn($input) => in_array($input->sub_tenant_type, [1, 2, 3, 4]), ['required'] ),
      'sub_tenant_occupation' => Rule::when(fn($input) => in_array($input->sub_tenant_type, [1, 2, 3, 4]), ['required'] ),
      'sub_tenant_employment_status' => Rule::when(fn($input) => in_array($input->sub_tenant_type, [1, 2, 3, 4]), ['required'] ),
      'sub_tenant_current_employer_name' => 'required_if:sub_tenant_employment_status,1',
      'accomodation_total_persons' => 'required_if:has_children,1',
      'accomodation_adults_qty' => 'required_if:has_children,1',
      'accomodation_children_qty' => 'required_if:has_children,true',
      'accomodation_children_age_group' => 'required_if:has_children,true',
      'accomodation_pets_yn' => 'required',
      'accomodation_pets' => 'required_if:accomodation_pets_yn,1',
      'emergency_contact_lastname' => 'required',
      'emergency_contact_firstname' => 'required',
      'emergency_contact_phone' => 'required',
      'emergency_contact_email' => 'required|email',
    ];
  }

  public function messages()
  {
    return [
      'token.required' => 'Please enter a token.',
      'main_tenant_salutation.required' => 'Please select a salutation.',
      'main_tenant_lastname.required' => 'Please enter a last name.',
      'main_tenant_firstname.required' => 'Please enter a first name.',
      'main_tenant_birthdate.required' => 'Please enter a birthdate.',
      'main_tenant_birthdate.date' => 'Please enter a valid birthdate.',
      'main_tenant_marital_status.required' => 'Please select a marital status.',
      'main_tenant_nationality.required' => 'Please select your nationality.',
      'main_tenant_residence_permit.required_if' => 'Please select your residence permit.',
      'main_tenant_swiss_residence_since.required_if' => 'Please select your residence in Switzerland.',
      'main_tenant_home_town.required_if' => 'Please enter your home town.',
      'main_tenant_email.required' => 'Please enter your email address.',
      'main_tenant_email.email' => 'Please enter a valid email address.',
      'main_tenant_private_phone.required' => 'Please enter your private phone number.',
      'main_tenant_private_phone.min' => 'Please enter a valid phone number.',
      'main_tenant_occupation.required' => 'Please enter your occupation.',
      'main_tenant_employment_status.required' => 'Please select your employment status.',
      'main_tenant_current_employer_name.required_if' => 'Please enter your current employer name.',
      'sub_tenant_yn.required' => 'Please select if you have a sub tenant.',
      'sub_tenant_type.required_if' => 'Please select the type of your sub tenant.',
      'sub_tenant_salutation.required_if' => 'Please select a salutation.',
      'sub_tenant_lastname.required_if' => 'Please enter a last name.',
      'sub_tenant_firstname.required_if' => 'Please enter a first name.',
      'sub_tenant_birthdate.required_if' => 'Please enter a birthdate.',
      'sub_tenant_marital_status.required_if' => 'Please select a marital status.',
      'sub_tenant_nationality.required_if' => 'Please select your sub tenants nationality',
      'sub_tenant_home_town.required_if' => 'Please enter your sub tenants home town.',
      'sub_tenant_email.required_if' => 'Please enter your sub tenants email address.',
      'sub_tenant_occupation.required_if' => 'Please enter your sub tenants occupation.',
      'sub_tenant_employment_status.required_if' => 'Please select your sub tenants employment status.',
      'sub_tenant_current_employer_name.required_if' => 'Please enter your sub tenants current employer name.',
      'sub_tenant_residence_permit.required_if' => 'Please select your sub tenants residence permit.',
      'sub_tenant_swiss_residence_since.required_if' => 'Please select your sub tenants residence.',
      'accomodation_pets_yn.required' => 'Please select if you have pets.',
      'accomodation_pets.required_if' => 'Please select your pets.',
      'accomodation_total_persons.required_if' => 'Please enter the total number of persons.',
      'accomodation_adults_qty.required_if' => 'Please enter the number of adults.',
      'accomodation_children_qty.required_if' => 'Please enter the number of children.',
      'accomodation_children_age_group.required_if' => 'Please select the age group of your children.',
      'emergency_contact_lastname.required' => 'Please enter the last name of your emergency contact.',
      'emergency_contact_firstname.required' => 'Please enter the first name of your emergency contact.',
      'emergency_contact_phone.required' => 'Please enter the phone number of your emergency contact.',
      'emergency_contact_email.required' => 'Please enter the email address of your emergency contact.',
    ];
  }

  protected function failedValidation(Validator $validator)
  {
    Log::error('Validation failed: ' . json_encode($validator->errors()));
    throw new HttpResponseException(response()->json([
      'message' => 'Validation failed',
      'errors' => $validator->errors()
    ], 422));
  }
}