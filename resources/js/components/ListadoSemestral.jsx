// resources/js/components/ListadoSemestral.jsx
import React, { useState } from 'react';
import { Container, Row, Col, Card, Form, Button, Table, Alert } from 'react-bootstrap';
import axios from 'axios';

function ListadoSemestral() {
    const [semestre, setSemestre] = useState('');
    const [results, setResults] = useState([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setError('');
        setResults([]);

        if (!semestre) {
            setError('Ingrese semestre (ej. 2025-1)');
            setLoading(false);
            return;
        }

        try {
            const response = await axios.get('/habilitacion/api/semestral', {
                params: {
                    semestre_inicio: semestre
                }
            });
            setResults(response.data.results || []);
        } catch (err) {
            setError(err.response?.data?.message || 'Error al cargar los datos');
        } finally {
            setLoading(false);
        }
    };

    return (
        <Container className="my-4">
            <Row className="justify-content-center">
                <Col md={12}>
                    <Card>
                        <Card.Header className="ucsc-header text-white">
                            <h4 className="mb-0">Listado Semestral</h4>
                        </Card.Header>
                        <Card.Body>
                            {error && <Alert variant="danger">{error}</Alert>}

                            <Form onSubmit={handleSubmit}>
                                <Row>
                                    <Form.Group as={Col} md={6} className="mb-3">
                                        <Form.Label>Semestre (YYYY-S)</Form.Label>
                                        <Form.Control
                                            type="text"
                                            name="semestre_inicio"
                                            value={semestre}
                                            onChange={(e) => setSemestre(e.target.value)}
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
                                                const titulo = r.titulo_proyecto || r.nombre_empresa || '';
                                                const desc = (r.descripcion_habilitacion || '').substring(0, 250);

                                                return (
                                                    <tr key={idx}>
                                                        <td>{r.id_habilitacion}</td>
                                                        <td>{r.rut_alumno}</td>
                                                        <td>{r.nombre_alumno}</td>
                                                        <td style={{ textAlign: 'center' }}>{r.rut_guia || ''}</td>
                                                        <td style={{ textAlign: 'center' }}>{r.rut_co_guia || ''}</td>
                                                        <td style={{ textAlign: 'center' }}>{r.rut_comision || ''}</td>
                                                        <td style={{ textAlign: 'center' }}>{r.rut_tutor || ''}</td>
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

                            {!loading && results.length === 0 && semestre && !error && (
                                <p className="mt-3">No hay resultados.</p>
                            )}
                        </Card.Body>
                    </Card>
                </Col>
            </Row>
        </Container>
    );
}

export default ListadoSemestral;
