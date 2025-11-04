import React, { useState } from 'react';
import axios from 'axios';

/**
 * NOTA: Este componente asume que ya has cargado el CSS de Bootstrap
 * en tu archivo principal (como 'welcome.blade.php').
 */

// Recibe la prop onLoginSuccess desde App.jsx
function Login({ onLoginSuccess }) {

    // --- Estados para manejar el formulario ---
    const [rut, setRut] = useState('');
    const [password, setPassword] = useState('');
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(false);

    /**
     * Maneja el envío del formulario de login.
     */
    const handleSubmit = async (e) => {
        // e.preventDefault() es CRUCIAL para evitar que la página se recargue
        // y nos mande a la URL con GET (el error que tenías).
        e.preventDefault();

        setLoading(true);
        setError(''); // Limpia errores anteriores

        try {
            // Llama a la ruta POST /api/login (la correcta)
            const response = await axios.post('/login', {
                rut_profesor: rut,
                password: password,
            });

            // Si axios.post tiene éxito, llamamos a la función de App.jsx
            // para que actualice el estado de la aplicación.
            onLoginSuccess(response.data.profesor);

        } catch (err) {
            // Si axios.post falla (ej: 401, 422)
            console.error('Error en el login:', err.response);

            // --- ¡CAMBIO IMPORTANTE PARA DEPURAR! ---
            // Vamos a mostrar el error específico que envía Laravel,
            // en lugar de un mensaje genérico.
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
            // --- FIN DEL CAMBIO ---

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
                                  src="/images/logo-ucsc-color.png" // ¡Recuerda tener esta imagen en public/images/!
                                  alt="Logo UCSC"
                                  style={{ height: '50px' }}
                                  className="mb-2"
                                />
                                <h1 className="h3 fw-bold text-dark mt-2">Portal Habilitacion Profesional</h1>
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
                                    <input
                                        type="password"
                                        id="password"
                                        name="password"
                                        className="form-control form-control-lg"
                                        placeholder="Contraseña"
                                        autoComplete="current-password"
                                        required
                                        value={password} // Conectado al estado
                                        onChange={(e) => setPassword(e.target.value)} // Conectado al estado
                                    />
                                </div>

                                <button
                                    type="submit"
                                    className="btn btn-ucsc w-100 btn-lg"
                                    disabled={loading} // Deshabilitado mientras carga
                                >
                                    {loading ? 'Ingresando...' : 'INGRESAR'}
                                </button>
                            </form>

                            {/* Link Olvidar Contraseña (Sin link de registro) */}
                            <div className="text-center mt-4">
                                <a href="#" className="link-ucsc small">
                                    Si olvidaste tu contraseña haz click aquí
                                </a>
                            </div>
                        </div>
                    </div>

                    {/* --- Columna Derecha (Banner) --- */}
                    <div className="col-lg-6 d-none d-lg-flex columna-banner">
                        <div className="banner-wrapper-nuevo">
                            {/* Los <img> fueron eliminados, ahora es un fondo CSS */}

                            {/* --- Bloques de Texto --- */}
                            <div className="banner-texto-wrapper">
                                <div className="texto-bloque">HABILITACION</div>
                                <div className="texto-bloque">PROFESIONAL</div>
                                <div className="admision-bloque">
                                    <strong>2026</strong>
                                    <span className="hoy-box">HOY</span>
                                </div>
                                <p className="carreras-texto">INGENIERIA CIVIL INFORMATICA</p>
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

