import { useState } from 'react';
import axios from 'axios';

function UcscDataForm({ onDataSubmitted }) {
    const [formData, setFormData] = useState({
        rut_alumno: '',
        rut_profesor: '',
        fecha_ingreso: '',
        nota_final: ''
    });
    const [loading, setLoading] = useState(false);
    const [message, setMessage] = useState(null);
    const [errors, setErrors] = useState({});

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData(prev => ({
            ...prev,
            [name]: value
        }));
        
        if (errors[name]) {
            setErrors(prev => ({
                ...prev,
                [name]: null
            }));
        }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setMessage(null);
        setErrors({});

        try {
            const dataToSend = {
                rut_alumno: parseInt(formData.rut_alumno),
                rut_profesor: parseInt(formData.rut_profesor),
                fecha_ingreso: formData.fecha_ingreso,
                nota_final: formData.nota_final ? parseFloat(formData.nota_final) : null
            };

            const response = await axios.post('/ucsc/carga-automatica', dataToSend);

            if (response.data.success) {
                setMessage({
                    type: 'success',
                    text: 'Datos cargados exitosamente desde sistemas UCSC'
                });
                
                setFormData({
                    rut_alumno: '',
                    rut_profesor: '',
                    fecha_ingreso: '',
                    nota_final: ''
                });

                if (onDataSubmitted) {
                    onDataSubmitted();
                }
            }
        } catch (error) {
            if (error.response?.data?.errors) {
                setErrors(error.response.data.errors);
            }
            
            setMessage({
                type: 'error',
                text: error.response?.data?.message || 'Error al procesar los datos'
            });
        } finally {
            setLoading(false);
        }
    };

    return (
        <div>
            <h2 className="text-xl font-semibold mb-4">
                R1. Carga automática de datos desde sistemas UCSC
            </h2>
            
            <form onSubmit={handleSubmit} className="space-y-4">
                <div>
                    <label htmlFor="rut_alumno" className="block text-sm font-medium text-gray-700">
                        RUT Alumno <span className="text-red-500">*</span>
                    </label>
                    <p className="text-xs text-gray-500 mb-1">
                        R1.1: Número entero entre 1,000,000 y 60,000,000
                    </p>
                    <input
                        type="number"
                        id="rut_alumno"
                        name="rut_alumno"
                        value={formData.rut_alumno}
                        onChange={handleChange}
                        min="1000000"
                        max="60000000"
                        className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 ${
                            errors.rut_alumno ? 'border-red-500' : ''
                        }`}
                        placeholder="Ej: 12345678"
                        required
                    />
                    {errors.rut_alumno && (
                        <p className="mt-1 text-sm text-red-600">{errors.rut_alumno[0]}</p>
                    )}
                </div>

                <div>
                    <label htmlFor="rut_profesor" className="block text-sm font-medium text-gray-700">
                        RUT Profesor <span className="text-red-500">*</span>
                    </label>
                    <p className="text-xs text-gray-500 mb-1">
                        R1.4: Número entero entre 10,000,000 y 60,000,000
                    </p>
                    <input
                        type="number"
                        id="rut_profesor"
                        name="rut_profesor"
                        value={formData.rut_profesor}
                        onChange={handleChange}
                        min="10000000"
                        max="60000000"
                        className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 ${
                            errors.rut_profesor ? 'border-red-500' : ''
                        }`}
                        placeholder="Ej: 11222333"
                        required
                    />
                    {errors.rut_profesor && (
                        <p className="mt-1 text-sm text-red-600">{errors.rut_profesor[0]}</p>
                    )}
                </div>

                <div>
                    <label htmlFor="fecha_ingreso" className="block text-sm font-medium text-gray-700">
                        Fecha de Ingreso <span className="text-red-500">*</span>
                    </label>
                    <p className="text-xs text-gray-500 mb-1">
                        R1.8: Formato DD/MM/AAAA entre 2025 y 2050
                    </p>
                    <input
                        type="date"
                        id="fecha_ingreso"
                        name="fecha_ingreso"
                        value={formData.fecha_ingreso}
                        onChange={handleChange}
                        min="2025-01-01"
                        max="2050-12-31"
                        className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 ${
                            errors.fecha_ingreso ? 'border-red-500' : ''
                        }`}
                        required
                    />
                    {errors.fecha_ingreso && (
                        <p className="mt-1 text-sm text-red-600">{errors.fecha_ingreso[0]}</p>
                    )}
                </div>

                <div>
                    <label htmlFor="nota_final" className="block text-sm font-medium text-gray-700">
                        Nota Final <span className="text-gray-400">(Opcional)</span>
                    </label>
                    <p className="text-xs text-gray-500 mb-1">
                        R1.9: Decimal entre 1.00 y 7.00 (Ejemplo: 6.56)
                    </p>
                    <input
                        type="number"
                        id="nota_final"
                        name="nota_final"
                        value={formData.nota_final}
                        onChange={handleChange}
                        min="1.00"
                        max="7.00"
                        step="0.01"
                        className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 ${
                            errors.nota_final ? 'border-red-500' : ''
                        }`}
                        placeholder="Ej: 6.56"
                    />
                    {errors.nota_final && (
                        <p className="mt-1 text-sm text-red-600">{errors.nota_final[0]}</p>
                    )}
                </div>

                {message && (
                    <div className={`p-4 rounded-md ${
                        message.type === 'success' 
                            ? 'bg-green-50 text-green-700 border border-green-200' 
                            : 'bg-red-50 text-red-700 border border-red-200'
                    }`}>
                        {message.text}
                    </div>
                )}

                <div>
                    <button
                        type="submit"
                        disabled={loading}
                        className="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {loading ? (
                            <>
                                <svg className="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                                    <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Procesando...
                            </>
                        ) : (
                            'Cargar Datos desde UCSC'
                        )}
                    </button>
                </div>
            </form>

            <div className="mt-6 p-4 bg-blue-50 rounded-md">
                <h3 className="text-sm font-medium text-blue-800">Información del proceso:</h3>
                <ul className="mt-2 text-sm text-blue-700 space-y-1">
                    <li> R1.11: Los datos del alumno y profesor se obtienen automáticamente desde el sistema UCSC</li>
                    <li> R1.12: El sistema verifica y actualiza la información automáticamente</li>
                    <li> R1.13: Se registra un log de la operación con todos los datos</li>
                    <li> R1.15: El proceso también se ejecuta automáticamente cada minuto</li>
                </ul>
            </div>
        </div>
    );
}

export default UcscDataForm;