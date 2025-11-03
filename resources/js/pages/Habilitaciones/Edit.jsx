import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm, Link, usePage } from '@inertiajs/react';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import PrimaryButton from '@/Components/PrimaryButton';
import SelectInput from '@/Components/SelectInput';

// --- (INICIO) SUB-COMPONENTES DEFINIDOS AFUERA ---
// Ahora son componentes estables y no se re-crearán en cada render.

/**
 * R3.4.2.1: Campos para PrIng o PrInv
 */
const PrIngPrInvFields = ({ data, setData, errors, profesores }) => (
    <div className="p-4 mt-4 border rounded-lg">
        <h3 className="text-lg font-medium">Datos de Proyecto</h3>
        
        {/* R2.6 Titulo */}
        <div className="mt-4">
            <InputLabel htmlFor="titulo_proyecto_practica" value="Título del Proyecto" />
            <TextInput id="titulo_proyecto_practica" name="titulo_proyecto_practica" value={data.titulo_proyecto_practica} className="mt-1 block w-full" onChange={(e) => setData('titulo_proyecto_practica', e.target.value)} />
            <InputError message={errors.titulo_proyecto_practica} className="mt-2" />
        </div>

        {/* R3.4.2.3.1 - Profesores */}
        <div className="mt-4">
            <InputLabel htmlFor="rut_profesor_guia" value="Profesor Guía" />
            <SelectInput id="rut_profesor_guia" name="rut_profesor_guia" className="mt-1 block w-full" value={data.rut_profesor_guia} onChange={(e) => setData('rut_profesor_guia', e.target.value)}>
                <option value="">-- Seleccione un profesor --</option>
                {profesores.map(p => (
                    <option key={p.rut_profesor} value={p.rut_profesor}>{p.nombre_profesor}</option>
                ))}
            </SelectInput>
            <InputError message={errors.rut_profesor_guia} className="mt-2" />
        </div>

        <div className="mt-4">
            <InputLabel htmlFor="rut_profesor_co_guia" value="Profesor Co-Guía" />
            <SelectInput id="rut_profesor_co_guia" name="rut_profesor_co_guia" className="mt-1 block w-full" value={data.rut_profesor_co_guia} onChange={(e) => setData('rut_profesor_co_guia', e.target.value)}>
                <option value="">-- Seleccione un profesor --</option>
                {profesores.map(p => (
                    <option key={p.rut_profesor} value={p.rut_profesor}>{p.nombre_profesor}</option>
                ))}
            </SelectInput>
            <InputError message={errors.rut_profesor_co_guia} className="mt-2" />
        </div>
        
        <div className="mt-4">
            <InputLabel htmlFor="rut_profesor_comision" value="Profesor Comisión" />
            <SelectInput id="rut_profesor_comision" name="rut_profesor_comision" className="mt-1 block w-full" value={data.rut_profesor_comision} onChange={(e) => setData('rut_profesor_comision', e.target.value)}>
                <option value="">-- Seleccione un profesor --</option>
                {profesores.map(p => (
                    <option key={p.rut_profesor} value={p.rut_profesor}>{p.nombre_profesor}</option>
                ))}
            </SelectInput>
            <InputError message={errors.rut_profesor_comision} className="mt-2" />
        </div>
    </div>
);

/**
 * R3.4.2.2: Campos para PrTut
 */
