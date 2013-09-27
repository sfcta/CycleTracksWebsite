-- SFCTA data model for bike modeling contract
-- mattpaul
-- 08.12.2009

CREATE TABLE trip (
	id      INTEGER UNSIGNED AUTO_INCREMENT,
	user_id INTEGER UNSIGNED,
	purpose VARCHAR(255),
	notes VARCHAR(255),
	start   TIMESTAMP,
	stop    TIMESTAMP,
	n_coord INTEGER UNSIGNED,
	PRIMARY KEY ( id ),
	UNIQUE KEY ( user_id, start )
) ENGINE=INNODB;

CREATE TABLE coord (
	trip_id   INTEGER UNSIGNED,
	recorded  TIMESTAMP,
	latitude  DOUBLE,
	longitude DOUBLE,
	altitude  DOUBLE,
	speed     DOUBLE,
	hAccuracy DOUBLE,
	vAccuracy DOUBLE,
	PRIMARY KEY ( trip_id, recorded )
) ENGINE=INNODB;

CREATE TABLE user (
	id        INTEGER UNSIGNED AUTO_INCREMENT,
	created   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	device    VARCHAR(32),
	email     VARCHAR(64),
	age       VARCHAR(32),
	gender    VARCHAR(32),
	homeZIP   VARCHAR(32),
	schoolZIP VARCHAR(32),
	workZIP   VARCHAR(32),
	cycling_freq  TINYINT default NULL,
	PRIMARY KEY ( id ),
	UNIQUE KEY ( device )
) ENGINE=INNODB;

CREATE TABLE cycling_freq (
	id   TINYINT,
	text VARCHAR(32),
	PRIMARY KEY ( id )
) ENGINE=INNODB;

INSERT INTO cycling_freq ( id, text ) VALUES ( 0, "Less than once a month" );
INSERT INTO cycling_freq ( id, text ) VALUES ( 1, "Several times per month" );
INSERT INTO cycling_freq ( id, text ) VALUES ( 2, "Several times per week" );
INSERT INTO cycling_freq ( id, text ) VALUES ( 3, "Daily" );

