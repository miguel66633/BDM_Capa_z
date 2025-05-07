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

ALTER TABLE Repost MODIFY COLUMN FechaRepost DATETIME;
ALTER TABLE Publicacion MODIFY FechaPublicacion DATETIME DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE Guardado MODIFY FechaGuardado DATETIME DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE Chat ADD COLUMN FechaCreacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE Repost MODIFY FechaRepost TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

UPDATE Usuario
SET TipoUsuario = 2
WHERE UsuarioID = 2;

ALTER TABLE Mensaje
ADD CONSTRAINT FK_RemitenteID
FOREIGN KEY (RemitenteID) REFERENCES Usuario(UsuarioID);

CREATE TABLE Usuario(
    UsuarioID INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
	NombreUsuario VARCHAR(50),
	Correo VARCHAR(50),
	PasswordUsu VARCHAR (255),
	Biografia VARCHAR(100),
	FechaRegistro DATE DEFAULT (CURRENT_DATE),
	ImagenPerfil LONGBLOB,
    BannerPerfil LONGBLOB,
    TipoUsuario INT
);

CREATE TABLE Publicacion(
    PublicacionID INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
	FechaPublicacion DATE DEFAULT (CURRENT_DATE),
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
    FOREIGN KEY (ChatID) REFERENCES Chat(ChatID)
);

CREATE TABLE Busqueda(
    BusquedaID INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
	TerminoBusqueda VARCHAR(50),
	FechaBusqueda DATE DEFAULT (CURRENT_DATE),
	UsuarioID INT,
	FOREIGN KEY (UsuarioID) REFERENCES Usuario(UsuarioID)
);

CREATE TABLE Bloqueo (
    BloqueoID INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
	AfectadoID INT,
    FOREIGN KEY (AfectadoID) REFERENCES Usuario(UsuarioID),
	EstadoBloqueo INT,
	FechaBloqueo DATE DEFAULT (CURRENT_DATE)
);

CREATE TABLE TablaLike(
    LikeID INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
	FechaLike DATE DEFAULT (CURRENT_DATE)
);

CREATE TABLE Repost(
    RepostID INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
	FechaRepost DATE DEFAULT (CURRENT_DATE)
);

