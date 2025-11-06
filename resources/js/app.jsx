import React, { useState, useEffect } from 'react';
import { createRoot } from 'react-dom/client';
import { Button } from 'react-bootstrap';
import './bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import '../css/app.css'; 
import axios from 'axios';

// Asegurar envío de cookies (sesión) en peticiones XHR hacia el backend
axios.defaults.withCredentials = true;
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
    
    // Si la ruta es para el formulario de agregar (embed)
    if (path.includes('agregar-embed')) {
        return (
            <div className="min-vh-100 d-flex align-items-center justify-content-center bg-light">            
                <HabilprofForm />
            </div>
        );
    }
    
    // ============ FUNCIONALIDAD NUEVA (sistema con login) ============
    // Para el resto de rutas, usar el sistema con autenticación
    // IMPORTANTE: La sesión NO persiste al recargar la página
    const [isLoggedIn, setIsLoggedIn] = useState(false); // Siempre inicia en false (no autenticado)
    const [profesor, setProfesor] = useState(null);
    const [activeTab, setActiveTab] = useState('semestral'); // Cambiado a 'semestral' por defecto
    const [registros, setRegistros] = useState([]);
    const [logs, setLogs] = useState([]);
    const [habilitaciones, setHabilitaciones] = useState([]);
    const [loading, setLoading] = useState(false);

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
            await axios.post('/api/logout');
            setIsLoggedIn(false);
            setProfesor(null);
            // No redirigir, simplemente mostrar el login
        } catch (error) {
            console.error('Error al cerrar sesión:', error);
            // Incluso si hay error, cerrar sesión localmente
            setIsLoggedIn(false);
            setProfesor(null);
        }
    };

    // Si no está autenticado, mostrar el login
    if (isLoggedIn === false) {
        return <Login onLoginSuccess={handleLoginSuccess} />;
    }

    return (
        <div style={{ minHeight: '100vh', display: 'flex', flexDirection: 'column' }}>
            {/* Barra Superior Roja - Estilo UCSC */}
            <nav className="navbar" style={{ 
                background: '#d6082b', 
                padding: '0.5rem 2rem',
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'space-between'
            }}>
                <div className="d-flex align-items-center gap-3">
                    <img 
                        src="/images/ucsc-hero.svg" 
                        alt="UCSC Logo" 
                        style={{ height: '40px', width: 'auto' }}
                    />
                    <span className="text-white fw-bold fs-5">HabilProf</span>
                </div>
                
                <div className="d-flex gap-2">
                    <Button 
                        variant={activeTab === 'historico' ? 'light' : 'outline-light'}
                        onClick={() => handleTabChange('historico')}
                        size="sm"
                    >
                        Histórico
                    </Button>
                    <Button 
                        variant={activeTab === 'semestral' ? 'light' : 'outline-light'}
                        onClick={() => handleTabChange('semestral')}
                        size="sm"
                    >
                        Semestral
                    </Button>
                    <Button 
                        variant={activeTab === 'form' ? 'light' : 'outline-light'}
                        onClick={() => handleTabChange('form')}
                        size="sm"
                    >
                        Ingresar datos
                    </Button>
                    <Button 
                        variant={activeTab === 'registros' ? 'light' : 'outline-light'}
                        onClick={() => handleTabChange('registros')}
                        size="sm"
                    >
                        Eliminar datos
                    </Button>
                    <Button 
                        variant="danger"
                        onClick={handleLogout}
                        size="sm"
                    >
                        Cerrar sesión
                    </Button>
                </div>
            </nav>

            {/* Contenido Principal */}
            <div style={{ flex: 1, background: '#f5f5f5' }}>
                <div className="container-fluid py-4">
                    {activeTab === 'historico' && (
                        <ListadoHistorico />
                    )}
                    {activeTab === 'semestral' && (
                        <ListadoSemestral />
                    )}
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
            </div>
        </div>
    );
}

const el = document.getElementById('app');
if (el) {
    createRoot(el).render(<App />);
}