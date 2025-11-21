--
-- PostgreSQL database dump
--

\restrict zkiNsWuw0SllfsPeWenfSQGOIfJDMBgGgBn0ZkIxbWfapMkWaWiA3gxVXkGdyCH

-- Dumped from database version 17.6
-- Dumped by pg_dump version 17.6

-- Started on 2025-11-20 20:58:12

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 4 (class 2615 OID 2200)
-- Name: public; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA public;


--
-- TOC entry 4971 (class 0 OID 0)
-- Dependencies: 4
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: -
--

COMMENT ON SCHEMA public IS 'standard public schema';


--
-- TOC entry 883 (class 1247 OID 48819)
-- Name: tipo_rol; Type: TYPE; Schema: public; Owner: -
--

CREATE TYPE public.tipo_rol AS ENUM (
    'Profesor_Guia',
    'Profesor_Co_Guia',
    'Profesor_Comision',
    'Profesor_Tutor'
);


SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 217 (class 1259 OID 48735)
-- Name: alumno; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.alumno (
    rut_alumno bigint NOT NULL,
    nombre_alumno character varying(255) NOT NULL,
    correo_alumno character varying(255) NOT NULL
);


--
-- TOC entry 227 (class 1259 OID 48827)
-- Name: asigna; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.asigna (
    id_habilitacion integer NOT NULL,
    rut_profesor bigint NOT NULL,
    rol character varying(255),
    tipo_rol public.tipo_rol
);


--
-- TOC entry 218 (class 1259 OID 48740)
-- Name: autentificacion_de_usuario; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.autentificacion_de_usuario (
    rut_admin bigint NOT NULL,
    "contraseña" character varying(255) NOT NULL
);


--
-- TOC entry 219 (class 1259 OID 48743)
-- Name: empresa; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.empresa (
    rut_empresa bigint NOT NULL,
    nombre_empresa character varying(255) NOT NULL
);


--
-- TOC entry 220 (class 1259 OID 48746)
-- Name: habilitacion_profesional; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.habilitacion_profesional (
    id_habilitacion integer NOT NULL,
    rut_alumno bigint NOT NULL,
    descripcion_habilitacion text NOT NULL,
    nota_final numeric,
    fecha_nota date,
    "año_semestre" integer NOT NULL,
    numero_semestre integer NOT NULL
);


--
-- TOC entry 221 (class 1259 OID 48751)
-- Name: habilitacion_profesional_id_habilitacion_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.habilitacion_profesional_id_habilitacion_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 4972 (class 0 OID 0)
-- Dependencies: 221
-- Name: habilitacion_profesional_id_habilitacion_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.habilitacion_profesional_id_habilitacion_seq OWNED BY public.habilitacion_profesional.id_habilitacion;


--
-- TOC entry 222 (class 1259 OID 48752)
-- Name: pring; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.pring (
    id_habilitacion integer NOT NULL,
    titulo_proy character varying(255) NOT NULL
);


--
-- TOC entry 223 (class 1259 OID 48755)
-- Name: prinv; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.prinv (
    id_habilitacion integer NOT NULL,
    titulo_proy character varying(255) NOT NULL
);


--
-- TOC entry 224 (class 1259 OID 48758)
-- Name: profesor; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.profesor (
    rut_profesor bigint NOT NULL,
    nombre_profesor character varying(255) NOT NULL,
    correo_profesor character varying(255) NOT NULL
);


--
-- TOC entry 225 (class 1259 OID 48763)
-- Name: prtut; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.prtut (
    id_habilitacion integer NOT NULL,
    rut_empresa bigint NOT NULL,
    rut_supervisor bigint NOT NULL
);


--
-- TOC entry 226 (class 1259 OID 48766)
-- Name: supervisor; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.supervisor (
    rut_supervisor bigint NOT NULL,
    nombre_supervisor character varying(255) NOT NULL,
    rut_empresa bigint NOT NULL
);


