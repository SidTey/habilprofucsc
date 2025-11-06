--
-- PostgreSQL database dump
--

\restrict VUpqdcyFEcgTdXYHT7Nkb3gxfyIYsrvU6o3XTtMIKgPmWgxoC45FjaNu7x6vRY3

-- Dumped from database version 17.6
-- Dumped by pg_dump version 17.6

-- Started on 2025-11-05 20:46:29

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
-- TOC entry 5 (class 2615 OID 2200)
-- Name: public; Type: SCHEMA; Schema: -; Owner: pg_database_owner
--

CREATE SCHEMA public;


ALTER SCHEMA public OWNER TO pg_database_owner;

--
-- TOC entry 4972 (class 0 OID 0)
-- Dependencies: 5
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: pg_database_owner
--

COMMENT ON SCHEMA public IS 'standard public schema';


--
-- TOC entry 855 (class 1247 OID 47223)
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
-- TOC entry 217 (class 1259 OID 47231)
-- Name: alumno; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.alumno (
    rut_alumno bigint NOT NULL,
    nombre_alumno character varying(100),
    correo_alumno character varying(255)
);


ALTER TABLE public.alumno OWNER TO postgres;

--
-- TOC entry 218 (class 1259 OID 47234)
-- Name: asigna; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.asigna (
    id_habilitacion character varying(20) NOT NULL,
    rut_profesor bigint NOT NULL,
    rol public.tipo_rol
);


ALTER TABLE public.asigna OWNER TO postgres;

--
-- TOC entry 219 (class 1259 OID 47237)
-- Name: autentificacion_de_usuario; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.autentificacion_de_usuario (
    rut_admin bigint NOT NULL,
    "contraseña" character varying(100)
);


ALTER TABLE public.autentificacion_de_usuario OWNER TO postgres;

--
-- TOC entry 220 (class 1259 OID 47240)
-- Name: empresa; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.empresa (
    rut_empresa bigint NOT NULL,
    nombre_empresa character varying(100)
);


ALTER TABLE public.empresa OWNER TO postgres;

--
-- TOC entry 221 (class 1259 OID 47243)
-- Name: habilitacion_profesional; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.habilitacion_profesional (
    id_habilitacion character varying(20) GENERATED ALWAYS AS ((((((rut_alumno)::text || '_'::text) || ("año_semestre")::text) || '-'::text) || (numero_semestre)::text)) STORED NOT NULL,
    rut_alumno bigint,
    "año_semestre" integer,
    numero_semestre integer,
    descripcion_habilitacion character varying(500),
    nota_final numeric(3,2),
    fecha_nota date,
    CONSTRAINT "habilitacion_profesional_año_semestre_check" CHECK ((("año_semestre" >= 2025) AND ("año_semestre" <= 2050))),
    CONSTRAINT habilitacion_profesional_nota_final_check CHECK (((nota_final >= 1.00) AND (nota_final <= 7.00))),
    CONSTRAINT habilitacion_profesional_numero_semestre_check CHECK ((numero_semestre = ANY (ARRAY[1, 2])))
);


ALTER TABLE public.habilitacion_profesional OWNER TO postgres;

--
-- TOC entry 222 (class 1259 OID 47252)
-- Name: pring; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.pring (
    id_habilitacion character varying(20) NOT NULL,
    titulo_proy character varying(500)
);


ALTER TABLE public.pring OWNER TO postgres;

--
-- TOC entry 223 (class 1259 OID 47257)
-- Name: prinv; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.prinv (
    id_habilitacion character varying(20) NOT NULL,
    titulo_proy character varying(500)
);


ALTER TABLE public.prinv OWNER TO postgres;

--
-- TOC entry 224 (class 1259 OID 47262)
-- Name: profesor; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.profesor (
    rut_profesor bigint NOT NULL,
    nombre_profesor character varying(100),
    correo_profesor character varying NOT NULL
);


ALTER TABLE public.profesor OWNER TO postgres;

--
-- TOC entry 225 (class 1259 OID 47267)
-- Name: prtut; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.prtut (
    id_habilitacion character varying(20) NOT NULL,
    rut_empresa bigint NOT NULL,
    rut_supervisor bigint NOT NULL
);


ALTER TABLE public.prtut OWNER TO postgres;

--
-- TOC entry 226 (class 1259 OID 47270)
-- Name: supervisor; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.supervisor (
    rut_supervisor bigint NOT NULL,
    nombre_supervisor character varying(100),
    rut_empresa bigint
);


ALTER TABLE public.supervisor OWNER TO postgres;

--
-- TOC entry 4957 (class 0 OID 47231)
-- Dependencies: 217
-- Data for Name: alumno; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 4958 (class 0 OID 47234)
-- Dependencies: 218
-- Data for Name: asigna; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 4959 (class 0 OID 47237)
-- Dependencies: 219
-- Data for Name: autentificacion_de_usuario; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 4960 (class 0 OID 47240)
-- Dependencies: 220
-- Data for Name: empresa; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 4961 (class 0 OID 47243)
-- Dependencies: 221
-- Data for Name: habilitacion_profesional; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 4962 (class 0 OID 47252)
-- Dependencies: 222
-- Data for Name: pring; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 4963 (class 0 OID 47257)
-- Dependencies: 223
-- Data for Name: prinv; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 4964 (class 0 OID 47262)
-- Dependencies: 224
-- Data for Name: profesor; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 4965 (class 0 OID 47267)
-- Dependencies: 225
-- Data for Name: prtut; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 4966 (class 0 OID 47270)
-- Dependencies: 226
-- Data for Name: supervisor; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 4785 (class 2606 OID 47274)
-- Name: alumno alumno_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.alumno
    ADD CONSTRAINT alumno_pkey PRIMARY KEY (rut_alumno);


