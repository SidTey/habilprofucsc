function UcscDataTable({ registros, loading, onRefresh }) {
    return (
        <div>
            <div className="flex justify-between items-center mb-4">
                <h2 className="text-xl font-semibold">Registros UCSC</h2>
                <button
                    onClick={onRefresh}
                    disabled={loading}
                    className="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50"
                >
                    {loading ? 'Cargando...' : 'Actualizar'}
                </button>
            </div>

            {loading ? (
                <div className="flex justify-center items-center h-32">
                    <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                </div>
            ) : registros.length === 0 ? (
                <div className="text-center py-8 text-gray-500">
                    No hay registros disponibles
                </div>
            ) : (
                <div className="overflow-x-auto">
                    <table className="min-w-full divide-y divide-gray-200">
                        <thead className="bg-gray-50">
                            <tr>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ID
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Alumno
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Profesor
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Fecha Ingreso
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nota Final
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Fecha Creación
                                </th>
                            </tr>
                        </thead>
                        <tbody className="bg-white divide-y divide-gray-200">
                            {registros.map((registro) => (
                                <tr key={registro.id} className="hover:bg-gray-50">
                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {registro.id}
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        <div className="text-sm text-gray-900">{registro.nombre_alumno || 'Sin nombre'}</div>
                                        <div className="text-sm text-gray-500">
                                            RUT: {registro.rut_alumno ? registro.rut_alumno.toLocaleString() : 'No disponible'}
                                        </div>
                                        <div className="text-sm text-gray-500">{registro.correo_alumno || 'Sin correo'}</div>
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        <div className="text-sm text-gray-900">{registro.nombre_profesor || 'Sin nombre'}</div>
                                        <div className="text-sm text-gray-500">
                                            RUT: {registro.rut_profesor ? registro.rut_profesor.toLocaleString() : 'No disponible'}
                                        </div>
                                        <div className="text-sm text-gray-500">{registro.correo_profesor || 'Sin correo'}</div>
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {registro.fecha_ingreso}
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        {registro.nota_final ? (
                                            <span className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full ${
                                                registro.nota_final >= 4.0 
                                                    ? 'bg-green-100 text-green-800' 
                                                    : 'bg-red-100 text-red-800'
                                            }`}>
                                                {registro.nota_final}
                                            </span>
                                        ) : (
                                            <span className="text-gray-400 text-sm">Sin nota</span>
                                        )}
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {registro.created_at}
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            )}

            <div className="mt-4 text-sm text-gray-600">
                Total de registros: {registros.length}
            </div>
        </div>
    );
}

export default UcscDataTable;
