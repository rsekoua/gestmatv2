<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $employeeId = $this->route('employee') ?? $this->route('record');

        return [
            'nom' => [
                'required',
                'string',
                'max:255',
            ],
            'prenom' => [
                'required',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('employees', 'email')->ignore($employeeId),
            ],
            'telephone' => [
                'nullable',
                'string',
                'max:10',
                'regex:/^[0-9]{10}$/',
            ],
            'service_id' => [
                'nullable',
                'uuid',
                'exists:services,id',
            ],
            'emploi' => [
                'nullable',
                'string',
                'max:255',
            ],
            'fonction' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom est obligatoire.',
            'nom.string' => 'Le nom doit être du texte.',
            'nom.max' => 'Le nom ne peut pas dépasser :max caractères.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'prenom.string' => 'Le prénom doit être du texte.',
            'prenom.max' => 'Le prénom ne peut pas dépasser :max caractères.',
            'email.required' => 'L\'email est obligatoire.',
            'email.email' => 'L\'email doit être une adresse email valide.',
            'email.max' => 'L\'email ne peut pas dépasser :max caractères.',
            'email.unique' => 'Cet email est déjà utilisé par un autre employé.',
            'telephone.string' => 'Le téléphone doit être du texte.',
            'telephone.max' => 'Le téléphone ne peut pas dépasser :max caractères.',
            'telephone.regex' => 'Le téléphone doit contenir exactement 10 chiffres.',
            'service_id.uuid' => 'L\'identifiant du service n\'est pas valide.',
            'service_id.exists' => 'Le service sélectionné n\'existe pas.',
            'emploi.string' => 'L\'emploi doit être du texte.',
            'emploi.max' => 'L\'emploi ne peut pas dépasser :max caractères.',
            'fonction.string' => 'La fonction doit être du texte.',
            'fonction.max' => 'La fonction ne peut pas dépasser :max caractères.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'nom' => 'nom',
            'prenom' => 'prénom',
            'email' => 'email',
            'telephone' => 'téléphone',
            'service_id' => 'service',
            'emploi' => 'emploi',
            'fonction' => 'fonction',
        ];
    }
}
