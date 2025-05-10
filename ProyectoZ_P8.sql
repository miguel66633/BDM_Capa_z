CREATE DATABASE IF NOT EXISTS PROYECTO_Z;
USE PROYECTO_Z;

SELECT * FROM Usuario;
SELECT * FROM Publicacion;
SELECT * FROM Multimedia;
SELECT * FROM Chat;
SELECT * FROM Mensaje;
SELECT * FROM Guardado;
SELECT * FROM Repost;
SELECT * FROM UsuarioRepost;
SELECT * FROM UsuarioSeguidor;

-- Cambio de usuarios a administradores --
UPDATE Usuario
SET TipoUsuario = 2
WHERE UsuarioID = 2;

CREATE TABLE Usuario(
    UsuarioID INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
	NombreUsuario VARCHAR(50),
	Correo VARCHAR(50),
	PasswordUsu VARCHAR (255),
	Biografia VARCHAR(100),
	FechaRegistro DATE DEFAULT (CURRENT_DATE),
	ImagenPerfil LONGBLOB,
    BannerPerfil LONGBLOB,
    TipoUsuario INT,
    SeguidosCount INT DEFAULT 0,
    SeguidoresCount INT DEFAULT 0
);

CREATE TABLE Publicacion(
    PublicacionID INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
	FechaPublicacion DATETIME DEFAULT CURRENT_TIMESTAMP,
	ContenidoPublicacion VARCHAR (100),
    UsuarioID INT,
    FOREIGN KEY (UsuarioID) REFERENCES Usuario(UsuarioID),
    PublicacionPadreID INT,
    FOREIGN KEY (PublicacionPadreID) REFERENCES Publicacion(PublicacionID)
);

CREATE TABLE Chat (
    ChatID INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    UsuarioID INT,
    DestinatarioID INT,
    FechaCreacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (UsuarioID) REFERENCES Usuario(UsuarioID),
    FOREIGN KEY (DestinatarioID) REFERENCES Usuario(UsuarioID)
);

CREATE TABLE Mensaje(
    MensajeID INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
	RemitenteID INT,
	ContenidoMensaje VARCHAR(100),
	FechaMensaje TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ChatID INT,
    FOREIGN KEY (RemitenteID) REFERENCES Usuario(UsuarioID),
    FOREIGN KEY (ChatID) REFERENCES Chat(ChatID)
);

CREATE TABLE Busqueda(
    BusquedaID INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
	TerminoBusqueda VARCHAR(50),
	FechaBusqueda DATE DEFAULT (CURRENT_DATE),
	UsuarioID INT,
	FOREIGN KEY (UsuarioID) REFERENCES Usuario(UsuarioID)
);

CREATE TABLE TablaLike(
    LikeID INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
	FechaLike DATE DEFAULT (CURRENT_DATE)
);

