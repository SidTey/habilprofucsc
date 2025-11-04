function UcscLogs({ logs, loading, onRefresh }) {
    const getStatusBadge = (estado) => {
        if (estado === 'exitoso') {
            return (
                <span className="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                    Exitoso
                </span>
            );
        } else {
            return (
                <span className="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                    Error
                </span>
            );
        }
    };

    return (
        <div>
            <div className="flex justify-between items-center mb-4">
                <h2 className="text-xl font-semibold">Logs del Sistema (R1.13)</h2>
                <button
                    onClick={onRefresh}
                    disabled={loading}
                    className="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50"
                >
                    {loading ? 'Cargando...' : 'Actualizar'}
                </button>
            </div>

            <div className="mb-4 p-3 bg-blue-50 rounded-md">
                <p className="text-sm text-blue-700">
                    <strong>R1.13:</strong> Se registra un log del sistema con los datos cargados, 
                    indicando la fecha de ingreso de la carga realizada al sistema.
                </p>
            </div>

            {loading ? (
                <div className="flex justify-center items-center h-32">
                    <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                </div>
            ) : logs.length === 0 ? (
                <div className="text-center py-8 text-gray-500">
                    No hay logs disponibles
                </div>
            ) : (
                <div className="space-y-4">
                    {logs.map((log) => (
                        <div 
                            key={log.id} 
                            className={`border rounded-lg p-4 ${
                                log.estado === 'exitoso' 
                                    ? 'border-green-200 bg-green-50' 
                                    : 'border-red-200 bg-red-50'
                            }`}
                        >
                            <div className="flex justify-between items-start mb-3">
                                <div className="flex items-center space-x-2">
                                    <span className="text-sm font-medium text-gray-600">Log #{log.id}</span>
                                    {getStatusBadge(log.estado)}
                                </div>
                                <span className="text-sm text-gray-500">{log.created_at}</span>
                            </div>

                            {log.estado === 'exitoso' ? (
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <h4 className="font-medium text-gray-800 mb-2">Datos del Alumno</h4>
                                        <div className="space-y-1 text-sm">
                                            <p><span className="font-medium">RUT:</span> {log.rut_alumno?.toLocaleString()}</p>
                                            <p><span className="font-medium">Nombre:</span> {log.nombre_alumno}</p>
                                            <p><span className="font-medium">Correo:</span> {log.correo_alumno}</p>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 className="font-medium text-gray-800 mb-2">Datos del Profesor</h4>
                                        <div className="space-y-1 text-sm">
                                            <p><span className="font-medium">RUT:</span> {log.rut_profesor?.toLocaleString()}</p>
                                            <p><span className="font-medium">Nombre:</span> {log.nombre_profesor}</p>
                                            <p><span className="font-medium">Correo:</span> {log.correo_profesor}</p>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 className="font-medium text-gray-800 mb-2">Información Adicional</h4>
                                        <div className="space-y-1 text-sm">
                                            <p><span className="font-medium">Fecha Ingreso:</span> {log.fecha_ingreso}</p>
                                            <p>
                                                <span className="font-medium">Nota Final:</span> 
                                                {log.nota_final ? ` ${log.nota_final}` : ' Sin nota'}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            ) : (
                                <div>
                                    <h4 className="font-medium text-red-800 mb-2">Error en el Proceso</h4>
                                    <div className="space-y-2 text-sm">
                                        {log.mensaje && (
                                            <p className="text-red-700 bg-red-100 p-2 rounded">
                                                <span className="font-medium">Mensaje:</span> {log.mensaje}
                                            </p>
                                        )}
                                        {log.rut_alumno && (
                                            <p><span className="font-medium">RUT Alumno:</span> {log.rut_alumno.toLocaleString()}</p>
                                        )}
                                        {log.rut_profesor && (
                                            <p><span className="font-medium">RUT Profesor:</span> {log.rut_profesor.toLocaleString()}</p>
                                        )}
                                        {log.fecha_ingreso && (
                                            <p><span className="font-medium">Fecha Ingreso:</span> {log.fecha_ingreso}</p>
                                        )}
                                    </div>
                                </div>
                            )}
                        </div>
                    ))}
                </div>
            )}

            <div className="mt-4 flex justify-between items-center text-sm text-gray-600">
                <span>Total de logs: {logs.length}</span>
                <span>Mostrando los últimos 100 registros</span>
            </div>
        </div>
    );
}

export default UcscLogs;
