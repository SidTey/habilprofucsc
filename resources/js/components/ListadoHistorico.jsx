// resources/js/components/ListadoHistorico.jsx
import React, { useState } from 'react';
import { Row, Col, Form, Button, Table, Alert, Badge } from 'react-bootstrap';
import axios from 'axios';

function ListadoHistorico() {
    console.log('🟢 ListadoHistorico se está renderizando');
    
    const [rutProfesor, setRutProfesor] = useState('');
    const [semestre, setSemestre] = useState('');
    const [habilitaciones, setHabilitaciones] = useState([]);
    const [profesor, setProfesor] = useState(null);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');
    const [semestreConsultado, setSemestreConsultado] = useState('');

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setError('');
        setHabilitaciones([]);
        setProfesor(null);

        if (!rutProfesor || !semestre) {
            setError('Por favor complete todos los campos requeridos');
            setLoading(false);
            return;
        }

        try {
            const response = await axios.get('/api/listado-historico', {
                params: {
                    rut_profesor: rutProfesor,
                    semestre_inicio: semestre
                }
            });

            if (response.data.success) {
                setHabilitaciones(response.data.habilitaciones || []);
                setProfesor(response.data.profesor);
                setSemestreConsultado(response.data.semestre);
            }
        } catch (err) {
            if (err.response?.status === 422) {
                setError(err.response.data.message || 'Datos inválidos');
            } else if (err.response?.status === 404) {
                setError(err.response.data.message || 
                    'El valor de Rut_Profesor no se encuentra en registrado en el sistema "Habilprof"');
            } else {
                setError('Error al cargar los datos del listado histórico');
            }
        } finally {
            setLoading(false);
        }
    };

    // Renderizar tabla para PrIng o PrInv
    const renderTablaPrIngPrInv = (habs) => {
        if (habs.length === 0) return null;

        return (
            <div className="mb-4">
                <h5 className="mb-3">
                    <Badge bg="primary">Proyecto de Grado e Investigación</Badge>
                </h5>
                <div className="table-responsive">
                    <Table striped bordered hover size="sm">
                        <thead className="table-dark">
                            <tr>
                                <th>Tipo</th>
                                <th>Rol Profesor</th>
                                <th>RUT Alumno</th>
                                <th>Nombre Alumno</th>
                                <th>Profesor Guía</th>
                                <th>Profesor Co-Guía</th>
                                <th>Profesor Comisión</th>
                                <th>Título Proyecto</th>
                                <th>Descripción</th>
                                <th>Nota Final</th>
                                <th>Fecha Nota</th>
                            </tr>
                        </thead>
                        <tbody>
                            {habs.map((hab, idx) => (
                                <tr key={idx}>
                                    <td>
                                        <Badge bg={hab.tipo_habilitacion === 'PrIng' ? 'success' : 'info'}>
                                            {hab.tipo_habilitacion}
                                        </Badge>
                                    </td>
                                    <td>
                                        <Badge bg="secondary">
                                            {hab.rol_profesor?.replace('Profesor_', '')}
                                        </Badge>
                                    </td>
                                    <td>{hab.rut_alumno}</td>
                                    <td>{hab.nombre_alumno}</td>
                                    <td>
                                        {hab.profesor_guia ? (
                                            <div>
                                                <small className="text-muted">RUT: {hab.profesor_guia.rut_profesor}</small>
                                                <br />
                                                <small>{hab.profesor_guia.nombre_profesor}</small>
                                            </div>
                                        ) : '-'}
                                    </td>
                                    <td>
                                        {hab.profesor_coguia ? (
                                            <div>
                                                <small className="text-muted">RUT: {hab.profesor_coguia.rut_profesor}</small>
                                                <br />
                                                <small>{hab.profesor_coguia.nombre_profesor}</small>
                                            </div>
                                        ) : '-'}
                                    </td>
                                    <td>
                                        {hab.profesor_comision ? (
                                            <div>
                                                <small className="text-muted">RUT: {hab.profesor_comision.rut_profesor}</small>
                                                <br />
                                                <small>{hab.profesor_comision.nombre_profesor}</small>
                                            </div>
                                        ) : '-'}
                                    </td>
                                    <td style={{ maxWidth: '200px' }}>
                                        <small>{hab.titulo_proyecto_practica || '-'}</small>
                                    </td>
                                    <td style={{ maxWidth: '200px' }}>
                                        <small>{hab.descripcion_habilitacion ? 
                                            hab.descripcion_habilitacion.substring(0, 80) + 
                                            (hab.descripcion_habilitacion.length > 80 ? '...' : '') 
                                            : '-'
                                        }</small>
                                    </td>
                                    <td className="text-center">
                                        <strong>{hab.nota_final}</strong>
                                    </td>
                                    <td><small>{hab.fecha_nota || '-'}</small></td>
                                </tr>
                            ))}
                        </tbody>
                    </Table>
                </div>
            </div>
        );
    };

    // Renderizar tabla para PrTut
    const renderTablaPrTut = (habs) => {
        if (habs.length === 0) return null;

        return (
            <div className="mb-4">
                <h5 className="mb-3">
                    <Badge bg="warning" text="dark">Práctica Profesional (PrTut)</Badge>
                </h5>
                <div className="table-responsive">
                    <Table striped bordered hover size="sm">
                        <thead className="table-dark">
                            <tr>
                                <th>Rol Profesor</th>
                                <th>RUT Alumno</th>
                                <th>Nombre Alumno</th>
                                <th>Profesor Tutor</th>
                                <th>Supervisor</th>
                                <th>Empresa</th>
                                <th>Descripción</th>
                                <th>Nota Final</th>
                                <th>Fecha Nota</th>
                            </tr>
                        </thead>
                        <tbody>
                            {habs.map((hab, idx) => (
                                <tr key={idx}>
                                    <td>
                                        <Badge bg="secondary">
                                            {hab.rol_profesor?.replace('Profesor_', '')}
                                        </Badge>
                                    </td>
                                    <td>{hab.rut_alumno}</td>
                                    <td>{hab.nombre_alumno}</td>
                                    <td>
                                        {hab.profesor_tutor ? (
                                            <div>
                                                <small className="text-muted">RUT: {hab.profesor_tutor.rut_profesor}</small>
                                                <br />
                                                <small>{hab.profesor_tutor.nombre_profesor}</small>
                                            </div>
                                        ) : '-'}
                                    </td>
                                    <td>
                                        {hab.supervisor ? (
                                            <div>
                                                <small className="text-muted">RUT: {hab.supervisor.rut_supervisor}</small>
                                                <br />
                                                <small>{hab.supervisor.nombre_supervisor}</small>
                                            </div>
                                        ) : '-'}
                                    </td>
                                    <td>
                                        {hab.empresa ? (
                                            <div>
                                                <small className="text-muted">RUT: {hab.empresa.rut_empresa}</small>
                                                <br />
                                                <small>{hab.empresa.nombre_empresa}</small>
                                            </div>
                                        ) : '-'}
                                    </td>
                                    <td style={{ maxWidth: '250px' }}>
                                        <small>{hab.descripcion_habilitacion ? 
                                            hab.descripcion_habilitacion.substring(0, 80) + 
                                            (hab.descripcion_habilitacion.length > 80 ? '...' : '') 
                                            : '-'
                                        }</small>
                                    </td>
                                    <td className="text-center">
                                        <strong>{hab.nota_final}</strong>
                                    </td>
                                    <td><small>{hab.fecha_nota || '-'}</small></td>
                                </tr>
                            ))}
                        </tbody>
                    </Table>
                </div>
            </div>
        );
    };

    // Separar habilitaciones por tipo
    const habsPrIngPrInv = habilitaciones.filter(h => 
        h.tipo_habilitacion === 'PrIng' || h.tipo_habilitacion === 'PrInv'
    );
    const habsPrTut = habilitaciones.filter(h => h.tipo_habilitacion === 'PrTut');

    return (
        <div className="p-4">
            {error && <Alert variant="danger">{error}</Alert>}

            <Form onSubmit={handleSubmit}>
                <Row className="align-items-end">
                    <Col md={4}>
                        <Form.Group className="mb-3">
                            <Form.Label>
                                <strong>RUT Profesor</strong> <span className="text-danger">*</span>
                            </Form.Label>
                            <Form.Control
                                type="text"
                                value={rutProfesor}
                                onChange={(e) => setRutProfesor(e.target.value)}
                                placeholder="Ej: 12345678"
                                disabled={loading}
                            />
                            <Form.Text className="text-muted">
                                                RUT sin puntos ni guión (entre 1000000 y 60000000)
                                            </Form.Text>
                                        </Form.Group>
                                    </Col>
                                    <Col md={4}>
                                        <Form.Group className="mb-3">
                                            <Form.Label>
                                                <strong>Semestre Inicio</strong> <span className="text-danger">*</span>
                                            </Form.Label>
                                            <Form.Control
                                                type="text"
                                                value={semestre}
                                                onChange={(e) => setSemestre(e.target.value)}
                                                placeholder="Ej: 2025-1"
                                                disabled={loading}
                                            />
                                            <Form.Text className="text-muted">
                                                Formato: YYYY-S (año 2020-2050, semestre 1 o 2)
                                            </Form.Text>
                                        </Form.Group>
                                    </Col>
                                    <Col md={2}>
                                        <Form.Group className="mb-3">
                                            <Button 
                                                type="submit" 
                                                className="btn-ucsc w-100" 
                                                disabled={loading}
                                            >
                                                {loading ? '⏳ Cargando...' : '🔍 Consultar'}
                                            </Button>
                                        </Form.Group>
                                    </Col>
                                </Row>
                            </Form>

                            {profesor && (
                                <Alert variant="info" className="mt-3">
                                    <Row>
                                        <Col>
                                            <strong>Profesor:</strong> {profesor.nombre_profesor} 
                                            <small className="text-muted"> (RUT: {profesor.rut_profesor})</small>
                                        </Col>
                                        <Col>
                                            <strong>Semestre:</strong> {semestreConsultado}
                                        </Col>
                                        <Col>
                                            <strong>Total habilitaciones:</strong> {habilitaciones.length}
                                        </Col>
                                    </Row>
                                </Alert>
                            )}

                            {habilitaciones.length > 0 && (
                                <div className="mt-4">
                                    {renderTablaPrIngPrInv(habsPrIngPrInv)}
                                    {renderTablaPrTut(habsPrTut)}
                                </div>
                            )}

                            {!loading && habilitaciones.length === 0 && profesor && (
                                <Alert variant="warning" className="mt-3">
                                    No se encontraron habilitaciones para el profesor {profesor.nombre_profesor} 
                                    en el semestre {semestreConsultado}
                                </Alert>
                            )}
        </div>
    );
}

export default ListadoHistorico;
