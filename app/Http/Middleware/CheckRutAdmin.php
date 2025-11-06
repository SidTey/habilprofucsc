<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRutAdmin
{
    /**
     * Comprueba que el RUT proporcionado exista en la tabla
     * `autentificacion_de_usuario` (rut_admin).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $rut = null;

        // 1) Si hay un usuario autenticado por el guard 'profesor', intentamos obtener un RUT
        if (Auth::guard('profesor')->check()) {
            $user = Auth::guard('profesor')->user();
            $rut = $user->rut_admin ?? $user->rut_profesor ?? null;
        }

        // 2) Si no hay usuario auth, intentamos obtener rut desde la sesión o request
        if (!$rut) {
            $rut = $request->input('rut_admin') ?? $request->route('rut_admin') ?? session('user_id');
        }

        if (!$rut) {
            abort(403, 'Acceso denegado: RUT no proporcionado');
        }

        $exists = DB::table('autentificacion_de_usuario')
            ->where('rut_admin', $rut)
            ->exists();

        if (!$exists) {
            abort(403, 'Acceso denegado: no es administrador');
        }

        return $next($request);
    }
}
