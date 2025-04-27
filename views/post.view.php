<?php require base_path('views/partials/head.z.php'); ?>
<div class="container">
    <?php require base_path('views/partials/nav.z.php'); ?>

    <main id="contenido">
        <div class="scrollable-content">
            <div class="post-header">
                <!-- Botón para volver al inicio -->
                <button class="back-btn" onclick="window.location.href='/inicio'">
                    <img src="/Resources/images/atras.svg" alt="Atrás">
                </button>
                <h2>Post</h2>
            </div>

            <!-- Publicación Principal -->
            <div class="publicacion">
                <!-- Encabezado de la publicación -->
                <div class="publicacion-header">
                    <img 
                        src="<?php echo isset($publicacion['ImagenPerfil']) ? 'data:image/jpeg;base64,' . base64_encode($publicacion['ImagenPerfil']) : '/Resources/images/perfilpre.jpg'; ?>" 
                        alt="Foto de perfil de <?php echo htmlspecialchars($publicacion['NombreUsuario']); ?>" 
                        class="publicacion-profile-pic"
                    >
                    <div class="publicacion-info">
                        <span class="publicacion-username"><?php echo htmlspecialchars($publicacion['NombreUsuario']); ?></span>
                        <span class="publicacion-user-handle">
                            @<?php echo htmlspecialchars(strtolower(str_replace(' ', '', $publicacion['NombreUsuario']))); ?> • 
                            <?php 
                                // Formatear la fecha
                                try {
                                    $fecha = new DateTime($publicacion['FechaPublicacion']);
                                    echo $fecha->format('d M.'); // Ejemplo: 10 Feb.
                                } catch (Exception $e) {
                                    echo 'Fecha inválida';
                                }
                            ?>
                        </span>
                    </div>
                </div>
                <!-- Contenido de la publicación -->
                <div class="publicacion-contenido">
                    <p><?php echo htmlspecialchars($publicacion['ContenidoPublicacion']); ?></p>
                    
                    <!-- Mostrar imagen/multimedia si existe -->
                    <?php if (!empty($publicacion['TipoMultimedia'])): ?>
                        <div class="img">
                             <img src="data:image/jpeg;base64,<?php echo base64_encode($publicacion['TipoMultimedia']); ?>" alt="Imagen de la publicación" class="publicacion-imagen">
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Acciones de la publicación (Contadores estáticos por ahora) -->
                <div class="publicacion-acciones">
                    <div class="accion">
                      <!-- Botón Like -->
                      <button class="accion-btn like-btn" data-publicacion-id="<?php echo $publicacion['PublicacionID']; ?>">
                        <img 
                            src="/Resources/images/<?php echo $publicacion['YaDioLike'] ? 'likeP.svg' : 'like.svg'; ?>" 
                            class="accion-icon"
                            alt="Botón de like"
                        >
                      </button>
                      <!-- Contador Likes -->
                      <span class="accion-count" id="like-count-<?php echo $publicacion['PublicacionID']; ?>">
                          <?php echo $publicacion['LikesCount'] ?? 0; ?>
                      </span> 
                    </div>
                    <div class="accion">
                        <!-- Botón Repost (funcionalidad pendiente) -->
                        <button class="accion-btn repost-btn" data-publicacion-id="<?php echo $publicacion['PublicacionID']; ?>">
                          <img src="/Resources/images/repost.svg" class="accion-icon" alt="Repost">
                        </button>
                        <!-- Contador Reposts (si se implementa) -->
                        <span class="accion-count">
                            <?php // echo $publicacion['RepostsCount'] ?? 0; ?>
                            0 
                        </span> 
                      </div>
                    <div class="accion">
                      <!-- Botón Comentarios (solo visual, la sección está abajo) -->
                      <button class="accion-btn">
                        <img src="/Resources/images/comments.svg" class="accion-icon">
                      </button>
                      <!-- Contador Comentarios -->
                      <span class="accion-count">
                          <?php echo $publicacion['CommentsCount'] ?? 0; ?>
                      </span> 
                    </div>
                    <div class="accion">
                      <!-- Botón Guardar -->
                      <button class="accion-btn saved-btn" data-publicacion-id="<?php echo $publicacion['PublicacionID']; ?>">
                        <img 
                            src="/Resources/images/<?php echo $publicacion['YaGuardo'] ? 'guardados.svg' : 'saved.svg'; ?>" 
                            class="accion-icon"
                            alt="Botón de guardado"
                        >
                      </button>
                      <!-- Contador Guardados -->
                      <span class="accion-count" id="save-count-<?php echo $publicacion['PublicacionID']; ?>">
                          <?php echo $publicacion['SavesCount'] ?? 0; ?>
                      </span> 
                    </div>
                  </div>
            </div>

            <!-- Sección de Comentarios (Contenido estático por ahora) -->
            <div class="comentarios">
                <div class="post-header">
                    <h2>Comentarios</h2>
                </div>

                <!-- Formulario para comentar -->
                <div class="comentar">
                    <img 
                        src="<?php echo isset($_SESSION['user_img']) && !empty($_SESSION['user_img']) ? 'data:image/jpeg;base64,' . $_SESSION['user_img'] : '/Resources/images/perfilpre.jpg'; ?>" 
                        alt="Tu foto de perfil" 
                        class="comentar-profile-pic"
                    >
                    <div class="comentar-content">
                        <!-- ***** CAMBIO: action apunta a la nueva ruta ***** -->
                        <form action="/post/<?php echo $publicacion['PublicacionID']; ?>/reply" method="POST" enctype="multipart/form-data">
                            <!-- Campo oculto para el ID padre ya no es estrictamente necesario aquí, 
                                ya que el ID está en la URL de acción, pero no hace daño dejarlo si quieres -->
                            <input type="hidden" name="publicacion_padre_id" value="<?php echo $publicacion['PublicacionID']; ?>">

                            <!-- ***** CAMBIO: name del textarea coincide con el controlador reply.php ***** -->
                            <textarea name="contenido_comentario" class="comentar-textarea" placeholder="Postea tu respuesta" required></textarea>
                            
                            <div class="comentar-footer">
                                <div class="comentar-icons">
                                    <label for="commentImage" class="image-upload-label">
                                        <img src="/Resources/images/img.svg" alt="Subir imagen" />
                                    </label>
                                    <!-- ***** CAMBIO: name del input file coincide con el controlador reply.php ***** -->
                                    <input type="file" id="commentImage" name="imagen_comentario" accept="image/*" style="display: none;" />
                                </div>
                                <button type="submit" class="responder-btn">Responder</button>
                            </div>
                        </form>
                    </div>
                </div>

                  <!-- Lista de comentarios (Ejemplos estáticos) -->
                  <div id="lista-respuestas"> 
                    <!-- ***** CAMBIO: Usar la variable $respuestas ***** -->
                    <?php if (!empty($respuestas)): ?> 
                        <?php foreach ($respuestas as $respuesta): ?>
                            <div class="comentario"> <!-- Puedes mantener la clase CSS si quieres -->
                                <div class="comentario-header">
                                    <img 
                                        src="<?php echo isset($respuesta['ImagenPerfil']) ? 'data:image/jpeg;base64,' . base64_encode($respuesta['ImagenPerfil']) : '/Resources/images/perfilpre.jpg'; ?>" 
                                        alt="Foto de perfil de <?php echo htmlspecialchars($respuesta['NombreUsuario']); ?>" 
                                        class="publicacion-profile-pic"
                                    >
                                    <div class="comentario-info">
                                        <span class="comentario-username"><?php echo htmlspecialchars($respuesta['NombreUsuario']); ?></span>
                                        <span class="comentario-user-handle">
                                            @<?php echo htmlspecialchars(strtolower(str_replace(' ', '', $respuesta['NombreUsuario']))); ?> • 
                                            <?php 
                                                try {
                                                    // ***** CAMBIO: Usar FechaPublicacion de la respuesta *****
                                                    $fechaRespuesta = new DateTime($respuesta['FechaPublicacion']); 
                                                    echo $fechaRespuesta->format('d M.'); 
                                                } catch (Exception $e) {
                                                    echo 'Fecha inválida';
                                                }
                                            ?>
                                        </span>
                                    </div>
                                </div>
                                <!-- ***** CAMBIO: Usar ContenidoPublicacion de la respuesta ***** -->
                                <p><?php echo htmlspecialchars($respuesta['ContenidoPublicacion']); ?></p> 
                                <!-- ***** CAMBIO: Usar ImagenRespuesta ***** -->
                                <?php if (!empty($respuesta['ImagenRespuesta'])): ?> 
                                    <div class="img">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($respuesta['ImagenRespuesta']); ?>" alt="Imagen de la respuesta" class="publicacion-imagen">
                                    </div>
                                <?php endif; ?>
                                
                                <!-- ***** NUEVO: Acciones de la respuesta (copiado de la publicación principal) ***** -->
                                <div class="publicacion-acciones">
                                    <div class="accion">
                                        <button class="accion-btn like-btn" data-publicacion-id="<?php echo $respuesta['PublicacionID']; ?>">
                                            <!-- ***** CAMBIO: Imagen dinámica según YaDioLikeRespuesta ***** -->
                                            <img 
                                                src="/Resources/images/<?php echo $respuesta['YaDioLikeRespuesta'] ? 'likeP.svg' : 'like.svg'; ?>" 
                                                class="accion-icon"
                                                alt="Botón de like"
                                            >
                                        </button>
                                        <!-- ***** CAMBIO: Mostrar LikesCount ***** -->
                                        <span class="accion-count" id="like-count-<?php echo $respuesta['PublicacionID']; ?>">
                                            <?php echo $respuesta['LikesCount'] ?? 0; ?>
                                        </span> 
                                    </div>
                                    <div class="accion">
                                        <button class="accion-btn repost-btn" data-publicacion-id="<?php echo $respuesta['PublicacionID']; ?>">
                                            <img src="/Resources/images/repost.svg" class="accion-icon" alt="Repost">
                                        </button>
                                        <!-- ***** CAMBIO: Mostrar RepostsCount (si lo añadiste a la consulta) ***** -->
                                        <span class="accion-count">
                                            <?php // echo $respuesta['RepostsCount'] ?? 0; ?> 
                                            0 <!-- Mantener 0 si no implementaste RepostsCount -->
                                        </span> 
                                    </div>
                                    <div class="accion">
                                        <button class="accion-btn"> 
                                            <img src="/Resources/images/comments.svg" class="accion-icon">
                                        </button>
                                        <span class="accion-count">0</span> 
                                    </div>
                                    <div class="accion">
                                        <button class="accion-btn saved-btn" data-publicacion-id="<?php echo $respuesta['PublicacionID']; ?>">
                                            <!-- ***** CAMBIO: Imagen dinámica según YaGuardoRespuesta ***** -->
                                            <img 
                                                src="/Resources/images/<?php echo $respuesta['YaGuardoRespuesta'] ? 'guardados.svg' : 'saved.svg'; ?>" 
                                                class="accion-icon"
                                                alt="Botón de guardado"
                                            >
                                        </button>
                                        <!-- ***** CAMBIO: Mostrar SavesCount ***** -->
                                        <span class="accion-count" id="save-count-<?php echo $respuesta['PublicacionID']; ?>">
                                            <?php echo $respuesta['SavesCount'] ?? 0; ?>
                                        </span> 
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="text-align: center; color: #888; padding: 20px;">Sé el primero en responder.</p>
                    <?php endif; ?>
                </div>
            </div> <!-- Fin div.comentarios -->
        </div> <!-- Fin div.scrollable-content -->
    </main>

    <?php require base_path('views/partials/lateral.php'); ?>
</div>
<?php require base_path('views/partials/modalPostear.php'); ?>

</body>
</html>