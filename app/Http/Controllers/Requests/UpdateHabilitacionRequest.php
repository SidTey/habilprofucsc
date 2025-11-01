<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateHabilitacionRequest extends FormRequest
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
         * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
         */

    protected function prepareForValidation()
    {
        $this->merge([
            'nota_final' => $this->nota_final === '' ? null : $this->nota_final,
            'fecha_nota' => $this->fecha_nota === '' ? null : $this->fecha_nota,
            'titulo_proyecto_practica' => $this->titulo_proyecto_practica === '' ? null : $this->titulo_proyecto_practica,
            'rut_empresa' => $this->rut_empresa === '' ? null : $this->rut_empresa,
            'nombre_empresa' => $this->nombre_empresa === '' ? null : $this->nombre_empresa,
            'rut_supervisor' => $this->rut_supervisor === '' ? null : $this->rut_supervisor,
            'nombre_supervisor' => $this->nombre_supervisor === '' ? null : $this->nombre_supervisor,
            'rut_profesor_guia' => $this->rut_profesor_guia === '' ? null : $this->rut_profesor_guia,
            'rut_profesor_co_guia' => $this->rut_profesor_co_guia === '' ? null : $this->rut_profesor_co_guia,
            'rut_profesor_comision' => $this->rut_profesor_comision === '' ? null : $this->rut_profesor_comision,
            'rut_profesor_tutor' => $this->rut_profesor_tutor === '' ? null : $this->rut_profesor_tutor,
        ]);
    }
    public function rules(): array
    {
        return [
            // --- Campos Obligatorios ---
            'tipo_habilitacion' => ['required', 'string', Rule::in(['PrIng', 'PrInv', 'PrTut'])],
            'descripcion_habilitacion' => ['required', 'string', 'min:50', 'max:500'],
        
            'año_semestre' => ['required', 'numeric', 'between:2025,2050'], 
            'numero_semestre' => ['required', 'numeric', Rule::in([1, 2])], 
        
            // --- Campos Opcionales ---
            'nota_final' => ['nullable', 'numeric', 'between:1.0,7.0'],
            'fecha_nota' => ['nullable', 'date_format:d/m/Y', 'after_or_equal:01/01/2025', 'before_or_equal:31/12/2050'],
            'titulo_proyecto_practica' => ['nullable', 'string', 'min:3', 'max:100'],
        
            // PrTut
            'rut_empresa' => ['nullable', 'numeric', 'between:1000000,60000000'],
            'nombre_empresa' => ['nullable', 'string'],
            'rut_supervisor' => ['nullable', 'numeric', 'between:1000000,60000000'],
            'nombre_supervisor' => ['nullable', 'string'],
        
            // Profesores (tabla 'asigna')
            'rut_profesor_guia' => ['nullable', 'numeric', Rule::exists('profesor', 'rut_profesor')], 
            'rut_profesor_co_guia' => ['nullable', 'numeric', Rule::exists('profesor', 'rut_profesor')],
            'rut_profesor_comision' => ['nullable', 'numeric', Rule::exists('profesor', 'rut_profesor')],
            'rut_profesor_tutor' => ['nullable', 'numeric', Rule::exists('profesor', 'rut_profesor')],
        ];
    }
}
