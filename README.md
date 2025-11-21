# Habilprof UCSC - Sistema de Habilitación Profesional

Sistema web integrado para la gestión de habilitaciones profesionales de estudiantes de la Universidad Católica de Santiago de Chile (UCSC).

---

##  Tabla de Contenidos

- [Requisitos Previos](#requisitos-previos)
- [Instalación Paso a Paso](#instalación-paso-a-paso)
- [Configuración del `.env`](#configuración-del-env)
- [Ejecución del Proyecto](#ejecución-del-proyecto)
- [Comandos Útiles](#comandos-útiles)
- [Estructura del Proyecto](#estructura-del-proyecto)
- [Documentación Técnica](#documentación-técnica)

---

## 🔧 Requisitos Previos

Asegúrate de tener instalado en tu sistema:

- **PHP 8.2+** → [Descargar](https://www.php.net/downloads)
- **Composer** → [Descargar](https://getcomposer.org/download/)
- **Node.js v18+** → [Descargar](https://nodejs.org/)
- **PostgreSQL 12+** → [Descargar](https://www.postgresql.org/download/)
- **Git** → [Descargar](https://git-scm.com/)

**Verificar instalaciones:**

```bash
php --version
composer --version
node --version
npm --version
psql --version
```

---

## Instalación Paso a Paso

### 1️⃣ **Clonar el Repositorio**

```bash
git clone https://github.com/SidTey/habilprofucsc.git
cd habilprofucsc
```

### 2️⃣ **Instalar Dependencias de PHP**

```bash
composer install
```

### 3️⃣ **Instalar Dependencias de Node.js**

```bash
npm install
```

### 4️⃣ **Crear el Archivo `.env`** ⚠️ **IMPORTANTE**

Crea un nuevo archivo `.env` en la raíz del proyecto. 

**Crear manualmente**
1. En la raíz del proyecto, crea un archivo nuevo llamado `.env`
3. Pega el contenido en tu archivo `.env`

**⚠️ IMPORTANTE:** El archivo `.env` es **PERSONAL** y **ÚNICO** para tu máquina. 

**NUNCA** commits el `.env` al repositorio (ya está en `.gitignore`). 

Este archivo contiene **credenciales confidenciales** que no deben ser compartidas.

### 5️⃣ **Configurar el `.env`** 

Abre el archivo `.env` en tu editor y **copia y pega todo lo siguiente**, luego personaliza los valores marcados:

```env
APP_NAME=Habilprof
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=usuario@example.com
MAIL_PASSWORD=tu_contraseña_mail
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="habilprof@ucsc.cl"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1

MIX_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
MIX_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

# ============ BASE DE DATOS LOCAL (habilprof_ucsc) ============
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=habilprof_ucsc
DB_USERNAME=postgres
DB_PASSWORD=TU_CONTRASEÑA_POSTGRES_AQUI
DB_SCHEMA=public

# ============ BASE DE DATOS FANTASMA (Sistemas_UCSC_ghost) ============
DB_FANTASMA_CONNECTION=pgsql
DB_FANTASMA_HOST=127.0.0.1
DB_FANTASMA_PORT=5432
DB_FANTASMA_DATABASE=sistemas_ucsc_ghost
DB_FANTASMA_USERNAME=postgres
DB_FANTASMA_PASSWORD=TU_CONTRASEÑA_POSTGRES_AQUI

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_DRIVER=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database
CACHE_PREFIX=
MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"
```

**⚠️ Valores que DEBES cambiar personalizado:**

| Variable | Descripción | Ejemplo |
|----------|-------------|---------|
| `DB_PASSWORD` | Tu contraseña de PostgreSQL local | `micontraseña123` |
| `DB_FANTASMA_PASSWORD` | Tu contraseña de PostgreSQL local | `micontraseña123` |
| `APP_KEY` | Se genera automáticamente en paso 6 | (No modificar) |

### 6️⃣ **Generar Application Key**

```bash
php artisan key:generate
```

### 7️⃣ **Restaurar la Base de Datos PostgreSQL**

**Ejecutar el siguiente comando en la terminal**

```bash
php artisan db:force-restore --force
```

### 8️⃣ **Ejecutar Migraciones**

```bash
php artisan migrate
```

Este comando crea todas las tablas necesarias en la BD para que funcione correctamente el proyecto.


### 9️⃣ **Compilar Assets (CSS/JS)**

```bash
npm run build
```
### 🔟 **Restaurar base de datos fantasma (Simulacion de sistemas UCSC)**

Debe crear una base de datos llamada "sistemas_ucsc_ghost" en alguna herramienta grafica y de manejo de datos para PostgreSQL (DBeaver o PgAdmin), una vez creada debe hacer un restore del backup que esta en habilprof\database\backup\Sistemas_UCSC_ghost.backup

---

## Ejecución del Proyecto


Con esto se inician **automáticamente**:
- ✅ Servidor PHP (puerto 8000)
- ✅ Servidor Vite (puerto 5173) 
- ✅ Sincronización UCSC cada 60 segundos

```bash
npm run dev:iniciar
```

Luego abre tu navegador en: **http://localhost:8000**

--- 

# ⚠️IMPORTANTE: USUARIO Y CONTRASEÑA DE ADMINISTRADOR
 **RUT: 12345678**

 **CONTRASEÑA: administrador**

## 📋 Comandos Útiles

### **Artisan (PHP/Laravel)**

```bash
# Migrar BD
php artisan migrate

# Revertir última migración
php artisan migrate:rollback

# Resetear BD completamente
php artisan migrate:refresh

# Ver rutas disponibles
php artisan route:list

# Sincronizar datos UCSC
php artisan sync:ucsc

# Ver logs de sincronización
tail -f storage/logs/sync_ucsc.log
```

### **NPM (Node.js)**

```bash
# Compilar assets en producción
npm run build

# Compilar assets en desarrollo con watch
npm run dev

# Ejecutar linter
npm run lint

# Iniciar proyecto completo
npm run dev:iniciar
```

### **Git**

```bash
# Ver commits personales
git log --author="TU_NOMBRE" --oneline

# Crear nueva rama
git checkout -b nombre-rama

# Hacer commit
git commit -m "Descripción del cambio"

# Subir cambios
git push origin nombre-rama
```

---

## 📁 Estructura del Proyecto

```
habilprofucsc/
├── app/
│   ├── Console/
│   │   └── Commands/
│   │       └── CargarDatosSistemasUCSC.php    # Comando de sincronización
│   ├── Http/
│   │   ├── Controllers/                       # Controladores Laravel
│   │   └── Middleware/                        # Middleware personalizado
│   ├── Models/                                # Modelos Eloquent
│   │   ├── HabilitacionProfesional.php       # Modelo principal
│   │   ├── Alumno.php
│   │   ├── Profesor.php
│   │   └── ...
│   └── Services/
│       └── UcscApiService.php                # Servicios UCSC
│
├── resources/
│   ├── css/                                   # Estilos CSS
│   ├── js/
│   │   ├── app.js
│   │   ├── app.jsx                           # Componentes React
│   │   └── components/                        # Componentes React
│   └── views/                                # Vistas Blade
│       ├── dashboard.blade.php
│       └── habilitacion/
│
├── routes/
│   ├── web.php                               # Rutas web
│   ├── api.php                               # Rutas API
│   └── console.php
│
├── database/
│   ├── migrations/                           # Migraciones BD
│   ├── seeders/                              # Seeders (datos iniciales)
│   └── backup/                               # Backups de BD
│
├── storage/
│   └── logs/
│       └── sync_ucsc.log                     # Log de sincronización
│
├── .env                                      # ⚠️ CONFIDENCIAL - NO COMMITEAR
├── .env.example                              # Ejemplo de configuración
├── composer.json                             # Dependencias PHP
├── package.json                              # Dependencias Node.js
└── vite.config.js                            # Configuración Vite

```

---

## 🔐 Configuración de Seguridad

### **Nunca Commits:**
- ✗ `.env` (archivo de configuración personal)
- ✗ `storage/logs/` (archivos de log)
- ✗ `node_modules/` y `vendor/` (dependencias)

### **Variables de Entorno Críticas:**
- `APP_KEY` - Generada automáticamente (NO modificar manualmente)
- `DB_PASSWORD` - Tu contraseña postgres (CONFIDENCIAL)
- `DB_FANTASMA_PASSWORD` - Contraseña UCSC (CONFIDENCIAL)

---

## 📊 Características Principales

### ✅ **Funcionalidades Implementadas**

1. **Carga de datos desde sistemas UCSC** 
2. **Ingreso de habilitaciones profesionales** 
3. **Eliminacion y actualizacion de habilitaciones** 
4. **Listado semestral** 
5. **Listado historico** 
6. **Login** - Endpoints para operaciones CRUD
7. **Modulos fantasmas (Simulan sistemas UCSC)** - Nomina de alumnos, profesores y Notas en linea

---

## 🐛 Solución de Problemas

### **Error: "No se puede conectar a PostgreSQL"**

```bash
# Verificar que PostgreSQL esté corriendo
psql -U postgres

# Si no funciona, reinicia el servicio PostgreSQL
```

---

## 📞 Contacto y Soporte

- **Repositorio:** https://github.com/SidTey/habilprofucsc
- **Branch Principal:** `main`
- **Branch de Desarrollo:** `Nico-Alvarado`
- **Contribuidores:** SHMEBUU, SidTey, Currutiad


**Última actualización:** 21 de Noviembre de 2025

# **HABILPROF** 