const PrTutFields = ({ data, setData, errors, profesores }) => (
    <div className="p-4 mt-4 border rounded-lg">
        <h3 className="text-lg font-medium">Datos de Práctica</h3>
        
        <div className="mt-4">
            <InputLabel htmlFor="nombre_empresa" value="Nombre Empresa" />
            <TextInput id="nombre_empresa" name="nombre_empresa" value={data.nombre_empresa} className="mt-1 block w-full" onChange={(e) => setData('nombre_empresa', e.target.value)} />
            <InputError message={errors.nombre_empresa} className="mt-2" />
        </div>
        <div className="mt-4">
            <InputLabel htmlFor="rut_empresa" value="RUT Empresa" />
            <TextInput id="rut_empresa" name="rut_empresa" value={data.rut_empresa} className="mt-1 block w-full" onChange={(e) => setData('rut_empresa', e.target.value)} />
            <InputError message={errors.rut_empresa} className="mt-2" />
        </div>
         <div className="mt-4">
            <InputLabel htmlFor="nombre_supervisor" value="Nombre Supervisor" />
            <TextInput id="nombre_supervisor" name="nombre_supervisor" value={data.nombre_supervisor} className="mt-1 block w-full" onChange={(e) => setData('nombre_supervisor', e.target.value)} />
            <InputError message={errors.nombre_supervisor} className="mt-2" />
        </div>
         <div className="mt-4">
            <InputLabel htmlFor="rut_supervisor" value="RUT Supervisor" />
            <TextInput id="rut_supervisor" name="rut_supervisor" value={data.rut_supervisor} className="mt-1 block w-full" onChange={(e) => setData('rut_supervisor', e.target.value)} />
            <InputError message={errors.rut_supervisor} className="mt-2" />
        </div>
        
        <div className="mt-4">
            <InputLabel htmlFor="rut_profesor_tutor" value="Profesor Tutor " />
            <SelectInput id="rut_profesor_tutor" name="rut_profesor_tutor" className="mt-1 block w-full" value={data.rut_profesor_tutor} onChange={(e) => setData('rut_profesor_tutor', e.target.value)}>
                <option value="">-- Seleccione un profesor --</option>
                {profesores.map(p => (
                    <option key={p.rut_profesor} value={p.rut_profesor}>{p.nombre_profesor}</option>
                ))}
            </SelectInput>
            <InputError message={errors.rut_profesor_tutor} className="mt-2" />
        </div>
    </div>
);
// --- (FIN) SUB-COMPONENTES DEFINIDOS AFUERA ---


/**
 * COMPONENTE PRINCIPAL
 */
