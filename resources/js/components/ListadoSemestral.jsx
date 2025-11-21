// resources/js/components/ListadoSemestral.jsx
import React, { useState } from 'react';
import { Row, Col, Form, Button, Table, Alert, Badge } from 'react-bootstrap';
import axios from 'axios';

function ListadoSemestral() {
    console.log('🔵 ListadoSemestral se está renderizando');
    
    const [semestre, setSemestre] = useState('');
    const [habilitaciones, setHabilitaciones] = useState([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');
    const [semestreConsultado, setSemestreConsultado] = useState('');

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setError('');
        setHabilitaciones([]);

        if (!semestre) {
            setError('Por favor ingrese el semestre en formato YYYY-S (ejemplo: 2025-1)');
            setLoading(false);
            return;
        }

        try {
            const response = await axios.get('/api/listado-semestral', {
                params: { semestre_inicio: semestre }
            });

            if (response.data.success) {
                setHabilitaciones(response.data.habilitaciones || []);
                setSemestreConsultado(response.data.semestre);
            }
        } catch (err) {
            if (err.response?.status === 422) {
                setError(err.response.data.message || 'El valor de Semestre_Inicio no es válido');
            } else {
                setError('Error al cargar los datos del listado semestral');
            }
        } finally {
            setLoading(false);
        }
    };

    // R4.2.1: Renderizar tabla para PrIng o PrInv
    // Campos: Rut_Alumno, Nombre_Alumno, Profesor_Guia, Profesor_Co_Guia (opcional),
    // Profesor_Comision, Tipo_Habilitación, Descripción_Habilitación, Título_Proyecto_Práctica, Nota_Final
    const renderTablaPrIngPrInv = (habs) => {
        if (habs.length === 0) return null;

        return (
            <div className="mb-4">
                <h5 className="mb-3">
                    <Badge bg="primary">Proyecto de Grado e Investigación (PrIng / PrInv)</Badge>
                </h5>
                <div className="table-responsive">
                    <Table striped bordered hover size="sm">
                        <thead className="table-dark">
                            <tr>
                                <th>Tipo Habilitación</th>
                                <th>Rut Alumno</th>
                                <th>Nombre Alumno</th>
                                <th>Profesor Guía</th>
                                <th>Profesor Co-Guía</th>
                                <th>Profesor Comisión</th>
                                <th>Título Proyecto/Práctica</th>
                                <th>Descripción Habilitación</th>
                                <th>Nota Final</th>
                            </tr>
                        </thead>
                        <tbody>
                            {habs.map((hab, idx) => (
                                <tr key={idx}>
                                    <td className="text-center">
                                        <Badge bg={hab.tipo_habilitacion === 'PrIng' ? 'success' : 'info'}>
                                            {hab.tipo_habilitacion}
                                        </Badge>
                                    </td>
                                    <td>{hab.rut_alumno}</td>
                                    <td>{hab.nombre_alumno}</td>
                                    <td>
                                        {hab.profesor_guia ? (
                                            <div>
                                                <strong>{hab.profesor_guia.nombre_profesor}</strong>
                                                <br />
                                                <small className="text-muted">RUT: {hab.profesor_guia.rut_profesor}</small>
                                            </div>
                                        ) : <span className="text-muted">-</span>}
                                    </td>
                                    <td>
                                        {hab.profesor_coguia ? (
                                            <div>
                                                <strong>{hab.profesor_coguia.nombre_profesor}</strong>
                                                <br />
                                                <small className="text-muted">RUT: {hab.profesor_coguia.rut_profesor}</small>
                                            </div>
                                        ) : <span className="text-muted">-</span>}
                                    </td>
                                    <td>
                                        {hab.profesor_comision ? (
                                            <div>
                                                <strong>{hab.profesor_comision.nombre_profesor}</strong>
                                                <br />
                                                <small className="text-muted">RUT: {hab.profesor_comision.rut_profesor}</small>
                                            </div>
                                        ) : <span className="text-muted">-</span>}
                                    </td>
                                    <td style={{ maxWidth: '250px' }}>
                                        <small>{hab.titulo_proyecto_practica || '-'}</small>
                                    </td>
                                    <td style={{ maxWidth: '300px' }}>
                                        <small>{hab.descripcion_habilitacion ? 
                                            (hab.descripcion_habilitacion.length > 150 ? 
                                                hab.descripcion_habilitacion.substring(0, 150) + '...' 
                                                : hab.descripcion_habilitacion)
                                            : '-'
                                        }</small>
                                    </td>
                                    <td className="text-center">
                                        <strong className="text-primary">{hab.nota_final}</strong>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </Table>
                </div>
            </div>
        );
    };

    // R4.2.2: Renderizar tabla para PrTut
    // Campos: Rut_Alumno, Nombre_Alumno, Nombre_Supervisor, Rut_Supervisor,
    // Profesor_Tutor, Nombre_Empresa, Rut_Empresa, Descripción_Habilitación, Nota_Final
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
                                <th>Rut Alumno</th>
                                <th>Nombre Alumno</th>
                                <th>Nombre Supervisor</th>
                                <th>Rut Supervisor</th>
                                <th>Profesor Tutor</th>
                                <th>Nombre Empresa</th>
                                <th>Rut Empresa</th>
                                <th>Descripción Habilitación</th>
                                <th>Nota Final</th>
                            </tr>
                        </thead>
                        <tbody>
                            {habs.map((hab, idx) => (
                                <tr key={idx}>
                                    <td>{hab.rut_alumno}</td>
                                    <td>{hab.nombre_alumno}</td>
                                    <td>
                                        {hab.supervisor ? (
                                            <strong>{hab.supervisor.nombre_supervisor}</strong>
                                        ) : <span className="text-muted">-</span>}
                                    </td>
                                    <td>
                                        {hab.supervisor ? (
                                            <span className="text-muted">{hab.supervisor.rut_supervisor}</span>
                                        ) : <span className="text-muted">-</span>}
                                    </td>
                                    <td>
                                        {hab.profesor_tutor ? (
                                            <div>
                                                <strong>{hab.profesor_tutor.nombre_profesor}</strong>
                                                <br />
                                                <small className="text-muted">RUT: {hab.profesor_tutor.rut_profesor}</small>
                                            </div>
                                        ) : <span className="text-muted">-</span>}
                                    </td>
                                    <td>
                                        {hab.empresa ? (
                                            <strong>{hab.empresa.nombre_empresa}</strong>
                                        ) : <span className="text-muted">-</span>}
                                    </td>
                                    <td>
                                        {hab.empresa ? (
                                            <span className="text-muted">{hab.empresa.rut_empresa}</span>
                                        ) : <span className="text-muted">-</span>}
                                    </td>
                                    <td style={{ maxWidth: '300px' }}>
                                        <small>{hab.descripcion_habilitacion ? 
                                            (hab.descripcion_habilitacion.length > 150 ? 
                                                hab.descripcion_habilitacion.substring(0, 150) + '...' 
                                                : hab.descripcion_habilitacion)
                                            : '-'
                                        }</small>
                                    </td>
                                    <td className="text-center">
                                        <strong className="text-primary">{hab.nota_final}</strong>
                                    </td>
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
                                <strong>Semestre Inicio</strong> (formato: YYYY-S)
                                            </Form.Label>
                                            <Form.Control
                                                type="text"
                                                value={semestre}
                                                onChange={(e) => setSemestre(e.target.value)}
                                                placeholder="Ej: 2025-1"
                                                disabled={loading}
                                            />
                                            <Form.Text className="text-muted">
                                                Ingrese el año (2020-2050) y semestre (1 o 2)
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

                            {semestreConsultado && (
                                <Alert variant="info" className="mt-3">
                                    <strong>Consultando semestre:</strong> {semestreConsultado} | 
                                    <strong> Total de habilitaciones:</strong> {habilitaciones.length}
                                </Alert>
                            )}

                            {habilitaciones.length > 0 && (
                                <div className="mt-4">
                                    {renderTablaPrIngPrInv(habsPrIngPrInv)}
                                    {renderTablaPrTut(habsPrTut)}
                                </div>
                            )}

                            {!loading && habilitaciones.length === 0 && semestreConsultado && (
                                <Alert variant="warning" className="mt-3">
                                    No se encontraron habilitaciones para el semestre {semestreConsultado}
                                </Alert>
                            )}
        </div>
    );
}

export default ListadoSemestral;