CREATE TABLE Repost(
    RepostID INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
	FechaRepost TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Guardado(
    GuardadoID INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
	EstadoGuardado INT,
    FechaGuardado DATETIME DEFAULT CURRENT_TIMESTAMP,
    UsuarioID INT,
    FOREIGN KEY (UsuarioID) REFERENCES Usuario (UsuarioID),
    PublicacionID INT,
	FOREIGN KEY (PublicacionID) REFERENCES Publicacion(PublicacionID)
);

CREATE TABLE Multimedia(
    MultimediaID INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
	TipoMultimedia LONGBLOB,
    URL VARCHAR(100),
    PublicacionID INT,
	FOREIGN KEY (PublicacionID) REFERENCES Publicacion(PublicacionID)
);

CREATE TABLE UsuarioSeguidor(
    UsuarioSeguidorID INT,
	UsuarioSeguidoID INT,
    FechaSeguimiento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (UsuarioSeguidorID, UsuarioSeguidoID),
	FOREIGN KEY (UsuarioSeguidorID) REFERENCES Usuario (UsuarioID),
	FOREIGN KEY (UsuarioSeguidoID) REFERENCES Usuario (UsuarioID)
);

CREATE TABLE UsuarioLike(
    UsuarioID INT,
	LikeID INT,
	PRIMARY KEY (UsuarioID, LikeID),
	FOREIGN KEY (UsuarioID) REFERENCES Usuario (UsuarioID),
	FOREIGN KEY (LikeID) REFERENCES TablaLike (LikeID) ON DELETE CASCADE
);

CREATE TABLE UsuarioRepost(
    UsuarioID INT,
	RepostID INT,
	PRIMARY KEY (UsuarioID, RepostID),
	FOREIGN KEY (UsuarioID) REFERENCES Usuario (UsuarioID),
	FOREIGN KEY (RepostID) REFERENCES Repost (RepostID) ON DELETE CASCADE
);

CREATE TABLE PublicacionRepost(
    PublicacionID INT,
	RepostID INT,
    PRIMARY KEY (PublicacionID, RepostID),
    FOREIGN KEY (PublicacionID) REFERENCES Publicacion(PublicacionID),
    FOREIGN KEY (RepostID) REFERENCES Repost(RepostID) ON DELETE CASCADE
);

CREATE TABLE PublicacionLike(
    PublicacionID INT,
	LikeID INT,
    PRIMARY KEY (PublicacionID, LikeID),
    FOREIGN KEY (PublicacionID) REFERENCES Publicacion(PublicacionID),
    FOREIGN KEY (LikeID) REFERENCES TablaLike(LikeID) ON DELETE CASCADE
);

-- PROCEDURES --

DELIMITER //
CREATE PROCEDURE sp_EventosAdmin (
IN PARAM_Accion VARCHAR(50),
IN PARAM_UsuarioID INT
)
BEGIN
DECLARE EXIT HANDLER FOR SQLEXCEPTION
	-- Si ocurre un error, se realiza el rollback --
    BEGIN
        ROLLBACK;
        SELECT 'Error: La transacción fue revertida.';
    END;
    
    -- Iniciar la transacción
    START TRANSACTION;
    
    IF PARAM_Accion = 'REPORTE' THEN
    SELECT * FROM Reporte
    WHERE UsuarioID = PARAM_UsuarioID;
    
    END IF;
    
    -- Confirmar la transacción
    COMMIT;
    
END //
    
DELIMITER ;


DELIMITER //
CREATE PROCEDURE sp_ToggleLikeAndGetCounts (
    IN p_UsuarioID INT,
    IN p_PublicacionID INT
)
BEGIN
    DECLARE v_LikeID INT;
    DECLARE v_ExistingLikeID INT;
    DECLARE v_Liked BOOLEAN;
    DECLARE v_LikesCount INT;

    -- Iniciar transacción
    START TRANSACTION;

    -- Verificar si ya existe un like del usuario para esta publicación
    SELECT tl.LikeID INTO v_ExistingLikeID
    FROM TablaLike tl
    JOIN UsuarioLike ul ON tl.LikeID = ul.LikeID
    JOIN PublicacionLike pl ON tl.LikeID = pl.LikeID
    WHERE ul.UsuarioID = p_UsuarioID AND pl.PublicacionID = p_PublicacionID
    LIMIT 1;

    IF v_ExistingLikeID IS NOT NULL THEN
        -- El like existe, eliminarlo
        -- Asegúrate que las tablas permitan ON DELETE CASCADE o elimina en el orden correcto
        DELETE FROM UsuarioLike WHERE UsuarioID = p_UsuarioID AND LikeID = v_ExistingLikeID;
        DELETE FROM PublicacionLike WHERE PublicacionID = p_PublicacionID AND LikeID = v_ExistingLikeID;
        DELETE FROM TablaLike WHERE LikeID = v_ExistingLikeID;
        SET v_Liked = FALSE;
    ELSE
        -- El like no existe, crearlo
        INSERT INTO TablaLike (FechaLike) VALUES (NOW()); -- Asumiendo FechaLike es DATETIME/TIMESTAMP
        SET v_LikeID = LAST_INSERT_ID();

        INSERT INTO UsuarioLike (UsuarioID, LikeID) VALUES (p_UsuarioID, v_LikeID);
        INSERT INTO PublicacionLike (PublicacionID, LikeID) VALUES (p_PublicacionID, v_LikeID);
        SET v_Liked = TRUE;
    END IF;

    -- Obtener el nuevo conteo de likes para la publicación
    SELECT COUNT(DISTINCT pl_count.LikeID) INTO v_LikesCount
    FROM PublicacionLike pl_count
    WHERE pl_count.PublicacionID = p_PublicacionID;

    COMMIT;

    -- Devolver el resultado como un conjunto de resultados
    SELECT v_Liked AS YaDioLike, v_LikesCount AS LikesCount;
END //

DELIMITER ;


DELIMITER //
CREATE PROCEDURE sp_GetChatParticipantInfo (
    IN p_ChatID INT,
    IN p_CurrentUsuarioID INT
)
BEGIN
    SELECT 
        u.UsuarioID,
        u.NombreUsuario,
        u.ImagenPerfil
    FROM 
        Chat c
    INNER JOIN 
        Usuario u ON (u.UsuarioID = c.DestinatarioID AND c.ChatID = p_ChatID AND u.UsuarioID != p_CurrentUsuarioID)
                 OR (u.UsuarioID = c.UsuarioID AND c.ChatID = p_ChatID AND u.UsuarioID != p_CurrentUsuarioID)
    LIMIT 1;
END //

DELIMITER ;


DELIMITER //
CREATE PROCEDURE sp_GetChatMessages (
    IN p_ChatID INT
)
BEGIN
    SELECT 
        m.MensajeID,
        m.RemitenteID, -- Es crucial que esta columna se seleccione
        m.ContenidoMensaje,
        m.FechaMensaje,
        u.NombreUsuario AS RemitenteNombre -- Nombre del remitente del mensaje
    FROM 
        Mensaje m
    INNER JOIN 
        Usuario u ON m.RemitenteID = u.UsuarioID
    WHERE 
        m.ChatID = p_ChatID
    ORDER BY 
        m.FechaMensaje ASC;
END //

DELIMITER ;


DELIMITER //
CREATE PROCEDURE sp_GetOrCreateChat (
    IN p_UsuarioID_Solicitante INT, -- ID del usuario que inicia la acción
    IN p_DestinatarioID INT      -- ID del usuario con el que se quiere chatear
)
-- Etiquetar el bloque principal del procedimiento
proc_block: BEGIN
    DECLARE v_ExistingChatID INT;
    DECLARE v_LesserUserID INT;
    DECLARE v_GreaterUserID INT;
    DECLARE v_StatusMessage VARCHAR(255);
    DECLARE v_FinalChatID INT;
    DECLARE v_Success BOOLEAN DEFAULT FALSE;

    SET v_FinalChatID = NULL; -- Inicializar

    -- 1. Verificar si el usuario intenta chatear consigo mismo
    IF p_UsuarioID_Solicitante = p_DestinatarioID THEN
        SET v_StatusMessage = 'No puedes iniciar un chat contigo mismo.';
        SET v_Success = FALSE;
        SELECT v_FinalChatID AS ChatID, v_StatusMessage AS StatusMessage, v_Success AS Success;
        LEAVE proc_block; -- Usar la etiqueta para salir del procedimiento
    END IF;

    -- Determinar el menor y mayor ID para consistencia en la tabla Chat
    SET v_LesserUserID = LEAST(p_UsuarioID_Solicitante, p_DestinatarioID);
    SET v_GreaterUserID = GREATEST(p_UsuarioID_Solicitante, p_DestinatarioID);

    -- 2. Verificar si el chat ya existe
    SELECT ChatID INTO v_ExistingChatID
    FROM Chat
    WHERE UsuarioID = v_LesserUserID AND DestinatarioID = v_GreaterUserID
    LIMIT 1;

    IF v_ExistingChatID IS NOT NULL THEN
        -- El chat ya existe
        SET v_FinalChatID = v_ExistingChatID;
        SET v_StatusMessage = 'El chat ya existe.';
        SET v_Success = TRUE; -- Consideramos éxito si ya existe y lo devolvemos
    ELSE
        -- 3. El chat no existe, crearlo
        START TRANSACTION;
        INSERT INTO Chat (UsuarioID, DestinatarioID)
        VALUES (v_LesserUserID, v_GreaterUserID);

        IF ROW_COUNT() > 0 THEN
            SET v_FinalChatID = LAST_INSERT_ID();
            SET v_StatusMessage = 'Chat creado exitosamente.';
            SET v_Success = TRUE;
            COMMIT;
        ELSE
            SET v_StatusMessage = 'Error: No se pudo crear el chat en la base de datos.';
            SET v_Success = FALSE;
            ROLLBACK;
        END IF;
    END IF;

    -- Devolver el resultado como un conjunto de resultados
    SELECT v_FinalChatID AS ChatID, v_StatusMessage AS StatusMessage, v_Success AS Success;
END //

DELIMITER ;


DELIMITER //
CREATE PROCEDURE sp_EnviarMensaje (
    IN p_RemitenteID INT,
    IN p_ContenidoMensaje VARCHAR(100), -- Asegúrate que coincida con la longitud de tu columna Mensaje.ContenidoMensaje
    IN p_ChatID INT
)
BEGIN
    DECLARE v_Success BOOLEAN DEFAULT FALSE;
    DECLARE v_StatusMessage VARCHAR(255);
    DECLARE v_MensajeID INT DEFAULT NULL;

    -- Manejador de errores SQL generales
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK; -- Revertir la transacción en caso de error
        SET v_Success = FALSE;
        SET v_StatusMessage = 'Error SQL: No se pudo enviar el mensaje.';
        SELECT v_Success AS Success, v_StatusMessage AS StatusMessage, v_MensajeID AS MensajeID;
    END;

    -- Iniciar transacción
    START TRANSACTION;

    -- Insertar el mensaje
    INSERT INTO Mensaje (RemitenteID, ContenidoMensaje, ChatID, FechaMensaje)
    VALUES (p_RemitenteID, p_ContenidoMensaje, p_ChatID, NOW());

    -- Verificar si la inserción fue exitosa
    IF ROW_COUNT() > 0 THEN
        SET v_MensajeID = LAST_INSERT_ID();
        SET v_Success = TRUE;
        SET v_StatusMessage = 'Mensaje enviado exitosamente.';
        COMMIT; -- Confirmar la transacción
    ELSE
        SET v_Success = FALSE;
        SET v_StatusMessage = 'Error: El mensaje no pudo ser guardado.';
        ROLLBACK; -- Revertir si ROW_COUNT() es 0 (aunque el handler de SQLEXCEPTION lo cubriría para errores)
    END IF;

    -- Devolver el resultado
    SELECT v_Success AS Success, v_StatusMessage AS StatusMessage, v_MensajeID AS MensajeID;
END //

DELIMITER ;


DELIMITER //
CREATE PROCEDURE sp_CrearPublicacion (
    IN p_UsuarioID INT,
    IN p_ContenidoPublicacion VARCHAR(100), -- Coincide con la definición de la tabla Publicacion.ContenidoPublicacion
    IN p_ContenidoMultimedia LONGBLOB,    -- Puede ser NULL si no hay archivo
    IN p_PublicacionPadreID INT          -- NUEVO PARÁMETRO: Puede ser NULL para posts principales
)
proc_block: BEGIN
    DECLARE v_NewPublicacionID INT DEFAULT NULL;
    DECLARE v_Success BOOLEAN DEFAULT FALSE;
    DECLARE v_StatusMessage VARCHAR(255) DEFAULT 'Error desconocido al crear la publicación.';

    -- Manejador de errores SQL generales
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK; 
        SET v_Success = FALSE;
        SET v_StatusMessage = 'Error SQL: No se pudo crear la publicación.';
        SELECT v_Success AS Success, v_StatusMessage AS StatusMessage, v_NewPublicacionID AS PublicacionID;
    END;

    START TRANSACTION;

    -- 1. Insertar la publicación
    -- Usamos p_PublicacionPadreID que puede ser NULL (para posts) o un ID (para respuestas)
    INSERT INTO Publicacion (UsuarioID, ContenidoPublicacion, FechaPublicacion, PublicacionPadreID)
    VALUES (p_UsuarioID, p_ContenidoPublicacion, NOW(), p_PublicacionPadreID); -- MODIFICADO

    SET v_NewPublicacionID = LAST_INSERT_ID();

    IF v_NewPublicacionID IS NULL OR v_NewPublicacionID = 0 THEN
        SET v_StatusMessage = 'Error: No se pudo obtener el ID de la nueva publicación.';
        ROLLBACK;
        SELECT FALSE AS Success, v_StatusMessage AS StatusMessage, NULL AS PublicacionID;
        LEAVE proc_block; 
    END IF;

    -- 2. Si hay multimedia, insertarla en la tabla Multimedia
    IF p_ContenidoMultimedia IS NOT NULL THEN
        INSERT INTO Multimedia (PublicacionID, TipoMultimedia) 
        VALUES (v_NewPublicacionID, p_ContenidoMultimedia);

        IF ROW_COUNT() = 0 THEN
            SET v_StatusMessage = 'Error: La publicación se creó pero no se pudo guardar el archivo multimedia.';
            ROLLBACK;
            SELECT FALSE AS Success, v_StatusMessage AS StatusMessage, v_NewPublicacionID AS PublicacionID;
            LEAVE proc_block; 
        END IF;
    END IF;

    COMMIT;
    SET v_Success = TRUE;
    IF p_PublicacionPadreID IS NULL THEN
        SET v_StatusMessage = 'Publicación creada exitosamente.';
    ELSE
        SET v_StatusMessage = 'Respuesta agregada exitosamente.';
    END IF;
    
    SELECT v_Success AS Success, v_StatusMessage AS StatusMessage, v_NewPublicacionID AS PublicacionID;

END //

DELIMITER ;


DELIMITER //
CREATE PROCEDURE sp_ToggleRepost (
    IN p_UsuarioID INT,
    IN p_PublicacionID INT
)
proc_block: BEGIN
    DECLARE v_ExistingRepostID INT DEFAULT NULL;
    DECLARE v_NewRepostID INT DEFAULT NULL;
    DECLARE v_YaReposteo BOOLEAN DEFAULT FALSE;
    DECLARE v_RepostsCount INT DEFAULT 0;
    DECLARE v_Success BOOLEAN DEFAULT FALSE;
    DECLARE v_StatusMessage VARCHAR(255) DEFAULT 'Error desconocido al procesar el repost.';

    -- Manejador de errores SQL generales
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET v_Success = FALSE;
        SET v_StatusMessage = 'Error SQL: No se pudo procesar la acción de repost.';
        SELECT v_Success AS Success, v_StatusMessage AS StatusMessage, v_YaReposteo AS YaReposteo, v_RepostsCount AS RepostsCount;
    END;

    START TRANSACTION;

    -- 1. Verificar si ya existe un repost del usuario para esta publicación
    SELECT r.RepostID INTO v_ExistingRepostID
    FROM Repost r
    JOIN UsuarioRepost ur ON r.RepostID = ur.RepostID
    JOIN PublicacionRepost pr ON r.RepostID = pr.RepostID
    WHERE ur.UsuarioID = p_UsuarioID AND pr.PublicacionID = p_PublicacionID
    LIMIT 1;

    IF v_ExistingRepostID IS NOT NULL THEN
        -- 2. Si existe, eliminar el repost
        DELETE FROM UsuarioRepost WHERE UsuarioID = p_UsuarioID AND RepostID = v_ExistingRepostID;
        DELETE FROM PublicacionRepost WHERE PublicacionID = p_PublicacionID AND RepostID = v_ExistingRepostID;
        DELETE FROM Repost WHERE RepostID = v_ExistingRepostID;
        
        SET v_YaReposteo = FALSE;
        SET v_StatusMessage = 'Repost eliminado.';
    ELSE
        -- 3. Si no existe, crear el repost
        INSERT INTO Repost (FechaRepost) VALUES (NOW());
        SET v_NewRepostID = LAST_INSERT_ID();

        IF v_NewRepostID IS NULL OR v_NewRepostID = 0 THEN
            SET v_StatusMessage = 'Error: No se pudo crear el registro de repost principal.';
            ROLLBACK;
            SELECT FALSE AS Success, v_StatusMessage AS StatusMessage, FALSE AS YaReposteo, 0 AS RepostsCount;
            LEAVE proc_block;
        END IF;

        INSERT INTO UsuarioRepost (UsuarioID, RepostID) VALUES (p_UsuarioID, v_NewRepostID);
        INSERT INTO PublicacionRepost (PublicacionID, RepostID) VALUES (p_PublicacionID, v_NewRepostID);
        
        SET v_YaReposteo = TRUE;
        SET v_StatusMessage = 'Repost realizado exitosamente.';
    END IF;

    -- 4. Obtener el nuevo conteo de reposts para esta publicación
    SELECT COUNT(DISTINCT pr.RepostID) INTO v_RepostsCount
    FROM PublicacionRepost pr
    WHERE pr.PublicacionID = p_PublicacionID;

    COMMIT;
    SET v_Success = TRUE;
    
    SELECT v_Success AS Success, v_StatusMessage AS StatusMessage, v_YaReposteo AS YaReposteo, v_RepostsCount AS RepostsCount;

END //

DELIMITER ;


DELIMITER //
CREATE PROCEDURE sp_BuscarAdminUsuarios (
    IN p_SearchTerm VARCHAR(255) -- Puede ser NULL o un término de búsqueda
)
BEGIN
    IF p_SearchTerm IS NULL OR p_SearchTerm = '' THEN
        -- Si no hay término de búsqueda, seleccionar todos los usuarios de la vista
        SELECT 
            UsuarioID, 
            NombreUsuario, 
            Correo, 
            ImagenPerfil 
        FROM 
            vw_AdminUsuarios;
    ELSE
        -- Si hay un término de búsqueda, filtrar por NombreUsuario o Correo
        SELECT 
            UsuarioID, 
            NombreUsuario, 
            Correo, 
            ImagenPerfil 
        FROM 
            vw_AdminUsuarios
        WHERE 
            NombreUsuario LIKE CONCAT('%', p_SearchTerm, '%') OR 
            Correo LIKE CONCAT('%', p_SearchTerm, '%');
    END IF;
END //

DELIMITER ;


DELIMITER //
CREATE PROCEDURE sp_BuscarUsuariosParaChat (
    IN p_SearchTerm VARCHAR(255), -- Término para buscar por NombreUsuario
    IN p_CurrentUsuarioID INT     -- ID del usuario actual, para excluirlo de los resultados
)
BEGIN
    SELECT 
        UsuarioID, 
        NombreUsuario, 
        ImagenPerfil 
    FROM 
        vw_UsuariosParaChat -- Usando la vista creada
    WHERE 
        NombreUsuario LIKE CONCAT('%', p_SearchTerm, '%') 
        AND UsuarioID != p_CurrentUsuarioID
    LIMIT 10; -- Mantenemos el límite de resultados
END //

DELIMITER ;


DELIMITER //
CREATE PROCEDURE sp_GetPublicacionesGuardadasUsuario (
    IN p_CurrentUsuarioID INT
)
BEGIN
    SELECT
        p_info.PublicacionID,
        p_info.ContenidoPublicacion,
        p_info.FechaPublicacion,
        p_info.AutorNombreUsuario AS NombreUsuario, -- Para coincidir con lo que espera la vista
        p_info.AutorImagenPerfil AS ImagenPerfil,   -- Para coincidir con lo que espera la vista
        p_info.TipoMultimedia,
        g.FechaGuardado,
        (SELECT COUNT(DISTINCT pl.LikeID) FROM PublicacionLike pl WHERE pl.PublicacionID = p_info.PublicacionID) AS Likes,
        (SELECT COUNT(*) FROM Guardado g_count WHERE g_count.PublicacionID = p_info.PublicacionID) AS Guardados,
        (SELECT COUNT(*) FROM Publicacion comm WHERE comm.PublicacionPadreID = p_info.PublicacionID) AS CommentsCount,
        (SELECT COUNT(DISTINCT pr.RepostID) FROM PublicacionRepost pr WHERE pr.PublicacionID = p_info.PublicacionID) AS RepostsCount,
        EXISTS (
            SELECT 1
            FROM PublicacionLike pl_user
            INNER JOIN UsuarioLike ul ON pl_user.LikeID = ul.LikeID
            WHERE ul.UsuarioID = p_CurrentUsuarioID AND pl_user.PublicacionID = p_info.PublicacionID
        ) AS YaDioLike,
        1 AS YaGuardo, -- Siempre es true ya que estamos consultando las publicaciones guardadas por el usuario
        EXISTS (
            SELECT 1
            FROM Repost r_user
            JOIN UsuarioRepost ur ON r_user.RepostID = ur.RepostID
            JOIN PublicacionRepost pr_user ON r_user.RepostID = pr_user.RepostID
            WHERE ur.UsuarioID = p_CurrentUsuarioID AND pr_user.PublicacionID = p_info.PublicacionID
        ) AS YaReposteo
    FROM
        Guardado g
    INNER JOIN
        vw_PublicacionConAutorYMultimedia p_info ON g.PublicacionID = p_info.PublicacionID
    WHERE
        g.UsuarioID = p_CurrentUsuarioID
    ORDER BY
        g.FechaGuardado DESC;
END //

DELIMITER ;


DELIMITER //
CREATE PROCEDURE sp_GetPublicacionesHome (
    IN p_CurrentUsuarioID INT -- ID del usuario que está viendo el feed
)
BEGIN
    SELECT
        p_info.PublicacionID,
        p_info.ContenidoPublicacion,
        p_info.FechaPublicacion,
        p_info.AutorNombreUsuario AS NombreUsuario, -- Alias para coincidir con la vista home.view.php
        p_info.AutorImagenPerfil AS ImagenPerfil,   -- Alias para coincidir con la vista home.view.php
        p_info.TipoMultimedia,
        -- Contadores de interacciones
        (SELECT COUNT(DISTINCT pl.LikeID) FROM PublicacionLike pl WHERE pl.PublicacionID = p_info.PublicacionID) AS Likes,
        (SELECT COUNT(*) FROM Guardado g_count WHERE g_count.PublicacionID = p_info.PublicacionID) AS Guardados,
        (SELECT COUNT(*) FROM Publicacion comm WHERE comm.PublicacionPadreID = p_info.PublicacionID) AS CommentsCount,
        (SELECT COUNT(DISTINCT pr.RepostID) FROM PublicacionRepost pr WHERE pr.PublicacionID = p_info.PublicacionID) AS RepostsCount,
        -- Estados de interacción del usuario actual
        EXISTS (
            SELECT 1
            FROM PublicacionLike pl_user
            INNER JOIN UsuarioLike ul ON pl_user.LikeID = ul.LikeID
            WHERE ul.UsuarioID = p_CurrentUsuarioID AND pl_user.PublicacionID = p_info.PublicacionID
        ) AS YaDioLike,
        EXISTS (
            SELECT 1
            FROM Guardado g_user
            WHERE g_user.UsuarioID = p_CurrentUsuarioID AND g_user.PublicacionID = p_info.PublicacionID
        ) AS YaGuardado,
        EXISTS (
            SELECT 1
            FROM Repost r_user
            JOIN UsuarioRepost ur ON r_user.RepostID = ur.RepostID
            JOIN PublicacionRepost pr_user ON r_user.RepostID = pr_user.RepostID
            WHERE ur.UsuarioID = p_CurrentUsuarioID AND pr_user.PublicacionID = p_info.PublicacionID
        ) AS YaReposteo
    FROM
        vw_PublicacionConAutorYMultimedia p_info
    WHERE
        p_info.PublicacionPadreID IS NULL -- Solo publicaciones principales
    ORDER BY
        p_info.FechaPublicacion DESC;
END //

DELIMITER ;


DELIMITER //
CREATE PROCEDURE sp_GetPerfilFeed (
    IN p_ProfileOwnerID INT, -- ID del usuario cuyo perfil se está viendo
    IN p_CurrentUsuarioID INT  -- ID del usuario que está visitando el perfil
)
BEGIN
    SELECT * FROM (
        -- Publicaciones originales del dueño del perfil
        SELECT
            p_info.PublicacionID,
            p_info.ContenidoPublicacion,
            p_info.FechaPublicacion AS EffectiveDate,
            p_info.FechaPublicacion AS FechaPublicacionOriginal,
            p_info.AutorNombreUsuario,
            p_info.AutorImagenPerfil,
            p_info.AutorID,
            p_info.TipoMultimedia,
            (SELECT COUNT(DISTINCT pl_count.LikeID) FROM PublicacionLike pl_count WHERE pl_count.PublicacionID = p_info.PublicacionID) AS LikesCount,
            (SELECT COUNT(*) FROM Guardado g_count WHERE g_count.PublicacionID = p_info.PublicacionID) AS SavesCount,
            (SELECT COUNT(*) FROM Publicacion comm_count WHERE comm_count.PublicacionPadreID = p_info.PublicacionID) AS CommentsCount,
            (SELECT COUNT(DISTINCT pr_count.RepostID) FROM PublicacionRepost pr_count WHERE pr_count.PublicacionID = p_info.PublicacionID) AS RepostsCount,
            EXISTS (
                SELECT 1
                FROM PublicacionLike pl
                INNER JOIN UsuarioLike ul ON pl.LikeID = ul.LikeID
                WHERE ul.UsuarioID = p_CurrentUsuarioID AND pl.PublicacionID = p_info.PublicacionID
            ) AS YaDioLike,
            EXISTS (
                SELECT 1
                FROM Guardado g
                WHERE g.UsuarioID = p_CurrentUsuarioID AND g.PublicacionID = p_info.PublicacionID
            ) AS YaGuardo,
            EXISTS (
                SELECT 1
                FROM Repost r_check
                JOIN UsuarioRepost ur_check ON r_check.RepostID = ur_check.RepostID
                JOIN PublicacionRepost pr_check ON r_check.RepostID = pr_check.RepostID
                WHERE ur_check.UsuarioID = p_CurrentUsuarioID AND pr_check.PublicacionID = p_info.PublicacionID
            ) AS YaReposteo,
            'original' AS TipoEntrada,
            NULL AS RepostadorNombreUsuario,
            NULL AS RepostadorID,
            NULL AS FechaRepostEvento
        FROM
            vw_PublicacionConAutorYMultimedia p_info
        WHERE
            p_info.AutorID = p_ProfileOwnerID AND p_info.PublicacionPadreID IS NULL

        UNION ALL

        -- Publicaciones reposteadas por el dueño del perfil
        SELECT
            p_original.PublicacionID,
            p_original.ContenidoPublicacion,
            r.FechaRepost AS EffectiveDate,
            p_original.FechaPublicacion AS FechaPublicacionOriginal,
            p_original.AutorNombreUsuario,
            p_original.AutorImagenPerfil,
            p_original.AutorID,
            p_original.TipoMultimedia,
            (SELECT COUNT(DISTINCT pl_count.LikeID) FROM PublicacionLike pl_count WHERE pl_count.PublicacionID = p_original.PublicacionID) AS LikesCount,
            (SELECT COUNT(*) FROM Guardado g_count WHERE g_count.PublicacionID = p_original.PublicacionID) AS SavesCount,
            (SELECT COUNT(*) FROM Publicacion comm_count WHERE comm_count.PublicacionPadreID = p_original.PublicacionID) AS CommentsCount,
            (SELECT COUNT(DISTINCT pr_count.RepostID) FROM PublicacionRepost pr_count WHERE pr_count.PublicacionID = p_original.PublicacionID) AS RepostsCount,
            EXISTS (
                SELECT 1
                FROM PublicacionLike pl
                INNER JOIN UsuarioLike ul ON pl.LikeID = ul.LikeID
                WHERE ul.UsuarioID = p_CurrentUsuarioID AND pl.PublicacionID = p_original.PublicacionID
            ) AS YaDioLike,
            EXISTS (
                SELECT 1
                FROM Guardado g
                WHERE g.UsuarioID = p_CurrentUsuarioID AND g.PublicacionID = p_original.PublicacionID
            ) AS YaGuardo,
            EXISTS (
                SELECT 1
                FROM Repost r_check
                JOIN UsuarioRepost ur_check ON r_check.RepostID = ur_check.RepostID
                JOIN PublicacionRepost pr_check ON r_check.RepostID = pr_check.RepostID
                WHERE ur_check.UsuarioID = p_CurrentUsuarioID AND pr_check.PublicacionID = p_original.PublicacionID
            ) AS YaReposteo,
            'repost' AS TipoEntrada,
            u_repostador.NombreUsuario AS RepostadorNombreUsuario,
            u_repostador.UsuarioID AS RepostadorID,
            r.FechaRepost AS FechaRepostEvento
        FROM
            Repost r
        JOIN
            UsuarioRepost ur ON r.RepostID = ur.RepostID AND ur.UsuarioID = p_ProfileOwnerID
        JOIN
            PublicacionRepost pr ON r.RepostID = pr.RepostID
        JOIN
            vw_PublicacionConAutorYMultimedia p_original ON pr.PublicacionID = p_original.PublicacionID
        JOIN
            Usuario u_repostador ON ur.UsuarioID = u_repostador.UsuarioID
    ) AS ProfileFeed
    ORDER BY
        EffectiveDate DESC;
END //

DELIMITER ;


DELIMITER //
CREATE PROCEDURE sp_GetPublicacionDetalles (
    IN p_PostID INT,
    IN p_CurrentUsuarioID INT
)
BEGIN
    SELECT
        p_info.PublicacionID,
        p_info.ContenidoPublicacion,
        p_info.FechaPublicacion,
        p_info.AutorNombreUsuario AS NombreUsuario, -- Alias para la vista post.view.php
        p_info.AutorImagenPerfil AS ImagenPerfil,   -- Alias para la vista post.view.php
        p_info.AutorID,
        p_info.TipoMultimedia,     -- Multimedia de la publicación
        (SELECT COUNT(DISTINCT pl.LikeID) FROM PublicacionLike pl WHERE pl.PublicacionID = p_info.PublicacionID) AS LikesCount,
        (SELECT COUNT(*) FROM Guardado g_count WHERE g_count.PublicacionID = p_info.PublicacionID) AS SavesCount,
        (SELECT COUNT(*) FROM Publicacion comm WHERE comm.PublicacionPadreID = p_info.PublicacionID) AS CommentsCount,
        (SELECT COUNT(DISTINCT pr.RepostID) FROM PublicacionRepost pr WHERE pr.PublicacionID = p_info.PublicacionID) AS RepostsCount,
        EXISTS (
            SELECT 1
            FROM PublicacionLike pl_user
            INNER JOIN UsuarioLike ul ON pl_user.LikeID = ul.LikeID
            WHERE ul.UsuarioID = p_CurrentUsuarioID AND pl_user.PublicacionID = p_info.PublicacionID
        ) AS YaDioLike,
        EXISTS (
            SELECT 1
            FROM Guardado g_user
            WHERE g_user.UsuarioID = p_CurrentUsuarioID AND g_user.PublicacionID = p_info.PublicacionID
        ) AS YaGuardo,
        EXISTS (
            SELECT 1
            FROM Repost r_user
            JOIN UsuarioRepost ur ON r_user.RepostID = ur.RepostID
            JOIN PublicacionRepost pr_user ON r_user.RepostID = pr_user.RepostID
            WHERE ur.UsuarioID = p_CurrentUsuarioID AND pr_user.PublicacionID = p_info.PublicacionID
        ) AS YaReposteo
    FROM
        vw_PublicacionConAutorYMultimedia p_info
    WHERE
        p_info.PublicacionID = p_PostID
    LIMIT 1;
END //

DELIMITER ;


DELIMITER //
CREATE PROCEDURE sp_GetPublicacionRespuestas (
    IN p_ParentPostID INT,
    IN p_CurrentUsuarioID INT
)
BEGIN
    SELECT
        resp_info.PublicacionID,
        resp_info.ContenidoPublicacion,
        resp_info.FechaPublicacion,
        resp_info.AutorNombreUsuario AS NombreUsuario, -- Autor de la respuesta
        resp_info.AutorImagenPerfil AS ImagenPerfil,   -- Imagen de perfil del autor de la respuesta
        resp_info.AutorID AS RespondedorID,            -- ID del autor de la respuesta
        resp_info.TipoMultimedia AS ImagenRespuesta,   -- Multimedia de la respuesta
        (SELECT COUNT(DISTINCT pl.LikeID) FROM PublicacionLike pl WHERE pl.PublicacionID = resp_info.PublicacionID) AS LikesCount,
        (SELECT COUNT(*) FROM Guardado g_count WHERE g_count.PublicacionID = resp_info.PublicacionID) AS SavesCount,
        (SELECT COUNT(*) FROM Publicacion comm WHERE comm.PublicacionPadreID = resp_info.PublicacionID) AS RepliesToReplyCount, -- Comentarios a esta respuesta
        (SELECT COUNT(DISTINCT pr.RepostID) FROM PublicacionRepost pr WHERE pr.PublicacionID = resp_info.PublicacionID) AS RepostsCount,
        EXISTS (
            SELECT 1
            FROM PublicacionLike pl_user
            INNER JOIN UsuarioLike ul ON pl_user.LikeID = ul.LikeID
            WHERE ul.UsuarioID = p_CurrentUsuarioID AND pl_user.PublicacionID = resp_info.PublicacionID
        ) AS YaDioLikeRespuesta,
        EXISTS (
            SELECT 1
            FROM Guardado g_user
            WHERE g_user.UsuarioID = p_CurrentUsuarioID AND g_user.PublicacionID = resp_info.PublicacionID
        ) AS YaGuardoRespuesta,
        EXISTS (
            SELECT 1
            FROM Repost r_user
            JOIN UsuarioRepost ur ON r_user.RepostID = ur.RepostID
            JOIN PublicacionRepost pr_user ON r_user.RepostID = pr_user.RepostID
            WHERE ur.UsuarioID = p_CurrentUsuarioID AND pr_user.PublicacionID = resp_info.PublicacionID
        ) AS YaReposteoRespuesta
    FROM
        vw_PublicacionConAutorYMultimedia resp_info
    WHERE
        resp_info.PublicacionPadreID = p_ParentPostID
    ORDER BY
        resp_info.FechaPublicacion ASC;
END //

DELIMITER ;


DELIMITER //
CREATE PROCEDURE sp_GetUsuarioChatsConDetalles (
    IN p_CurrentUsuarioID INT
)
BEGIN
    SELECT
        vc.ChatID,
        CASE
            WHEN vc.Usuario1ID = p_CurrentUsuarioID THEN vc.Usuario2ID
            ELSE vc.Usuario1ID
        END AS PersonaID,
        CASE
            WHEN vc.Usuario1ID = p_CurrentUsuarioID THEN vc.Usuario2Nombre
            ELSE vc.Usuario1Nombre
        END AS NombreUsuario,
        CASE
            WHEN vc.Usuario1ID = p_CurrentUsuarioID THEN vc.Usuario2ImagenPerfil
            ELSE vc.Usuario1ImagenPerfil
        END AS ImagenPerfil,
        vc.FechaCreacionChat AS FechaCreacion,
        vc.UltimoMensajeContenido AS UltimoMensaje,
        vc.FechaUltimaActividad AS HoraUltimoMensaje -- <<< USA EL CAMPO DE LA VISTA
    FROM
        vw_ChatConParticipantesYUltimoMensaje vc
    WHERE
        vc.Usuario1ID = p_CurrentUsuarioID OR vc.Usuario2ID = p_CurrentUsuarioID
    ORDER BY
        vc.FechaUltimaActividad DESC, vc.FechaCreacionChat DESC; -- <<< ORDENA POR LA NUEVA COLUMNA
END //

DELIMITER ;


DELIMITER //
CREATE PROCEDURE sp_ToggleSeguimiento (
    IN p_UsuarioSeguidorID INT, -- ID del usuario que realiza la acción
    IN p_UsuarioSeguidoID INT   -- ID del usuario del perfil que se está viendo/siguiendo
)
proc_block: BEGIN
    DECLARE v_RelacionExiste BOOLEAN DEFAULT FALSE;
    DECLARE v_EstaSiguiendo BOOLEAN DEFAULT FALSE;
    DECLARE v_NuevosSeguidoresCountDelPerfil INT DEFAULT 0;
    DECLARE v_Success BOOLEAN DEFAULT FALSE;
    DECLARE v_StatusMessage VARCHAR(255) DEFAULT 'Error desconocido.';

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET v_Success = FALSE;
        SET v_StatusMessage = 'Error SQL: No se pudo procesar la acción de seguimiento.';
        SELECT v_Success AS Success, v_StatusMessage AS StatusMessage, FALSE AS EstaSiguiendo, 0 AS NuevosSeguidoresCountDelPerfil;
    END;

    IF p_UsuarioSeguidorID = p_UsuarioSeguidoID THEN
        SET v_StatusMessage = 'No puedes seguirte a ti mismo.';
        SELECT v_Success AS Success, v_StatusMessage AS StatusMessage, FALSE AS EstaSiguiendo, 0 AS NuevosSeguidoresCountDelPerfil;
        LEAVE proc_block;
    END IF;

    START TRANSACTION;

    SELECT EXISTS(
        SELECT 1 FROM UsuarioSeguidor
        WHERE UsuarioSeguidorID = p_UsuarioSeguidorID AND UsuarioSeguidoID = p_UsuarioSeguidoID
    ) INTO v_RelacionExiste;

    IF v_RelacionExiste THEN
        -- Dejar de seguir
        DELETE FROM UsuarioSeguidor
        WHERE UsuarioSeguidorID = p_UsuarioSeguidorID AND UsuarioSeguidoID = p_UsuarioSeguidoID;
        SET v_EstaSiguiendo = FALSE;
        SET v_StatusMessage = 'Has dejado de seguir a este usuario.';
    ELSE
        -- Seguir
        INSERT INTO UsuarioSeguidor (UsuarioSeguidorID, UsuarioSeguidoID)
        VALUES (p_UsuarioSeguidorID, p_UsuarioSeguidoID);
        SET v_EstaSiguiendo = TRUE;
        SET v_StatusMessage = 'Ahora sigues a este usuario.';
    END IF;

    -- Obtener el nuevo conteo de seguidores del perfil que se está viendo
    -- El trigger ya habrá actualizado este valor en la tabla Usuario
    SELECT SeguidoresCount INTO v_NuevosSeguidoresCountDelPerfil
    FROM Usuario
    WHERE UsuarioID = p_UsuarioSeguidoID;

    COMMIT;
    SET v_Success = TRUE;

    SELECT v_Success AS Success, v_StatusMessage AS StatusMessage, v_EstaSiguiendo AS EstaSiguiendo, v_NuevosSeguidoresCountDelPerfil AS NuevosSeguidoresCountDelPerfil;

END //

DELIMITER ;


DELIMITER //
CREATE PROCEDURE sp_GetUsuarioPerfilDetalles (
    IN p_ProfileUserID INT,     -- ID del usuario cuyo perfil se está viendo
    IN p_CurrentUsuarioID INT   -- ID del usuario que está visitando el perfil (puede ser el mismo o diferente)
)
BEGIN
    SELECT
        upd.UsuarioID,
        upd.NombreUsuario,
        upd.Biografia,
        upd.ImagenPerfil,
        upd.BannerPerfil,
        upd.TipoUsuario,
        upd.SeguidosCount,
        upd.SeguidoresCount,
        EXISTS ( -- Verificar si el visitante actual sigue al dueño del perfil
            SELECT 1
            FROM UsuarioSeguidor us
            WHERE us.UsuarioSeguidorID = p_CurrentUsuarioID AND us.UsuarioSeguidoID = p_ProfileUserID
        ) AS EstaSiguiendo
    FROM
        vw_UsuarioPerfilDetalles upd
    WHERE
        upd.UsuarioID = p_ProfileUserID
    LIMIT 1;
END //

DELIMITER ;


DELIMITER //
CREATE PROCEDURE sp_ToggleGuardado (
    IN p_UsuarioID INT,
    IN p_PublicacionID INT
)
proc_block: BEGIN
    DECLARE v_GuardadoExistenteID INT DEFAULT NULL;
    DECLARE v_YaGuardo BOOLEAN DEFAULT FALSE;
    DECLARE v_SavesCount INT DEFAULT 0;
    DECLARE v_Success BOOLEAN DEFAULT FALSE;
    DECLARE v_StatusMessage VARCHAR(255) DEFAULT 'Error desconocido al procesar el guardado.';

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET v_Success = FALSE;
        SET v_StatusMessage = 'Error SQL: No se pudo procesar la acción de guardado.';
        SELECT v_Success AS Success, v_StatusMessage AS StatusMessage, FALSE AS YaGuardo, 0 AS SavesCount;
    END;

    IF p_UsuarioID IS NULL OR p_PublicacionID IS NULL THEN
        SET v_StatusMessage = 'UsuarioID y PublicacionID no pueden ser nulos.';
        SELECT v_Success AS Success, v_StatusMessage AS StatusMessage, FALSE AS YaGuardo, 0 AS SavesCount;
        LEAVE proc_block;
    END IF;

    START TRANSACTION;

    -- 1. Verificar si el usuario ya guardó la publicación
    SELECT GuardadoID INTO v_GuardadoExistenteID
    FROM Guardado
    WHERE UsuarioID = p_UsuarioID AND PublicacionID = p_PublicacionID
    LIMIT 1;

    IF v_GuardadoExistenteID IS NOT NULL THEN
        -- Ya existe, entonces eliminar (dejar de guardar)
        DELETE FROM Guardado
        WHERE GuardadoID = v_GuardadoExistenteID;

        SET v_YaGuardo = FALSE;
        SET v_StatusMessage = 'Publicación eliminada de guardados.';
    ELSE
        -- No existe, entonces insertar (guardar)
        INSERT INTO Guardado (UsuarioID, PublicacionID, EstadoGuardado, FechaGuardado)
        VALUES (p_UsuarioID, p_PublicacionID, 1, NOW());
        
        SET v_YaGuardo = TRUE;
        SET v_StatusMessage = 'Publicación guardada exitosamente.';
    END IF;

    -- 2. Obtener el nuevo conteo de guardados para esta publicación
    SELECT COUNT(*) INTO v_SavesCount
    FROM Guardado
    WHERE PublicacionID = p_PublicacionID;

    COMMIT;
    SET v_Success = TRUE;
    
    SELECT v_Success AS Success, v_StatusMessage AS StatusMessage, v_YaGuardo AS YaGuardo, v_SavesCount AS SavesCount;

END //

DELIMITER ;


DROP PROCEDURE sp_GetEstadisticasSitio;
DELIMITER //
CREATE PROCEDURE sp_GetEstadisticasSitio() -- Cambio a funcion --
BEGIN
    SELECT
        PublicacionesGenerales,
        UsuariosRegistrados
    FROM
        Estadisticas
    LIMIT 1; -- La vista Estadisticas está diseñada para devolver una sola fila
END //

DELIMITER ;


DELIMITER //
CREATE PROCEDURE sp_ObtenerChatsPorUsuario (
    IN p_UsuarioID INT
)
BEGIN
    SELECT
        vcdc.ChatID,
        CASE
            WHEN vcdc.IniciadorID = p_UsuarioID THEN vcdc.DestinatarioID
            ELSE vcdc.IniciadorID
        END AS OtroUsuarioID,
        CASE
            WHEN vcdc.IniciadorID = p_UsuarioID THEN vcdc.NombreDestinatario
            ELSE vcdc.NombreIniciador
        END AS OtroNombreUsuario,
        CASE
            WHEN vcdc.IniciadorID = p_UsuarioID THEN vcdc.ImagenDestinatario
            ELSE vcdc.ImagenIniciador
        END AS OtroImagenPerfil,
        vcdc.UltimoMensaje,
        vcdc.FechaUltimoMensaje,
        vcdc.FechaCreacionChat
    FROM
        vw_ChatDetallesCompletos vcdc
    INNER JOIN
        UsuarioChat uc ON vcdc.ChatID = uc.ChatID -- Asegura que el usuario es parte del chat
    WHERE
        uc.UsuarioID = p_UsuarioID -- Filtra los chats para el usuario especificado
    ORDER BY
        vcdc.FechaUltimoMensaje DESC, vcdc.FechaCreacionChat DESC;
END //

DELIMITER ;


DELIMITER //
CREATE PROCEDURE sp_ObtenerTodasPublicaciones()
BEGIN
    SELECT
        PublicacionID,
        ContenidoPublicacion,
        FechaPublicacion,
        AutorID,
        AutorNombreUsuario,
        AutorImagenPerfil,
        TipoMultimedia, -- Este es el LONGBLOB del contenido multimedia
        PublicacionPadreID
    FROM
        vw_PublicacionConAutorYMultimedia
    WHERE PublicacionPadreID IS NULL -- Para obtener solo publicaciones principales, no comentarios
    ORDER BY
        FechaPublicacion DESC;
END //

DELIMITER ;


DELIMITER //
CREATE PROCEDURE sp_BuscarUsuariosLateral (
    IN p_SearchTerm VARCHAR(255) -- Puede ser NULL o un término de búsqueda
)
BEGIN
    IF p_SearchTerm IS NOT NULL AND p_SearchTerm != '' THEN
        -- Si hay un término de búsqueda, filtrar por NombreUsuario o Correo
        SELECT
            UsuarioID,
            NombreUsuario,
            Correo,
            ImagenPerfil
        FROM
            vw_UsuariosParaBusquedaLateral -- Esta vista selecciona UsuarioID, NombreUsuario, Correo, ImagenPerfil
        WHERE
            NombreUsuario LIKE CONCAT('%', p_SearchTerm, '%') OR
            Correo LIKE CONCAT('%', p_SearchTerm, '%');
    ELSE
        -- Si no hay término de búsqueda o está vacío, devolver todos los usuarios de la vista
        SELECT
            UsuarioID,
            NombreUsuario,
            Correo,
            ImagenPerfil
        FROM
            vw_UsuariosParaBusquedaLateral;
    END IF;
END //

DELIMITER ;

-- PROCEDURES --



-- VIEWS --
CREATE OR REPLACE VIEW Reporte AS
SELECT 
    u.UsuarioID,
    u.NombreUsuario,
    COUNT(DISTINCT CASE WHEN p.PublicacionPadreID IS NULL THEN p.PublicacionID END) AS TotalPublicaciones,
    COUNT(DISTINCT ul.LikeID) AS TotalLikes,
    COUNT(DISTINCT CASE WHEN p.PublicacionPadreID IS NOT NULL THEN p.PublicacionID END) AS TotalComentarios,
    COUNT(DISTINCT g.GuardadoID) AS TotalGuardados
FROM Usuario u
LEFT JOIN Publicacion p ON p.UsuarioID = u.UsuarioID
LEFT JOIN UsuarioLike ul ON ul.UsuarioID = u.UsuarioID
LEFT JOIN Guardado g ON g.UsuarioID = u.UsuarioID
GROUP BY u.UsuarioID, u.NombreUsuario;

SELECT * FROM Reporte;


CREATE OR REPLACE VIEW Estadisticas AS
SELECT
    (SELECT COUNT(*) FROM Publicacion WHERE PublicacionPadreID IS NULL) AS PublicacionesGenerales,
    (SELECT COUNT(*) FROM Usuario) AS UsuariosRegistrados;

SELECT * FROM Estadisticas;


CREATE OR REPLACE VIEW vw_AdminUsuarios AS
SELECT 
    UsuarioID, 
    NombreUsuario, 
    Correo, 
    ImagenPerfil 
FROM Usuario;


CREATE OR REPLACE VIEW vw_UsuariosParaChat AS
SELECT 
    UsuarioID, 
    NombreUsuario, 
    ImagenPerfil 
FROM Usuario;    


CREATE OR REPLACE VIEW vw_UsuariosParaBusquedaLateral AS
SELECT 
    UsuarioID, 
    NombreUsuario, 
    Correo, 
    ImagenPerfil 
FROM Usuario;


CREATE OR REPLACE VIEW vw_PublicacionConAutorYMultimedia AS
SELECT
    p.PublicacionID,
    p.ContenidoPublicacion,
    p.FechaPublicacion,
    p.UsuarioID AS AutorID, 
    u.NombreUsuario AS AutorNombreUsuario,
    u.ImagenPerfil AS AutorImagenPerfil,
    m.TipoMultimedia,
    p.PublicacionPadreID
FROM Publicacion p
LEFT JOIN Usuario u ON p.UsuarioID = u.UsuarioID
LEFT JOIN Multimedia m ON p.PublicacionID = m.PublicacionID;


CREATE OR REPLACE VIEW vw_UsuarioPerfilDetalles AS
SELECT
    UsuarioID,
    NombreUsuario,
    Biografia,
    ImagenPerfil,
    BannerPerfil,
    TipoUsuario -- Podría ser útil si quieres mostrar algo diferente para admins, etc.
FROM Usuario;


CREATE OR REPLACE VIEW vw_ChatConParticipantesYUltimoMensaje AS
SELECT
    c.ChatID,
    c.UsuarioID AS Usuario1ID,         -- El que inició el chat o el de menor ID si se normaliza
    u1.NombreUsuario AS Usuario1Nombre,
    u1.ImagenPerfil AS Usuario1ImagenPerfil,
    c.DestinatarioID AS Usuario2ID,   -- El otro participante
    u2.NombreUsuario AS Usuario2Nombre,
    u2.ImagenPerfil AS Usuario2ImagenPerfil,
    c.FechaCreacion AS FechaCreacionChat,
    (SELECT m.ContenidoMensaje
     FROM Mensaje m
     WHERE m.ChatID = c.ChatID
     ORDER BY m.FechaMensaje DESC
     LIMIT 1) AS UltimoMensajeContenido,
    (SELECT m.FechaMensaje
     FROM Mensaje m
     WHERE m.ChatID = c.ChatID
     ORDER BY m.FechaMensaje DESC
     LIMIT 1) AS UltimoMensajeFecha
FROM Chat c
INNER JOIN Usuario u1 ON c.UsuarioID = u1.UsuarioID
INNER JOIN Usuario u2 ON c.DestinatarioID = u2.UsuarioID;


CREATE OR REPLACE VIEW vw_ChatConParticipantesYUltimoMensaje AS
SELECT
    c.ChatID,
    c.UsuarioID AS Usuario1ID,
    u1.NombreUsuario AS Usuario1Nombre,
    u1.ImagenPerfil AS Usuario1ImagenPerfil,
    c.DestinatarioID AS Usuario2ID,
    u2.NombreUsuario AS Usuario2Nombre,
    u2.ImagenPerfil AS Usuario2ImagenPerfil,
    c.FechaCreacion AS FechaCreacionChat,
    c.FechaUltimaActividad, -- <<< UTILIZA LA NUEVA COLUMNA DIRECTAMENTE
    (SELECT m.ContenidoMensaje
     FROM Mensaje m
     WHERE m.ChatID = c.ChatID
     ORDER BY m.FechaMensaje DESC
     LIMIT 1) AS UltimoMensajeContenido
    -- La subconsulta para UltimoMensajeFecha ya no es necesaria aquí
FROM Chat c
INNER JOIN Usuario u1 ON c.UsuarioID = u1.UsuarioID
INNER JOIN Usuario u2 ON c.DestinatarioID = u2.UsuarioID;


CREATE OR REPLACE VIEW vw_UsuarioPerfilDetalles AS
SELECT
    UsuarioID,
    NombreUsuario,
    Biografia,
    ImagenPerfil,
    BannerPerfil,
    TipoUsuario,
    SeguidosCount,    -- <<< NUEVA COLUMNA
    SeguidoresCount   -- <<< NUEVA COLUMNA
FROM Usuario;


CREATE OR REPLACE VIEW vw_ChatDetallesCompletos AS
SELECT
    c.ChatID,
    c.UsuarioID AS IniciadorID,
    u1.NombreUsuario AS NombreIniciador,
    u1.ImagenPerfil AS ImagenIniciador,
    c.DestinatarioID AS DestinatarioID,
    u2.NombreUsuario AS NombreDestinatario,
    u2.ImagenPerfil AS ImagenDestinatario,
    (SELECT ContenidoMensaje FROM Mensaje m WHERE m.ChatID = c.ChatID ORDER BY m.FechaMensaje DESC LIMIT 1) AS UltimoMensaje,
    (SELECT FechaMensaje FROM Mensaje m WHERE m.ChatID = c.ChatID ORDER BY m.FechaMensaje DESC LIMIT 1) AS FechaUltimoMensaje,
    c.FechaCreacion AS FechaCreacionChat
FROM Chat c
INNER JOIN Usuario u1 ON c.UsuarioID = u1.UsuarioID  -- Detalles del usuario que inició el chat
INNER JOIN Usuario u2 ON c.DestinatarioID = u2.UsuarioID; -- Detalles del usuario destinatario

-- VIEWS --



-- TRIGGERS --
DELIMITER //
-- Crea el trgiger para la fecha de la hora en la vista de mensajes en la barra central
CREATE TRIGGER trg_AfterInsert_Mensaje_UpdateChatActivity
AFTER INSERT ON Mensaje
FOR EACH ROW
BEGIN
    UPDATE Chat
    SET FechaUltimaActividad = NEW.FechaMensaje -- Usar la fecha del nuevo mensaje
    WHERE ChatID = NEW.ChatID;
END //

DELIMITER ;


DELIMITER //
CREATE TRIGGER trg_AfterInsert_UsuarioSeguidor
AFTER INSERT ON UsuarioSeguidor
FOR EACH ROW
BEGIN
    -- Incrementar el contador de 'Seguidos' para el usuario que realiza la acción de seguir
    UPDATE Usuario
    SET SeguidosCount = SeguidosCount + 1
    WHERE UsuarioID = NEW.UsuarioSeguidorID;

    -- Incrementar el contador de 'Seguidores' para el usuario que es seguido
    UPDATE Usuario
    SET SeguidoresCount = SeguidoresCount + 1
    WHERE UsuarioID = NEW.UsuarioSeguidoID;
END //

DELIMITER ;


DELIMITER //
CREATE TRIGGER trg_AfterDelete_UsuarioSeguidor
AFTER DELETE ON UsuarioSeguidor
FOR EACH ROW
BEGIN
    -- Decrementar el contador de 'Seguidos' para el usuario que deja de seguir
    UPDATE Usuario
    SET SeguidosCount = GREATEST(0, SeguidosCount - 1) -- Evitar conteos negativos
    WHERE UsuarioID = OLD.UsuarioSeguidorID;

    -- Decrementar el contador de 'Seguidores' para el usuario que deja de ser seguido
    UPDATE Usuario
    SET SeguidoresCount = GREATEST(0, SeguidoresCount - 1) -- Evitar conteos negativos
    WHERE UsuarioID = OLD.UsuarioSeguidoID;
END //

DELIMITER ;

-- TRIGGERS --



-- FUNCTIONS --
DELIMITER //
CREATE FUNCTION fn_GetTotalPublicaciones() RETURNS INT  
DETERMINISTIC READS SQL DATA
BEGIN
    DECLARE v_Total INT;
    SELECT PublicacionesGenerales INTO v_Total FROM Estadisticas;
    RETURN v_Total;
END //

DELIMITER ;


DELIMITER //
CREATE FUNCTION fn_GetUsuariosRegistrados() RETURNS INT  
DETERMINISTIC READS SQL DATA
BEGIN
    DECLARE v_Total INT;
    SELECT UsuariosRegistrados INTO v_Total FROM Estadisticas;
    RETURN v_Total;
END //

DELIMITER ;

-- FUNCTIONS --







-- CAMBIOS IMPORTANTES --
ALTER TABLE Chat
ADD COLUMN FechaUltimaActividad TIMESTAMP NULL DEFAULT NULL AFTER FechaCreacion;

-- MUY RECOMENDADO:
-- Actualizar la FechaUltimaActividad para los chats existentes una sola vez.
-- Ejecuta esto directamente en tu cliente MySQL o como parte de tu script de migración,
-- DESPUÉS de añadir la columna y ANTES de crear el trigger.

SET SQL_SAFE_UPDATES = 0;

UPDATE Chat c
SET c.FechaUltimaActividad = (
    SELECT MAX(m.FechaMensaje)
    FROM Mensaje m
    WHERE m.ChatID = c.ChatID
)
WHERE EXISTS ( -- Solo actualiza chats que tienen mensajes
    SELECT 1
    FROM Mensaje m
    WHERE m.ChatID = c.ChatID
);
-- Para chats sin mensajes, FechaUltimaActividad podría quedar NULL o igual a FechaCreacion
UPDATE Chat
SET FechaUltimaActividad = FechaCreacion
WHERE FechaUltimaActividad IS NULL;

-- Reactivar el modo de actualizaciones seguras (recomendado)
SET SQL_SAFE_UPDATES = 1;


-- CAMBIO 2 --
-- Desactivar el modo de actualizaciones seguras temporalmente
SET SQL_SAFE_UPDATES = 0;

-- Actualizar SeguidosCount (cuántos usuarios sigue cada usuario)
UPDATE Usuario u
SET u.SeguidosCount = (
    SELECT COUNT(*)
    FROM UsuarioSeguidor us
    WHERE us.UsuarioSeguidorID = u.UsuarioID
);

-- Actualizar SeguidoresCount (cuántos seguidores tiene cada usuario)
UPDATE Usuario u
SET u.SeguidoresCount = (
    SELECT COUNT(*)
    FROM UsuarioSeguidor us
    WHERE us.UsuarioSeguidoID = u.UsuarioID
);

-- Reactivar el modo de actualizaciones seguras
SET SQL_SAFE_UPDATES = 1;