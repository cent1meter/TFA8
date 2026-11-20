<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class CustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authentication is also enforced by the resource route group.
        return $this->user() !== null;
    }
    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20'],
        ];
    }
    public function messages(): array
    {
        return [
            'full_name.required' => 'Enter the customer’s full name.',
            'email.required' => 'Enter an email address.',
            'email.email' => 'Enter a valid email address.',
        ];
    }
}
