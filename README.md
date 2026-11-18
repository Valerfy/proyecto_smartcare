# SmartCare CNS

**Sistema de Gestión de Citas Médicas y Atención Médica en la Caja Nacional de Salud (CNS)**

Aplicación web desarrollada con **PHP** y **MySQL** para la gestión de citas médicas, con paneles diferenciados por rol y un chatbot opcional con inteligencia artificial.

---

## Tabla de contenidos

- [Características](#características)
- [Tecnologías](#tecnologías)
- [Requisitos previos](#requisitos-previos)
- [Instalación](#instalación)
- [Credenciales de prueba](#credenciales-de-prueba)
- [Chatbot con IA (opcional)](#chatbot-con-ia-opcional)
- [Pruebas unitarias](#pruebas-unitarias)
- [Estructura relevante del proyecto](#estructura-relevante-del-proyecto)
- [Integrantes](#integrantes)

---

## Características

- Inicio de sesión con acceso diferenciado por rol: **Paciente, Médico, Administrador y Recepcionista**.
- Registro de nuevos pacientes con validación de datos (nombre, apellido, CI, celular, correo, fecha de nacimiento, edad mínima de 18 años, ocupación, dirección, matrícula, contraseña, términos y foto de perfil).
- Gestión de citas médicas y atención médica: agendar, editar, atender y registrar diagnósticos.
- Botón **Volver** propio del sitio en todos los paneles: recuerda las últimas páginas visitadas y regresa a la anterior sin usar el botón del navegador.
- **Reportes clínicos del médico** con filtros por rango de fechas y por paciente, y exportación a PDF.
- **Reporte de fichas de recepción** con filtros por nombre o CI, médico, rango de fechas y tipo de paciente (nuevo o antiguo). El PDF respeta los filtros y solo se abre con sesión de recepcionista.
- Base de datos normalizada (3FN) con 17 tablas, llaves foráneas y datos de prueba listos para usar.
- Contraseñas almacenadas cifradas con `password_hash`.
- Chatbot con IA local (opcional) mediante Ollama.

## Tecnologías

| Componente | Tecnología |
|---|---|
| Backend | PHP 8.2.12 (PDO, cURL, mbstring) |
| Frontend | HTML5, CSS3, JavaScript, Bootstrap 5.3.3, Bootstrap Icons 1.11.3 y Chart.js 4.4.3 |
| Control de versiones | Git y GitHub |
| Base de datos | MySQL (MariaDB 10.4.32) administrada con phpMyAdmin 5.2.1 |
| Servidor local | XAMPP 8.2.12 para Windows (Apache 2.4 + MariaDB) |
| Chatbot (opcional) | Ollama + modelo `qwen3:4b` |

## Requisitos previos

### Sistema operativo y hardware

| Requisito | Especificación |
|---|---|
| Sistema operativo | Windows 10 versión 22H2 o superior, o Windows 11, ambos de 64 bits (x64) |
| Procesador | 64 bits, mínimo 2 núcleos (para el chatbot se recomiendan 4 núcleos o más) |
| Memoria RAM | Mínimo 4 GB; 8 GB recomendados; 16 GB recomendados si se usa el chatbot |
| Espacio en disco | Mínimo 2 GB libres para XAMPP y el proyecto; 8 GB adicionales si se instala el chatbot |
| Conexión a internet | Necesaria para cargar Bootstrap, Bootstrap Icons, Chart.js y Google Fonts desde CDN, y para clonar el repositorio |
| Permisos | Usuario con permisos para instalar programas y escribir en `C:\xampp\htdocs` |

### Software obligatorio

| Software | Versión exacta | Detalle |
|---|---|---|
| XAMPP para Windows | 8.2.12 (x64) | Incluye PHP 8.2.12, MariaDB 10.4.32, Apache 2.4 y phpMyAdmin 5.2.1. Instalar en `C:\xampp`. Descarga: [apachefriends.org](https://www.apachefriends.org/) |
| Navegador web | Google Chrome 120 o superior (recomendado), Microsoft Edge 120 o superior, o Mozilla Firefox 120 o superior | Con JavaScript habilitado |

Componentes de XAMPP que deben estar marcados durante la instalación: **Apache**, **MySQL** y **phpMyAdmin**.

Extensiones de PHP requeridas (activas por defecto en XAMPP 8.2.12): `pdo_mysql`, `curl`, `mbstring`, `json`, `fileinfo`.

Puertos que deben estar libres en el equipo:

| Servicio | Puerto |
|---|---|
| Apache (HTTP) | 80 |
| MySQL / MariaDB | 3306 |

### Software opcional

| Software | Versión exacta | Cuándo se necesita |
|---|---|---|
| Git para Windows | 2.56.0 (64-bit, instalador x64 estándar) | Solo si se clona el repositorio. Descarga: [git-scm.com/install/windows](https://git-scm.com/install/windows). Requiere Windows 10 o superior |
| Ollama para Windows | Última versión estable, instalador `OllamaSetup.exe` | Solo para el chatbot. Requiere Windows 10 22H2 o superior y 4 GB libres para el programa. Descarga: [ollama.com](https://ollama.com/) |
| Modelo `qwen3:4b` | Etiqueta `qwen3:4b` | Solo para el chatbot. Ocupa aproximadamente 2.5 GB. Ollama debe escuchar en `http://localhost:11434` |
| Controlador NVIDIA | 551.61 o superior | Solo si el equipo tiene tarjeta gráfica NVIDIA y se desea aceleración por GPU en el chatbot |

### Verificación de versiones

Abre **PowerShell** o **CMD** y ejecuta:

```powershell
C:\xampp\php\php.exe -v
C:\xampp\php\php.exe -m
git --version
ollama --version
```

Resultados esperados:

- `php -v` muestra `PHP 8.2.12`.
- `php -m` lista `curl`, `mbstring`, `PDO` y `pdo_mysql`.
- `git --version` muestra `git version 2.56.0.windows.x`.
- `ollama --version` muestra la versión instalada.

## Instalación

### 1. Obtener el proyecto

Abre una terminal (CMD, PowerShell o Git Bash) y ejecuta:

```bash
cd C:\xampp\htdocs
git clone https://github.com/Valerfy/proyecto_smartcare.git
cd proyecto_smartcare
```

Para actualizar tu copia local más adelante:

```bash
git pull origin main
```

> Si prefieres descargar el repositorio como ZIP, extráelo y **renombra la carpeta a exactamente `proyecto_smartcare`** (GitHub la nombra `proyecto_smartcare-main`; si no la renombras, las rutas de abajo no funcionarán).

### 2. Ubicar el proyecto en XAMPP

La carpeta `proyecto_smartcare` debe quedar dentro de:

```
C:\xampp\htdocs\
```

Si clonaste con los comandos del paso anterior, ya está en el lugar correcto. Si descargaste el ZIP, muévela manualmente a esa ruta.

### 3. Iniciar los servicios

Abre el **Panel de Control de XAMPP** (`C:\xampp\xampp-control.exe`) como administrador e inicia **Apache** y **MySQL**. Ambos módulos deben mostrarse en verde.

### 4. Crear la base de datos

1. Abre [http://localhost/phpmyadmin/](http://localhost/phpmyadmin/) en el navegador.
2. Ve a la pestaña **Importar** y selecciona el archivo:

   ```
   proyecto_smartcare/database/cns_sistema_v2.sql
   ```

Este archivo crea la base de datos `cns_sistema_v2` y carga los datos de prueba necesarios.

### 5. Verificar la conexión

Revisa que las credenciales de conexión sean correctas en:

```
proyecto_smartcare/config/conexion.php
```

Los valores por defecto de XAMPP son: servidor `localhost`, base de datos `cns_sistema_v2`, usuario `root` y contraseña vacía.

### 6. Abrir el sistema

Ingresa desde el navegador a:

[http://localhost/proyecto_smartcare/public/](http://localhost/proyecto_smartcare/public/)

Se mostrará la pantalla de inicio de sesión (Login).

## Credenciales de prueba

Inicia sesión con cada cuenta para comprobar los distintos paneles y sus funcionalidades. Los datos corresponden a los registros de prueba incluidos en la base de datos importada.

| Rol | Correo electrónico | Contraseña |
|---|---|---|
| Paciente | `valerofernandezmatias@gmail.com` | `1234` |
| Médico | `anuar.vargas@cns.org.bo` | `1234` |
| Administrador | `matias.valero@cns.com` | `1234` |
| Recepcionista | `sofia.loayza@cns.rec.bo` | `1234` |

> Estas credenciales son solo para pruebas en entorno local.

## Chatbot con IA (opcional)

1. Instala [Ollama](https://ollama.com/) para Windows con el instalador `OllamaSetup.exe`.
2. Abre PowerShell y descarga el modelo:

   ```bash
   ollama pull qwen3:4b
   ```
3. Comprueba que el modelo quedó instalado:

   ```bash
   ollama list
   ```
4. Asegúrate de que el servicio de Ollama esté en ejecución y responda en `http://localhost:11434`.

> **Nota:** el resto del sistema funciona con normalidad sin instalar ni configurar el chatbot.

## Pruebas unitarias

Con el proyecto dentro de `C:\xampp\htdocs\proyecto_smartcare`, abre una terminal en esa carpeta y ejecuta:

```bash
C:\xampp\php\php.exe pruebas_unitarias/ejecutar.php
```

Funciones evaluadas: `HistorialModel::separarLista`, `MedicoModel::generarHoras`, `ReporteRecepcionModel::filtrosDesdeGet`, `ChatbotController::detectarIntent` y `ChatbotController::limpiarRespuesta`.

## Estructura relevante del proyecto

```
proyecto_smartcare/
├── assets/
├── config/
│   └── conexion.php
├── controllers/
├── database/
│   └── cns_sistema_v2.sql
├── models/
├── public/
├── pruebas_unitarias/
├── views/
│   ├── admin/
│   ├── auth/
│   ├── components/
│   │   └── volver.php
│   ├── helper/
│   ├── medico/
│   └── paciente/
└── README.md
```

| Carpeta | Contenido |
|---|---|
| `assets/` | CSS, JavaScript e imágenes (fotos de perfil) |
| `config/` | Conexión a la base de datos |
| `controllers/` | Lógica de cada rol (Auth, Paciente, Medico, Admin, Helper, Chatbot) |
| `database/` | Script de la base de datos y datos de prueba |
| `models/` | Acceso a datos (Usuario, Cita, Historial, Reportes) |
| `public/` | Punto de entrada: enrutador y Login |
| `pruebas_unitarias/` | Pruebas de funciones de modelos y controladores |
| `views/` | Vistas por rol: administrador, autenticación, recepcionista, médico y paciente |

## Integrantes

| Integrante | GitHub |
|---|---|
| Matías Valero Fernández | [@Valerfy](https://github.com/Valerfy) |
| Juan Anuar Vargas Claros | [@Anufox](https://github.com/Anufox) |
| Jhonatan Torrez Pocoma | [@3cuentajtp-debug](https://github.com/3cuentajtp-debug) |
