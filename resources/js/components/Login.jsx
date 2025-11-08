import React, { useState } from 'react';
import axios from 'axios';
// Asegurar envío de cookies (sesión) en peticiones hacia el backend
axios.defaults.withCredentials = true;

function Login({ onLoginSuccess }) {

    // --- Estados para manejar el formulario ---
    const [rut, setRut] = useState('');
    const [password, setPassword] = useState('');
    const [showPassword, setShowPassword] = useState(false); // Estado para el "ojito"
    const [showForgotModal, setShowForgotModal] = useState(false); // Estado para el modal
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(false);
    const [forgotRut, setForgotRut] = useState('');
    const [forgotEmail, setForgotEmail] = useState('');
    const [modalLoading, setModalLoading] = useState(false);
    const [modalMessage, setModalMessage] = useState({ type: '', text: '' });

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

    /**
     * Maneja el envío del formulario "Olvidé Contraseña" del modal.
     */
    const handleForgotSubmit = async (e) => {
        e.preventDefault(); // Evita que el formulario recargue la página
        setModalLoading(true);
        setModalMessage({ type: '', text: '' }); // Limpia mensajes anteriores

        try {
            // Hacemos la petición a la nueva ruta del backend
            const response = await axios.post('/api/forgot-password', {
                rut_profesor: forgotRut,
                email: forgotEmail
            });

            // Éxito: Mostramos el mensaje de Laravel
            setModalLoading(false);
            setModalMessage({ type: 'success', text: response.data.message });

            // Opcional: Limpiar los campos tras el éxito
            setForgotRut('');
            setForgotEmail('');

        } catch (err) {
            // Error: Mostramos el mensaje de error de Laravel
            setModalLoading(false);
            if (err.response && err.response.data && err.response.data.message) {
                // Si Laravel envía un mensaje de error (ej. validación)
                setModalMessage({ type: 'error', text: err.response.data.message });
            } else {
                // Error genérico de red o servidor
                setModalMessage({ type: 'error', text: 'Error al enviar la solicitud. Intente más tarde.' });
            }
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



                .password-wrapper {
                  position: relative;
                }
                .password-toggle-icon {
                  position: absolute;
                  top: 50%;
                  right: 15px;
                  transform: translateY(-50%);
                  background: transparent !important;
                  border: none !important;
                  padding: 0 !important;
                  cursor: pointer;
                  color: #888 !important; /* Color gris del ojo */
                  font-size: 1.1rem;
                  line-height: 1;
                  transition: color 0.2s ease-in-out;
                  outline: none;
                }
                .password-toggle-icon:hover {
                  color: var(--ucsc-red) !important; /* Opcional: bórralo si no quieres el efecto rojo */
                }
                .form-control-con-icono {
                  padding-right: 45px !important; /* Espacio para el icono */
                }



                .modal-overlay {
                  position: fixed;
                  top: 0;
                  left: 0;
                  width: 100vw;
                  height: 100vh;
                  background-color: rgba(0, 0, 0, 0.6);
                  display: flex;
                  justify-content: center;
                  align-items: center;
                  z-index: 1050;
                }
                .modal-content-box {
                  background: #fff;
                  padding: 2rem 2.5rem;
                  border-radius: 8px;
                  width: 90%;
                  max-width: 550px;
                  position: relative;
                  box-shadow: 0 5px 15px rgba(0,0,0,.3);

                  max-height: 90vh; /* Limita la altura del modal al 90% de la ventana */
                  overflow-y: auto; /* Muestra el scroll vertical cuando el contenido es más alto */
                }
                .modal-close-btn {
                  position: absolute;
                  top: 0.8rem;
                  right: 0.8rem;
                  background: #E83B4E;
                  border: none;
                  color: white;
                  font-weight: bold;
                  font-size: 1.1rem;
                  width: 30px;
                  height: 30px;
                  border-radius: 4px;
                  cursor: pointer;
                  line-height: 28px;
                  transition: background-color 0.2s;
                }
                .modal-close-btn:hover {
                  background-color: #C8102E;
                }
                .modal-content-box h5 {
                    font-weight: 700;
                    margin-bottom: 0.5rem;
                    color: var(--ucsc-red);
                }
                .modal-content-box p {
                    font-size: 0.9rem;
                    color: #333;
                }
                .btn-solicitar {
                  background-color: #F8D7DA;
                  border-color: #F8D7DA;
                  color: #842029;
                  font-weight: 600;
                  padding: 0.75rem;
                  width: 100%;
                }
                .btn-solicitar:hover {
                  background-color: #F5C2C7;
                  border-color: #F5C2C7;
                  color: #842029;
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

                                {/* --- BLOQUE DE CONTRASEÑA--- */}
                                <div className="mb-4">
                                    <label htmlFor="password" className="form-label">Contraseña</label>
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

                            {/* --- LINK PARA ABRIR EL MODAL --- */}
                            <div className="text-center mt-4">
                                <a
                                    href="#"
                                    className="link-ucsc small"
                                    onClick={(e) => {
                                        e.preventDefault();
                                        setShowForgotModal(true); // Muestra el modal
                                    }}
                                >
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


                        {/* --- INICIO: MODAL "OLVIDÉ CONTRASEÑA"  --- */}
            {showForgotModal && (
                <div className="modal-overlay" onClick={() => {
                    // No cerrar si está cargando
                    if (!modalLoading) setShowForgotModal(false);
                }}>
                    {/* Detiene la propagación para que al hacer clic en el modal no se cierre */}
                    <div className="modal-content-box" onClick={(e) => e.stopPropagation()}>

                        {/* Botón de Cerrar (X) */}
                        <button
                            className="modal-close-btn"
                            onClick={() => setShowForgotModal(false)}
                            disabled={modalLoading} // Se deshabilita al cargar
                        >
                            X
                        </button>

                        <h4 className="fw-bold text-center mb-3" style={{color: '#555'}}>
                            FORMULARIO PARA SOLICITAR CAMBIO DE CONTRASEÑA
                        </h4>

                        <h5>Solicitar código de seguridad</h5>

                        <p style={{fontSize: '0.85rem', color: '#444'}}>
                            Te enviaremos un <strong>CÓDIGO DE SEGURIDAD</strong> a tu correo institucional(*) y
                            personal (**) (en este caso revisar carpeta SPAM) para que posteriormente
                            puedas cambiar tu contraseña. A continuación ingresa tu rut y correo:
                        </p>

                        {/* Formulario del Modal conectado a la nueva función */}
                        <form onSubmit={handleForgotSubmit}>

                            {/* --- Mensajes de Éxito o Error --- */}
                            {modalMessage.text && (
                                <div className={`alert ${modalMessage.type === 'success' ? 'alert-success' : 'alert-danger'}`}>
                                    {modalMessage.text}
                                </div>
                            )}

                            <div className="mb-3">
                                <label htmlFor="rut_forgot" className="form-label" style={{fontWeight: 700}}>RUT</label>
                                <input
                                    type="text"
                                    id="rut_forgot"
                                    className="form-control form-control-lg"
                                    placeholder="Sin puntos, ni dígito verificador"
                                    value={forgotRut} // Conectado al estado
                                    onChange={(e) => setForgotRut(e.target.value)}
                                    required
                                    disabled={modalLoading} // Se deshabilita al cargar
                                />
                            </div>

                            {/* --- CAMPO DE EMAIL AGREGADO --- */}
                            <div className="mb-3">
                                <label htmlFor="email_forgot" className="form-label" style={{fontWeight: 700}}>Correo Electrónico</label>
                                <input
                                    type="email"
                                    id="email_forgot"
                                    className="form-control form-control-lg"
                                    placeholder="ejemplo@ucsc.cl"
                                    value={forgotEmail} // Conectado al estado
                                    onChange={(e) => setForgotEmail(e.target.value)}
                                    required
                                    disabled={modalLoading} // Se deshabilita al cargar
                                />
                            </div>

                            <button type="submit" className="btn btn-solicitar btn-lg mt-3" disabled={modalLoading}>
                                {modalLoading ? 'Enviando...' : 'SOLICITAR CÓDIGO DE SEGURIDAD'}
                            </button>
                        </form>

                        {/* Textos de ayuda del footer del modal */}
                        <div className="mt-4" style={{fontSize: '0.7rem', color: '#666', lineHeight: '1.4'}}>
                            <p className="mb-2">
                                <strong>(*) ¿Cuál es mi correo institucional y/o cuál es mi contraseña?</strong><br/>
                                Solicita asistencia ingresando a la <strong>Plataforma Service Desk UCSC</strong>. Puedes
                                revisar el siguiente <strong>video tutorial</strong> para más detalles. El ingreso a esta
                                plataforma se realiza con las credenciales de acceso a Microsoft 365
                                (dirección de correo institucional y contraseña). También puedes dirigirte
                                personalmente con tu cédula de identidad vigente, al encargado de
                                informática de tu Facultad o Sede, para solicitar asistencia.
                            </p>
                            <p>
                                <strong>(**) Tu correo personal también llamado 'alternativo'</strong> es el correo que tú
                                informaste a la Universidad
                            </p>
                        </div>

                    </div>
                </div>
            )}


        </>
    );
}

export default Login;
