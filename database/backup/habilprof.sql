--
-- PostgreSQL database dump
--

\restrict 6M6b0BBkyQ32IdYCXbEdjXnf31lvfEPzwiyfwoIT7kTlCWqnsnCjLqfWd4wGH4k

-- Dumped from database version 17.6
-- Dumped by pg_dump version 17.6

-- Started on 2025-11-21 10:06:13

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
-- Name: public; Type: SCHEMA; Schema: -; Owner: pg_database_owner
--

CREATE SCHEMA public;


ALTER SCHEMA public OWNER TO pg_database_owner;

--
-- TOC entry 4971 (class 0 OID 0)
-- Dependencies: 4
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: pg_database_owner
--

COMMENT ON SCHEMA public IS 'standard public schema';


--
-- TOC entry 856 (class 1247 OID 49575)
-- Name: tipo_rol; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE public.tipo_rol AS ENUM (
    'Profesor_Guia',
    'Profesor_Co_Guia',
    'Profesor_Comision',
    'Profesor_Tutor'
);


ALTER TYPE public.tipo_rol OWNER TO postgres;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 217 (class 1259 OID 49583)
-- Name: alumno; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.alumno (
    rut_alumno bigint NOT NULL,
    nombre_alumno character varying(255) NOT NULL,
    correo_alumno character varying(255) NOT NULL
);


ALTER TABLE public.alumno OWNER TO postgres;

--
-- TOC entry 218 (class 1259 OID 49588)
-- Name: asigna; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.asigna (
    id_habilitacion integer NOT NULL,
    rut_profesor bigint NOT NULL,
    rol public.tipo_rol
);


ALTER TABLE public.asigna OWNER TO postgres;

--
-- TOC entry 219 (class 1259 OID 49591)
-- Name: autentificacion_de_usuario; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.autentificacion_de_usuario (
    rut_admin bigint NOT NULL,
    "contraseña" character varying(255) NOT NULL
);


ALTER TABLE public.autentificacion_de_usuario OWNER TO postgres;

--
-- TOC entry 220 (class 1259 OID 49594)
-- Name: empresa; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.empresa (
    rut_empresa bigint NOT NULL,
    nombre_empresa character varying(255) NOT NULL
);


ALTER TABLE public.empresa OWNER TO postgres;

--
-- TOC entry 221 (class 1259 OID 49597)
-- Name: habilitacion_profesional; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.habilitacion_profesional OWNER TO postgres;

--
-- TOC entry 222 (class 1259 OID 49602)
-- Name: habilitacion_profesional_id_habilitacion_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.habilitacion_profesional_id_habilitacion_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.habilitacion_profesional_id_habilitacion_seq OWNER TO postgres;

--
-- TOC entry 4972 (class 0 OID 0)
-- Dependencies: 222
-- Name: habilitacion_profesional_id_habilitacion_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.habilitacion_profesional_id_habilitacion_seq OWNED BY public.habilitacion_profesional.id_habilitacion;


--
-- TOC entry 223 (class 1259 OID 49603)
-- Name: pring; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.pring (
    id_habilitacion integer NOT NULL,
    titulo_proy character varying(255) NOT NULL
);


ALTER TABLE public.pring OWNER TO postgres;

--
-- TOC entry 224 (class 1259 OID 49606)
-- Name: prinv; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.prinv (
    id_habilitacion integer NOT NULL,
    titulo_proy character varying(255) NOT NULL
);


ALTER TABLE public.prinv OWNER TO postgres;

--
-- TOC entry 225 (class 1259 OID 49609)
-- Name: profesor; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.profesor (
    rut_profesor bigint NOT NULL,
    nombre_profesor character varying(255) NOT NULL,
    correo_profesor character varying(255) NOT NULL
);


ALTER TABLE public.profesor OWNER TO postgres;

--
-- TOC entry 226 (class 1259 OID 49614)
-- Name: prtut; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.prtut (
    id_habilitacion integer NOT NULL,
    rut_empresa bigint NOT NULL,
    rut_supervisor bigint NOT NULL
);


ALTER TABLE public.prtut OWNER TO postgres;

--
-- TOC entry 227 (class 1259 OID 49617)
-- Name: supervisor; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.supervisor (
    rut_supervisor bigint NOT NULL,
    nombre_supervisor character varying(255) NOT NULL,
    rut_empresa bigint NOT NULL
);


ALTER TABLE public.supervisor OWNER TO postgres;

--
-- TOC entry 4781 (class 2604 OID 49620)
-- Name: habilitacion_profesional id_habilitacion; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.habilitacion_profesional ALTER COLUMN id_habilitacion SET DEFAULT nextval('public.habilitacion_profesional_id_habilitacion_seq'::regclass);


