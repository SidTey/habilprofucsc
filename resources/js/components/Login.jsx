import React, { useState } from 'react';
import axios from 'axios';
// Asegurar envío de cookies (sesión) en peticiones hacia el backend
axios.defaults.withCredentials = true;

function Login({ onLoginSuccess }) {

    // --- Estados para manejar el formulario ---
    const [rut, setRut] = useState('');
    const [password, setPassword] = useState('');
    const [showPassword, setShowPassword] = useState(false);
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(false);

    /**
     * Maneja el envío del formulario de login.
     */
    const handleSubmit = async (e) => {
        // e.preventDefault() es CRUCIAL para evitar que la página se recargue
        // y nos mande a la URL con GET.
        e.preventDefault();

        setLoading(true);
        setError(''); // Limpia errores anteriores

        try {
            // Llama a la ruta POST /api/login (la correcta)
            const response = await axios.post('/api/login', {
                rut_profesor: rut,
                password: password,
            });

            // Login exitoso - redirigir al dashboard Blade
            window.location.href = '/dashboard';

        } catch (err) {
            // Si axios.post falla (ej: 401, 422)
            console.error('Error en el login:', err.response);



            if (err.response && err.response.data) {
                if (err.response.data.message) {
                    // Causa 4 o 5 del Controller (ej: "El rut ingresado es incorrecto")
                    setError(err.response.data.message);
                } else if (err.response.data.errors) {
                    // Causa 3 del Controller (Error de validación)
                    // Tomamos el primer error de la lista
                    const firstErrorKey = Object.keys(err.response.data.errors)[0];
                    setError(err.response.data.errors[firstErrorKey][0]);
                } else {
                    setError('RUT o contraseña incorrectos. (Error desconocido)');
                }
            } else {
                setError('RUT o contraseña incorrectos. (Error de red)');
            }


            setLoading(false); // Reactiva el botón
        }
    };

    return (
        <>
            {/* --- Estilos personalizados --- */}
            <style>
            {`
                :root {
                    --ucsc-red: #C8102E;
                    --ucsc-dark: #231F20; /* Color de los bloques de texto */
                }

                /* --- Estilos Generales del Formulario --- */
                .btn-ucsc {
                    background-color: var(--ucsc-red);
                    border-color: var(--ucsc-red);
                    color: #fff;
                    font-weight: 600;
                    padding-top: 0.75rem;
                    padding-bottom: 0.75rem;
                }
                .btn-ucsc:hover {
                    background-color: #A80D26;
                    border-color: #A80D26;
                    color: #fff;
                }
                .form-control:focus {
                    border-color: var(--ucsc-red);
                    box-shadow: 0 0 0 0.25rem rgba(200, 16, 46, 0.25);
                }
                .link-ucsc {
                    color: var(--ucsc-red);
                    font-weight: 600;
                    text-decoration: none;
                }
                .link-ucsc:hover {
                    color: #A80D26;
                    text-decoration: underline;
                }
                .form-label {
                    font-family: 'Roboto', sans-serif;
                    font-weight: 700;
                }

                /* --- Password Toggle --- */
                .password-wrapper {
                    position: relative;
                    width: 100%;
                    display: flex; /* Asegura que el wrapper tome altura */
                    align-items: center;
                }
                .password-wrapper .form-control {
                    width: 100%;
                    padding-right: 50px; /* Espacio para el botón */
                    z-index: 1;
                }
                .password-toggle-btn {
                    position: absolute;
                    right: 20px;
                    top: calc(48% - 1px);
                    transform: translateY(-50%);

                    background: transparent !important;
                    border: none !important;
                    padding: 0 !important;
                    margin: 0 !important;

                    display: flex;
                    align-items: center;
                    justify-content: center;

                    color: #000 !important; /* Negro forzado */
                    z-index: 1000 !important; /* Z-index muy alto */
                    cursor: pointer;
                    width: 30px; /* Ancho fijo para asegurar área de clic */
                    height: 30px; /* Alto fijo */
                }
                .password-toggle-btn:hover {
                    color: var(--ucsc-red) !important;
                }
                .password-toggle-btn:focus {
                    outline: none;
                }
                .password-toggle-btn svg {
                    width: 24px;
                    height: 24px;
                    stroke-width: 2;
                }

                /* --- Estilos del Banner --- */
                .columna-banner {
                    background-color: var(--ucsc-red);
                    position: relative;
                    overflow: hidden;
                    min-height: 500px;
                }

                /* Capa de fondo (imagen) y overlay */
                .columna-banner::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background-image: url('/images/banner-ucsc.jpg'); /* ¡La imagen de fondo! */
                    background-size: cover;
                    background-position: center center;
                    background-repeat: no-repeat;

                    /* Overlay para opacidad */
                    background-color: rgba(0, 0, 0, 0.5);
                    background-blend-mode: multiply;
                }

                .banner-wrapper-nuevo {
                    position: relative; /* Se pone sobre el ::before */
                    width: 100%;
                    height: 100%;
                    min-height: 600px;
                    display: flex;
                    align-items: center;
                    padding: 2rem 4rem;
                    color: white;
                }

                /* --- Estilos de Texto del Banner --- */
                .banner-texto-wrapper {
                    position: relative;
                    z-index: 3;
                }
                .texto-bloque {
                    background-color: var(--ucsc-dark);
                    font-size: 2.8rem;
                    font-weight: 700;
                    padding: 0.25rem 1.5rem;
                    margin-bottom: 0.5rem;
                    display: table;
                }
                .admision-bloque {
                    margin-top: 1.5rem;
                    font-size: 2rem;
                    font-weight: 300;
                    display: flex;
                    align-items: center;
                    flex-wrap: wrap;
                }
                .admision-bloque strong {
                    font-weight: 700;
                    margin: 0 0.5rem;
                }
                .admision-bloque .hoy-box {
                    background-color: var(--ucsc-red);
                    border: 2px solid white;
                    font-weight: 700;
                    padding: 0.25rem 1rem;
                    margin-left: 0.5rem;
                    font-size: 2.2rem;
                    line-height: 1;
                }
                .carreras-texto {
                    font-size: 1.5rem;
                    font-weight: 600;
                    margin-top: 1.5rem;
                    color: white; /* Asegura que sea blanco */
                }
            `}
            </style>

            {/* --- Contenedor Principal (Layout de 2 columnas) --- */}
            <div className="container-fluid p-0">
                <div className="row g-0">

                    {/* --- Columna Izquierda (Formulario) --- */}
                    <div className="col-lg-6 bg-light d-flex align-items-center justify-content-center min-vh-100">
                        <div className="p-4 p-md-5" style={{ maxWidth: '500px', width: '100%' }}>

                            {/* Logo y Título */}
                            <div className="mb-4">
                                                                <img
                                                                    src="/images/ucsc-hero.svg" // Usar logo disponible en public/images/
                                  alt="Logo UCSC"
                                  style={{ height: '50px' }}
                                  className="mb-2"
                                />
                                <h1 className="h3 fw-bold text-dark mt-2">Portal Habilitación Profesional</h1>
                            </div>

                            {/* --- FORMULARIO CONECTADO A REACT --- */}
                            <form onSubmit={handleSubmit}>

                                {/* Mensaje de Error */}
                                {error && (
                                    <div className="alert alert-danger mb-3">
                                        {error}
                                    </div>
                                )}

                                <div className="mb-3">
                                    <label htmlFor="rut" className="form-label">RUT</label>
                                    <input
                                        type="text"
                                        id="rut"
                                        name="rut_profesor"
                                        className="form-control form-control-lg"
                                        placeholder="Sin puntos, ni dígito verificador"
                                        autoComplete="username"
                                        required
                                        value={rut} // Conectado al estado
                                        onChange={(e) => setRut(e.target.value)} // Conectado al estado
                                    />
                                </div>

                                <div className="mb-4">
                                    <label htmlFor="password" className="form-label">Contraseña</label>
                                    <div className="password-wrapper">
                                        <input
                                            type={showPassword ? "text" : "password"}
                                            id="password"
                                            name="password"
                                            className="form-control form-control-lg"
                                            placeholder="Contraseña"
                                            autoComplete="current-password"
                                            required
                                            value={password} // Conectado al estado
                                            onChange={(e) => setPassword(e.target.value)} // Conectado al estado
                                            style={{ paddingRight: '45px' }}
                                        />
                                        <button
                                            type="button"
                                            className="password-toggle-btn"
                                            onClick={() => setShowPassword(!showPassword)}
                                            aria-label={showPassword ? "Ocultar contraseña" : "Mostrar contraseña"}
                                            tabIndex="-1"
                                        >
                                            {showPassword ? (
                                                // Ojo tachado (Ocultar)
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                                                    <path strokeLinecap="round" strokeLinejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                                </svg>
                                            ) : (
                                                // Ojo normal (Mostrar)
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                                                    <path strokeLinecap="round" strokeLinejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                    <path strokeLinecap="round" strokeLinejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                            )}
                                        </button>
                                    </div>
                                </div>

                                <button
                                    type="submit"
                                    className="btn btn-ucsc w-100 btn-lg"
                                    disabled={loading} // Deshabilitado mientras carga
                                >
                                    {loading ? 'Ingresando...' : 'INGRESAR'}
                                </button>
                            </form>


                        </div>
                    </div>

                    {/* --- Columna Derecha (Banner) --- */}
                    <div className="col-lg-6 d-none d-lg-flex columna-banner">
                        <div className="banner-wrapper-nuevo">


                            {/* --- Bloques de Texto --- */}
                            <div className="banner-texto-wrapper">
                                <div className="texto-bloque">HABILITACIÓN</div>
                                <div className="texto-bloque">PROFESIONAL</div>
                                <div className="admision-bloque">
                                    <strong>2026</strong>
                                    <span className="hoy-box">DINF</span>
                                </div>
                                <p className="carreras-texto">INGENIERÍA CIVIL INFORMÁTICA</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {/* --- Footer (Copyright) --- */}
            <footer className="text-center p-3 bg-light text-muted small border-top">
                © 2025 UCSC. Todos los derechos reservados
            </footer>
        </>
    );
}

export default Login;

