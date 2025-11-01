import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, usePage, useForm, Link } from '@inertiajs/react';

export default function Index({ auth, habilitaciones }) {

    // Hook para mostrar mensajes flash (como "Borrado con éxito")
    const { props } = usePage();

    // Hook de Inertia para manejar el borrado
    const { delete: destroy } = useForm();

    // Función que se llama al hacer clic en "Eliminar"
    const submitDelete = (e, habilitacion) => {
        e.preventDefault();

        // R3.4.1: Pedir confirmación
        if (window.confirm('¿Seguro que desea eliminar esta habilitación profesional?')) {
            // R3.4.1.1: Enviar la petición DELETE a la ruta "destroy"
            destroy(route('habilitaciones.destroy', habilitacion.id_habilitacion));
        }
        // R3.4.1.2 (Cancelar Operación) se maneja automáticamente
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Habilitaciones</h2>}
        >
            <Head title="Habilitaciones" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                        {/* Mostrar mensaje de éxito (R3.4.1.1) */}
                        {props.flash && props.flash.message && (
                            <div className="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                                {props.flash.message}
                            </div>
                        )}
                        {props.flash && props.flash.error && (
                        <div className="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                        {props.flash.error}
                        </div>
                        )}

                        {/* Tabla con la lista de habilitaciones */}
                        <table className="min-w-full divide-y divide-gray-200">
                            <thead className="bg-gray-50">
                                <tr>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        ID Habilitación
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        RUT Alumno
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Nombre Alumno
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Tipo
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="bg-white divide-y divide-gray-200">
                                {habilitaciones.map((habilitacion) => (
                                    <tr key={habilitacion.id_habilitacion}>
                                    
                                        {/* Col 1: ID HABILITACIÓN */}
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            {habilitacion.id_habilitacion}
                                        </td>
                                
                                        {/* Col 2: RUT ALUMNO */}
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            {habilitacion.rut_alumno}
                                        </td>
                                
                                        {/* Col 3: NOMBRE ALUMNO (desde la relación) */}
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            {habilitacion.alumno ? habilitacion.alumno.nombre_alumno : (
                                                <span className="text-xs text-red-500">SIN ALUMNO</span>
                                            )}
                                        </td>
                                        
                                        {/* Col 4: TIPO (Esta celda ahora está limpia) */}
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            {habilitacion.tipo_habilitacion}
                                        </td>
                                        
                                        {/* Col 5: ACCIONES (Ambos botones JUNTOS) */}
                                        <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <Link
                                                href={route('habilitaciones.edit', habilitacion.id_habilitacion)}
                                                className="text-indigo-600 hover:text-indigo-900 mr-4" // mr-4 = margen derecho
                                            >
                                                Modificar
                                            </Link>
                                        
                                            <form onSubmit={(e) => submitDelete(e, habilitacion)} className="inline">
                                                <button
                                                    type="submit"
                                                    className="text-red-600 hover:text-red-900"
                                                >
                                                    Eliminar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}