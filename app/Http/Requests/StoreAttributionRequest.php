<?php

namespace App\Http\Requests;

use App\Models\Attribution;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreAttributionRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'materiel_id' => ['required', 'exists:materiels,id'],
            'employee_id' => ['required', 'exists:employees,id'],
            'date_attribution' => ['required', 'date'],
            'observations_att' => ['nullable', 'string'],
            'accessories' => ['nullable', 'array'],
            'accessories.*' => ['exists:accessories,id'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            // Vérifier que le matériel n'est pas déjà attribué
            $existingMaterielAttribution = Attribution::where('materiel_id', $this->materiel_id)
                ->whereNull('date_restitution')
                ->exists();

            if ($existingMaterielAttribution) {
                $validator->errors()->add(
                    'materiel_id',
                    'Ce matériel est déjà attribué à un autre employé.'
                );
            }

            // Vérifier que l'employé n'a pas déjà une attribution active du même matériel
            $existingEmployeeAttribution = Attribution::where('employee_id', $this->employee_id)
                ->where('materiel_id', $this->materiel_id)
                ->whereNull('date_restitution')
                ->exists();

            if ($existingEmployeeAttribution) {
                $validator->errors()->add(
                    'employee_id',
                    'Cet employé a déjà ce matériel attribué.'
                );
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'materiel_id.required' => 'Le matériel est obligatoire.',
            'materiel_id.exists' => 'Le matériel sélectionné n\'existe pas.',
            'employee_id.required' => 'L\'employé est obligatoire.',
            'employee_id.exists' => 'L\'employé sélectionné n\'existe pas.',
            'date_attribution.required' => 'La date d\'attribution est obligatoire.',
            'date_attribution.date' => 'La date d\'attribution doit être une date valide.',
        ];
    }
}
