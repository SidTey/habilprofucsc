<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    /**
     * Mostrar formulario de login
     */
    public function showLogin()
    {
        // Si ya está autenticado, redirigir al dashboard
        if (Session::has('user_authenticated')) {
            return redirect()->route('dashboard');
        }
        
        return view('login');
    }

    /**
     * Procesar login
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $username = $request->input('username');
        $password = $request->input('password');

        try {
            // Buscar usuario en la tabla autentificacion_de_usuarios
            $user = DB::table('autentificacion_de_usuarios')
                ->where('nombre_usuario', $username)
                ->first();

            if (!$user) {
                return back()->with('error', 'Usuario o contraseña incorrectos');
            }

            // Verificar contraseña (asumiendo que está hasheada con bcrypt)
            // Si las contraseñas están en texto plano en la BD, usar: $password === $user->contraseña
            if (Hash::check($password, $user->contraseña)) {
                // Guardar información en sesión
                Session::put('user_authenticated', true);
                Session::put('user_id', $user->id_usuario);
                Session::put('username', $user->nombre_usuario);
                Session::put('user_type', $user->tipo_usuario);

                return redirect()->route('dashboard');
            } else {
                return back()->with('error', 'Usuario o contraseña incorrectos');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error al intentar iniciar sesión: ' . $e->getMessage());
        }
    }

    /**
     * Cerrar sesión
     */
    public function logout()
    {
        Session::flush();
        return redirect()->route('login')->with('error', 'Sesión cerrada exitosamente');
    }
}
