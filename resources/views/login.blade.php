<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - HabilProf UCSC</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <img src="{{ asset('images/ucsc-hero.svg') }}" alt="UCSC Logo" class="login-logo">
                <h1>HabilProf</h1>
                <p>Sistema de Habilitación Profesional</p>
            </div>

            @if(session('error'))
                <div class="alert-error">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="login-form">
                @csrf
                <div class="form-group">
                    <label for="username">Usuario</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        placeholder="Ingrese su usuario"
                        value="{{ old('username') }}"
                        required
                        autofocus
                    >
                    @error('username')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Ingrese su contraseña"
                        required
                    >
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn-login">Iniciar Sesión</button>
            </form>
        </div>
    </div>
</body>
</html>
