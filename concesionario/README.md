# Sistema de gestión para concesionario de autos

Trabajo Práctico Final · Informática 4.º año · Instituto Sagrado Corazón de Almagro

## Instalación (XAMPP)
1. Copiar la carpeta `concesionario` dentro de `C:\xampp\htdocs\`.
2. Abrir XAMPP e iniciar **Apache** y **MySQL**.
3. Entrar a `http://localhost/phpmyadmin`, ir a **Importar** y subir `sql/concesionario.sql`.
4. Si tu MySQL tiene otra contraseña, editar `conexion.php`.
5. Abrir `http://localhost/concesionario/login.php`.

## Usuario administrador de prueba
- Correo: `admin@concesionario.com`
- Contraseña: `Admin1234`

Los usuarios que se registran reciben siempre el rol `usuario`.

## Archivos
| Archivo | Función |
|---|---|
| conexion.php | Conexión PDO |
| funciones.php | Sesión, escapado, CSRF, permisos, validación y subida de imágenes |
| encabezado.php / pie.php | Estructura común de las páginas |
| registro.php / login.php / cerrar_sesion.php | Registro, inicio y cierre de sesión |
| panel.php | Panel personal y catálogo |
| admin_autos.php | Tabla de autos con búsqueda (admin) |
| crear_auto.php / editar_auto.php | Alta y edición con foto (admin) |
| archivar_auto.php / autos_archivados.php / reactivar_auto.php | Ciclo de archivado (admin) |
| comprar_auto.php | Compra de un auto (usuario logueado, POST + CSRF + transacción) |
| admin_compras.php | Listado de todas las compras (admin) |
| admin_usuarios.php | Búsqueda, archivado y reactivación de usuarios (admin) |
| sql/concesionario.sql | Estructura de la base de datos |
| uploads/autos/ | Fotos subidas (con `.htaccess` en `uploads/`) |

## Si ya habías importado la base antes
Ejecutar `sql/agregar_compras.sql` en phpMyAdmin para crear la tabla `compras`.

## Pruebas sugeridas
1. Registrar un usuario y comprobar que no puede entrar a `admin_autos.php` (error 403).
2. Iniciar sesión como admin, cargar un auto con foto y buscarlo.
3. Editar el auto e intentar repetir una patente.
4. Archivar el auto, verlo en archivados y reactivarlo.
5. Con un usuario común, comprar un auto disponible: pasa a `vendido` y aparece en "Mis compras" y en el panel admin (Compras).
6. Subir un archivo `.php` renombrado como `.jpg` (debe rechazarlo).

## Medidas de seguridad
password_hash / password_verify · PDO con consultas preparadas · sesiones con cookie HttpOnly y SameSite · session_regenerate_id al ingresar · control de rol en el servidor · htmlspecialchars en toda la salida · validación en el servidor · token CSRF y POST en acciones que modifican datos · borrado lógico · subida de imágenes con finfo, límite de 2 MB y nombre aleatorio · errores sin detalles técnicos.
