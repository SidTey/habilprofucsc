import { useState } from 'react';

function HabilitacionTable({ habilitaciones, loading, onRefresh }) {
    const [expandedRow, setExpandedRow] = useState(null);

    const toggleRow = (id) => {
        setExpandedRow(expandedRow === id ? null : id);
    };

    return (
        <div>
            <div className="flex justify-between items-center mb-4">
                <h2 className="text-xl font-semibold">Habilitaciones Profesionales</h2>
                <button
                    onClick={onRefresh}
                    disabled={loading}
                    className="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
                >
                    {loading ? 'Cargando...' : 'Actualizar'}
                </button>
            </div>

            <div className="overflow-x-auto">
                <table className="min-w-full divide-y divide-gray-200">
                    <thead className="bg-gray-50">
                        <tr>
                            <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ID Habilitación
                            </th>
                            <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Alumno
                            </th>
                            <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tipo
                            </th>
                            <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado
                            </th>
                            <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nota Final
                            </th>
                            <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody className="bg-white divide-y divide-gray-200">
                        {habilitaciones.map((hab) => (
                            <>
                                <tr key={hab.id_habilitacion} className="hover:bg-gray-50">
                                    <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {hab.id_habilitacion}
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {hab.alumno?.nombre_alumno}
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {hab.tipo_habilitacion}
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {hab.nota_final ? 'Finalizada' : 'En Proceso'}
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {hab.nota_final || 'No disponible'}
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button
                                            onClick={() => toggleRow(hab.id_habilitacion)}
                                            className="text-blue-600 hover:text-blue-900"
                                        >
                                            {expandedRow === hab.id_habilitacion ? 'Ocultar' : 'Ver Detalles'}
                                        </button>
                                    </td>
                                </tr>
                                {expandedRow === hab.id_habilitacion && (
                                    <tr>
                                        <td colSpan="6" className="px-6 py-4 bg-gray-50">
                                            <div className="grid grid-cols-2 gap-4">
                                                <div>
                                                    <h4 className="font-semibold mb-2">Descripción:</h4>
                                                    <p className="text-sm text-gray-600">{hab.descripcion_habilitacion}</p>
                                                </div>
                                                <div>
                                                    <h4 className="font-semibold mb-2">Profesores:</h4>
                                                    <ul className="text-sm text-gray-600">
                                                        {hab.asignaciones?.map((asig) => (
                                                            <li key={asig.rut_profesor}>
                                                                {asig.rol}: {asig.profesor?.nombre_profesor}
                                                            </li>
                                                        ))}
                                                    </ul>
                                                </div>
                                                {hab.practica_ingenieria && (
                                                    <div className="col-span-2">
                                                        <h4 className="font-semibold mb-2">Título del Proyecto:</h4>
                                                        <p className="text-sm text-gray-600">{hab.practica_ingenieria.titulo_proy}</p>
                                                    </div>
                                                )}
                                            </div>
                                        </td>
                                    </tr>
                                )}
                            </>
                        ))}
                    </tbody>
                </table>
            </div>
        </div>
    );
}

export default HabilitacionTable;