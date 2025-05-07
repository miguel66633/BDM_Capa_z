<?php
include base_path('controllers/busqueda.php');
?>

<aside id="lateral">
    <div class="search-container">
        <div class="search-bar">
            <img src="/Resources/images/buscar.svg" alt="Buscar" class="search-icon">
            <form method="GET" action="" id="full-search-form">
                <input type="text" name="term" placeholder="Buscar" class="search-input" id="search-input-lateral" autocomplete="off" value="<?= isset($_GET['term']) ? htmlspecialchars($_GET['term']) : ''; ?>">
                <button type="submit" style="display: none;">Buscar</button>
            </form>
        </div>
    </div>

    <?php
    // *** NUEVO: Condición para mostrar la lista solo si hay un término de búsqueda ***
    $searchTermDisplay = $_GET['term'] ?? null;
    if ($searchTermDisplay !== null && trim($searchTermDisplay) !== ''):
    ?>
        <!-- Lista de usuarios (Solo se muestra si hay búsqueda activa) -->
        <div id="user-list" class="user-list">
            <div class="list-header">
                <h3>Resultados para "<?= htmlspecialchars($searchTermDisplay) ?>"</h3>
            </div>
            <ul>
                <?php if (isset($usuarios) && !empty($usuarios)): ?>
                    <?php foreach ($usuarios as $usuario): ?>
                        <li>
                    <a href="/perfil/<?= $usuario['UsuarioID']; ?>" class="user-list-link">
                        <?php
                            $imgSrc = formatarImagen($usuario['ImagenPerfil'] ?? null, '/Resources/images/perfilPre.jpg');
                        ?>
                        <img src="<?= $imgSrc; ?>" alt="<?= htmlspecialchars($usuario['NombreUsuario']); ?>" class="user-img">
                        <span class="user-list-name"><?= htmlspecialchars($usuario['NombreUsuario']); ?></span>
                    </a>
                </li>
                    <?php endforeach; ?>
                <?php else: // Si hay término de búsqueda pero no se encontraron usuarios ?>
                     <li>No se encontraron usuarios.</li>
                <?php endif; ?>
            </ul>
        </div>
    <?php endif; // Fin de la condición if ($searchTermDisplay) ?>

</aside>