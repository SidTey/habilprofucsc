import React, { useState } from 'react'; // Importamos React
import axios from 'axios';

// Esta prop 'onShowLogin' la usaremos en el Paso 9 para volver al login
export default function Register({ onShowLogin }) {

    // --- (1) TU LÓGICA DE ESTADO SE MANTIENE INTACTA ---
    const [nombre, setNombre] = useState('');
    const [rut, setRut] = useState('');
    const [correo, setCorreo] = useState('');
    const [password, setPassword] = useState('');
    const [passwordConfirmation, setPasswordConfirmation] = useState('');

    const [errors, setErrors] = useState({});
    const [generalError, setGeneralError] = useState('');
    const [successMessage, setSuccessMessage] = useState('');
    const [loading, setLoading] = useState(false); // (Añadido para deshabilitar el botón)

    // --- (2) TU LÓGICA DE SUBMIT SE MANTIENE INTACTA ---
    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true); // Deshabilitamos el botón

        setErrors({});
        setGeneralError('');
        setSuccessMessage('');

        try {
            const response = await axios.post('/register', {
                nombre_profesor: nombre,
                rut_profesor: rut,
                correo_profesor: correo,
                password: password,
                password_confirmation: passwordConfirmation
            });

            setSuccessMessage(response.data.message + " Ahora puedes iniciar sesión.");
            setNombre('');
            setRut('');
            setCorreo('');
            setPassword('');
            setPasswordConfirmation('');

        } catch (error) {
            if (error.response && error.response.status === 422) {
                setErrors(error.response.data.errors);
            } else {
                console.error(error);
                setGeneralError('Ha ocurrido un error inesperado al registrar.');
            }
        } finally {
            setLoading(false); // Volvemos a habilitar el botón
        }
    };

    return (
        <>
            {/* --- (3) AÑADIMOS LOS ESTILOS DE BOOTSTRAP (igual que en Login) --- */}
            <style>
            {`
                :root {
                    --ucsc-red: #C8102E;
                }
                .form-label {
                    font-family: 'Roboto', sans-serif;
                    font-weight: 700;
                }
                .form-control:focus {
                    border-color: var(--ucsc-red);
                    box-shadow: 0 0 0 0.25rem rgba(200, 16, 46, 0.25);
                }
                .btn-success {
                    font-weight: 600;
                    padding-top: 0.75rem;
                    padding-bottom: 0.75rem;
                }
                .link-primary {
                    font-weight: 600;
                    text-decoration: none;
                }
                .link-primary:hover {
                    text-decoration: underline;
                }
                /* Estilo para los mensajes de error de validación */
                .validation-error {
                    color: var(--bs-danger, #dc3545);
                    font-size: 0.875em; /* un poco más pequeño */
                    margin-top: 0.25rem;
                }
            `}
            </style>

            {/* --- (4) USAMOS LA NUEVA ESTRUCTURA VISUAL DE BOOTSTRAP --- */}
            <div className="container-fluid bg-light min-vh-100 d-flex flex-column align-items-center justify-content-center p-3">

                <div className="card shadow-sm rounded-3 border-0" style={{ maxWidth: '500px', width: '100%' }}>
                    <div className="card-body p-4 p-md-5">

                        <h1 className="h3 fw-bold text-dark text-center mb-4">
                            Registrar Profesor
                        </h1>

                        {/* --- (5) MOSTRAMOS ERRORES Y ÉXITO CON 'ALERTS' DE BOOTSTRAP --- */}
                        {generalError && <div className="alert alert-danger">{generalError}</div>}
                        {successMessage && <div className="alert alert-success">{successMessage}</div>}

                        <form onSubmit={handleSubmit}>

                            <div className="mb-3">
                                <label htmlFor="nombre" className="form-label">Nombre Completo</label>
                                <input
                                    type="text"
                                    id="nombre"
                                    value={nombre}
                                    onChange={(e) => setNombre(e.target.value)}
                                    required
                                    className={`form-control form-control-lg ${errors.nombre_profesor ? 'is-invalid' : ''}`}
                                />
                                {errors.nombre_profesor && <div className="validation-error">{errors.nombre_profesor[0]}</div>}
                            </div>

                            <div className="mb-3">
                                <label htmlFor="rut_register" className="form-label">RUT (sin puntos, sin guion)</label>
                                <input
                                    type="text"
                                    id="rut_register"
                                    value={rut}
                                    onChange={(e) => setRut(e.target.value)}
                                    required
                                    className={`form-control form-control-lg ${errors.rut_profesor ? 'is-invalid' : ''}`}
                                />
                                {errors.rut_profesor && <div className="validation-error">{errors.rut_profesor[0]}</div>}
                            </div>

                            <div className="mb-3">
                                <label htmlFor="email" className="form-label">Correo Electrónico</label>
                                <input
                                    type="email"
                                    id="email"
                                    value={correo}
                                    onChange={(e) => setCorreo(e.target.value)}
                                    required
                                    className={`form-control form-control-lg ${errors.correo_profesor ? 'is-invalid' : ''}`}
                                />
                                {errors.correo_profesor && <div className="validation-error">{errors.correo_profesor[0]}</div>}
                            </div>

                            <div className="mb-3">
                                <label htmlFor="password_register" className="form-label">Contraseña (mín. 8 caracteres)</label>
                                <input
                                    type="password"
                                    id="password_register"
                                    value={password}
                                    onChange={(e) => setPassword(e.target.value)}
                                    required
                                    className={`form-control form-control-lg ${errors.password ? 'is-invalid' : ''}`}
                                />
                                {errors.password && <div className="validation-error">{errors.password[0]}</div>}
                            </div>

                            <div className="mb-4">
                                <label htmlFor="confirm_password" className="form-label">Confirmar Contraseña</label>
                                <input
                                    type="password"
                                    id="confirm_password"
                                    value={passwordConfirmation}
                                    onChange={(e) => setPasswordConfirmation(e.target.value)}
                                    required
                                    className="form-control form-control-lg"
                                />
                            </div>

                            <button
                                type="submit"
                                className="btn btn-success w-100 btn-lg"
                                disabled={loading} // Se deshabilita al enviar
                            >
                                {loading ? 'Registrando...' : 'Registrarme'}
                            </button>
                        </form>

                        <div className="text-center mt-4">
                            <a
                                href="#"
                                onClick={(e) => {
                                    e.preventDefault();
                                    onShowLogin(); // Tu función para volver al login
                                }}
                                className="link-primary small"
                            >
                                ¿Ya tienes cuenta? Inicia sesión
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
