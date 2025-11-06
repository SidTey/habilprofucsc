import React from 'react';

/**
 * Componente para mostrar tabla de registros UCSC
 * TODO: Implementar la funcionalidad completa según el código de tu amigo
 */
function UcscDataTable({ registros, loading, onRefresh }) {
    return (
        <div>
            <div className="flex justify-between items-center mb-4">
                <h2 className="text-xl font-bold">Registros UCSC</h2>
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
                    <p className="text-gray-600">Cargando registros...</p>
                </div>
            ) : registros && registros.length > 0 ? (
                <div className="overflow-x-auto">
                    <table className="min-w-full bg-white border border-gray-300">
                        <thead className="bg-gray-100">
                            <tr>
                                <th className="px-4 py-2 border">ID</th>
                                <th className="px-4 py-2 border">Datos</th>
                                <th className="px-4 py-2 border">Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            {registros.map((registro, index) => (
                                <tr key={index} className="hover:bg-gray-50">
                                    <td className="px-4 py-2 border">{index + 1}</td>
                                    <td className="px-4 py-2 border">
                                        {JSON.stringify(registro).substring(0, 50)}...
                                    </td>
                                    <td className="px-4 py-2 border">-</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            ) : (
                <div className="text-center py-8 bg-gray-50 rounded">
                    <p className="text-gray-600">No hay registros disponibles</p>
                    <p className="text-sm text-gray-500 mt-2">
                        (Componente stub - pendiente de implementación completa)
                    </p>
                </div>
            )}
        </div>
    );
}

export default UcscDataTable;
