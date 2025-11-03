// resources/js/components/HabilprofForm.jsx
import React, { useState, useEffect } from 'react';
import axios from 'axios';
import { 
    Container, 
    Row, 
    Col, 
    Card, 
    Form, 
    Button, 
    Spinner, 
    Alert 
} from 'react-bootstrap'; // ¡Ahora sí tienes esto instalado!

// Componente para los campos de PrIng y PrInv
const PrIngInvFields = ({ formData, handleChange, profesores, errors }) => (
    <>
        <h5 className="text-ucsc mt-4 mb-3">Datos de la Práctica (PrIng / PrInv)</h5>
        <Form.Group className="mb-3" controlId="titulo_proyecto">
            <Form.Label>Título del Proyecto</Form.Label>
            <Form.Control
                type="text"
                name="titulo_proyecto"
                value={formData.titulo_proyecto}
                onChange={handleChange}
                isInvalid={!!errors.titulo_proyecto}
                placeholder="Título del proyecto"
            />
            <Form.Control.Feedback type="invalid">
                {errors.titulo_proyecto && errors.titulo_proyecto[0]}
            </Form.Control.Feedback>
        </Form.Group>

        <Row>
            <Form.Group as={Col} md={6} className="mb-3" controlId="rut_profesor_guia">
                <Form.Label>Profesor Guía</Form.Label>
                <Form.Select
                    name="rut_profesor_guia"
                    value={formData.rut_profesor_guia}
                    onChange={handleChange}
                    isInvalid={!!errors.rut_profesor_guia}
                >
                    <option value="">Seleccionar profesor...</option>
                    {profesores.map(profe => (
                        <option key={profe.rut_profesor} value={profe.rut_profesor}>
                            {profe.nombre_profesor}
                        </option>
                    ))}
                </Form.Select>
                <Form.Control.Feedback type="invalid">
                    {errors.rut_profesor_guia && errors.rut_profesor_guia[0]}
                </Form.Control.Feedback>
            </Form.Group>

            <Form.Group as={Col} md={6} className="mb-3" controlId="rut_profesor_comision">
                <Form.Label>Profesor Comisión</Form.Label>
                <Form.Select
                    name="rut_profesor_comision"
                    value={formData.rut_profesor_comision}
                    onChange={handleChange}
                    isInvalid={!!errors.rut_profesor_comision}
                >
                    <option value="">Seleccionar profesor...</option>
                    {profesores.map(profe => (
                        <option key={profe.rut_profesor} value={profe.rut_profesor}>
                            {profe.nombre_profesor}
                        </option>
                    ))}
                </Form.Select>
                <Form.Control.Feedback type="invalid">
                    {errors.rut_profesor_comision && errors.rut_profesor_comision[0]}
                </Form.Control.Feedback>
            </Form.Group>
        </Row>

        <Form.Group className="mb-3" controlId="rut_profesor_co_guia">
            <Form.Label>Profesor Co-Guía (Opcional)</Form.Label>
            <Form.Select
                name="rut_profesor_co_guia"
                value={formData.rut_profesor_co_guia}
                onChange={handleChange}
            >
                <option value="">Seleccionar profesor...</option>
                {profesores.map(profe => (
                    <option key={profe.rut_profesor} value={profe.rut_profesor}>
                            {profe.nombre_profesor}
                    </option>
                ))}
            </Form.Select>
        </Form.Group>
    </>
);

