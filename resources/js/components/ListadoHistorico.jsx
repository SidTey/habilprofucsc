// resources/js/components/ListadoHistorico.jsx
import React, { useState } from 'react';
import { Container, Row, Col, Card, Form, Button, Table, Alert } from 'react-bootstrap';
import axios from 'axios';

function ListadoHistorico() {
    const [formData, setFormData] = useState({
        rut_profesor: '',
        semestre_inicio: ''
    });
    const [results, setResults] = useState([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData(prev => ({
            ...prev,
            [name]: value
        }));
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setError('');
        setResults([]);

        if (!formData.rut_profesor || !formData.semestre_inicio) {
            setError('Ingrese rut_profesor y semestre');
            setLoading(false);
            return;
        }

        try {
            const response = await axios.get('/habilitacion/api/historico', {
                params: {
                    rut_profesor: formData.rut_profesor,
                    semestre_inicio: formData.semestre_inicio
                }
            });
            setResults(response.data.results || []);
        } catch (err) {
            setError(err.response?.data?.message || 'Error al cargar los datos');
        } finally {
            setLoading(false);
        }
    };

    const renderTickOrRut = (condition, rutValue) => {
        if (condition) {
            return <span style={{ textAlign: 'center', display: 'block' }}>✓</span>;
        }
        return rutValue || '';
    };

    return (
        <Container className="my-4">
            <Row className="justify-content-center">
                <Col md={12}>
                    <Card>
                        <Card.Header className="ucsc-header text-white">
                            <h4 className="mb-0">Listado Histórico</h4>
                        </Card.Header>
                        <Card.Body>
                            {error && <Alert variant="danger">{error}</Alert>}

                            <Form onSubmit={handleSubmit}>
                                <Row>
                                    <Form.Group as={Col} md={6} className="mb-3">
                                        <Form.Label>Rut Profesor</Form.Label>
                                        <Form.Control
                                            type="text"
                                            name="rut_profesor"
                                            value={formData.rut_profesor}
                                            onChange={handleChange}
                                            placeholder="11111111"
                                        />
                                    </Form.Group>
                                    <Form.Group as={Col} md={6} className="mb-3">
                                        <Form.Label>Semestre (YYYY-S)</Form.Label>
                                        <Form.Control
                                            type="text"
                                            name="semestre_inicio"
                                            value={formData.semestre_inicio}
                                            onChange={handleChange}
                                            placeholder="2025-1"
                                        />
                                    </Form.Group>
                                </Row>
                                <Button type="submit" className="btn-ucsc" disabled={loading}>
                                    {loading ? 'Cargando...' : 'Consultar'}
                                </Button>
                            </Form>

                            {results.length > 0 && (
                                <div className="mt-4 table-responsive">
                                    <Table striped bordered hover>
                                        <thead>
                                            <tr>
                                                <th>Id</th>
                                                <th>Rut Alumno</th>
                                                <th>Nombre Alumno</th>
                                                <th>Profesor Guía</th>
                                                <th>Co-Guía</th>
                                                <th>Comisión</th>
                                                <th>Tutor</th>
                                                <th>Título/Empresa</th>
                                                <th>Descripción</th>
                                                <th>Nota</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {results.map((r, idx) => {
                                                const guiaTick = r.rol ? (r.rol === 'Profesor_Guia') : (String(r.rut_guia) === String(formData.rut_profesor));
                                                const coTick = r.rol ? (r.rol === 'Profesor_Co_Guia') : (String(r.rut_co_guia) === String(formData.rut_profesor));
                                                const comTick = r.rol ? (r.rol === 'Profesor_Comision') : (String(r.rut_comision) === String(formData.rut_profesor));
                                                const tutorTick = r.rol ? (r.rol === 'Profesor_Tutor') : (String(r.rut_tutor) === String(formData.rut_profesor));
                                                const titulo = r.titulo_proyecto || r.nombre_empresa || '';
                                                const desc = (r.descripcion_habilitacion || '').substring(0, 250);

                                                return (
                                                    <tr key={idx}>
                                                        <td>{r.id_habilitacion}</td>
                                                        <td>{r.rut_alumno}</td>
                                                        <td>{r.nombre_alumno}</td>
                                                        <td style={{ textAlign: 'center' }}>{renderTickOrRut(guiaTick, r.rut_guia)}</td>
                                                        <td style={{ textAlign: 'center' }}>{renderTickOrRut(coTick, r.rut_co_guia)}</td>
                                                        <td style={{ textAlign: 'center' }}>{renderTickOrRut(comTick, r.rut_comision)}</td>
                                                        <td style={{ textAlign: 'center' }}>{renderTickOrRut(tutorTick, r.rut_tutor)}</td>
                                                        <td>{titulo}</td>
                                                        <td>{desc}</td>
                                                        <td>{r.nota_final}</td>
                                                    </tr>
                                                );
                                            })}
                                        </tbody>
                                    </Table>
                                </div>
                            )}

                            {!loading && results.length === 0 && formData.rut_profesor && formData.semestre_inicio && !error && (
                                <p className="mt-3">No hay resultados.</p>
                            )}
                        </Card.Body>
                    </Card>
                </Col>
            </Row>
        </Container>
    );
}

export default ListadoHistorico;
