## Sistema de Gestión de Citas Médicas y Atencion Medica en la CAJA NACIONAL DE SALUD (CNS)

Aplicación web desarrollada con PHP y MySQL para la gestión de citas médicas.

## Integrantes del proyecto

- **Integrante 1:** Matias Valero Fernandez — **Usuario de GitHub:** [@Valerfy](https://github.com/Valerfy)
- **Integrante 2:** Juan Anuar Vargas Claros — [@Anufox](https://github.com/Anufox)
- **Integrante 3:** Jhonatan Torrez Pocoma — [@usuario-github](https://github.com/usuario-github)

## Pasos para ejecutar el proyecto

1. Instalar y abrir XAMPP.
2. Copiar la carpeta del proyecto `smartcare` dentro de `C:\xampp\htdocs\`.
3. Iniciar los servicios Apache y MySQL desde el panel de XAMPP.
4. Abrir `http://localhost/phpmyadmin/` en el navegador.
5. Importar el archivo `database/cns_sistema_v2.sql` para crear la base de datos y cargar los datos de prueba.
6. Verificar las credenciales de conexión en `config/conexion.php`.
7. Abrir el sistema desde el navegador mediante la siguiente dirección:

   `http://localhost/smartcare/public/`

## Inicio de sesión y pruebas

Al ingresar a la dirección del sistema, se mostrará la pantalla de inicio de sesión (login).

Para probar el funcionamiento de la aplicación, iniciar sesión con las credenciales de prueba correspondientes a cada uno de los cuatro roles:

CORREOS Y CONTRASEÑAS DE CADA ROL PARA EL LOGIN:

- paciente: `valerofernandezmatias@gmail.com`
  contraseña: 1234

- medico: `anuar.vargas@cns.org.bo`
  contraseña: 1234

- Admin: `matias.valero@cns.com`
  contraseña: 1234

- recepcionista: `sofia.loayza@cns.rec.bo`
  contraseña: 1234

Las credenciales de cada rol deben obtenerse de los datos de prueba incluidos en la base de datos importada. Ingresar con cada cuenta para comprobar el acceso a los diferentes paneles y sus funcionalidades.

## Chatbot (opcional)

Para utilizar el chatbot con inteligencia artificial, instalar Ollama, descargar el modelo `qwen3:4b` y asegurarse de que el servicio esté ejecutándose.

El resto del sistema puede utilizarse sin el chatbot.

