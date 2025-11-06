import React, { useState, useEffect } from 'react';
import { createRoot } from 'react-dom/client';
import './bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import '../css/app.css'; 
import axios from 'axios';

// Componentes originales (tu funcionalidad)
import HabilprofForm from './components/HabilprofForm.jsx';
import ListadoHistorico from './components/ListadoHistorico.jsx';
import ListadoSemestral from './components/ListadoSemestral.jsx';

// Componentes nuevos (funcionalidad de tu amigo)
import Login from './components/Login';
import UcscDataForm from './components/UcscDataForm';
import UcscDataTable from './components/UcscDataTable';
import UcscLogs from './components/UcscLogs';
import HabilitacionTable from './components/HabilitacionTable';

function App() {
    const path = window.location.pathname;
    
    // ============ FUNCIONALIDAD ORIGINAL (embed routes) ============
    // Si la ruta es embed, renderizar sin login (tu funcionalidad original)
    if (path.includes('historico-embed')) {
        return <ListadoHistorico />;
    }
    
    if (path.includes('semestral-embed')) {
        return <ListadoSemestral />;
    }
    
    // Si la ruta es para el formulario simple sin login
    if (path.includes('agregar-habilitacion')) {
        return (
            <div className="min-vh-100 d-flex align-items-center justify-content-center bg-light">            
                <HabilprofForm />
            </div>
        );
    }
    
    // ============ FUNCIONALIDAD NUEVA (sistema con login) ============
    // Para el resto de rutas, usar el sistema con autenticación
    const [isLoggedIn, setIsLoggedIn] = useState(null); // null = 'verificando...', false = 'no logueado', true = 'logueado'
    const [profesor, setProfesor] = useState(null);
    const [activeTab, setActiveTab] = useState('form');
    const [registros, setRegistros] = useState([]);
    const [logs, setLogs] = useState([]);
    const [habilitaciones, setHabilitaciones] = useState([]);
    const [loading, setLoading] = useState(false);

    // Verificar sesión al cargar
    useEffect(() => {
        axios.get('/user')
            .then(response => {
                setProfesor(response.data);
                setIsLoggedIn(true);
            })
            .catch(error => {
                setIsLoggedIn(false);
            });
    }, []);

    const cargarRegistros = async () => {
        try {
            setLoading(true);
            console.log('Intentando cargar registros...');
            const response = await axios.get('/ucsc/registros');
            console.log('Respuesta recibida:', response.data);
            if (response.data.success) {
                setRegistros(response.data.data);
                console.log('Registros actualizados:', response.data.data);
            }
        } catch (error) {
            console.error('Error cargando registros:', error);
            console.error('Detalles del error:', error.response?.data);
        } finally {
            setLoading(false);
        }
    };

    const cargarLogs = async () => {
        try {
            setLoading(true);
            const response = await axios.get('/ucsc/logs');
            if (response.data.success) {
                setLogs(response.data.data);
            }
        } catch (error) {
            console.error('Error cargando logs:', error);
        } finally {
            setLoading(false);
        }
    };

    const cargarHabilitaciones = async () => {
        try {
            setLoading(true);
            const response = await axios.get('/habilitaciones');
            if (response.data.success) {
                setHabilitaciones(response.data.data);
            }
        } catch (error) {
            console.error('Error cargando habilitaciones:', error);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        if (isLoggedIn) {
            if (activeTab === 'registros') {
                cargarRegistros();
            } else if (activeTab === 'logs') {
                cargarLogs();
            } else if (activeTab === 'habilitaciones') {
                cargarHabilitaciones();
            }
        }
    }, [activeTab, isLoggedIn]);

    const handleTabChange = (tab) => {
        setActiveTab(tab);
    };

    const handleDataSubmitted = () => {
        if (activeTab === 'registros') {
            cargarRegistros();
        }
    };

    const handleLoginSuccess = (profesorLogueado) => {
        setProfesor(profesorLogueado);
        setIsLoggedIn(true);
    };

    const handleLogout = async () => {
        try {
            await axios.post('/logout');
            setIsLoggedIn(false);
            setProfesor(null);
        } catch (error) {
            console.error('Error al cerrar sesión:', error);
        }
    };

    if (isLoggedIn === null) {
        return (
            <div className="min-h-screen bg-gray-100 flex items-center justify-center">
                <h1 className="text-2xl font-bold">Cargando Sistema HabilProf...</h1>
            </div>
        );
    }

    if (isLoggedIn === false) {
        return <Login onLoginSuccess={handleLoginSuccess} />;
    }

    return (
        <div className="min-h-screen bg-gray-100">
            <header className="bg-blue-600 text-white p-4">
                <div className="container mx-auto flex justify-between items-center">
                    <div>
                        <h1 className="text-2xl font-bold">Sistema HabilProf - UCSC</h1>
                        <p className="text-blue-200">Carga automática de datos desde sistemas UCSC</p>
                    </div>
                    <div>
                        <span className="mr-4">Hola, {profesor?.nombre_profesor || 'Usuario'}</span>
                        <button
                            onClick={handleLogout}
                            className="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                        >
                            Cerrar Sesión
                        </button>
                    </div>
                </div>
            </header>

            <main className="container mx-auto py-6 px-4">
                <div className="mb-6">
                    <nav className="flex space-x-8">
                        <button
                            onClick={() => handleTabChange('form')}
                            className={`py-2 px-1 border-b-2 font-medium text-sm ${
                                activeTab === 'form'
                                    ? 'border-blue-500 text-blue-600'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                            }`}
                        >
                            Carga de Datos (R1)
                        </button>
                        <button
                            onClick={() => handleTabChange('registros')}
                            className={`py-2 px-1 border-b-2 font-medium text-sm ${
                                activeTab === 'registros'
                                    ? 'border-blue-500 text-blue-600'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                            }`}
                        >
                            Registros UCSC
                        </button>
                        <button
                            onClick={() => handleTabChange('logs')}
                            className={`py-2 px-1 border-b-2 font-medium text-sm ${
                                activeTab === 'logs'
                                    ? 'border-blue-500 text-blue-600'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                            }`}
                        >
                            Logs del Sistema (R1.13)
                        </button>
                        <button
                            onClick={() => handleTabChange('habilitaciones')}
                            className={`py-2 px-1 border-b-2 font-medium text-sm ${
                                activeTab === 'habilitaciones'
                                    ? 'border-blue-500 text-blue-600'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                            }`}
                        >
                            Habilitaciones (R2)
                        </button>
                    </nav>
                </div>

                <div className="bg-white rounded-lg shadow-md p-6">
                    {activeTab === 'form' && (
                        <UcscDataForm onDataSubmitted={handleDataSubmitted} />
                    )}
                    {activeTab === 'registros' && (
                        <UcscDataTable
                            registros={registros}
                            loading={loading}
                            onRefresh={cargarRegistros}
                        />
                    )}
                    {activeTab === 'logs' && (
                        <UcscLogs
                            logs={logs}
                            loading={loading}
                            onRefresh={cargarLogs}
                        />
                    )}
                    {activeTab === 'habilitaciones' && (
                        <HabilitacionTable
                            habilitaciones={habilitaciones}
                            loading={loading}
                            onRefresh={cargarHabilitaciones}
                        />
                    )}
                </div>
            </main>

            <footer className="bg-gray-800 text-white p-4 mt-8">
                <div className="container mx-auto text-center">
                    <p>&copy; 2025 Sistema HabilProf - Universidad Católica de la Santísima Concepción</p>
                </div>
            </footer>
        </div>
    );
}

const el = document.getElementById('app');
if (el) {
    createRoot(el).render(<App />);
}