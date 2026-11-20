<?php
namespace App\Http\Requests;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }
    public function rules(): array
    {
        $username = Rule::unique('users', 'username');
        $user = $this->route('user');
        if ($user instanceof User) {
            // Only trust the route-bound record, never a submitted ignore ID.
            $username->ignore($user);
        }
        return [
            'username' => ['required', 'string', 'max:50', $username],
            'full_name' => ['required', 'string', 'max:100'],
            'avatar' => ['bail', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:max_width=4096,max_height=4096'],
        ];
    }
    public function messages(): array
    {
        return [
            'username.required' => 'Enter a username.',
            'username.unique' => 'That username is already in use. Choose another.',
            'full_name.required' => 'Enter the user’s full name.',
            'avatar.image' => 'Choose a valid image file.',
            'avatar.mimes' => 'Choose a JPEG, PNG, or WebP image.',
            'avatar.max' => 'The avatar must be no larger than 2 MB.',
            'avatar.dimensions' => 'The avatar must be no larger than 4096 × 4096 pixels.',
            'avatar.uploaded' => 'The image could not be uploaded. Check its size and try again.',
        ];
    }
}
