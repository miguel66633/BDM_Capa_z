<?php include base_path('controllers/busqueda.php'); ?>

<aside id="lateral">
    <div class="search-bar">
        <img src="/Resources/images/buscar.svg" alt="Buscar" class="search-icon">

        <!-- Formulario de búsqueda -->
        <form method="GET" action="">
            <input type="text" name="term" placeholder="Buscar" class="search-input" value="<?= isset($_GET['term']) ? htmlspecialchars($_GET['term']) : ''; ?>">
            <button type="submit" style="display: none;">Buscar</button>
        </form>
    </div>

    <!-- Lista de usuarios -->
    <div id="user-list" class="user-list">
        <div class="list-header">
            <h3>Usuarios</h3>
            <button class="clear-all">Borrar todo</button>
        </div>
        <ul>
            <?php if (!empty($usuarios)): ?>
                <?php foreach ($usuarios as $usuario): ?>
                    <li>
                        <!-- Mostrar imagen de usuario -->
                        <?php if ($usuario['ImagenPerfil']): ?>
                            <img src="data:image/jpeg;base64,<?= base64_encode($usuario['ImagenPerfil']); ?>" alt="<?= htmlspecialchars($usuario['NombreUsuario']); ?>" class="user-img">
                        <?php else: ?>
                            <img src="/Resources/images/perfilPre.jpg" alt="Imagen por defecto" class="user-img">
                        <?php endif; ?>

                        <?= htmlspecialchars($usuario['NombreUsuario']); ?>
                        <button class="remove-user">X</button>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>No se encontraron usuarios.</li>
            <?php endif; ?>
        </ul>
    </div>
</aside>