<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
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
    // Aceptar tanto 'rut_admin' (backup) como 'rut_profesor' (frontend)
    $rut = $request->input('rut_admin') ?? $request->input('rut_profesor');

    // 1. Clave para el bloqueo de cuenta (R5.6)
    $throttleKey = Str::lower($rut) . '|' . $request->ip();

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
        // (La validación de 'rut_admin' es correcta, de 7 a 9 dígitos)
        // Normalizamos los datos para validación (usamos 'rut_admin' como campo canónico)
        $dataToValidate = [
            'rut_admin' => $rut,
            'password' => $request->input('password'),
        ];

        $validator = Validator::make($dataToValidate, [
            'rut_admin' => 'required|integer|min:1000000|max:999999999',
            'password' => 'required|string|min:8|max:30',
        ], [
            'rut_admin.min' => 'El formato del RUT no es válido.',
            'rut_admin.max' => 'El formato del RUT no es válido.',
            'rut_admin.required' => 'El rut ingresado es incorrecto.',
            'password.required' => 'La contraseña ingresada es incorrecta.',
        ]);

        if ($validator->fails()) {
            RateLimiter::hit($throttleKey, 15 * 60);
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 4. Validar existencia del RUT (R5.2.1)
        // ¡CAMBIO CLAVE! Buscamos en la tabla de autenticación, no en 'profesor'
        // Usamos find() porque 'rut_admin' es la Primary Key.
    $authData = AutentificacionDeUsuario::find($rut);

        if (!$authData) {
            RateLimiter::hit($throttleKey, 15 * 60);
            return response()->json(['message' => 'El rut ingresado es incorrecto.'], 422);
        }

        // 5. Intentar autenticación (R5.4)

        // Laravel usará el 'password' del formulario y lo comparará con
        // la columna 'contraseña' de la BD, gracias a la función
        // getAuthPasswordName() que definimos en el modelo.
        // Construir credenciales usando la columna PK del modelo
        $idName = (new AutentificacionDeUsuario())->getKeyName();
        $credentials = [
            $idName => $rut,
            'password' => $request->input('password')
        ];

        if (!Auth::guard('profesor')->attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey, 15 * 60);
            return response()->json(['message' => 'La contraseña ingresada es incorrecta.'], 422); // R5.2.2
        }

        // 6. Éxito en la autenticación (R5.5)
        RateLimiter::clear($throttleKey);
        $request->session()->regenerate(); // R5.5.1: Crear sesión


        // Obtenemos el usuario logueado (que es un modelo 'AutentificacionDeUsuario')
        $authUser = Auth::guard('profesor')->user();
        
        // Por ahora devolvemos un objeto simple con los datos del admin
        // (La tabla autentificacion_de_usuario solo tiene rut_admin, no nombre)
        $userData = [
            'rut_admin' => $authUser->rut_admin,
            'nombre_profesor' => 'Administrador', // Placeholder hasta tener relación con profesor
        ];

        // Devolvemos los datos del usuario y el mensaje de éxito
        return response()->json([
            'message' => 'Inicio de sesión exitoso', // R5.5.3
            'profesor' => $userData
        ], 200);
    }

    /**
     * Cierra la sesión del usuario (R5.8).
     * Compatible con sistema original (AuthController) y nuevo sistema (React).
     */
    public function destroy(Request $request)
    {
        // Logout del guard 'profesor' (sistema nuevo React)
        Auth::guard('profesor')->logout();

        // Limpiar sesión completa (compatible con sistema original)
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

        if (!$authData) {
            return response()->json(null, 401);
        }

        // Devolvemos un objeto simple con los datos del admin
        return response()->json([
            'rut_admin' => $authData->rut_admin,
            'nombre_profesor' => 'Administrador', // Placeholder
        ]);
    }

    /**
     * === MÉTODOS COMPATIBLES CON TU SISTEMA ORIGINAL (AuthController) ===
     */

    /**
     * Mostrar formulario de login (compatible con rutas web originales)
     * Retorna la SPA React que incluye el componente Login
     */
    public function showLogin()
    {
        // Si ya está autenticado con el guard 'profesor', redirigir al dashboard
        if (Auth::guard('profesor')->check()) {
            return redirect('/dashboard');
        }
        
        // Si hay sesión manual del sistema original
        if (Session::has('user_authenticated')) {
            return redirect('/dashboard');
        }
        
        // Retornar la vista welcome.blade.php que contiene la SPA React con Login
        return view('welcome');
    }

    /**
     * Procesar login (versión web/blade compatible con sistema original)
     * Alternativa al método store() para rutas web tradicionales
     */
    public function loginWeb(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $username = $request->input('username');
        $password = $request->input('password');

        try {
            // Intentar login con guard 'profesor' usando rut_admin
            // Asumiendo que username puede ser el RUT
            $credentials = [
                'rut_admin' => $username,
                'password' => $password
            ];

            if (Auth::guard('profesor')->attempt($credentials, false)) { // remember = false
                $request->session()->regenerate();
                
                // Compatibilidad: Guardar también en sesión manual
                $authUser = Auth::guard('profesor')->user();
                Session::put('user_authenticated', true);
                Session::put('user_id', $authUser->rut_admin);
                Session::put('username', 'admin'); // Placeholder

                return redirect('/dashboard');
            } else {
                return back()->with('error', 'Usuario o contraseña incorrectos');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error al intentar iniciar sesión: ' . $e->getMessage());
        }
    }

    /**
     * Cerrar sesión (versión web/blade compatible con sistema original)
     */
    public function logoutWeb()
    {
        Auth::guard('profesor')->logout();
        Session::flush();
        return redirect('/login')->with('message', 'Sesión cerrada exitosamente');
    }
}
