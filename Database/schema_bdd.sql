CREATE TABLE IF NOT EXISTS user (
    id              VARCHAR(8)                  PRIMARY KEY,
    userType        VARCHAR(10)     NOT NULL    DEFAULT 'USER',
    email           VARCHAR(255)    NOT NULL    UNIQUE,
    login           VARCHAR(255)    NOT NULL    UNIQUE,
    password        VARCHAR(255)    NOT NULL,
    dateNaissance   DATE            NOT NULL,
    isVerified      BOOLEAN         NOT NULL    DEFAULT FALSE,
    registeredAt    TIMESTAMP       NOT NULL    DEFAULT CURRENT_TIMESTAMP,
    lastLogin       TIMESTAMP       NOT NULL    DEFAULT CURRENT_TIMESTAMP,
    grade           VARCHAR(20)     NOT NULL    DEFAULT 'PLAYER',
    is_deleted      BOOLEAN         NOT NULL    DEFAULT FALSE,

    CHECK (userType IN ('USER', 'COMPUTER')),
    CHECK (grade IN ('PLAYER', 'ADMIN')),
    CHECK (lastLogin >= registeredAt)
);

CREATE TABLE IF NOT EXISTS game (
    id          INT                             PRIMARY KEY AUTO_INCREMENT,
    winner      VARCHAR(8),
    status      VARCHAR(20)         NOT NULL    DEFAULT 'IN_PROGRESS',
    dateStart   TIMESTAMP           NOT NULL    DEFAULT CURRENT_TIMESTAMP,
    dateEnd     TIMESTAMP           NULL,

    FOREIGN KEY (winner) REFERENCES user(id),
    CHECK (status IN ('IN_PROGRESS', 'COMPLETED', 'CANCELED')),
    CHECK (dateEnd IS NULL OR dateEnd >= dateStart)
);

CREATE TABLE IF NOT EXISTS player_game (
    player_id VARCHAR(8),
    game_id INT,
  
    PRIMARY KEY (player_id, game_id),
    FOREIGN KEY (player_id) REFERENCES user(id),
    FOREIGN KEY (game_id) REFERENCES game(id)
);


CREATE TABLE IF NOT EXISTS sessions (
    user_id             VARCHAR(8)      NOT NULL,
    session_id          VARCHAR(255)    NOT NULL,
    expiration_date     BIGINT          NOT NULL,

    PRIMARY KEY (user_id, session_id),
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE
);

CREATE OR REPLACE VIEW nbPartieParJoueur AS (
  SELECT 
    user.id AS userId,
    COUNT(user.id) AS parties_jouees
  FROM user
  INNER JOIN player_game
    ON player_id = user.id
  GROUP BY user.id
  ORDER BY user.id
);

CREATE OR REPLACE VIEW nbPartiesGagneesParJoueur AS (
  SELECT 
    user.id AS userId,
    COUNT(user.id) AS parties_gagnees
  FROM user
  INNER JOIN game
    ON game.winner = user.id
  GROUP BY user.id
  ORDER BY user.id
);

CREATE OR REPLACE VIEW statsAdminPage AS (
  SELECT
    user.id,
    user.login,
    user.dateNaissance,
    COALESCE(nbPartieParJoueur.parties_jouees, 0) AS nbParties,
    COALESCE(nbPartiesGagneesParJoueur.parties_gagnees, 0) AS nbVictoires,
    COALESCE(nbPartieParJoueur.parties_jouees, 0) - COALESCE(nbPartiesGagneesParJoueur.parties_gagnees, 0) AS nbDefaites
  FROM user
  LEFT JOIN nbPartieParJoueur
    ON user.id = nbPartieParJoueur.userId
  LEFT JOIN nbPartiesGagneesParJoueur 
    ON user.id = nbPartiesGagneesParJoueur.userId
	WHERE user.is_deleted = FALSE
);