import { useState, useEffect } from 'react';
import axios from 'axios';

function HabilitacionForm({ onHabilitacionSubmitted }) {
    const [formData, setFormData] = useState({
        rut_alumno: '',
        tipo_habilitacion: '',
        descripcion_habilitacion: '',
        titulo_proyecto: '',
        rut_profesor_guia: '',
        rut_profesor_comision: '',
        rut_profesor_co_guia: '',
        rut_supervisor: '',
        nombre_supervisor: '',
        rut_empresa: '',
        nombre_empresa: '',
        rut_profesor_tutor: ''
    });

    const [alumnos, setAlumnos] = useState([]);
    const [profesores, setProfesores] = useState([]);
    const [errors, setErrors] = useState({});
    const [loading, setLoading] = useState(false);

    useEffect(() => {
        cargarAlumnos();
        cargarProfesores();
    }, []);

    const cargarAlumnos = async () => {
        try {
            const response = await axios.get('/habilitaciones/alumnos-disponibles');
            if (response.data.success) {
                setAlumnos(response.data.data);
            }
        } catch (error) {
            console.error('Error cargando alumnos:', error);
        }
    };

    const cargarProfesores = async () => {
        try {
            const response = await axios.get('/habilitaciones/profesores-disponibles');
            if (response.data.success) {
                setProfesores(response.data.data);
            }
        } catch (error) {
            console.error('Error cargando profesores:', error);
        }
    };

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData(prev => ({
            ...prev,
            [name]: value
        }));
        // Limpiar error del campo cuando cambia
        if (errors[name]) {
            setErrors(prev => ({
                ...prev,
                [name]: null
            }));
        }
    };

    const validate = () => {
        const newErrors = {};

        // R2.16 - Campos obligatorios
        if (!formData.rut_alumno) {
            newErrors.rut_alumno = 'El campo Rut_Alumno es obligatorio';
        }
        if (!formData.tipo_habilitacion) {
            newErrors.tipo_habilitacion = 'El campo Tipo_Habilitacion es obligatorio';
        }
        if (!formData.descripcion_habilitacion) {
            newErrors.descripcion_habilitacion = 'El campo Descripción_Habilitacion es obligatorio';
        } else if (formData.descripcion_habilitacion.length < 50 || formData.descripcion_habilitacion.length > 500) {
            newErrors.descripcion_habilitacion = 'La descripción debe tener entre 50 y 500 caracteres';
        }

        // Validaciones específicas según tipo de habilitación
        if (['PrIng', 'PrInv'].includes(formData.tipo_habilitacion)) {
            if (!formData.titulo_proyecto) {
                newErrors.titulo_proyecto = 'Título de proyecto no válido';
            }
            if (!formData.rut_profesor_guia) {
                newErrors.rut_profesor_guia = 'El campo Profesor_Guía es obligatorio';
            }
            if (!formData.rut_profesor_comision) {
                newErrors.rut_profesor_comision = 'El campo Profesor_Comision es obligatorio';
            }
        } else if (formData.tipo_habilitacion === 'PrTut') {
            if (!formData.rut_supervisor) {
                newErrors.rut_supervisor = 'El Rut de supervisor no es válido';
            }
            if (!formData.nombre_supervisor) {
                newErrors.nombre_supervisor = 'El Nombre del supervisor no es válido';
            }
            if (!formData.rut_empresa) {
                newErrors.rut_empresa = 'El Rut de la empresa no es válido';
            }
            if (!formData.nombre_empresa) {
                newErrors.nombre_empresa = 'El Nombre de la empresa no es válido';
            }
            if (!formData.rut_profesor_tutor) {
                newErrors.rut_profesor_tutor = 'El campo Profesor_Tutor es obligatorio';
            }
        }

        return newErrors;
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        const newErrors = validate();

        if (Object.keys(newErrors).length > 0) {
            setErrors(newErrors);
            return;
        }

        setLoading(true);
        try {
            const response = await axios.post('/habilitaciones', formData);
            if (response.data.success) {
                alert('Se ha ingresado correctamente la Habilitacion Profesional');
                if (onHabilitacionSubmitted) {
                    onHabilitacionSubmitted();
                }
                // Limpiar formulario
                setFormData({
                    rut_alumno: '',
                    tipo_habilitacion: '',
                    descripcion_habilitacion: '',
                    titulo_proyecto: '',
                    rut_profesor_guia: '',
                    rut_profesor_comision: '',
                    rut_profesor_co_guia: '',
                    rut_supervisor: '',
                    nombre_supervisor: '',
                    rut_empresa: '',
                    nombre_empresa: '',
                    rut_profesor_tutor: ''
                });
            }
        } catch (error) {
            console.error('Error:', error);
            const errorMsg = error.response?.data?.message || 'Error al procesar la solicitud';
            alert(errorMsg);
        } finally {
            setLoading(false);
        }
    };

    return (
        <form onSubmit={handleSubmit} className="space-y-6">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                {/* Datos básicos */}
                <div className="space-y-4">
                    <div>
                        <label className="block text-sm font-medium text-gray-700">
                            Alumno
                        </label>
                        <select
                            name="rut_alumno"
                            value={formData.rut_alumno}
                            onChange={handleChange}
                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">Seleccione un alumno</option>
                            {alumnos.map(alumno => (
                                <option key={alumno.rut_alumno} value={alumno.rut_alumno}>
                                    {alumno.rut_alumno} - {alumno.nombre_alumno}
                                </option>
                            ))}
                        </select>
                        {errors.rut_alumno && (
                            <p className="mt-1 text-sm text-red-600">{errors.rut_alumno}</p>
                        )}
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-gray-700">
                            Tipo de Habilitación
                        </label>
                        <select
                            name="tipo_habilitacion"
                            value={formData.tipo_habilitacion}
                            onChange={handleChange}
                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">Seleccione tipo</option>
                            <option value="PrIng">Práctica Ingeniería</option>
                            <option value="PrInv">Práctica Investigación</option>
                            <option value="PrTut">Práctica Tutorial</option>
                        </select>
                        {errors.tipo_habilitacion && (
                            <p className="mt-1 text-sm text-red-600">{errors.tipo_habilitacion}</p>
                        )}
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-gray-700">
                            Descripción
                        </label>
                        <textarea
                            name="descripcion_habilitacion"
                            value={formData.descripcion_habilitacion}
                            onChange={handleChange}
                            rows="4"
                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Ingrese descripción (mínimo 50 caracteres)"
                        />
                        {errors.descripcion_habilitacion && (
                            <p className="mt-1 text-sm text-red-600">{errors.descripcion_habilitacion}</p>
                        )}
                    </div>
                </div>

                {/* Campos específicos según tipo */}
                <div className="space-y-4">
                    {['PrIng', 'PrInv'].includes(formData.tipo_habilitacion) && (
                        <>
                            <div>
                                <label className="block text-sm font-medium text-gray-700">
                                    Título del Proyecto
                                </label>
                                <input
                                    type="text"
                                    name="titulo_proyecto"
                                    value={formData.titulo_proyecto}
                                    onChange={handleChange}
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                                {errors.titulo_proyecto && (
                                    <p className="mt-1 text-sm text-red-600">{errors.titulo_proyecto}</p>
                                )}
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700">
                                    Profesor Guía
                                </label>
                                <select
                                    name="rut_profesor_guia"
                                    value={formData.rut_profesor_guia}
                                    onChange={handleChange}
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option value="">Seleccione profesor guía</option>
                                    {profesores.map(profesor => (
                                        <option key={profesor.rut_profesor} value={profesor.rut_profesor}>
                                            {profesor.nombre_profesor}
                                        </option>
                                    ))}
                                </select>
                                {errors.rut_profesor_guia && (
                                    <p className="mt-1 text-sm text-red-600">{errors.rut_profesor_guia}</p>
                                )}
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700">
                                    Profesor Comisión
                                </label>
                                <select
                                    name="rut_profesor_comision"
                                    value={formData.rut_profesor_comision}
                                    onChange={handleChange}
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option value="">Seleccione profesor comisión</option>
                                    {profesores.map(profesor => (
                                        <option key={profesor.rut_profesor} value={profesor.rut_profesor}>
                                            {profesor.nombre_profesor}
                                        </option>
                                    ))}
                                </select>
                                {errors.rut_profesor_comision && (
                                    <p className="mt-1 text-sm text-red-600">{errors.rut_profesor_comision}</p>
                                )}
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700">
                                    Profesor Co-Guía (Opcional)
                                </label>
                                <select
                                    name="rut_profesor_co_guia"
                                    value={formData.rut_profesor_co_guia}
                                    onChange={handleChange}
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option value="">Seleccione profesor co-guía</option>
                                    {profesores.map(profesor => (
                                        <option key={profesor.rut_profesor} value={profesor.rut_profesor}>
                                            {profesor.nombre_profesor}
                                        </option>
                                    ))}
                                </select>
                            </div>
                        </>
                    )}

                    {formData.tipo_habilitacion === 'PrTut' && (
                        <>
                            <div>
                                <label className="block text-sm font-medium text-gray-700">
                                    Profesor Tutor
                                </label>
                                <select
                                    name="rut_profesor_tutor"
                                    value={formData.rut_profesor_tutor}
                                    onChange={handleChange}
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option value="">Seleccione profesor tutor</option>
                                    {profesores.map(profesor => (
                                        <option key={profesor.rut_profesor} value={profesor.rut_profesor}>
                                            {profesor.nombre_profesor}
                                        </option>
                                    ))}
                                </select>
                                {errors.rut_profesor_tutor && (
                                    <p className="mt-1 text-sm text-red-600">{errors.rut_profesor_tutor}</p>
                                )}
                            </div>

                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">
                                        RUT Empresa
                                    </label>
                                    <input
                                        type="text"
                                        name="rut_empresa"
                                        value={formData.rut_empresa}
                                        onChange={handleChange}
                                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                    {errors.rut_empresa && (
                                        <p className="mt-1 text-sm text-red-600">{errors.rut_empresa}</p>
                                    )}
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">
                                        Nombre Empresa
                                    </label>
                                    <input
                                        type="text"
                                        name="nombre_empresa"
                                        value={formData.nombre_empresa}
                                        onChange={handleChange}
                                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                    {errors.nombre_empresa && (
                                        <p className="mt-1 text-sm text-red-600">{errors.nombre_empresa}</p>
                                    )}
                                </div>
                            </div>

                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">
                                        RUT Supervisor
                                    </label>
                                    <input
                                        type="text"
                                        name="rut_supervisor"
                                        value={formData.rut_supervisor}
                                        onChange={handleChange}
                                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                    {errors.rut_supervisor && (
                                        <p className="mt-1 text-sm text-red-600">{errors.rut_supervisor}</p>
                                    )}
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">
                                        Nombre Supervisor
                                    </label>
                                    <input
                                        type="text"
                                        name="nombre_supervisor"
                                        value={formData.nombre_supervisor}
                                        onChange={handleChange}
                                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                    {errors.nombre_supervisor && (
                                        <p className="mt-1 text-sm text-red-600">{errors.nombre_supervisor}</p>
                                    )}
                                </div>
                            </div>
                        </>
                    )}
                </div>
            </div>

            <div className="flex justify-end">
                <button
                    type="submit"
                    disabled={loading}
                    className="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
                >
                    {loading ? 'Guardando...' : 'Guardar Habilitación'}
                </button>
            </div>
        </form>
    );
}

export default HabilitacionForm;