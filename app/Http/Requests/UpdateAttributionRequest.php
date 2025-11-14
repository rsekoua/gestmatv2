<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAttributionRequest extends FormRequest
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
            'date_restitution' => ['nullable', 'date', 'after_or_equal:date_attribution'],
            'observations_att' => ['nullable', 'string'],
            'observations_res' => ['nullable', 'string', 'required_with:date_restitution'],
            'etat_general_res' => [
                'nullable',
                Rule::in(['excellent', 'bon', 'moyen', 'mauvais']),
                'required_with:date_restitution',
            ],
            'etat_fonctionnel_res' => [
                'nullable',
                Rule::in(['parfait', 'defauts_mineurs', 'dysfonctionnements', 'hors_service']),
                'required_with:date_restitution',
            ],
            'decision_res' => [
                'nullable',
                Rule::in(['remis_en_stock', 'a_reparer', 'rebut']),
            ],
            'dommages_res' => ['nullable', 'string'],
            'accessories' => ['nullable', 'array'],
            'accessories.*' => ['exists:accessories,id'],
        ];
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
            'date_restitution.date' => 'La date de restitution doit être une date valide.',
            'date_restitution.after_or_equal' => 'La date de restitution doit être après ou égale à la date d\'attribution.',
            'observations_res.required_with' => 'Les observations de restitution sont obligatoires lors de la restitution.',
            'etat_general_res.required_with' => 'L\'état général est obligatoire lors de la restitution.',
            'etat_fonctionnel_res.required_with' => 'L\'état fonctionnel est obligatoire lors de la restitution.',
        ];
    }
}
