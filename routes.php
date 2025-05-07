<?php

$router->get('/', 'controllers/inicioSesion.php');

$router->get('/register', 'controllers/registration/create.php')->only('guest');
$router->post('/register', 'controllers/registration/store.php');

// Rutas para la API
$router->post('/api', 'api.php');
$router->get('/api', 'api.php');

$router->get('/Z', 'controllers/inicioSesion.php');
$router->get('/inicio', 'controllers/home.php')->only('auth');
$router->get('/guardados', 'controllers/guardados.php')->only('auth');
$router->get('/mensajes', 'controllers/mensajes.php')->only('auth');
$router->get('/perfil/{id}', 'controllers/perfil.php')->only('auth');
$router->get('/admin', 'controllers/admin.php')->only('auth')->only('admin');
$router->get('/logout', 'controllers/logout.php')->only('auth');
$router->get('/post/{id}', 'controllers/post.php')->only('auth');


$router->post('/crear-publicacion', 'controllers/publicacion/crear.php')->only('auth');
$router->post('/like', 'controllers/like.php')->only('auth');
$router->post('/guardar', 'controllers/guardar.php')->only('auth');
$router->post('/repost/toggle', 'controllers/repost/toggle.php')->only('auth');

$router->post('/buscar-usuario', 'controllers/buscarUsuario.php')->only('auth');

$router->post('/crear-chat', 'controllers/chat/crear.php')->only('auth');
$router->post('/cargar-chat', 'controllers/chat/cargar.php')->only('auth');

$router->post('/mensaje/cargar', 'controllers/mensaje/cargar.php')->only('auth');
$router->post('/mensaje/enviar', 'controllers/mensaje/enviar.php')->only('auth');

$router->post('/post/{id}/reply', 'controllers/publicacion/reply.php')->only('auth'); 
