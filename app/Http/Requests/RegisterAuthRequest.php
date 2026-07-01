<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class RegisterAuthRequest extends FormRequest
{
	public function authorize()
	{
		return true;
	}

	public function rules()
	{
		return [
			'password' => 'required',
		];
	}

	public function messages()
	{
		return [
			'password.required' => 'Please enter a password.',
		];
	}
}