--
-- TOC entry 4781 (class 2604 OID 48769)
-- Name: habilitacion_profesional id_habilitacion; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.habilitacion_profesional ALTER COLUMN id_habilitacion SET DEFAULT nextval('public.habilitacion_profesional_id_habilitacion_seq'::regclass);


--
-- TOC entry 4955 (class 0 OID 48735)
-- Dependencies: 217
-- Data for Name: alumno; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.alumno (rut_alumno, nombre_alumno, correo_alumno) FROM stdin;
\.


--
-- TOC entry 4965 (class 0 OID 48827)
-- Dependencies: 227
-- Data for Name: asigna; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.asigna (id_habilitacion, rut_profesor, rol, tipo_rol) FROM stdin;
\.


--
-- TOC entry 4956 (class 0 OID 48740)
-- Dependencies: 218
-- Data for Name: autentificacion_de_usuario; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.autentificacion_de_usuario (rut_admin, "contraseña") FROM stdin;
12345678	$2y$12$QJtA8Gyd.aPHfWcs4j9c5et01.EcZrDxL7lqQeCeZd.YV5yYLOUvq
\.


--
-- TOC entry 4957 (class 0 OID 48743)
-- Dependencies: 219
-- Data for Name: empresa; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.empresa (rut_empresa, nombre_empresa) FROM stdin;
\.


--
-- TOC entry 4958 (class 0 OID 48746)
-- Dependencies: 220
-- Data for Name: habilitacion_profesional; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.habilitacion_profesional (id_habilitacion, rut_alumno, descripcion_habilitacion, nota_final, fecha_nota, "año_semestre", numero_semestre) FROM stdin;
\.


--
-- TOC entry 4960 (class 0 OID 48752)
-- Dependencies: 222
-- Data for Name: pring; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.pring (id_habilitacion, titulo_proy) FROM stdin;
\.


--
-- TOC entry 4961 (class 0 OID 48755)
-- Dependencies: 223
-- Data for Name: prinv; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.prinv (id_habilitacion, titulo_proy) FROM stdin;
\.


--
-- TOC entry 4962 (class 0 OID 48758)
-- Dependencies: 224
-- Data for Name: profesor; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.profesor (rut_profesor, nombre_profesor, correo_profesor) FROM stdin;
\.


--
-- TOC entry 4963 (class 0 OID 48763)
-- Dependencies: 225
-- Data for Name: prtut; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.prtut (id_habilitacion, rut_empresa, rut_supervisor) FROM stdin;
\.


--
-- TOC entry 4964 (class 0 OID 48766)
-- Dependencies: 226
-- Data for Name: supervisor; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.supervisor (rut_supervisor, nombre_supervisor, rut_empresa) FROM stdin;
\.


--
-- TOC entry 4973 (class 0 OID 0)
-- Dependencies: 221
-- Name: habilitacion_profesional_id_habilitacion_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.habilitacion_profesional_id_habilitacion_seq', 1, false);


--
-- TOC entry 4783 (class 2606 OID 48771)
-- Name: alumno alumno_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.alumno
    ADD CONSTRAINT alumno_pkey PRIMARY KEY (rut_alumno);


--
-- TOC entry 4801 (class 2606 OID 48831)
-- Name: asigna asigna_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.asigna
    ADD CONSTRAINT asigna_pkey PRIMARY KEY (id_habilitacion, rut_profesor);


--
-- TOC entry 4785 (class 2606 OID 48773)
-- Name: autentificacion_de_usuario autentificacion_de_usuarios_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.autentificacion_de_usuario
    ADD CONSTRAINT autentificacion_de_usuarios_pkey PRIMARY KEY (rut_admin);


--
-- TOC entry 4787 (class 2606 OID 48775)
-- Name: empresa empresa_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.empresa
    ADD CONSTRAINT empresa_pkey PRIMARY KEY (rut_empresa);


--
-- TOC entry 4789 (class 2606 OID 48777)
-- Name: habilitacion_profesional habilitacion_profesional_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.habilitacion_profesional
    ADD CONSTRAINT habilitacion_profesional_pkey PRIMARY KEY (id_habilitacion);