// Componente para los campos de PrTut
const PrTutFields = ({ formData, handleChange, profesores, errors }) => (
    <>
        <h5 className="text-ucsc mt-4 mb-3">Datos de la Práctica (PrTut)</h5>
        <Row>
            <Form.Group as={Col} md={6} className="mb-3" controlId="rut_empresa">
                <Form.Label>RUT Empresa</Form.Label>
                <Form.Control
                    type="text"
                    name="rut_empresa"
                    value={formData.rut_empresa}
                    onChange={handleChange}
                    isInvalid={!!errors.rut_empresa}
                    placeholder="76.123.456-7"
                />
                <Form.Control.Feedback type="invalid">{errors.rut_empresa && errors.rut_empresa[0]}</Form.Control.Feedback>
            </Form.Group>
            <Form.Group as={Col} md={6} className="mb-3" controlId="nombre_empresa">
                <Form.Label>Nombre Empresa</Form.Label>
                <Form.Control
                    type="text"
                    name="nombre_empresa"
                    value={formData.nombre_empresa}
                    onChange={handleChange}
                    isInvalid={!!errors.nombre_empresa}
                    placeholder="Nombre de la empresa"
                />
                <Form.Control.Feedback type="invalid">{errors.nombre_empresa && errors.nombre_empresa[0]}</Form.Control.Feedback>
            </Form.Group>
        </Row>
        <Row>
            <Form.Group as={Col} md={6} className="mb-3" controlId="rut_supervisor">
                <Form.Label>RUT Supervisor</Form.Label>
                <Form.Control
                    type="text"
                    name="rut_supervisor"
                    value={formData.rut_supervisor}
                    onChange={handleChange}
                    isInvalid={!!errors.rut_supervisor}
                    placeholder="15.123.456-7"
                />
                <Form.Control.Feedback type="invalid">{errors.rut_supervisor && errors.rut_supervisor[0]}</Form.Control.Feedback>
            </Form.Group>
            <Form.Group as={Col} md={6} className="mb-3" controlId="nombre_supervisor">
                <Form.Label>Nombre Supervisor</Form.Label>
                <Form.Control
                    type="text"
                    name="nombre_supervisor"
                    value={formData.nombre_supervisor}
                    onChange={handleChange}
                    isInvalid={!!errors.nombre_supervisor}
                    placeholder="Nombre supervisor"
                />
                <Form.Control.Feedback type="invalid">{errors.nombre_supervisor && errors.nombre_supervisor[0]}</Form.Control.Feedback>
            </Form.Group>
        </Row>
        <Form.Group className="mb-3" controlId="rut_profesor_tutor">
            <Form.Label>Profesor Tutor</Form.Label>
            <Form.Select
                name="rut_profesor_tutor"
                value={formData.rut_profesor_tutor}
                onChange={handleChange}
                isInvalid={!!errors.rut_profesor_tutor}
            >
                <option value="">Seleccionar profesor...</option>
                {profesores.map(profe => (
                    <option key={profe.rut_profesor} value={profe.rut_profesor}>
                        {profe.nombre_profesor}
                    </option>
                ))}
            </Form.Select>
            <Form.Control.Feedback type="invalid">{errors.rut_profesor_tutor && errors.rut_profesor_tutor[0]}</Form.Control.Feedback>
        </Form.Group>
    </>
);


