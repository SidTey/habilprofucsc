<?php

namespace App\Http\Controllers; // Asegúrate de que no diga \Auth

use App\Models\Profesor;
use App\Models\AutentificacionDeUsuarios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // ¡Importante para la transacción!
use Illuminate\Support\Facades\Hash; // ¡Importante para cifrar!
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password; // Para reglas de contraseña


class RegisterProfesorController extends Controller
{
    /**
     * Maneja una petición de registro (store).
     */
    public function store(Request $request)
    {
        // 1. Validación de los datos
        // Ahora validamos también el correo y la confirmación de contraseña
        $validator = Validator::make($request->all(), [
            'nombre_profesor' => 'required|string|max:100',
            'rut_profesor' => [
                'required',
                'integer',
                'min:1000000',
                'max:999999999',
                'unique:profesor,rut_profesor' // Regla clave: el RUT no debe existir
            ],
            'correo_profesor' => [
                'required',
                'string',
                'email',
                'max:100',
                'unique:autentificacion_de_usuarios,correo_profesor' // El correo no debe existir
            ],
            'password' => [
                'required',
                'confirmed', // Requiere un campo llamado 'password_confirmation'
                Password::min(8) // Reglas de contraseña (mínimo 8 caracteres)
            ],
        ], [
            // Mensajes de error personalizados
            'rut_profesor.unique' => 'Este RUT ya está registrado.',
            'correo_profesor.unique' => 'Este correo ya está registrado.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 2. Usamos una "Transacción"
        // Esto asegura que si falla la creación en una tabla,
        // se deshace la creación en la otra. Es "todo o nada".
        try {

            DB::transaction(function () use ($request) {

                // 2A. Crea el Profesor (solo con nombre y RUT)
                $profesor = Profesor::create([
                    'nombre_profesor' => $request->nombre_profesor,
                    'rut_profesor' => $request->rut_profesor,
                    'correo_profesor' => $request->correo_profesor // <-- AÑADE ESTO
                     // ¡Cifra la contraseña!
                ]);

                // 2B. Crea los datos de autenticación asociados
                // Usamos la relación 'autenticacion()' que definimos en el Modelo Profesor
                $profesor->autenticacion()->create([
                    'rut_profesor' => $request->rut_profesor,
                    'correo_profesor' => $request->correo_profesor,
                    'contraseña' => Hash::make($request->password), // ¡Cifra la contraseña!
                ]);

            });

        } catch (\Exception $e) {
            // Si la transacción falla por cualquier motivo
            return response()->json([
                'message' => 'Error al crear el usuario.',
                'error' => $e->getMessage() // Envía el error real para depuración
            ], 500);
        }

        // 3. Devolver respuesta de éxito
        return response()->json([
            'message' => 'Profesor registrado exitosamente.'
        ], 201); // 201 = Recurso Creado
    }
}
