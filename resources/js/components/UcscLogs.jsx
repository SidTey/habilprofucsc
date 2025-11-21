import React from 'react';

/**
 * Componente para mostrar logs del sistema (R1.13)

 */
function UcscLogs({ logs, loading, onRefresh }) {
    return (
        <div>
            <div className="flex justify-between items-center mb-4">
                <h2 className="text-xl font-bold">Logs del Sistema (R1.13)</h2>
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
                    <p className="text-gray-600">Cargando logs...</p>
                </div>
            ) : logs && logs.length > 0 ? (
                <div className="overflow-x-auto">
                    <table className="min-w-full bg-white border border-gray-300">
                        <thead className="bg-gray-100">
                            <tr>
                                <th className="px-4 py-2 border">Timestamp</th>
                                <th className="px-4 py-2 border">Tipo</th>
                                <th className="px-4 py-2 border">Mensaje</th>
                            </tr>
                        </thead>
                        <tbody>
                            {logs.map((log, index) => (
                                <tr key={index} className="hover:bg-gray-50">
                                    <td className="px-4 py-2 border text-sm">{log.timestamp || '-'}</td>
                                    <td className="px-4 py-2 border">
                                        <span className={`px-2 py-1 rounded text-xs ${
                                            log.type === 'error' ? 'bg-red-100 text-red-700' :
                                            log.type === 'warning' ? 'bg-yellow-100 text-yellow-700' :
                                            'bg-blue-100 text-blue-700'
                                        }`}>
                                            {log.type || 'info'}
                                        </span>
                                    </td>
                                    <td className="px-4 py-2 border">{log.message || JSON.stringify(log)}</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            ) : (
                <div className="text-center py-8 bg-gray-50 rounded">
                    <p className="text-gray-600">No hay logs disponibles</p>
                    <p className="text-sm text-gray-500 mt-2">
                        (Componente stub - pendiente de implementación completa)
                    </p>
                </div>
            )}
        </div>
    );
}

export default UcscLogs;
