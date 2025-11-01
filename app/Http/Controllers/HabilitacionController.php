<?php

namespace App\Http\Controllers;

use App\Models\Habilitacion;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Database\QueryException;
use App\Http\Requests\UpdateHabilitacionRequest;
use App\Models\Profesor;
use Illuminate\Support\Facades\DB;
use App\Models\Pring;
use App\Models\Prinv;
use App\Models\Prtut;


class HabilitacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

    $habilitaciones = \App\Models\Habilitacion::with(['alumno', 'pring', 'prinv', 'prtut'])->get();

    // 1. Obtenemos todas las habilitaciones
    $habilitaciones->each(function ($hab) {
        if ($hab->pring) {
            $hab->tipo_habilitacion = 'PrIng';
        } elseif ($hab->prinv) {
            $hab->tipo_habilitacion = 'PrInv';
        } elseif ($hab->prtut) {
            $hab->tipo_habilitacion = 'PrTut';
        } else {
            $hab->tipo_habilitacion = 'N/A'; // O 'Indefinido'
        }
    });

    // 2. Renderizamos la página de React y le pasamos los datos
        return Inertia::render('Habilitaciones/Index', ['habilitaciones' => $habilitaciones,]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Habilitacion $habilitacion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $habilitacione) // <-- 1. CAMBIO AQUÍ
    {
        // 2. BUSCAMOS MANUALMENTE EL MODELO USANDO EL ID DE LA URL
        $habilitacion = \App\Models\Habilitacion::findOrFail($habilitacione);

        // 3. AHORA SÍ CARGAMOS LAS RELACIONES EN EL MODELO CORRECTO
        $habilitacion->load('alumno', 'profesoresAsignados', 'pring', 'prinv', 'prtut');

        // 4. Carga la lista completa de profesores
        $profesores = \App\Models\Profesor::all(['rut_profesor', 'nombre_profesor']);
        
        // 5. Pre-procesa los roles actuales para el formulario
        $roles_actuales = $habilitacion->profesoresAsignados
            ->mapWithKeys(function ($profesor) {
                // Usamos la columna 'rol' que encontramos en tu DBeaver
                return [$profesor->pivot->rol => $profesor->rut_profesor];
            });

        // 6. Renderiza la vista con los datos correctos
        return Inertia::render('Habilitaciones/Edit', [
            'habilitacion' => $habilitacion,
            'profesores' => $profesores,
            'roles_actuales' => $roles_actuales,
        ]);
    }

    public function update(UpdateHabilitacionRequest $request, string $habilitacion)
    {

        $habilitacion = \App\Models\Habilitacion::findOrFail($habilitacion);
        // 1. Obtenemos TODA la data que pasó la validación
        $validatedData = $request->validated();


        // Llaves de la tabla 'habilitacion_profesional' (la "padre")
        $habilitacionKeys = [
            'descripcion_habilitacion', 'año_semestre', 'numero_semestre',
            'nota_final', 'fecha_nota'
        ];

        // Llaves de la tabla 'asigna' (los profesores)
        $pivotKeys = [
            'rut_profesor_guia' => 'Profesor_Guia',
            'rut_profesor_co_guia' => 'Profesor_Co_Guia',
            'rut_profesor_comision' => 'Profesor_Comision',
            'rut_profesor_tutor' => 'Profesor_Tutor'
        ];

        // 3. Construimos el array para la tabla pivote 'asigna'
        $pivotData = [];
        foreach ($pivotKeys as $key => $rol) {
            if (!empty($validatedData[$key])) {
                $pivotData[$validatedData[$key]] = ['rol' => $rol];
            }
        }

        try {
            DB::beginTransaction();

            // 4. Actualizamos la tabla 'habilitacion_profesional' (la "padre")
            //    Usamos $request->only() para tomar SÓLO los campos de esta tabla.
            $habilitacion->update($request->only($habilitacionKeys));

            // 5. Sincronizamos la tabla 'asigna' (profesores)
            $habilitacion->profesoresAsignados()->sync($pivotData);

            // 6. Actualizamos las tablas "hijas" (pring, prtut)
            //    Esta es la lógica que maneja el cambio de tipo

            if ($validatedData['tipo_habilitacion'] === 'PrIng') {
                Pring::updateOrCreate(
                    ['id_habilitacion' => $habilitacion->id_habilitacion],
                    ['titulo_proy' => $validatedData['titulo_proyecto_practica']] // 'titulo_proy' de tu imagen
                );
                // Limpiamos las otras
                Prtut::where('id_habilitacion', $habilitacion->id_habilitacion)->delete();
                Prinv::where('id_habilitacion', $habilitacion->id_habilitacion)->delete();

            } 
            elseif ($validatedData['tipo_habilitacion'] === 'PrTut') {
            Prtut::updateOrCreate(
                ['id_habilitacion' => $habilitacion->id_habilitacion],
                [
                    'rut_empresa' => $validatedData['rut_empresa'],
                    'rut_supervisor' => $validatedData['rut_supervisor']
                ]
            );
            // Limpiamos las otras
            Pring::where('id_habilitacion', $habilitacion->id_habilitacion)->delete();
            Prinv::where('id_habilitacion', $habilitacion->id_habilitacion)->delete();
        } 
            elseif ($validatedData['tipo_habilitacion'] === 'PrInv') {
            // (Lógica para PrInv aquí)
            
            // Limpiamos las otras
            Pring::where('id_habilitacion', $habilitacion->id_habilitacion)->delete();
            Prtut::where('id_habilitacion', $habilitacion->id_habilitacion)->delete();
        }
        
            DB::commit();
        
            return redirect()->route('habilitaciones.index')
               ->with('message', 'Datos actualizados con éxito');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
               ->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }
    
    public function destroy(string $habilitacione) // <-- 1. Cambio aquí
        {
            try {
                // 2. Buscamos el modelo MANUALMENTE usando el string de la URL
                $habilitacion = Habilitacion::findOrFail($habilitacione);
            
                // 3. Ahora SÍ tenemos el modelo correcto, lo borramos.
                $deleted = $habilitacion->delete();
            
                if ($deleted) {
                    return redirect()->route('habilitaciones.index')
                        ->with('message', 'Habilitación Borrada con éxito');
                } else {
                    return redirect()->route('habilitaciones.index')
                        ->with('error', 'Error: El registro no se pudo borrar (fallo silencioso).');
                }
            
            } catch (QueryException $e) {
                // ... (el resto del catch para llaves foráneas sigue igual)
                if ($e->getCode() == '23503') {
                    return redirect()->route('habilitaciones.index')
                        ->with('error', 'Error: Esta habilitación no se puede borrar porque está siendo usada por otros registros.');
                }
                return redirect()->route('habilitaciones.index')
                    ->with('error', 'Error de base de datos: ' . $e->getMessage());
            }
        }
}