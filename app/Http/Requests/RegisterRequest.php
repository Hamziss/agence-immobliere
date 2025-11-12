<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   */
  public function rules(): array
  {
    return [
      'name' => ['required', 'string', 'max:255'],
      'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
      'password' => ['required', 'string', 'min:8', 'confirmed'],
      'role' => ['sometimes', 'string', 'in:agent,guest'],
    ];
  }

  /**
   * Get custom messages for validator errors.
   */
  public function messages(): array
  {
    return [
      'name.required' => 'Le nom est obligatoire.',
      'email.required' => 'L\'email est obligatoire.',
      'email.email' => 'L\'email doit être valide.',
      'email.unique' => 'Cet email est déjà utilisé.',
      'password.required' => 'Le mot de passe est obligatoire.',
      'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
      'password.confirmed' => 'Les mots de passe ne correspondent pas.',
      'role.in' => 'Le rôle doit être agent ou guest. Les admins ne peuvent pas être créés via cette route.',
    ];
  }
}
