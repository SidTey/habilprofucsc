import React, { useState } from 'react';
import axios from 'axios';

/**
 * Componente para formulario de carga de datos UCSC (R1)
 * TODO: Implementar la funcionalidad completa según el código de tu amigo
 */
function UcscDataForm({ onDataSubmitted }) {
    const [loading, setLoading] = useState(false);
    const [message, setMessage] = useState('');

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setMessage('');

        try {
            // TODO: Implementar la lógica de envío de datos UCSC
            const response = await axios.post('/ucsc/data', {
                // ... datos del formulario
            });
            
            setMessage('Datos cargados exitosamente');
            if (onDataSubmitted) {
                onDataSubmitted();
            }
        } catch (error) {
            console.error('Error cargando datos:', error);
            setMessage('Error al cargar los datos');
        } finally {
            setLoading(false);
        }
    };

    return (
        <div>
            <h2 className="text-xl font-bold mb-4">Carga de Datos UCSC (R1)</h2>
            <form onSubmit={handleSubmit}>
                <div className="mb-4">
                    <p className="text-gray-600">
                        Formulario de carga de datos desde sistemas UCSC.
                    </p>
                    <p className="text-sm text-gray-500 mt-2">
                        (Componente stub - pendiente de implementación completa)
                    </p>
                </div>
                
                {message && (
                    <div className={`p-3 mb-4 rounded ${message.includes('Error') ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'}`}>
                        {message}
                    </div>
                )}

                <button
                    type="submit"
                    disabled={loading}
                    className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50"
                >
                    {loading ? 'Cargando...' : 'Cargar Datos'}
                </button>
            </form>
        </div>
    );
}

export default UcscDataForm;
