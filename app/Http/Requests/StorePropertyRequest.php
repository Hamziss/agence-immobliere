<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePropertyRequest extends FormRequest
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
      'type' => ['required', 'string', 'in:appartement,villa,terrain,bureau,local_commercial'],
      'rooms' => ['nullable', 'integer', 'min:1', 'max:50'],
      'surface' => ['required', 'numeric', 'min:1', 'max:100000'],
      'price' => ['required', 'numeric', 'min:0'],
      'city' => ['required', 'string', 'max:255'],
      'district' => ['nullable', 'string', 'max:255'],
      'description' => ['nullable', 'string', 'max:5000'],
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
      'type.required' => 'Le type de bien est obligatoire.',
      'type.in' => 'Le type de bien est invalide.',
      'surface.required' => 'La surface est obligatoire.',
      'surface.min' => 'La surface doit être au minimum de 1 m².',
      'price.required' => 'Le prix est obligatoire.',
      'price.min' => 'Le prix ne peut pas être négatif.',
      'city.required' => 'La ville est obligatoire.',
      'status.in' => 'Le statut doit être disponible, vendu ou location.',
    ];
  }
}
