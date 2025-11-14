<?php

namespace App\Http\Requests;

use App\Models\Attribution;
use App\Models\Materiel;
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
            'employee_id' => ['nullable', 'required_without:service_id', 'exists:employees,id'],
            'service_id' => ['nullable', 'required_without:employee_id', 'exists:services,id'],
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
            // Vérifier qu'on n'a pas les deux (employee_id ET service_id)
            if ($this->filled('employee_id') && $this->filled('service_id')) {
                $validator->errors()->add(
                    'employee_id',
                    'Vous ne pouvez pas attribuer à la fois à un employé et à un service.'
                );
                $validator->errors()->add(
                    'service_id',
                    'Vous ne pouvez pas attribuer à la fois à un employé et à un service.'
                );
            }

            // Vérifier la cohérence entre le type de matériel et le destinataire
            if ($this->materiel_id) {
                $materiel = Materiel::with('materielType')->find($this->materiel_id);

                if ($materiel) {
                    $isComputer = $materiel->materielType->isComputer();

                    // Les ordinateurs doivent être attribués à des employés
                    if ($isComputer && $this->filled('service_id')) {
                        $validator->errors()->add(
                            'service_id',
                            'Les ordinateurs doivent être attribués à des employés, pas à des services.'
                        );
                    }

                    // Les non-ordinateurs doivent être attribués à des services
                    if (! $isComputer && $this->filled('employee_id')) {
                        $validator->errors()->add(
                            'employee_id',
                            'Les équipements autres que les ordinateurs doivent être attribués à des services.'
                        );
                    }
                }
            }

            // Vérifier que le matériel n'est pas déjà attribué
            $existingMaterielAttribution = Attribution::where('materiel_id', $this->materiel_id)
                ->whereNull('date_restitution')
                ->exists();

            if ($existingMaterielAttribution) {
                $validator->errors()->add(
                    'materiel_id',
                    'Ce matériel est déjà attribué.'
                );
            }

            // Vérifier que l'employé n'a pas déjà une attribution active du même matériel
            if ($this->filled('employee_id')) {
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
            }

            // Vérifier que le service n'a pas déjà une attribution active du même matériel
            if ($this->filled('service_id')) {
                $existingServiceAttribution = Attribution::where('service_id', $this->service_id)
                    ->where('materiel_id', $this->materiel_id)
                    ->whereNull('date_restitution')
                    ->exists();

                if ($existingServiceAttribution) {
                    $validator->errors()->add(
                        'service_id',
                        'Ce service a déjà ce matériel attribué.'
                    );
                }
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
            'employee_id.required_without' => 'L\'employé est obligatoire si aucun service n\'est sélectionné.',
            'employee_id.exists' => 'L\'employé sélectionné n\'existe pas.',
            'service_id.required_without' => 'Le service est obligatoire si aucun employé n\'est sélectionné.',
            'service_id.exists' => 'Le service sélectionné n\'existe pas.',
            'date_attribution.required' => 'La date d\'attribution est obligatoire.',
            'date_attribution.date' => 'La date d\'attribution doit être une date valide.',
        ];
    }
}
