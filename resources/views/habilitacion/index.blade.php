@section('content')
<div style="max-width:1000px;margin:2rem auto;padding:1rem;">
    <style>
        /* clases utilitarias locales para evitar colocar Blade en atributos style */
        .hp-visible { display: block; }
        .hp-hidden { display: none; }
        .hp-mb { margin-bottom: 1rem; }
    </style>
    <h1>Listado de Habilitaciones</h1>

    {{-- Pestañas simples --}} 
    <div class="hp-mb">
        <button type="button" onclick="showTab('semestral')">Listado semestral</button>
        <button type="button" onclick="showTab('historico')">Listado histórico</button>
    </div>

    {{-- Mensajes de error HTTP simple --}}
    @if (session('error'))
        <div style="color:red">{{ session('error') }}</div>
    @endif

    {{-- Formulario semestral --}}
    <div id="semestral-form" class="{{ (isset($tab) && $tab==='historico') ? 'hp-hidden' : 'hp-visible' }} hp-mb">
        <form method="POST" action="{{ route('habilitacion.listado.post') }}">
            @csrf
            <label>Semestre (formato YYYY-S, ej. 2025-1)</label>
            <input name="semestre_inicio" value="{{ old('semestre_inicio', $semestre ?? '') }}" />
            <button type="submit">Consultar</button>
        </form>
    </div>

    {{-- Formulario histórico --}}
    <div id="historico-form" class="{{ (isset($tab) && $tab==='historico') ? 'hp-visible' : 'hp-hidden' }} hp-mb">
        <form method="POST" action="{{ route('habilitacion.historico') }}">
            @csrf
            <label>Rut Profesor</label>
            <input name="rut_profesor" value="{{ old('rut_profesor', $rut_profesor ?? '') }}" />
            <label>Semestre (YYYY-S)</label>
            <input name="semestre_inicio" value="{{ old('semestre_inicio', $semestre ?? '') }}" />
            <button type="submit">Consultar Histórico</button>
        </form>
    </div>

    {{-- Resultados --}}
    @if (isset($results) && count($results))
        <h2>Resultados ({{ $tab ?? '' }})</h2>
        <table border="1" cellpadding="6" cellspacing="0" style="width:100%;border-collapse:collapse;">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Rut Alumno</th>
                    <th>Nombre Alumno</th>
                    <th>Profesor Guia</th>
                    <th>Co-Guia</th>
                    <th>Comision</th>
                    <th>Título / Empresa</th>
                    <th>Descripción</th>
                    <th>Nota</th>
                </tr>
            </thead>
            <tbody>
                @foreach($results as $r)
                    <tr>
                        <td>{{ $r->id_habilitacion }}</td>
                        <td>{{ $r->rut_alumno }}</td>
                        <td>{{ $r->nombre_alumno }}</td>
                        <td>{{ $r->nombre_guia ?? '' }}</td>
                        <td>{{ $r->nombre_co_guia ?? '' }}</td>
                        <td>{{ $r->nombre_comision ?? '' }}</td>
                        <td>{{ $r->titulo_proyecto ?? $r->nombre_empresa ?? '' }}</td>
                        <td style="max-width:300px">{{ \Illuminate\Support\Str::limit($r->descripcion_habilitacion, 250) }}</td>
                        <td>{{ $r->nota_final }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No hay resultados para el criterio indicado.</p>
    @endif

    </div>

    <script>
        // Alterna entre las pestañas usando clases CSS para evitar inline styles con Blade
        function showTab(tab) {
            const sem = document.getElementById('semestral-form');
            const hist = document.getElementById('historico-form');
            if (!sem || !hist) return;
            if (tab === 'semestral') {
                sem.classList.remove('hp-hidden'); sem.classList.add('hp-visible');
                hist.classList.remove('hp-visible'); hist.classList.add('hp-hidden');
            } else {
                hist.classList.remove('hp-hidden'); hist.classList.add('hp-visible');
                sem.classList.remove('hp-visible'); sem.classList.add('hp-hidden');
            }
        }

        // Mantener el estado inicial según la clase aplicada por Blade
        (function(){
            // No hacer nada; Blade ya aplica las clases iniciales. Esta IIFE sirve de documento de referencia.
        })();
    </script>

    @endsection
