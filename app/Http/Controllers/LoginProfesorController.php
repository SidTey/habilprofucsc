<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\AutentificacionDeUsuario;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LoginProfesorController extends Controller
{
    /**
     * Maneja el intento de autenticación (R5).
     */
    public function store(Request $request)
    {
        // 1. Clave para el bloqueo de cuenta (R5.6)
        $throttleKey = Str::lower($request->input('rut_profesor')) . '|' . $request->ip();

        // 2. Revisar si la cuenta está bloqueada (R5.6.1)
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $minutes = ceil($seconds / 60);

            // R5.6.2: Mensaje de cuenta bloqueada
            return response()->json([
                'message' => "Cuenta bloqueada por exceso de intentos. Intente nuevamente en {$minutes} minutos."
            ], 429); // 429: Too Many Requests
        }

        // 3. Validar el formato de los campos (R5.3, R1.4, R5.1)
        // (La validación de 'rut_profesor' es correcta, de 7 a 9 dígitos)
        $validator = Validator::make($request->all(), [
            'rut_profesor' => 'required|integer|min:1000000|max:999999999',
            'password' => 'required|string|min:8|max:30',
        ], [
            'rut_profesor.min' => 'El formato del RUT no es válido.',
            'rut_profesor.max' => 'El formato del RUT no es válido.',
            'rut_profesor.required' => 'El rut ingresado es incorrecto.',
            'password.required' => 'La contraseña ingresada es incorrecta.',
        ]);

        if ($validator->fails()) {
            RateLimiter::hit($throttleKey, 15 * 60);
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 4. Validar existencia del RUT (R5.2.1)
        // ¡CAMBIO CLAVE! Buscamos en la tabla de autenticación, no en 'profesor'
        // Usamos find() porque 'rut_profesor' es la Primary Key.
        $authData = AutentificacionDeUsuario::find($request->rut_profesor);

        if (!$authData) {
            RateLimiter::hit($throttleKey, 15 * 60);
            return response()->json(['message' => 'El rut ingresado es incorrecto.'], 422);
        }

        // 5. Intentar autenticación (R5.4)

        // Laravel usará el 'password' del formulario y lo comparará con
        // la columna 'contraseña' de la BD, gracias a la función
        // getAuthPasswordName() que definimos en el modelo.
        $credentials = [
            'rut_profesor' => $request->rut_profesor,
            'password' => $request->password
        ];

        if (!Auth::guard('profesor')->attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey, 15 * 60);
            return response()->json(['message' => 'La contraseña ingresada es incorrecta.'], 422); // R5.2.2
        }

        // 6. Éxito en la autenticación (R5.5)
        RateLimiter::clear($throttleKey);
        $request->session()->regenerate(); // R5.5.1: Crear sesión


        // Obtenemos el usuario logueado (que es un modelo 'AutentificacionDeUsuarios')
        $authUser = Auth::guard('profesor')->user();
        // Usamos la relación 'profesor()' que definimos para obtener los datos del profesor (nombre, etc.)
        $profesor = $authUser->profesor;

        // Devolvemos el profesor (con el nombre) y el mensaje de éxito
        return response()->json([
            'message' => 'Inicio de sesión exitoso', // R5.5.3
            'profesor' => $profesor // <-- Ahora enviamos el objeto Profesor, no el de Auth
        ], 200);
    }

    /**
     * Cierra la sesión del usuario (R5.8).
     */
    public function destroy(Request $request)
    {
        Auth::guard('profesor')->logout();

        $request->session()->invalidate(); // R5.8.1
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Sesión cerrada exitosamente.'], 200);
    }

    /**
     * Devuelve el usuario autenticado (para React)
     */
    public function user(Request $request)
    {

        // Obtenemos el modelo de Autenticación
        $authData = $request->user('profesor');

        // Devolvemos el modelo de Profesor asociado (que tiene el nombre)
        return $authData ? $authData->profesor : null;
    }
}
