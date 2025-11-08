import React, { useState } from 'react';
import axios from 'axios';
// Asegurar envío de cookies (sesión) en peticiones hacia el backend
axios.defaults.withCredentials = true;

function Login({ onLoginSuccess }) {

    // --- Estados para manejar el formulario ---
    const [rut, setRut] = useState('');
    const [password, setPassword] = useState('');
    const [showPassword, setShowPassword] = useState(false); // Estado para el "ojito"
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(false);

    /**
     * Maneja el envío del formulario de login.
     */
    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setError('');

        try {
            const response = await axios.post('/api/login', {
                rut_profesor: rut,
                password: password,
            });
            // Login exitoso - redirigir al dashboard Blade
            window.location.href = '/dashboard';

        } catch (err) {
            console.error('Error en el login:', err.response);
            if (err.response && err.response.data) {
                if (err.response.data.message) {
                    setError(err.response.data.message);
                } else if (err.response.data.errors) {
                    const firstErrorKey = Object.keys(err.response.data.errors)[0];
                    setError(err.response.data.errors[firstErrorKey][0]);
                } else {
                    setError('RUT o contraseña incorrectos. (Error desconocido)');
                }
            } else {
                setError('RUT o contraseña incorrectos. (Error de red)');
            }
            setLoading(false);
        }
    };

    return (
        <>
            {/* --- Estilos personalizados --- */}
            <style>
            {`
                :root {
                    --ucsc-red: #C8102E;
                    --ucsc-dark: #231F20;
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

                /* --- Estilos del Banner (Sin cambios) --- */
                .columna-banner {
                    background-color: var(--ucsc-red);
                    position: relative;
                    overflow: hidden;
                    min-height: 500px;
                }
                .columna-banner::before {
                    content: '';
                    position: absolute;
                    top: 0; left: 0;
                    width: 100%; height: 100%;
                    background-image: url('/images/banner-ucsc.jpg');
                    background-size: cover;
                    background-position: center center;
                    background-repeat: no-repeat;
                    background-color: rgba(0, 0, 0, 0.5);
                    background-blend-mode: multiply;
                }
                .banner-wrapper-nuevo {
                    position: relative;
                    width: 100%;
                    height: 100%;
                    min-height: 600px;
                    display: flex;
                    align-items: center;
                    padding: 2rem 4rem;
                    color: white;
                }
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
                    color: white;
                }



                /* 1. El contenedor DEBE envolver SÓLO al input y al botón */
                .password-wrapper {
                  position: relative;
                }

                /* 2. El icono se posiciona de forma absoluta DENTRO del wrapper */
                .password-toggle-icon {
                  position: absolute;
                  top: 50%;
                  right: 15px; /* Distancia desde la derecha */
                  transform: translateY(-50%); /* Centrado vertical perfecto */

                  background: transparent !important; /* Fondo transparente */
                  border: none !important; /* Sin borde */
                  padding: 0 !important;
                  width: auto !important; /* Evita que ocupe todo el ancho posible */
                  height: auto !important; /* Evita que ocupe toda la altura posible */
                  line-height: 1 !important;

                  cursor: pointer;
                  color: #888 !important; /* Un gris suave y profesional */
                  font-size: 1.1rem; /* Tamaño legible */

                  transition: color 0.2s ease-in-out;
                  outline: none; /* Sin contorno azul al hacer click */
                }

                /* 3. Efecto hover rojo que pediste */
                .password-toggle-icon:hover {
                  color: var(--ucsc-red) !important;
                }

                /* 4. Padding para el input, para que el texto no se escriba encima del icono */
                .form-control-con-icono {
                  padding-right: 45px !important; /* Espacio para el icono */
                }
            `}
            </style>

            {/* --- Contenedor Principal (Layout de 2 columnas) --- */}
            <div className="container-fluid p-0">
                <div className="row g-0">

                    {/* --- Columna Izquierda (Formulario) --- */}
                    <div className="col-lg-6 bg-light d-flex align-items-center justify-content-center min-vh-100">
                        <div className="p-4 p-md-5" style={{ maxWidth: '500px', width: '100%' }}>

                            <div className="mb-4">
                                <img
                                    src="/images/ucsc-hero.svg"
                                    alt="Logo UCSC"
                                    style={{ height: '50px' }}
                                    className="mb-2"
                                />
                                <h1 className="h3 fw-bold text-dark mt-2">Portal Habilitación Profesional</h1>
                            </div>

                            {/* --- FORMULARIO CONECTADO A REACT --- */}
                            <form onSubmit={handleSubmit}>

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
                                        value={rut}
                                        onChange={(e) => setRut(e.target.value)}
                                    />
                                </div>


                                <div className="mb-4">
                                    {/* La etiqueta (Label) va AFUERA del wrapper */}
                                    <label htmlFor="password" className="form-label">Contraseña</label>

                                    {/* Este div wrapper es clave para la posición */}
                                    <div className="password-wrapper">
                                        <input
                                            type={showPassword ? 'text' : 'password'}
                                            id="password"
                                            name="password"
                                            className="form-control form-control-lg form-control-con-icono"
                                            placeholder="Contraseña"
                                            autoComplete="current-password"
                                            required
                                            value={password}
                                            onChange={(e) => setPassword(e.target.value)}
                                        />

                                        <button
                                            type="button"
                                            className="password-toggle-icon"
                                            onClick={() => setShowPassword(!showPassword)}
                                            title={showPassword ? 'Ocultar contraseña' : 'Ver contraseña'}
                                        >
                                            <i className={showPassword ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'}></i>
                                        </button>
                                    </div>
                                </div>
                                {/* --- FIN DEL BLOQUE DE CONTRASEÑA --- */}

                                <button
                                    type="submit"
                                    className="btn btn-ucsc w-100 btn-lg"
                                    disabled={loading}
                                >
                                    {loading ? 'Ingresando...' : 'INGRESAR'}
                                </button>
                            </form>

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

            <footer className="text-center p-3 bg-light text-muted small border-top">
                © 2025 UCSC. Todos los derechos reservados
            </footer>
        </>
    );
}

export default Login;
