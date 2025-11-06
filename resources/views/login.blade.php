<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>HabilProf — Login</title>
    @vite(['resources/css/app.css'])
  </head>
  <body class="login-page">
    <div class="login-container">
      <div class="login-card">
        <div class="login-header">
          <img src="{{ asset('images/ucsc-hero.svg') }}" alt="UCSC Logo" class="login-logo">
          <h1>HabilProf</h1>
          <p>Sistema de Habilitación Profesional</p>
        </div>

        @if(session('error'))
          <div class="alert alert-danger">
            {{ session('error') }}
          </div>
        @endif

        @if(session('message'))
          <div class="alert alert-success">
            {{ session('message') }}
          </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" class="login-form">
          @csrf
          
          <div class="form-group">
            <label for="username">RUT (sin puntos ni guión)</label>
            <input 
              type="text" 
              id="username" 
              name="username" 
              class="form-control" 
              placeholder="Ej: 12345678"
              required
              autofocus
            >
          </div>

          <div class="form-group">
            <label for="password">Contraseña</label>
            <input 
              type="password" 
              id="password" 
              name="password" 
              class="form-control" 
              placeholder="Ingrese su contraseña"
              required
            >
          </div>

          <button type="submit" class="btn btn-primary btn-block">
            Iniciar Sesión
          </button>
        </form>
      </div>
    </div>
  </body>
</html>