CREATE TABLE Guardado(
    GuardadoID INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
	EstadoGuardado INT,
    FechaGuardado DATE DEFAULT (CURRENT_DATE),
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

CREATE TABLE UsuarioBloqueo(
    UsuarioID INT,
	BloqueoID INT,
	PRIMARY KEY (UsuarioID, BloqueoID),
	FOREIGN KEY (UsuarioID) REFERENCES Usuario (UsuarioID),
	FOREIGN KEY (BloqueoID) REFERENCES Bloqueo (BloqueoID)
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

DELIMITER //
CREATE PROCEDURE sp_AdministrarUsuario (
IN PARAM_Accion VARCHAR(50),
IN PARAM_UsuarioID INT,
IN PARAM_NombreUsuario VARCHAR(50),
IN PARAM_Correo VARCHAR(50),
IN PARAM_PasswordUsu VARCHAR(20),
IN PARAM_Biografia VARCHAR(100), 
IN PARAM_ImagenPerfil LONGBLOB,
IN PARAM_BannerPerfil LONGBLOB,
IN PARAM_TipoUsuario INT)
BEGIN
DECLARE EXIT HANDLER FOR SQLEXCEPTION
	-- Si ocurre un error, se realiza el rollback --
    BEGIN
        ROLLBACK;
        SELECT 'Error: La transacción fue revertida.';
    END;
    
    -- Iniciar la transacción
    START TRANSACTION;
    -- Agregar Usuario --
    IF PARAM_Accion = 'INSERT' THEN
        INSERT INTO Usuario (NombreUsuario, Correo, PasswordUsu, Biografia, ImagenPerfil, BannerPerfil, TipoUsuario)
        VALUES (PARAM_NombreUsuario, PARAM_Correo, PARAM_PasswordUsu, PARAM_Biografia, PARAM_ImagenPerfil, PARAM_BannerPerfil, PARAM_TipoUsuario);
    
    -- Editar Usuario --
    ELSEIF PARAM_Accion = 'UPDATE' THEN
        UPDATE Usuario
        SET NombreUsuario = PARAM_NombreUsuario, Correo = PARAM_Correo, PasswordUsu = PARAM_PasswordUsu, Biografia = PARAM_Biografia,
            ImagenPerfil = PARAM_ImagenPerfil, BannerPerfil = PARAM_BannerPerfil
        WHERE UsuarioID = PARAM_UsuarioID;
    
    END IF;
    
    -- Confirmar la transacción
    COMMIT;

END //
DELIMITER ;



DELIMITER //
CREATE PROCEDURE sp_AdministrarPublicacion (
IN PARAM_Accion VARCHAR(50),
IN PARAM_ContenidoPublicacion VARCHAR (100),
IN PARAM_UsuarioID INT,
IN PARAM_PublicacionPadreID INT,
IN PARAM_TipoMultimedia LONGBLOB,
IN PARAM_URL VARCHAR(100),
IN PARAM_CantidadArchivos INT
)
BEGIN
DECLARE EXIT HANDLER FOR SQLEXCEPTION
	-- Si ocurre un error, se realiza el rollback --
    BEGIN
        ROLLBACK;
        SELECT 'Error: La transacción fue revertida.';
    END;
    
DECLARE i INT DEFAULT 1;

    -- Iniciar la transacción
    START TRANSACTION;
    -- Agregar Publicacion --
    IF PARAM_Accion = 'INSERT' THEN
        INSERT INTO Publicacion (ContenidoPublicacion, UsuarioID, PublicacionPadreID)
        VALUES (PARAM_ContenidoPublicacion, PARAM_UsuarioID, PARAM_PublicacionPadreID);
    
    -- Agregar publicacion con archivos --
	ELSEIF PARAM_Accion = 'INSERT_W_MULTI' THEN
        INSERT INTO Publicacion (ContenidoPublicacion, UsuarioID, PublicacionPadreID)
        VALUES (PARAM_ContenidoPublicacion, PARAM_UsuarioID, PARAM_PublicacionPadreID);
        
		SET @LastPublicacionID = LAST_INSERT_ID();
        
		WHILE i <= PARAM_CantidadArchivos DO
            INSERT INTO Multimedia (TipoMultimedia, URL, PublicacionID)
            VALUES (PARAM_TipoMultimedia, PARAM_URL, @LastPublicacionID);
            SET i = i + 1;
	    END WHILE;
    
    END IF;
    
    -- Confirmar la transacción
    COMMIT;

END //
DELIMITER ;

-- NOTA: Al momento de hacer publicaciones con archivos multimedia hay que regresar una variable
-- que cuente la cantidad de archivos que hay para que los guarde todos



DELIMITER //
CREATE PROCEDURE sp_AdministrarChat (
IN PARAM_Accion VARCHAR(50),
IN PARAM_UsuarioID INT,
IN PARAM_DestinatarioID INT
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
    -- Agregar Chat --
    
    IF PARAM_Accion = 'INSERT' THEN
    IF NOT EXISTS (SELECT 1 FROM Chat WHERE UsuarioID = LEAST(PARAM_UsuarioID, PARAM_DestinatarioID) AND
	DestinatarioID = GREATEST(PARAM_UsuarioID, PARAM_DestinatarioID)) THEN
        INSERT INTO Chat (UsuarioID, DestinatarioID)
        VALUES (LEAST(PARAM_UsuarioID, PARAM_DestinatarioID), GREATEST(PARAM_UsuarioID, PARAM_DestinatarioID));
	ELSE
        SELECT 'El chat ya existe.' AS Mensaje;
	END IF;
    
    END IF;
    
    -- Confirmar la transacción
    COMMIT;

END //
DELIMITER ;



DELIMITER //
CREATE PROCEDURE sp_AdministrarMensaje (
IN PARAM_Accion VARCHAR(50),
IN PARAM_RemitenteID INT,
IN PARAM_ContenidoMensaje VARCHAR(100),
IN PARAM_ChatID INT
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
    -- Agregar Mensaje --
    IF PARAM_Accion = 'INSERT' THEN
        INSERT INTO Mensaje (RemitenteID, ContenidoMensaje, ChatID)
        VALUES (PARAM_RemitenteID, PARAM_ContenidoMensaje, PARAM_ChatID);
    
    END IF;
    
    -- Confirmar la transacción
    COMMIT;

END //
DELIMITER ;



DELIMITER //
CREATE PROCEDURE sp_AdministrarBusqueda (
IN PARAM_Accion VARCHAR(50),
IN PARAM_TerminoBusqueda VARCHAR(50),
IN PARAM_UsuarioID VARCHAR(50)
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
    -- Agregar Busqueda --
    IF PARAM_Accion = 'INSERT' THEN
        INSERT INTO Busqueda (TerminoBusqueda, UsuarioID)
        VALUES (PARAM_TerminoBusqueda, PARAM_UsuarioID);
    
    END IF;
    
    -- Confirmar la transacción
    COMMIT;

END //
DELIMITER ;
    


DELIMITER //
CREATE PROCEDURE sp_AdministrarBloqueo (
IN PARAM_Accion VARCHAR(50),
IN PARAM_BloqueoID INT,
IN PARAM_UsuarioID INT,
IN PARAM_AfectadoID INT,
IN PARAM_EstadoBloqueo INT
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
    -- Agregar Bloqueo --
    IF PARAM_Accion = 'INSERT' THEN
        INSERT INTO Bloqueo (AfectadoID, EstadoBloqueo)
        VALUES (PARAM_AfectadoID, 1);
        
        SET @LastBloqueoID = LAST_INSERT_ID();
        INSERT INTO UsuarioBloqueo (UsuarioID, BloqueoID)
        VALUES (PARAM_UsuarioID, @LastBloqueoID);
	
    -- Editar Bloqueo --
	ELSEIF PARAM_Accion = 'UPDATE' THEN
        UPDATE Bloqueo
        SET EstadoBloqueo = PARAM_EstadoBloqueo, FechaBloqueo = CURRENT_DATE
        WHERE BloqueoID = PARAM_BloqueoID;
    
    END IF;
    
    -- Confirmar la transacción
    COMMIT;

END //
DELIMITER ;



DELIMITER //
CREATE PROCEDURE sp_AdministrarLike (
IN PARAM_Accion VARCHAR(50),
IN PARAM_LikeID INT,
IN PARAM_UsuarioID INT,
IN PARAM_PublicacionID INT
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
    -- Agregar Like --
    IF PARAM_Accion = 'INSERT' THEN
        INSERT INTO TablaLike ()
        VALUES ();
        
        SET @LastLikeID = LAST_INSERT_ID();
        INSERT INTO UsuarioLike (UsuarioID, LikeID)
        VALUES (PARAM_UsuarioID, @LastLikeID);
        
        INSERT INTO PublicacionLike (PublicacionID, LikeID)
        VALUES (PARAM_PublicacionID, @LastLikeID);
	
    -- Eliminar Like --
	ELSEIF PARAM_Accion = 'DELETE' THEN
        DELETE FROM TablaLike
        WHERE LikeID = PARAM_LikeID;
    
    END IF;
    
    -- Confirmar la transacción
    COMMIT;

END //
DELIMITER ;



DELIMITER //
CREATE PROCEDURE sp_AdministrarRepost (
IN PARAM_Accion VARCHAR(50),
IN PARAM_RepostID INT,
IN PARAM_UsuarioID INT,
IN PARAM_PublicacionID INT
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
    -- Agregar Repost --
    IF PARAM_Accion = 'INSERT' THEN
        INSERT INTO Repost ()
        VALUES ();
        
        SET @LastRepostID = LAST_INSERT_ID();
        INSERT INTO UsuarioRepost (UsuarioID, RepostID)
        VALUES (PARAM_UsuarioID, @LastRepostID);
        
        INSERT INTO PublicacionRepost (PublicacionID, RepostID)
        VALUES (PARAM_PublicacionID, @LastRepostID);
	
    -- Eliminar Repost --
	ELSEIF PARAM_Accion = 'DELETE' THEN
        DELETE FROM Repost
        WHERE RepostID = PARAM_RepostID;
    
    END IF;
    
    -- Confirmar la transacción
    COMMIT;

END //
DELIMITER ;



DELIMITER //
CREATE PROCEDURE sp_AdministrarGuardado (
IN PARAM_Accion VARCHAR(50),
IN PARAM_GuardadoID INT,
IN PARAM_EstadoGuardado INT,
IN PARAM_UsuarioID INT,
IN PARAM_PublicacionID INT
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
    -- Agregar Guardado --
    IF PARAM_Accion = 'INSERT' THEN
        INSERT INTO Guardado (EstadoGuardado, UsuarioID, PublicacionID)
        VALUES (1, PARAM_UsuarioID, PARAM_PublicacionID);
	
    -- Actualizar Guardado --
	ELSEIF PARAM_Accion = 'UPDATE' THEN
        UPDATE Guardado
        SET EstadoGuardado = PARAM_EstadoGuardado, FechaGuardado = CURRENT_DATE
        WHERE GuardadoID = PARAM_GuardadoID;
    
    END IF;
    
    -- Confirmar la transacción
    COMMIT;

END //
DELIMITER ;



DELIMITER //
CREATE PROCEDURE sp_AdministrarSeguidor (
IN PARAM_Accion VARCHAR(50),
IN PARAM_UsuarioSeguidorID INT,
IN PARAM_UsuarioSeguidoID INT
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
    -- Agregar Seguidor --
    IF PARAM_Accion = 'INSERT' THEN
        INSERT INTO UsuarioSeguidor (UsuarioSeguidorID, UsuarioSeguidoID)
        VALUES (PARAM_UsuarioSeguidorID, PARAM_UsuarioSeguidoID);
	
    -- Eliminar Seguidor --
	ELSEIF PARAM_Accion = 'DELETE' THEN
        DELETE FROM UsuarioSeguidor
        WHERE UsuarioSeguidorID = PARAM_UsuarioSeguidorID AND UsuarioSeguidoID = PARAM_UsuarioSeguidoID;
    
    END IF;
    
    -- Confirmar la transacción
    COMMIT;

END //
DELIMITER ;

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

CALL sp_EventosAdmin ('REPORTE', 2);

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


DELIMITER //


-- todo esto es lo que yo empece a hacer yo -- 
-- stored procedures

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
DROP PROCEDURE IF EXISTS sp_GetChatParticipantInfo;


DELIMITER //

DROP PROCEDURE IF EXISTS sp_GetOrCreateChat; -- Añadir por si ya existe y necesitas reemplazarlo
//

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

DELIMITER //

DROP PROCEDURE IF EXISTS sp_EnviarMensaje; -- Para asegurar que se actualiza si ya existe
//
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

DELIMITER //

DROP PROCEDURE IF EXISTS sp_CrearPublicacion;
//
CREATE PROCEDURE sp_CrearPublicacion (
    IN p_UsuarioID INT,
    IN p_ContenidoPublicacion VARCHAR(100),
    IN p_ContenidoMultimedia LONGBLOB
)
-- Etiquetar el bloque principal del procedimiento
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

    INSERT INTO Publicacion (UsuarioID, ContenidoPublicacion, FechaPublicacion, PublicacionPadreID)
    VALUES (p_UsuarioID, p_ContenidoPublicacion, NOW(), NULL);

    SET v_NewPublicacionID = LAST_INSERT_ID();

    IF v_NewPublicacionID IS NULL OR v_NewPublicacionID = 0 THEN
        SET v_StatusMessage = 'Error: No se pudo obtener el ID de la nueva publicación.';
        ROLLBACK;
        SELECT FALSE AS Success, v_StatusMessage AS StatusMessage, NULL AS PublicacionID;
        LEAVE proc_block; -- Usar la etiqueta para salir del procedimiento
    END IF;

    IF p_ContenidoMultimedia IS NOT NULL THEN
        INSERT INTO Multimedia (PublicacionID, TipoMultimedia)
        VALUES (v_NewPublicacionID, p_ContenidoMultimedia);

        IF ROW_COUNT() = 0 THEN
            SET v_StatusMessage = 'Error: La publicación se creó pero no se pudo guardar el archivo multimedia.';
            ROLLBACK;
            SELECT FALSE AS Success, v_StatusMessage AS StatusMessage, v_NewPublicacionID AS PublicacionID;
            LEAVE proc_block; -- Usar la etiqueta para salir del procedimiento
        END IF;
    END IF;

    COMMIT;
    SET v_Success = TRUE;
    SET v_StatusMessage = 'Publicación creada exitosamente.';
    
    SELECT v_Success AS Success, v_StatusMessage AS StatusMessage, v_NewPublicacionID AS PublicacionID;

END //

DELIMITER ;