export default function Edit({ auth, habilitacion, profesores, roles_actuales }) {
    
    // Obtenemos los props flash (para errores del servidor)
    const { props } = usePage();

    const { data, setData, patch, processing, errors } = useForm({
        // --- Datos Alumno (Solo Lectura) ---
        rut_alumno: habilitacion.rut_alumno || '',
        nombre_alumno: habilitacion.alumno ? habilitacion.alumno.nombre_alumno : '',

        // --- Campos Obligatorios (R3.1) ---
        tipo_habilitacion: habilitacion.tipo || 'PrIng',
        descripcion_habilitacion: habilitacion.descripcion_habilitacion || '',
        año_semestre: habilitacion.año_semestre || '2025',
        numero_semestre: habilitacion.numero_semestre || '1',

        // --- Campos Opcionales ---
        titulo_proyecto_practica: habilitacion.pring ? habilitacion.pring.titulo_proy : '',
        nombre_empresa: habilitacion.prtut ? habilitacion.prtut.nombre_empresa : '',
        rut_empresa: habilitacion.prtut ? habilitacion.prtut.rut_empresa : '',
        nombre_supervisor: habilitacion.prtut ? habilitacion.prtut.nombre_supervisor : '',
        rut_supervisor: habilitacion.prtut ? habilitacion.prtut.rut_supervisor : '',
        nota_final: habilitacion.nota_final || '',
        fecha_nota: habilitacion.fecha_nota || '',
        
        // --- Campos de Profesor (leídos de 'roles_actuales') ---
        rut_profesor_guia: roles_actuales.Profesor_Guia || '',
        rut_profesor_co_guia: roles_actuales.Profesor_Co_Guia || '',
        rut_profesor_comision: roles_actuales.Profesor_Comision || '',
        rut_profesor_tutor: roles_actuales.Profesor_Tutor || '',
    });

    // Envía el formulario
    const submit = (e) => {
        e.preventDefault();
        patch(route('habilitaciones.update', habilitacion.id_habilitacion));
    };

    // Las definiciones de PrIngPrInvFields y PrTutFields YA NO ESTÁN AQUÍ

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Modificar Habilitación: {habilitacion.id_habilitacion}</h2>}
        >
            <Head title="Modificar Habilitación" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        
                        <form onSubmit={submit}>
                            
                            {/* --- BLOQUE DE ERROR FLASH (DEL SERVIDOR) --- */}
                            {props.flash && props.flash.error && (
                                <div className="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                                    <h4 className="font-bold">Error del Servidor:</h4>
                                    <p>{props.flash.error}</p>
                                </div>
                            )}

                            {/* --- BLOQUE DE ERROR DE VALIDACIÓN --- */}
                            {Object.keys(errors).length > 0 && (
                                <div className="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                                    <h4 className="font-bold">Error de Validación:</h4>
                                    <ul className="list-disc list-inside">
                                        {Object.values(errors).map((error, index) => (
                                            <li key={index}>{error}</li>
                                        ))}
                                    </ul>
                                </div>
                            )}

                            {/* --- DATOS ALUMNO (SOLO LECTURA) --- */}
                            <h3 className="text-lg font-medium">Datos del Alumno</h3>
                            <div className="mt-4 grid grid-cols-2 gap-4">
                                <div>
                                    <InputLabel htmlFor="rut_alumno" value="RUT Alumno" />
                                    <TextInput id="rut_alumno" name="rut_alumno" value={data.rut_alumno} className="mt-1 block w-full bg-gray-100" readOnly />
                                </div>
                                <div>
                                    <InputLabel htmlFor="nombre_alumno" value="Nombre Alumno" />
                                    <TextInput id="nombre_alumno" name="nombre_alumno" value={data.nombre_alumno} className="mt-1 block w-full bg-gray-100" readOnly />
                                </div>
                            </div>
                            
                            {/* --- DATOS HABILITACIÓN (OBLIGATORIOS) --- */}
                            <h3 className="text-lg font-medium mt-6">Datos de la Habilitación</h3>
                            
                            <div className="mt-4">
                                <InputLabel htmlFor="tipo_habilitacion" value="Tipo de Habilitación" />
                                <SelectInput id="tipo_habilitacion" name="tipo_habilitacion" className="mt-1 block w-full" value={data.tipo_habilitacion} onChange={(e) => setData('tipo_habilitacion', e.target.value)}>
                                    <option value="PrIng">PrIng</option>
                                    <option value="PrInv">PrInv</option>
                                    <option value="PrTut">PrTut</option>
                                </SelectInput>
                                <InputError message={errors.tipo_habilitacion} className="mt-2" />
                            </div>

                            <div className="mt-4">
                                <InputLabel htmlFor="descripcion_habilitacion" value="Descripción (Min 50, Max 500)" />
                                <textarea
                                    id="descripcion_habilitacion" name="descripcion_habilitacion"
                                    value={data.descripcion_habilitacion}
                                    className="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    onChange={(e) => setData('descripcion_habilitacion', e.target.value)}
                                    rows="5"
                                ></textarea>
                                <InputError message={errors.descripcion_hijabilitacion} className="mt-2" />
                            </div>
                            
                            <div className="mt-4 grid grid-cols-2 gap-4">
                                <div>
                                    <InputLabel htmlFor="año_semestre" value="Año Inicio" />
                                    <TextInput id="año_semestre" name="año_semestre" value={data.año_semestre} className="mt-1 block w-full" onChange={(e) => setData('año_semestre', e.target.value)} />
                                    <InputError message={errors.año_semestre} className="mt-2" />
                                </div>
                                <div>
                                    <InputLabel htmlFor="numero_semestre" value="Semestre Inicio" />
                                    <SelectInput id="numero_semestre" name="numero_semestre" className="mt-1 block w-full" value={data.numero_semestre} onChange={(e) => setData('numero_semestre', e.target.value)}>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                    </SelectInput>
                                    <InputError message={errors.numero_semestre} className="mt-2" />
                                </div>
                            </div>

                            {/* --- CAMPOS CONDICIONALES (R3.4.2) --- */}
                            
                            {(data.tipo_habilitacion === 'PrIng' || data.tipo_habilitacion === 'PrInv') && 
                                <PrIngPrInvFields data={data} setData={setData} errors={errors} profesores={profesores} />
                            }
                            
                            {(data.tipo_habilitacion === 'PrTut') && 
                                <PrTutFields data={data} setData={setData} errors={errors} profesores={profesores} />
                            }

                            {/* --- DATOS OPCIONALES (NOTA) --- */}
                            <h3 className="text-lg font-medium mt-6">Datos Adicionales</h3>
                            <div className="mt-4 grid grid-cols-2 gap-4">
                                <div>
                                    <InputLabel htmlFor="nota_final" value="Nota Final" />
                                    <TextInput id="nota_final" name="nota_final" value={data.nota_final} className="mt-1 block w-full" onChange={(e) => setData('nota_final', e.target.value)} />
                                    <InputError message={errors.nota_final} className="mt-2" />
                                </div>
                                <div>
                                    <InputLabel htmlFor="fecha_nota" value="Fecha Nota (DD/MM/AAAA)" />
                                    <TextInput id="fecha_nota" name="fecha_nota" value={data.fecha_nota} className="mt-1 block w-full" onChange={(e) => setData('fecha_nota', e.target.value)} placeholder="Ej: 30/10/2025" />
                                    <InputError message={errors.fecha_nota} className="mt-2" />
                                </div>
                            </div>

                            {/* --- Botón de Envío --- */}
                            <div className="flex items-center justify-end mt-6">
                                <Link href={route('habilitaciones.index')} className="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Cancelar
                                </Link>

                                <PrimaryButton className="ms-4" disabled={processing}>
                                    Terminar Modificación
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}