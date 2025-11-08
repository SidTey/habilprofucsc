<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use App\Models\AutentificacionDeUsuario;
use App\Models\Profesor;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\PasswordResetCode;


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
        $authData = AutentificacionDeUsuario::find($rut);

        if (!$authData) {
            RateLimiter::hit($throttleKey, 15 * 60);
            return response()->json(['message' => 'El rut ingresado es incorrecto.'], 422);
        }

        // 5. Intentar autenticación (R5.4)
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

        $authUser = Auth::guard('profesor')->user();
        $userData = [
            'rut_admin' => $authUser->rut_admin,
            'nombre_profesor' => 'Administrador', // Placeholder hasta tener relación con profesor
        ];

        return response()->json([
            'message' => 'Inicio de sesión exitoso', // R5.5.3
            'profesor' => $userData
        ], 200);
    }

    /**
     * ✅ Paso 1: Generar código de seguridad y enviarlo por correo.
     * Ruta: POST /api/forgot-password
     */
    public function sendPasswordResetLink(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rut_profesor' => 'required|integer|min:1000000|max:999999999',
            'email' => 'required|email',
        ], [
            'rut_profesor.required' => 'El campo RUT es obligatorio.',
            'email.required' => 'El campo Correo Electrónico es obligatorio.',
            'email.email' => 'El Correo Electrónico debe ser una dirección válida.',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $rut = $request->rut_profesor;
        $email = $request->email;

        // 1. Verificar RUT y CORREO en la tabla AutentificacionDeUsuario (usando la columna 'correo')
        $userAuth = AutentificacionDeUsuario::where('rut_admin', $rut)
                            ->where('correo', $email)
                            ->first();

        if (!$userAuth) {
            // Mensaje genérico por seguridad
            return response()->json(['message' => 'Los datos ingresados no coinciden con nuestros registros.'], 422);
        }

        // 2. Obtener el nombre del profesor (si existe) para el correo.
        // NOTA: Usando 'rut_admin' para buscar en 'profesor'

        $userName = 'Estimado Usuario';

        // 3. Generar código (6 dígitos) y guardar hasheado en DB
        $plainToken = Str::padLeft(rand(1, 999999), 6, '0');
        $hashedToken = Hash::make($plainToken);

        // Guardar/Actualizar token en la tabla password_reset_tokens
        DB::table('password_reset_tokens')->updateOrInsert(
            ['rut_admin' => $rut],
            ['token' => $hashedToken, 'created_at' => now()]
        );

        // 4. Enviar el correo electrónico
        try {
            Mail::to($email)->send(new PasswordResetCode($plainToken, $userName));

        } catch (\Exception $e) {
            // Uso de la Facade Log importada:
            Log::error('Error enviando correo de reseteo: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error al intentar enviar el código de seguridad. Intente nuevamente.'
            ], 500);
        }

        // 5. Respuesta de Éxito
        return response()->json([
            'rut' => $rut,
            'message' => 'Si los datos son correctos, el código de seguridad ha sido enviado al correo registrado. Por favor, revise su bandeja de entrada (incluyendo SPAM).'
        ], 200);
    }

    /**
     * ✅ Paso 2: Verificar código y cambiar contraseña.
     * Ruta: POST /api/reset-password
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rut_profesor' => 'required|integer|min:1000000|max:999999999',
            'code' => 'required|string|size:6',
            'password' => 'required|string|min:8|max:30|confirmed',
        ], [
            'code.required' => 'Debe ingresar el código de seguridad.',
            'code.size' => 'El código de seguridad debe tener 6 dígitos.',
            'password.required' => 'El campo nueva contraseña es obligatorio.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $rut = $request->rut_profesor;
        $code = $request->code;
        $newPassword = $request->password;

        // 1. Buscar token y verificar expiración (60 minutos)
        $tokenData = DB::table('password_reset_tokens')
                    ->where('rut_admin', $rut)
                    ->first();

        if (!$tokenData || now()->subMinutes(60)->greaterThan($tokenData->created_at)) {
            if ($tokenData) {
                DB::table('password_reset_tokens')->where('rut_admin', $rut)->delete();
            }
            return response()->json([
                'message' => 'El código de seguridad es inválido o ha expirado. Por favor, solicite uno nuevo.'
            ], 422);
        }

        // 2. Verificar código
        if (!Hash::check($code, $tokenData->token)) {
            return response()->json(['message' => 'El código de seguridad ingresado es incorrecto.'], 422);
        }

        // 3. Actualizar la contraseña en AutentificacionDeUsuario
        $user = AutentificacionDeUsuario::find($rut);
        if (!$user) {
            return response()->json(['message' => 'Error de usuario. Intente solicitar un nuevo código.'], 422);
        }

        // Usamos la columna 'contraseña' de tu tabla
        $user->contraseña = Hash::make($newPassword);
        $user->save();

        // 4. Eliminar el token
        DB::table('password_reset_tokens')->where('rut_admin', $rut)->delete();

        // 5. Éxito
        return response()->json([
            'message' => 'Contraseña restablecida exitosamente. Ahora puede iniciar sesión con su nueva contraseña.'
        ], 200);
    }

    /**
     * Cierra la sesión del usuario (R5.8).
     */
    public function destroy(Request $request)
    {
        Auth::guard('profesor')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Sesión cerrada exitosamente.'], 200);
    }

    /**
     * Devuelve el usuario autenticado (para React)
     */
    public function user(Request $request)
    {
        $authData = $request->user('profesor');

        if (!$authData) {
            return response()->json(null, 401);
        }

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
     */
    public function showLogin()
    {
        if (Auth::guard('profesor')->check()) {
            return redirect('/dashboard');
        }

        if (Session::has('user_authenticated')) {
            return redirect('/dashboard');
        }

        return view('welcome');
    }

    /**
     * Procesar login (versión web/blade compatible con sistema original)
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
            $credentials = [
                'rut_admin' => $username,
                'password' => $password
            ];

            if (Auth::guard('profesor')->attempt($credentials, false)) {
                $request->session()->regenerate();

                $authUser = Auth::guard('profesor')->user();
                Session::put('user_authenticated', true);
                Session::put('user_id', $authUser->rut_admin);
                Session::put('username', 'admin');

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