--
-- TOC entry 4955 (class 0 OID 49583)
-- Dependencies: 217
-- Data for Name: alumno; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.alumno (rut_alumno, nombre_alumno, correo_alumno) FROM stdin;
19123456	Juan Pérez	jperez@ing.ucsc.cl
19234567	María García	mgarcia@ing.ucsc.cl
19345678	Pedro Rodríguez	prodriguez@ing.ucsc.cl
19456789	Ana Martínez	amartinez@ing.ucsc.cl
19567890	Luis Hernández	lhernandez@ing.ucsc.cl
18123456	Sofía López	slopez@ing.ucsc.cl
18234567	Diego González	dgonzalez@ing.ucsc.cl
18345678	Laura Díaz	ldiaz@ing.ucsc.cl
18456789	Carlos Sánchez	csanchez@ing.ucsc.cl
18567890	Valeria Torres	vtorres@ing.ucsc.cl
17123123	Fernando Rojas	frojas@ing.ucsc.cl
17234234	Gabriela Castro	gcastro@ing.ucsc.cl
17345345	Jorge Morales	jmorales@ing.ucsc.cl
17456456	Camila Soto	csoto@ing.ucsc.cl
17567567	Ricardo Herrera	rherrera@ing.ucsc.cl
20123123	Isidora Fuentes	ifuentes@ing.ucsc.cl
20234234	Patricio Guzmán	pguzman@ing.ucsc.cl
20345345	Daniela Silva	dsilva@ing.ucsc.cl
20456456	Felipe Rivera	frivera@ing.ucsc.cl
20567567	Andrea Vega	avega@ing.ucsc.cl
21111111	Sergio Peña	spena@ing.ucsc.cl
21222222	Beatriz Rivas	brivas@ing.ucsc.cl
21333333	Manuel Orellana	morellana@ing.ucsc.cl
21444444	Constanza Muñoz	cmuñoz@ing.ucsc.cl
21555555	Sebastián Godoy	sgodoy@ing.ucsc.cl
21554443	Sebastián Hustley	shustley@ing.ucsc.cl
21476231	Nicolas Alvarado	nalvarado@ing.ucsc.cl
\.


--
-- TOC entry 4956 (class 0 OID 49588)
-- Dependencies: 218
-- Data for Name: asigna; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.asigna (id_habilitacion, rut_profesor, rol) FROM stdin;
\.


--
-- TOC entry 4957 (class 0 OID 49591)
-- Dependencies: 219
-- Data for Name: autentificacion_de_usuario; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.autentificacion_de_usuario (rut_admin, "contraseña") FROM stdin;
12345678	$2y$10$O5.Vh3I5zyH43Gglw7WHcOe9AJIs9YnL6E9ly643Tk4SbGv1h1C7C
\.


--
-- TOC entry 4958 (class 0 OID 49594)
-- Dependencies: 220
-- Data for Name: empresa; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.empresa (rut_empresa, nombre_empresa) FROM stdin;
\.


--
-- TOC entry 4959 (class 0 OID 49597)
-- Dependencies: 221
-- Data for Name: habilitacion_profesional; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.habilitacion_profesional (id_habilitacion, rut_alumno, descripcion_habilitacion, nota_final, fecha_nota, "año_semestre", numero_semestre) FROM stdin;
\.


--
-- TOC entry 4961 (class 0 OID 49603)
-- Dependencies: 223
-- Data for Name: pring; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.pring (id_habilitacion, titulo_proy) FROM stdin;
\.


--
-- TOC entry 4962 (class 0 OID 49606)
-- Dependencies: 224
-- Data for Name: prinv; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.prinv (id_habilitacion, titulo_proy) FROM stdin;
\.


--
-- TOC entry 4963 (class 0 OID 49609)
-- Dependencies: 225
-- Data for Name: profesor; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.profesor (rut_profesor, nombre_profesor, correo_profesor) FROM stdin;
8111222	Francisco Gutiérrez	fgutierrez@ucsc.cl
9222333	María Paz Soto	mpsoto@ucsc.cl
10333444	Roberto Araya	raraya@ucsc.cl
11444555	Verónica Cárdenas	vcardenas@ucsc.cl
12555666	Gabriel Salazar	gsalazar@ucsc.cl
8987654	Carolina Fuentes	cfuentes@ucsc.cl
9876543	Andrés Pino	apino@ucsc.cl
10765432	Claudia Rojas	crojas@ucsc.cl
11654321	Daniel Vargas	dvargas@ucsc.cl
12432109	Elena Bustamante	ebustamante@ucsc.cl
\.


--
-- TOC entry 4964 (class 0 OID 49614)
-- Dependencies: 226
-- Data for Name: prtut; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.prtut (id_habilitacion, rut_empresa, rut_supervisor) FROM stdin;
\.


--
-- TOC entry 4965 (class 0 OID 49617)
-- Dependencies: 227
-- Data for Name: supervisor; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.supervisor (rut_supervisor, nombre_supervisor, rut_empresa) FROM stdin;
\.


--
-- TOC entry 4973 (class 0 OID 0)
-- Dependencies: 222
-- Name: habilitacion_profesional_id_habilitacion_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.habilitacion_profesional_id_habilitacion_seq', 2, true);


