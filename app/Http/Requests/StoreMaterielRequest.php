<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMaterielRequest extends FormRequest
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
        return [
            'materiel_type_id' => [
                'required',
                'uuid',
                'exists:materiel_types,id',
            ],
            'marque' => [
                'nullable',
                'string',
                'max:100',
            ],
            'modele' => [
                'nullable',
                'string',
                'max:100',
            ],
            'numero_serie' => [
                'required',
                'string',
                'max:100',
                'unique:materiels,numero_serie',
            ],
            'processor' => [
                'nullable',
                'string',
                'max:255',
            ],
            'ram_size_gb' => [
                'nullable',
                'integer',
                'min:0',
                'max:1024',
            ],
            'storage_size_gb' => [
                'nullable',
                'integer',
                'min:0',
                'max:10000',
            ],
            'screen_size' => [
                'nullable',
                'numeric',
                'min:0',
                'max:99.99',
            ],
            'purchase_date' => [
                'required',
                'date',
                'before_or_equal:today',
            ],
            'acquision' => [
                'nullable',
                'string',
                'max:255',
            ],
            'statut' => [
                'required',
                Rule::in(['disponible', 'attribué', 'en_panne', 'en_maintenance', 'rebuté']),
            ],
            'etat_physique' => [
                'required',
                Rule::in(['excellent', 'bon', 'moyen', 'mauvais']),
            ],
            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'materiel_type_id.required' => 'Le type de matériel est obligatoire.',
            'materiel_type_id.uuid' => 'L\'identifiant du type de matériel n\'est pas valide.',
            'materiel_type_id.exists' => 'Le type de matériel sélectionné n\'existe pas.',
            'marque.string' => 'La marque doit être du texte.',
            'marque.max' => 'La marque ne peut pas dépasser :max caractères.',
            'modele.string' => 'Le modèle doit être du texte.',
            'modele.max' => 'Le modèle ne peut pas dépasser :max caractères.',
            'numero_serie.required' => 'Le numéro de série est obligatoire.',
            'numero_serie.string' => 'Le numéro de série doit être du texte.',
            'numero_serie.max' => 'Le numéro de série ne peut pas dépasser :max caractères.',
            'numero_serie.unique' => 'Ce numéro de série est déjà utilisé par un autre matériel.',
            'processor.string' => 'Le processeur doit être du texte.',
            'processor.max' => 'Le processeur ne peut pas dépasser :max caractères.',
            'ram_size_gb.integer' => 'La mémoire RAM doit être un nombre entier.',
            'ram_size_gb.min' => 'La mémoire RAM doit être au moins :min GB.',
            'ram_size_gb.max' => 'La mémoire RAM ne peut pas dépasser :max GB.',
            'storage_size_gb.integer' => 'Le stockage doit être un nombre entier.',
            'storage_size_gb.min' => 'Le stockage doit être au moins :min GB.',
            'storage_size_gb.max' => 'Le stockage ne peut pas dépasser :max GB.',
            'screen_size.numeric' => 'La taille de l\'écran doit être un nombre.',
            'screen_size.min' => 'La taille de l\'écran doit être au moins :min pouces.',
            'screen_size.max' => 'La taille de l\'écran ne peut pas dépasser :max pouces.',
            'purchase_date.required' => 'La date d\'achat est obligatoire.',
            'purchase_date.date' => 'La date d\'achat doit être une date valide.',
            'purchase_date.before_or_equal' => 'La date d\'achat ne peut pas être dans le futur.',
            'acquision.string' => 'Le mode d\'acquisition doit être du texte.',
            'acquision.max' => 'Le mode d\'acquisition ne peut pas dépasser :max caractères.',
            'statut.required' => 'Le statut est obligatoire.',
            'statut.in' => 'Le statut sélectionné n\'est pas valide.',
            'etat_physique.required' => 'L\'état physique est obligatoire.',
            'etat_physique.in' => 'L\'état physique sélectionné n\'est pas valide.',
            'notes.string' => 'Les notes doivent être du texte.',
            'notes.max' => 'Les notes ne peuvent pas dépasser :max caractères.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'materiel_type_id' => 'type de matériel',
            'marque' => 'marque',
            'modele' => 'modèle',
            'numero_serie' => 'numéro de série',
            'processor' => 'processeur',
            'ram_size_gb' => 'mémoire RAM',
            'storage_size_gb' => 'stockage',
            'screen_size' => 'taille de l\'écran',
            'purchase_date' => 'date d\'achat',
            'acquision' => 'mode d\'acquisition',
            'statut' => 'statut',
            'etat_physique' => 'état physique',
            'notes' => 'notes',
        ];
    }
}
