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

