import React from 'react';

/**
 * Componente para mostrar tabla de habilitaciones (R2)
 * TODO: Implementar la funcionalidad completa según el código de tu amigo
 */
function HabilitacionTable({ habilitaciones, loading, onRefresh }) {
    return (
        <div>
            <div className="flex justify-between items-center mb-4">
                <h2 className="text-xl font-bold">Habilitaciones (R2)</h2>
                <button
                    onClick={onRefresh}
                    disabled={loading}
                    className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50"
                >
                    {loading ? 'Cargando...' : 'Actualizar'}
                </button>
            </div>

            {loading ? (
                <div className="text-center py-8">
                    <p className="text-gray-600">Cargando habilitaciones...</p>
                </div>
            ) : habilitaciones && habilitaciones.length > 0 ? (
                <div className="overflow-x-auto">
                    <table className="min-w-full bg-white border border-gray-300">
                        <thead className="bg-gray-100">
                            <tr>
                                <th className="px-4 py-2 border">ID</th>
                                <th className="px-4 py-2 border">Alumno</th>
                                <th className="px-4 py-2 border">Descripción</th>
                                <th className="px-4 py-2 border">Nota</th>
                                <th className="px-4 py-2 border">Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            {habilitaciones.map((hab, index) => (
                                <tr key={hab.id_habilitacion || index} className="hover:bg-gray-50">
                                    <td className="px-4 py-2 border">{hab.id_habilitacion || index + 1}</td>
                                    <td className="px-4 py-2 border">{hab.rut_alumno || '-'}</td>
                                    <td className="px-4 py-2 border">{hab.descripcion_habilitacion || '-'}</td>
                                    <td className="px-4 py-2 border">{hab.nota_final || '-'}</td>
                                    <td className="px-4 py-2 border">{hab.fecha_nota || '-'}</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            ) : (
                <div className="text-center py-8 bg-gray-50 rounded">
                    <p className="text-gray-600">No hay habilitaciones disponibles</p>
                    <p className="text-sm text-gray-500 mt-2">
                        (Componente stub - pendiente de implementación completa)
                    </p>
                </div>
            )}
        </div>
    );
}

export default HabilitacionTable;
