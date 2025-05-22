# PWCI

Miguel Angel Reyes Flores 2007721
Felix Maximiliano Coronado Ruiz 1957675

Funcionabilidades:

1.- likes: En cada publicacion esta disponible el boton de likes, esto puede servir para que cada persona vea el alcance y exito de su publicacion

2.- Guardar publicaciones: en cada publicacion hay un boton disponible para guardarlas, las publicaciones guardadas estaran disponibles en el apartado de Guardados

3.- Personalización de perfil: Al entrar en tu perfil podras modificar tanto como tu foto de perfil, el banner de perfil 

4.- Comentarios: en cada publicacion al presionar el boton de comentarios, entraras al apartado del post donde ademas de ver mas a detalle la publicacion podras comentar y ver los comentarios de la publicacion

5.- Reposteados: los resposteados son una forma de poder compartir tus publicaciones favoritas de otros usuarios haciendo que estos se muestren en tu perfil. (tambien puedes ver los reposteados de los demas usuarios si entrar a sus perfiles desde el buscador de la derecha) 



para hacer funcionar el codigo necesitas correr primero todo el codigo de ProyectoZ_P8.sql y ver que todas las tablas y funciones, views y triggers esten correctamente iniciados al igual deberas cambiar los deatos de config.php para que la base de datos se pueda conectar correctamente a nuestro proyecto


```
con MYsql iniciado deberas correr el siguiente codigo en la terminal de visual:

php -S localhost:8000 -t public
```

Validaciones:

Inicio de sesion:

Nombre de usuario:
    No puede estar vacío.
    Solo permite letras, números y espacios.
Correo electrónico:
    No puede estar vacío.
    Debe tener formato válido de email.
Contraseña:
    No puede estar vacía.
    Mínimo 8 caracteres.
    Debe contener al menos una mayúscula, un número y un carácter especial (!@#$%^&*).
Errores de autenticación:
    Se muestran mensajes si el correo o la contraseña son incorrectos.

Publicaciones:

Contenido:
    No puede estar vacío si no se sube un archivo.
    No puede exceder los 100 caracteres.
Archivo multimedia:
    Tipos permitidos: jpg, png, gif, mp4, webm, ogg.
    Tamaño máximo: 50MB.
    Se valida que el archivo sea subido correctamente y que sea un archivo válido.
Errores:
    Si hay errores, se almacenan en la sesión y se redirige al usuario.

Comentarios:
    No puede estar vacío si no se adjunta archivo.
    No puede exceder los 100 caracteres.
Archivo multimedia:
    Mismos tipos y tamaño que en publicaciones.
ID de publicación padre:
    Se valida que exista y sea válido.

Guardar:

Autenticación:
    Solo usuarios autenticados pueden guardar publicaciones.
ID de publicación:
    Se valida que el ID de la publicación sea válido.


Mensajes:

Autenticación:
    Solo usuarios autenticados pueden enviar mensajes o crear chats.
Contenido del mensaje:
    No puede estar vacío.
ID de chat/destinatario:
    Se valida que el ID sea válido.
    No se permite iniciar chat consigo mismo.

Backend (validator):

Validación de archivos:
    Checa tipo MIME, tamaño y si el archivo fue subido correctamente.

Middleware 
en todas las paginas exeptuando la de iniciar sesion se pide que se inicie sesion para entrar, de lo contrario se redirige a la pagina de iniciar sesion

la ventana de admin pide que seas administrador para poder ingresar a la pagina, de lo contrario se redirigira a la pagina home del proyecto