--
-- TOC entry 4783 (class 2606 OID 49622)
-- Name: alumno alumno_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.alumno
    ADD CONSTRAINT alumno_pkey PRIMARY KEY (rut_alumno);


--
-- TOC entry 4785 (class 2606 OID 49624)
-- Name: asigna asigna_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asigna
    ADD CONSTRAINT asigna_pkey PRIMARY KEY (id_habilitacion, rut_profesor);


--
-- TOC entry 4787 (class 2606 OID 49626)
-- Name: autentificacion_de_usuario autentificacion_de_usuarios_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.autentificacion_de_usuario
    ADD CONSTRAINT autentificacion_de_usuarios_pkey PRIMARY KEY (rut_admin);


--
-- TOC entry 4789 (class 2606 OID 49628)
-- Name: empresa empresa_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.empresa
    ADD CONSTRAINT empresa_pkey PRIMARY KEY (rut_empresa);


--
-- TOC entry 4791 (class 2606 OID 49630)
-- Name: habilitacion_profesional habilitacion_profesional_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.habilitacion_profesional
    ADD CONSTRAINT habilitacion_profesional_pkey PRIMARY KEY (id_habilitacion);


--
-- TOC entry 4793 (class 2606 OID 49632)
-- Name: pring pring_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pring
    ADD CONSTRAINT pring_pkey PRIMARY KEY (id_habilitacion);


--
-- TOC entry 4795 (class 2606 OID 49634)
-- Name: prinv prinv_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prinv
    ADD CONSTRAINT prinv_pkey PRIMARY KEY (id_habilitacion);


--
-- TOC entry 4797 (class 2606 OID 49636)
-- Name: profesor profesor_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.profesor
    ADD CONSTRAINT profesor_pkey PRIMARY KEY (rut_profesor);


--
-- TOC entry 4799 (class 2606 OID 49638)
-- Name: prtut prtut_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prtut
    ADD CONSTRAINT prtut_pkey PRIMARY KEY (id_habilitacion);


--
-- TOC entry 4801 (class 2606 OID 49640)
-- Name: supervisor supervisor_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.supervisor
    ADD CONSTRAINT supervisor_pkey PRIMARY KEY (rut_supervisor);


--
-- TOC entry 4802 (class 2606 OID 49641)
-- Name: asigna asigna_id_habilitacion_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asigna
    ADD CONSTRAINT asigna_id_habilitacion_fkey FOREIGN KEY (id_habilitacion) REFERENCES public.habilitacion_profesional(id_habilitacion);


--
-- TOC entry 4803 (class 2606 OID 49646)
-- Name: asigna asigna_rut_profesor_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asigna
    ADD CONSTRAINT asigna_rut_profesor_fkey FOREIGN KEY (rut_profesor) REFERENCES public.profesor(rut_profesor);


--
-- TOC entry 4804 (class 2606 OID 49651)
-- Name: habilitacion_profesional habilitacion_profesional_rut_alumno_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.habilitacion_profesional
    ADD CONSTRAINT habilitacion_profesional_rut_alumno_fkey FOREIGN KEY (rut_alumno) REFERENCES public.alumno(rut_alumno);


--
-- TOC entry 4805 (class 2606 OID 49656)
-- Name: pring pring_id_habilitacion_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pring
    ADD CONSTRAINT pring_id_habilitacion_fkey FOREIGN KEY (id_habilitacion) REFERENCES public.habilitacion_profesional(id_habilitacion) ON DELETE CASCADE;


--
-- TOC entry 4806 (class 2606 OID 49661)
-- Name: prinv prinv_id_habilitacion_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prinv
    ADD CONSTRAINT prinv_id_habilitacion_fkey FOREIGN KEY (id_habilitacion) REFERENCES public.habilitacion_profesional(id_habilitacion) ON DELETE CASCADE;


--
-- TOC entry 4807 (class 2606 OID 49666)
-- Name: prtut prtut_id_habilitacion_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prtut
    ADD CONSTRAINT prtut_id_habilitacion_fkey FOREIGN KEY (id_habilitacion) REFERENCES public.habilitacion_profesional(id_habilitacion) ON DELETE CASCADE;


--
-- TOC entry 4808 (class 2606 OID 49671)
-- Name: prtut prtut_rut_empresa_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prtut
    ADD CONSTRAINT prtut_rut_empresa_fkey FOREIGN KEY (rut_empresa) REFERENCES public.empresa(rut_empresa);


--
-- TOC entry 4809 (class 2606 OID 49676)
-- Name: prtut prtut_rut_supervisor_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prtut
    ADD CONSTRAINT prtut_rut_supervisor_fkey FOREIGN KEY (rut_supervisor) REFERENCES public.supervisor(rut_supervisor);


-- Completed on 2025-11-21 10:06:13

--
-- PostgreSQL database dump complete
--

\unrestrict 6M6b0BBkyQ32IdYCXbEdjXnf31lvfEPzwiyfwoIT7kTlCWqnsnCjLqfWd4wGH4k