--
-- TOC entry 4791 (class 2606 OID 48779)
-- Name: pring pring_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pring
    ADD CONSTRAINT pring_pkey PRIMARY KEY (id_habilitacion);


--
-- TOC entry 4793 (class 2606 OID 48781)
-- Name: prinv prinv_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.prinv
    ADD CONSTRAINT prinv_pkey PRIMARY KEY (id_habilitacion);


--
-- TOC entry 4795 (class 2606 OID 48783)
-- Name: profesor profesor_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.profesor
    ADD CONSTRAINT profesor_pkey PRIMARY KEY (rut_profesor);


--
-- TOC entry 4797 (class 2606 OID 48785)
-- Name: prtut prtut_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.prtut
    ADD CONSTRAINT prtut_pkey PRIMARY KEY (id_habilitacion);


--
-- TOC entry 4799 (class 2606 OID 48787)
-- Name: supervisor supervisor_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supervisor
    ADD CONSTRAINT supervisor_pkey PRIMARY KEY (rut_supervisor);


--
-- TOC entry 4808 (class 2606 OID 48832)
-- Name: asigna asigna_id_habilitacion_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.asigna
    ADD CONSTRAINT asigna_id_habilitacion_fkey FOREIGN KEY (id_habilitacion) REFERENCES public.habilitacion_profesional(id_habilitacion);


--
-- TOC entry 4809 (class 2606 OID 48837)
-- Name: asigna asigna_rut_profesor_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.asigna
    ADD CONSTRAINT asigna_rut_profesor_fkey FOREIGN KEY (rut_profesor) REFERENCES public.profesor(rut_profesor);


--
-- TOC entry 4802 (class 2606 OID 48788)
-- Name: habilitacion_profesional habilitacion_profesional_rut_alumno_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.habilitacion_profesional
    ADD CONSTRAINT habilitacion_profesional_rut_alumno_fkey FOREIGN KEY (rut_alumno) REFERENCES public.alumno(rut_alumno);


--
-- TOC entry 4803 (class 2606 OID 48793)
-- Name: pring pring_id_habilitacion_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pring
    ADD CONSTRAINT pring_id_habilitacion_fkey FOREIGN KEY (id_habilitacion) REFERENCES public.habilitacion_profesional(id_habilitacion) ON DELETE CASCADE;


--
-- TOC entry 4804 (class 2606 OID 48798)
-- Name: prinv prinv_id_habilitacion_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.prinv
    ADD CONSTRAINT prinv_id_habilitacion_fkey FOREIGN KEY (id_habilitacion) REFERENCES public.habilitacion_profesional(id_habilitacion) ON DELETE CASCADE;


--
-- TOC entry 4805 (class 2606 OID 48803)
-- Name: prtut prtut_id_habilitacion_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.prtut
    ADD CONSTRAINT prtut_id_habilitacion_fkey FOREIGN KEY (id_habilitacion) REFERENCES public.habilitacion_profesional(id_habilitacion) ON DELETE CASCADE;


--
-- TOC entry 4806 (class 2606 OID 48808)
-- Name: prtut prtut_rut_empresa_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.prtut
    ADD CONSTRAINT prtut_rut_empresa_fkey FOREIGN KEY (rut_empresa) REFERENCES public.empresa(rut_empresa);


--
-- TOC entry 4807 (class 2606 OID 48813)
-- Name: prtut prtut_rut_supervisor_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.prtut
    ADD CONSTRAINT prtut_rut_supervisor_fkey FOREIGN KEY (rut_supervisor) REFERENCES public.supervisor(rut_supervisor);


-- Completed on 2025-11-20 20:58:12

--
-- PostgreSQL database dump complete
--

\unrestrict zkiNsWuw0SllfsPeWenfSQGOIfJDMBgGgBn0ZkIxbWfapMkWaWiA3gxVXkGdyCH

