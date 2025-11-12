<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadImageRequest extends FormRequest
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
    // Si c'est un fichier unique, on adapte la validation
    $isArray = $this->hasFile('images') && is_array($this->file('images'));

    if ($isArray) {
      // Plusieurs fichiers
      return [
        'images' => ['required', 'array', 'min:1', 'max:10'],
        'images.*' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
      ];
    } else {
      // Un seul fichier
      return [
        'images' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
      ];
    }
  }

  /**
   * Get custom messages for validator errors.
   */
  public function messages(): array
  {
    return [
      'images.required' => 'Au moins une image est requise.',
      'images.array' => 'Les images doivent être envoyées sous forme de tableau.',
      'images.max' => 'Vous ne pouvez pas télécharger plus de 10 images à la fois.',
      'images.*.required' => 'Chaque image est requise.',
      'images.*.image' => 'Le fichier doit être une image.',
      'images.*.mimes' => 'Les formats autorisés sont : JPEG, JPG, PNG, WebP.',
      'images.*.max' => 'Chaque image ne doit pas dépasser 5MB.',
    ];
  }
}
