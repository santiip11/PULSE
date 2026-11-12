# PULSE - Sistema de Gestión de Usuarios

## Descripción del proyecto

PULSE es un proyecto web con temática musical que incluye un sistema de registro, inicio de sesión y administración de usuarios.

Fue desarrollado como Trabajo Práctico Final de Informática de 4.º año.

## Tecnologías utilizadas

- HTML5
- CSS3
- PHP 8 o superior
- MySQL
- PDO
- XAMPP
- Visual Studio Code
- phpMyAdmin

## Funciones del sistema

### Registro
- Crear una cuenta.
- Ingresar nombre, apellido, DNI, correo electrónico y contraseña.
- Validar los datos ingresados.
- Evitar registros con DNI o correo repetidos.

### Inicio de sesión
- Ingresar con correo y contraseña.
- Verificar las credenciales.
- Crear una sesión para el usuario.

### Panel de usuario
- Acceder al panel personal.
- Cerrar sesión.

### Panel de administrador
- Consultar usuarios registrados.
- Buscar usuarios.
- Editar sus datos.
- Archivar cuentas.
- Consultar usuarios archivados.
- Reactivar cuentas.

## Base de datos

El sistema utiliza una base de datos MySQL llamada `sistema_usuarios`.

La tabla principal se llama `usuarios` y almacena:

- ID del usuario.
- Nombre y apellido.
- DNI.
- Correo electrónico.
- Contraseña protegida.
- Rol.
- Fecha de creación.
- Fecha de archivado.

## Seguridad

Para proteger el sistema se utilizaron:

- `password_hash()` para proteger las contraseñas.
- `password_verify()` para verificar las contraseñas.
- Consultas preparadas mediante PDO.
- Control de sesiones.
- Validación de permisos de administrador.
- `htmlspecialchars()` para mostrar datos de forma segura.

## Instalación desde otra pc

1. Instalar XAMPP.
2. Copiar la carpeta del proyecto dentro de `htdocs`.
3. Iniciar Apache y MySQL desde XAMPP.
4. Abrir phpMyAdmin.
5. Crear e importar la base de datos utilizando el archivo SQL del proyecto.
6. Revisar los datos de conexión en `conexion.php`.
7. Abrir el proyecto desde el navegador:

`http://localhost/PULSE/`


## Dificultades y soluciones

Mientras armaba el proyecto tuve varios problemas que fui solucionando a medida que avanzaba.

Una de las primeras dificultades fue lograr que PHP se conectara correctamente con la base de datos. Al principio aparecían errores de conexión. Para solucionarlo revisé los datos de conexion.php, como el nombre de la base de datos y los datos del servidor, hasta conseguir que la conexión funcionara.

También tuve problemas con algunas funciones de administración de usuarios. Al editar cuentas, los cambios no siempre se guardaban correctamente. Para solucionarlo revisé cómo se recibía el ID del usuario y cómo se ejecutaba la consulta para actualizar sus datos.

Con las funciones de archivar y reactivar usuarios también aparecieron errores. En algunos casos los botones llevaban a páginas que no existían o no recibían correctamente los datos. Para solucionarlo revisé los enlaces, los archivos PHP y la información que se enviaba desde cada formulario.

Otra dificultad fue hacer que solamente el administrador pudiera acceder a determinadas páginas. Para solucionarlo agregué controles al inicio de los archivos, que revisan si existe una sesión iniciada y si el usuario tiene el rol de administrador. De esta manera, un usuario común no puede acceder a esas funciones.

Por último, también tuve que dedicar tiempo al diseño de la página, porque algunos elementos no se veían como quería. Para solucionarlo fui modificando el archivo CSS, ajustando los colores, los tamaños, los espacios y los bordes de los formularios y las tablas hasta conseguir un diseño que me gustara más.

En general, el proyecto me llevó muchas pruebas y correcciones. Esto me permitió entender mejor cómo funciona PHP, cómo se conecta con MySQL y cómo se administran los usuarios.


## creador

Santino Ipar

Trabajo Práctico Final - Informática 4.A