--
-- TOC entry 4787 (class 2606 OID 47276)
-- Name: asigna asigna_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asigna
    ADD CONSTRAINT asigna_pkey PRIMARY KEY (id_habilitacion, rut_profesor);


--
-- TOC entry 4789 (class 2606 OID 47278)
-- Name: autentificacion_de_usuario autentificacion_de_usuarios_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.autentificacion_de_usuario
    ADD CONSTRAINT autentificacion_de_usuarios_pkey PRIMARY KEY (rut_admin);


--
-- TOC entry 4791 (class 2606 OID 47280)
-- Name: empresa empresa_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.empresa
    ADD CONSTRAINT empresa_pkey PRIMARY KEY (rut_empresa);


--
-- TOC entry 4793 (class 2606 OID 47282)
-- Name: habilitacion_profesional habilitacion_profesional_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.habilitacion_profesional
    ADD CONSTRAINT habilitacion_profesional_pkey PRIMARY KEY (id_habilitacion);


--
-- TOC entry 4795 (class 2606 OID 47284)
-- Name: pring pring_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pring
    ADD CONSTRAINT pring_pkey PRIMARY KEY (id_habilitacion);


--
-- TOC entry 4797 (class 2606 OID 47286)
-- Name: prinv prinv_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prinv
    ADD CONSTRAINT prinv_pkey PRIMARY KEY (id_habilitacion);


--
-- TOC entry 4799 (class 2606 OID 47288)
-- Name: profesor profesor_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.profesor
    ADD CONSTRAINT profesor_pkey PRIMARY KEY (rut_profesor);


--
-- TOC entry 4801 (class 2606 OID 47290)
-- Name: prtut prtut_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prtut
    ADD CONSTRAINT prtut_pkey PRIMARY KEY (id_habilitacion);


--
-- TOC entry 4803 (class 2606 OID 47292)
-- Name: supervisor supervisor_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.supervisor
    ADD CONSTRAINT supervisor_pkey PRIMARY KEY (rut_supervisor);


--
-- TOC entry 4804 (class 2606 OID 47293)
-- Name: asigna fk_asigna_habilitacion; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asigna
    ADD CONSTRAINT fk_asigna_habilitacion FOREIGN KEY (id_habilitacion) REFERENCES public.habilitacion_profesional(id_habilitacion) ON DELETE CASCADE;


--
-- TOC entry 4805 (class 2606 OID 47298)
-- Name: asigna fk_asigna_profesor; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.asigna
    ADD CONSTRAINT fk_asigna_profesor FOREIGN KEY (rut_profesor) REFERENCES public.profesor(rut_profesor) ON DELETE CASCADE;


--
-- TOC entry 4806 (class 2606 OID 47303)
-- Name: habilitacion_profesional habilitacion_profesional_rut_alumno_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.habilitacion_profesional
    ADD CONSTRAINT habilitacion_profesional_rut_alumno_fkey FOREIGN KEY (rut_alumno) REFERENCES public.alumno(rut_alumno) ON DELETE CASCADE;


--
-- TOC entry 4807 (class 2606 OID 47308)
-- Name: pring pring_id_habilitacion_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pring
    ADD CONSTRAINT pring_id_habilitacion_fkey FOREIGN KEY (id_habilitacion) REFERENCES public.habilitacion_profesional(id_habilitacion) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 4808 (class 2606 OID 47313)
-- Name: prinv prinv_id_habilitacion_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prinv
    ADD CONSTRAINT prinv_id_habilitacion_fkey FOREIGN KEY (id_habilitacion) REFERENCES public.habilitacion_profesional(id_habilitacion) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 4809 (class 2606 OID 47318)
-- Name: prtut prtut_id_habilitacion_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prtut
    ADD CONSTRAINT prtut_id_habilitacion_fkey FOREIGN KEY (id_habilitacion) REFERENCES public.habilitacion_profesional(id_habilitacion) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 4810 (class 2606 OID 47323)
-- Name: prtut prtut_rut_empresa_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prtut
    ADD CONSTRAINT prtut_rut_empresa_fkey FOREIGN KEY (rut_empresa) REFERENCES public.empresa(rut_empresa);


--
-- TOC entry 4811 (class 2606 OID 47328)
-- Name: prtut prtut_rut_supervisor_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.prtut
    ADD CONSTRAINT prtut_rut_supervisor_fkey FOREIGN KEY (rut_supervisor) REFERENCES public.supervisor(rut_supervisor);


--
-- TOC entry 4973 (class 0 OID 0)
-- Dependencies: 5
-- Name: SCHEMA public; Type: ACL; Schema: -; Owner: pg_database_owner
--

REVOKE USAGE ON SCHEMA public FROM PUBLIC;


-- Completed on 2025-11-05 20:46:29

--
-- PostgreSQL database dump complete
--

\unrestrict VUpqdcyFEcgTdXYHT7Nkb3gxfyIYsrvU6o3XTtMIKgPmWgxoC45FjaNu7x6vRY3