// --- COMPONENTE PRINCIPAL DEL FORMULARIO ---
function HabilprofForm() {
    const [formData, setFormData] = useState({
        rut_alumno: '',
        tipo_habilitacion: '',
        descripcion_habilitacion: '',
        año_semestre: new Date().getFullYear(),
        numero_semestre: (new Date().getMonth() < 6) ? 1 : 2, // Semestre actual
        titulo_proyecto: '',
        rut_profesor_guia: '',
        rut_profesor_comision: '',
        rut_profesor_co_guia: '',
        rut_supervisor: '',
        nombre_supervisor: '',
        rut_empresa: '',
        nombre_empresa: '',
        rut_profesor_tutor: '',
    });

    const [alumnos, setAlumnos] = useState([]);
    const [profesores, setProfesores] = useState([]);
    const [loading, setLoading] = useState(false);
    const [loadingData, setLoadingData] = useState(true);
    const [errors, setErrors] = useState({});
    const [successMessage, setSuccessMessage] = useState('');

    // Cargar alumnos y profesores al montar el componente
    useEffect(() => {
        const fetchData = async () => {
            try {
                setLoadingData(true);
                const [alumnosRes, profesRes] = await Promise.all([
                    axios.get('/api/alumnos-disponibles'),
                    axios.get('/api/profesores-disponibles')
                ]);
                setAlumnos(alumnosRes.data.data || []);
                setProfesores(profesRes.data.data || []);
                setLoadingData(false);
            } catch (error) {
                console.error("Error al cargar datos:", error);
                setErrors({ general: 'No se pudieron cargar los alumnos o profesores.' });
                setLoadingData(false);
            }
        };
        fetchData();
    }, []);

    // Manejador inputs
    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData(prevData => ({
            ...prevData,
            [name]: value
        }));
    };

    // Manejador envío
    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setErrors({});
        setSuccessMessage('');

        try {
            const response = await axios.post('/api/habilitacion-profesional', formData);
            setSuccessMessage(response.data.message);
  
        } catch (error) {
            if (error.response && error.response.status === 422) {
                setErrors(error.response.data.errors);
            } else {
                setErrors({ general: error.response?.data?.message || 'Error al enviar el formulario.' });
            }
        } finally {
            setLoading(false);
        }
    };

    return (
        <Container className="my-5">
            <Row className="justify-content-center">
                <Col md={10} lg={8}>
                    <Card className="shadow-sm">
                        <Card.Header className="ucsc-header text-white">
                            <h4 className="mb-0">Habilprof</h4>
                            <p className="mb-0 small">Formulario de Inscripción de Habilitación Profesional</p>
                        </Card.Header>
                        <Card.Body>

                            {successMessage && <Alert variant="success">{successMessage}</Alert>}
                            {errors.general && <Alert variant="danger">{errors.general}</Alert>}

                            <Form onSubmit={handleSubmit}>
                                <h5 className="text-ucsc mb-3">Datos Generales</h5>

                                <Form.Group className="mb-3" controlId="rut_alumno">
                                    <Form.Label>Alumno</Form.Label>
                                    <Form.Select
                                        name="rut_alumno"
                                        value={formData.rut_alumno}
                                        onChange={handleChange}
                                        isInvalid={!!errors.rut_alumno}
                                    >
                                        <option value="">Seleccionar alumno...</option>
                                        {alumnos.map(alumno => (
                                            <option key={alumno.rut_alumno} value={alumno.rut_alumno}>
                                                {alumno.rut_alumno} - {alumno.nombre_alumno}
                                            </option>
                                        ))}
                                    </Form.Select>
                                    <Form.Control.Feedback type="invalid">
                                        {errors.rut_alumno && errors.rut_alumno[0]}
                                    </Form.Control.Feedback>
                                </Form.Group>

                                <Row>
                                    <Form.Group as={Col} md={6} className="mb-3" controlId="año_semestre">
                                        <Form.Label>Año</Form.Label>
                                        <Form.Control
                                            type="number"
                                            name="año_semestre"
                                            value={formData.año_semestre}
                                            onChange={handleChange}
                                            isInvalid={!!errors.año_semestre}
                                            placeholder="Año"
                                        />
                                        <Form.Control.Feedback type="invalid">
                                            {errors.año_semestre && errors.año_semestre[0]}
                                        </Form.Control.Feedback>
                                    </Form.Group>
                                    <Form.Group as={Col} md={6} className="mb-3" controlId="numero_semestre">
                                        <Form.Label>Semestre</Form.Label>
                                        <Form.Select
                                            name="numero_semestre"
                                            value={formData.numero_semestre}
                                            onChange={handleChange}
                                            isInvalid={!!errors.numero_semestre}
                                        >
                                            <option value="">Seleccionar...</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                        </Form.Select>
                                        <Form.Control.Feedback type="invalid">
                                            {errors.numero_semestre && errors.numero_semestre[0]}
                                        </Form.Control.Feedback>
                                    </Form.Group>
                                </Row>
                                
                                <Form.Group className="mb-3" controlId="tipo_habilitacion">
                                    <Form.Label>Tipo de Habilitación</Form.Label>
                                    <div>
                                        {['Proyecto de Ingeniería', 'Proyecto de Investigación', 'Práctica Tutelada'].map(tipo => (
                                            <Form.Check
                                                inline
                                                key={tipo}
                                                type="radio"
                                                name="tipo_habilitacion"
                                                id={`tipo-${tipo}`}
                                                label={tipo}
                                                value={tipo}
                                                checked={formData.tipo_habilitacion === tipo}
                                                onChange={handleChange}
                                            />
                                        ))}
                                    </div>
                                </Form.Group>

                                <Form.Group className="mb-3" controlId="descripcion_habilitacion">
                                    <Form.Label>Descripción</Form.Label>
                                    <Form.Control
                                        as="textarea"
                                        rows={3}
                                        name="descripcion_habilitacion"
                                        value={formData.descripcion_habilitacion}
                                        onChange={handleChange}
                                        isInvalid={!!errors.descripcion_habilitacion}
                                        placeholder="Descripción detallada (mín. 50 caracteres)"
                                    />
                                    <Form.Control.Feedback type="invalid">
                                        {errors.descripcion_habilitacion && errors.descripcion_habilitacion[0]}
                                    </Form.Control.Feedback>
                                </Form.Group>

                                {/* --- CAMPOS DINÁMICOS --- */}
                                {(formData.tipo_habilitacion === 'Proyecto de Ingeniería' || formData.tipo_habilitacion === 'Proyecto de Investigación') && (
                                    <PrIngInvFields 
                                        formData={formData} 
                                        handleChange={handleChange} 
                                        profesores={profesores} 
                                        errors={errors} 
                                    />
                                )}

                                {formData.tipo_habilitacion === 'Práctica Tutelada' && (
                                    <PrTutFields 
                                        formData={formData} 
                                        handleChange={handleChange} 
                                        profesores={profesores} 
                                        errors={errors} 
                                    />
                                )}
                                
                                <hr />

                                <div className="text-end">
                                    <Button type="submit" className="btn-ucsc" disabled={loading}>
                                        {loading ? (
                                            <>
                                                <Spinner as="span" animation="border" size="sm" role="status" aria-hidden="true" />
                                                Inscribiendo...
                                            </>
                                        ) : (
                                            'Inscribir Habilitación'
                                        )}
                                    </Button>
                                </div>
                            </Form>
                        </Card.Body>
                    </Card>
                </Col>
            </Row>
        </Container>
    );
}

export default HabilprofForm;