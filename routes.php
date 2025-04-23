<?php

// return [
//     '/' => 'controllers/index.php',
//     '/about' => 'controllers/about.php',
//     '/contact' => 'controllers/contact.php',
//     '/notes' => 'controllers/notes.php',    '/note' => 'controllers/note.php',
//     '/note/create' => 'controllers/note-create.php',
// ];


$router->get('/', 'controllers/inicioSesion.php');
$router->get('/about', 'controllers/about.php');
$router->get('/contact', 'controllers/contact.php');

$router->get('/notes', 'controllers/notes/index.php')->only('auth');
$router->get('/note', 'controllers/notes/show.php');
$router->delete('/note', 'controllers/notes/destroy.php');

$router->get('/note/edit', 'controllers/notes/edit.php');
$router->patch('/note', 'controllers/notes/update.php');

$router->get('/notes/create', 'controllers/notes/create.php');
$router->post('/notes', 'controllers/notes/store.php');

$router->get('/register', 'controllers/registration/create.php')->only('guest');
$router->post('/register', 'controllers/registration/store.php');

// Rutas para la API
$router->post('/api', 'api.php');
$router->get('/api', 'api.php');


$router->get('/Z', 'controllers/inicioSesion.php');
$router->get('/inicio', 'controllers/home.php');
$router->get('/guardados', 'controllers/guardados.php');
$router->get('/mensajes', 'controllers/mensajes.php');
$router->get('/perfil', 'controllers/perfil.php');
$router->get('/admin', 'controllers/admin.php');
$router->get('/logout', 'controllers/logout.php');



$router->post('/crear-publicacion', 'controllers/publicacion/crear.php');
$router->post('/like', 'controllers/like.php');
$router->post('/guardar', 'controllers/guardar.php');