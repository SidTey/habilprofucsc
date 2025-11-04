<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Prueba R4 — Habilitaciones (JSON)</title>
    @vite(['resources/css/app.css'])
  </head>
  <body>
    <!-- Barra de navegación -->
    <nav class="navbar">
      <div class="navbar-left">
        <img src="{{ asset('images/ucsc-hero.svg') }}" alt="UCSC Logo" class="navbar-logo">
      </div>
      <h1>HabilProf</h1>
      <div class="nav-menu">
        <button class="nav-btn active" data-section="historico">Histórico</button>
        <button class="nav-btn" data-section="semestral">Semestral</button>
        <button class="nav-btn" data-section="ingresar">Ingresar datos</button>
        <button class="nav-btn" data-section="eliminar">Eliminar datos</button>
        <button class="nav-btn logout" onclick="handleLogout()">Cerrar sesión</button>
      </div>
    </nav>

    <div class="container">
      <!-- Sección Histórico -->
      <section id="section-historico" class="section active">
        <h2>Listado Histórico</h2>
        <iframe src="{{ route('habilitacion.historico.embed') }}" class="form-iframe"></iframe>
      </section>

      <!-- Sección Semestral -->
      <section id="section-semestral" class="section">
        <h2>Listado Semestral</h2>
        <iframe src="{{ route('habilitacion.semestral.embed') }}" class="form-iframe"></iframe>
      </section>

      <!-- Sección Ingresar datos -->
      <section id="section-ingresar" class="section">
        <h2>Ingresar Datos</h2>
        <iframe src="{{ route('habilitacion.agregar.embed') }}" class="form-iframe"></iframe>
      </section>

      <!-- Sección Eliminar datos -->
      <section id="section-eliminar" class="section">
        <h2>Eliminar Datos</h2>
        <p>Formulario de eliminación de habilitación profesional (próximamente).</p>
      </section>
    </div>

    <script>
    // Navegación entre secciones
    function handleLogout() {
      if (confirm('¿Está seguro que desea cerrar sesión?')) {
        window.location.href = '{{ route("logout") }}';
      }
    }

    document.addEventListener('DOMContentLoaded', function(){
      // Manejo de navegación
      const navButtons = document.querySelectorAll('.nav-btn:not(.logout)');
      const sections = document.querySelectorAll('.section');
      
      navButtons.forEach(btn => {
        btn.addEventListener('click', function() {
          const targetSection = this.getAttribute('data-section');
          
          // Remover active de todos los botones y secciones
          navButtons.forEach(b => b.classList.remove('active'));
          sections.forEach(s => s.classList.remove('active'));
          
          // Activar botón y sección correspondiente
          this.classList.add('active');
          document.getElementById('section-' + targetSection).classList.add('active');
        });
      });
    });
    </script>
  </body>
</html>
