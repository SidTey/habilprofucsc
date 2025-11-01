<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Prueba R4 — Habilitaciones (JSON)</title>
    <style>
      body{font-family:system-ui,Segoe UI,Roboto,Helvetica,Arial,sans-serif;margin:1rem}
      input{margin:0 .5rem .5rem 0;padding:.35rem;border:1px solid #ccc;border-radius:4px}
      button{padding:.35rem .6rem}
      pre{background:#f8f8f8;border:1px solid #ddd;padding:8px;white-space:pre-wrap;max-height:300px;overflow:auto}
      label{display:block;margin-top:.5rem}
      h1,h2{margin:.5rem 0}
    </style>
  </head>
  <body>
    <div style="max-width:900px;margin:0 auto;padding:1rem;">
      <h1>Prueba R4 — API de Habilitaciones (JSON)</h1>

      <section style="margin-bottom:1.5rem;">
        <h2>Listado semestral (JSON)</h2>
        <label>Semestre (YYYY-S)</label>
        <input id="semestre_input" placeholder="2025-1" />
        <button id="btn_semestral">Consultar (JSON)</button>
  <pre id="out_semestral" style="display:none"></pre>
  <div id="table_semestral"></div>
      </section>

      <section style="margin-bottom:1.5rem;">
        <h2>Listado histórico (JSON)</h2>
        <label>Rut Profesor</label>
        <input id="rut_input" placeholder="11111111" />
        <label>Semestre (YYYY-S)</label>
        <input id="sem_input_hist" placeholder="2025-1" />
        <button id="btn_hist">Consultar (JSON)</button>
  <pre id="out_hist" style="display:none"></pre>
  <div id="table_hist"></div>
      </section>

      <p>Nota: estos endpoints devuelven JSON y no requieren que Vite esté activo. Útil para probar la lógica del servidor.</p>
    </div>

    <script>
    async function doGet(url, outEl, callback) {
        outEl.textContent = 'Cargando...';
        try {
            const res = await fetch(url, { credentials: 'same-origin' });
            const text = await res.text();
            try {
                const json = JSON.parse(text);
                outEl.textContent = JSON.stringify(json, null, 2);
                if (typeof callback === 'function') callback(json);
            } catch(e) {
                outEl.textContent = text;
                if (typeof callback === 'function') callback(null);
            }
        } catch(err) {
            outEl.textContent = 'Error: ' + err.message;
            if (typeof callback === 'function') callback(null);
        }
    }

    function renderTable(rows, container, searchRut){
      if (!rows || !rows.length) { container.innerHTML = '<p>No hay resultados.</p>'; return; }
      const table = document.createElement('table');
      table.style.width = '100%';
      table.style.borderCollapse = 'collapse';
      table.border = '1';
      table.cellPadding = '6';
      const thead = document.createElement('thead');
      const hrow = document.createElement('tr');
  const headers = ['Id','Rut Alumno','Nombre Alumno','Profesor Guia','Co-Guia','Comision','Tutor','Título/Empresa','Descripción','Nota'];
      headers.forEach(h=>{ const th = document.createElement('th'); th.textContent = h; th.style.padding='6px'; hrow.appendChild(th); });
      thead.appendChild(hrow); table.appendChild(thead);
      const tbody = document.createElement('tbody');
      rows.forEach(r=>{
        const tr = document.createElement('tr');
        const get = k => (r[k] !== undefined && r[k] !== null) ? r[k] : '';
        const desc = get('descripcion_habilitacion') || '';
        const titulo = get('titulo_proyecto') || get('nombre_empresa') || '';
        // Determine ticks for roles. If `rol` is present (historico endpoint), use it.
        // Otherwise, if rut columns exist (semestral endpoint), compare against searchRut when provided.
  const guiaTick = (r.rol) ? (r.rol === 'Profesor_Guia') : (searchRut && String(get('rut_guia')) === String(searchRut));
  const coTick = (r.rol) ? (r.rol === 'Profesor_Co_Guia') : (searchRut && String(get('rut_co_guia')) === String(searchRut));
  const comTick = (r.rol) ? (r.rol === 'Profesor_Comision') : (searchRut && String(get('rut_comision')) === String(searchRut));
  const tutorTick = (r.rol) ? (r.rol === 'Profesor_Tutor') : (searchRut && String(get('rut_tutor')) === String(searchRut));

        const cells = [get('id_habilitacion'), get('rut_alumno'), get('nombre_alumno')];
        cells.forEach(c=>{ const td = document.createElement('td'); td.style.padding='6px'; td.textContent = c; tr.appendChild(td); });

        // Role columns: for histórico mostramos ticks según `rol`; para semestral mostramos el RUT asignado (si existe)
        const tick = v => { const td = document.createElement('td'); td.style.padding='6px'; td.style.textAlign='center'; td.textContent = v ? '✓' : ''; return td; };
        const rutCell = v => { const td = document.createElement('td'); td.style.padding='6px'; td.style.textAlign='center'; td.textContent = v ? String(v) : ''; return td; };

        if (r.rol) {
          // histórico: marcar tick según el rol de la fila
          tr.appendChild(tick(guiaTick));
          tr.appendChild(tick(coTick));
          tr.appendChild(tick(comTick));
          tr.appendChild(tick(tutorTick));
        } else {
          // semestral: mostrar el RUT asignado para cada rol si está disponible
          tr.appendChild(rutCell(get('rut_guia')));
          tr.appendChild(rutCell(get('rut_co_guia')));
          tr.appendChild(rutCell(get('rut_comision')));
          // intentar mostrar rut_tutor si el select lo incluye; si no existe, dejar vacío
          tr.appendChild(rutCell(get('rut_tutor')));
        }

        // remaining columns (title/empresa, descripción, nota)
        const tail = [titulo, desc.substring(0,250), get('nota_final')];
        tail.forEach(c=>{ const td = document.createElement('td'); td.style.padding='6px'; td.textContent = c; tr.appendChild(td); });
        tbody.appendChild(tr);
      });
      table.appendChild(tbody);
      container.appendChild(table);
    }

    document.addEventListener('DOMContentLoaded', function(){
      document.getElementById('btn_semestral').addEventListener('click', function(){
        const sem = document.getElementById('semestre_input').value.trim();
        const out = document.getElementById('out_semestral');
        const table = document.getElementById('table_semestral');
        table.innerHTML = '';
        out.style.display = 'block'; out.textContent = '';
        if (!sem) { out.textContent = 'Ingrese semestre (ej. 2025-1)'; return; }
        doGet(`/habilitacion/api/semestral?semestre_inicio=${encodeURIComponent(sem)}`, out, function(json){
          renderTable((json && json.results) ? json.results : [], table);
        });
      });

      document.getElementById('btn_hist').addEventListener('click', function(){
        const rut = document.getElementById('rut_input').value.trim();
        const sem = document.getElementById('sem_input_hist').value.trim();
        const out = document.getElementById('out_hist');
        const table = document.getElementById('table_hist');
        table.innerHTML = '';
        out.style.display = 'block'; out.textContent = '';
        if (!rut || !sem) { out.textContent = 'Ingrese rut_profesor y semestre'; return; }
        doGet(`/habilitacion/api/historico?rut_profesor=${encodeURIComponent(rut)}&semestre_inicio=${encodeURIComponent(sem)}`, out, function(json){
          renderTable((json && json.results) ? json.results : [], table, rut);
        });
      });
    });
    </script>
  </body>
</html>
