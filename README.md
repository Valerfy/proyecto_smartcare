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
- [Estructura relevante del proyecto](#estructura-relevante-del-proyecto)
- [Integrantes](#integrantes)

---

## Características

- Inicio de sesión con acceso diferenciado por rol: **Paciente, Médico, Administrador y Recepcionista**.
- Gestión de citas médicas y atención médica.
- Base de datos con datos de prueba listos para usar.
- Chatbot con IA local (opcional) mediante Ollama.

## Tecnologías

| Componente | Tecnología |
|---|---|
| Backend | PHP |
| Base de datos | MySQL |
| Servidor local | XAMPP (Apache + MySQL) |
| Chatbot (opcional) | Ollama + modelo `qwen3:4b` |

## Requisitos previos

- [XAMPP](https://www.apachefriends.org/) instalado (incluye Apache, MySQL y phpMyAdmin).
- Un navegador web actualizado.
- [Git](https://git-scm.com/) (solo si clonas el repositorio).
- *(Opcional)* [Ollama](https://ollama.com/) para el chatbot.

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

Abre el **Panel de Control de XAMPP** e inicia **Apache** y **MySQL**.

### 4. Crear la base de datos

1. Abre [http://localhost/phpmyadmin/](http://localhost/phpmyadmin/) en el navegador.
2. Ve a la pestaña **Importar** y selecciona el archivo:

   ```
   proyecto_smartcare/database/cns_sistema_v2.sql
   ```

Este archivo crea la base de datos y carga los datos de prueba necesarios.

### 5. Verificar la conexión

Revisa que las credenciales de conexión sean correctas en:

```
proyecto_smartcare/config/conexion.php
```

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

1. Instala [Ollama](https://ollama.com/).
2. Descarga el modelo:

   ```bash
   ollama pull qwen3:4b
   ```
3. Asegúrate de que el servicio de Ollama esté en ejecución.

> **Nota:** el resto del sistema funciona con normalidad sin instalar ni configurar el chatbot.

## Estructura relevante del proyecto

```
proyecto_smartcare/
├── config/
│   └── conexion.php          # Conexión a la base de datos
├── database/
│   └── cns_sistema_v2.sql    # Script de la base de datos y datos de prueba
└── public/                   # Punto de entrada (Login)
```

## Integrantes

| Integrante | GitHub |
|---|---|
| Matías Valero Fernández | [@Valerfy](https://github.com/Valerfy) |
| Juan Anuar Vargas Claros | [@Anufox](https://github.com/Anufox) |
| Jhonatan Torrez Pocoma | @2cuentajtp-del |
