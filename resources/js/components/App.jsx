import { useState, useEffect } from 'react';
import UcscDataForm from './UcscDataForm';
import UcscDataTable from './UcscDataTable';
import UcscLogs from './UcscLogs';
import HabilitacionTable from './HabilitacionTable';
import axios from 'axios';
import Login from './Login';
// import Register from './Register'; // <-- ELIMINADO

function App() {

    const [isLoggedIn, setIsLoggedIn] = useState(null); // null = 'verificando...', false = 'no logueado', true = 'logueado'
    const [profesor, setProfesor] = useState(null); // Almacena los datos del profesor logueado
    // const [showRegister, setShowRegister] = useState(false); // <-- ELIMINADO

    const [activeTab, setActiveTab] = useState('form');
    const [registros, setRegistros] = useState([]);
    const [logs, setLogs] = useState([]);
    const [habilitaciones, setHabilitaciones] = useState([]);
    const [loading, setLoading] = useState(false);

    // Configurar axios con base URL
    useEffect(() => {
        axios.get('/user')
            .then(response => {
                // Si la petición tiene éxito, significa que ya estamos logueados
                setProfesor(response.data);
                setIsLoggedIn(true);
            })
            .catch(error => {
                // Si da un error (como 401), no estamos logueados
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
        if (isLoggedIn) { // <-- Solo carga datos si estamos logueados
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
        // Recargar registros después de enviar datos
        if (activeTab === 'registros') {
            cargarRegistros();
        }
    };

    const handleLoginSuccess = (profesorLogueado) => {
        setProfesor(profesorLogueado); // Guarda los datos del profesor
        setIsLoggedIn(true); // Marca la app como "logueada"
    };

    const handleLogout = async () => {
        try {
            await axios.post('/logout'); // Llama a la ruta /api/logout
            setIsLoggedIn(false); // Marca la app como "no logueada"
            setProfesor(null); // Borra los datos del profesor
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

    // --- LÓGICA DE LOGIN SIMPLIFICADA ---
    if (isLoggedIn === false) {
        // Muestra el formulario de Login (ya no hay lógica de registro)
        return <Login
            onLoginSuccess={handleLoginSuccess}
            // onShowRegister prop eliminada
        />;
    }

    return (
        <div className="min-h-screen bg-gray-100">
            <header className="bg-blue-600 text-white p-4">
                <div className="container mx-auto">
                    <h1 className="text-2xl font-bold">Sistema HabilProf - UCSC</h1>
                    <p className="text-blue-200">Carga automática de datos desde sistemas UCSC</p>
                </div>

                <div>
                    {/* Mostramos el nombre del profesor logueado */}
                    <span className="mr-4">Hola, {profesor.nombre_profesor}</span>
                    <button
                        onClick={handleLogout}
                        className="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                    >
                        Cerrar Sesión
                    </button>
                </div>
            </header>

            <main className="container mx-auto py-6 px-4">
                {/* Navigation Tabs */}
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

                {/* Tab Content */}
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

export default App;
