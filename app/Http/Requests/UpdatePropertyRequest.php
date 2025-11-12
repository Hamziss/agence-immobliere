<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePropertyRequest extends FormRequest
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
      'type' => ['sometimes', 'string', 'in:appartement,villa,terrain,bureau,local_commercial'],
      'rooms' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:50'],
      'surface' => ['sometimes', 'numeric', 'min:1', 'max:100000'],
      'price' => ['sometimes', 'numeric', 'min:0'],
      'city' => ['sometimes', 'string', 'max:255'],
      'district' => ['sometimes', 'nullable', 'string', 'max:255'],
      'description' => ['sometimes', 'nullable', 'string', 'max:5000'],
      'status' => ['sometimes', 'string', 'in:disponible,vendu,location'],
      'is_published' => ['sometimes', 'boolean'],
    ];
  }

  /**
   * Get custom messages for validator errors.
   */
  public function messages(): array
  {
    return [
      'type.in' => 'Le type de bien est invalide.',
      'surface.min' => 'La surface doit être au minimum de 1 m².',
      'price.min' => 'Le prix ne peut pas être négatif.',
      'status.in' => 'Le statut doit être disponible, vendu ou location.',
    ];
  }
}
