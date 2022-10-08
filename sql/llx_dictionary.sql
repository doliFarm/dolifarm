-- INformations 

CREATE TABLE llx_dolifarm_dictionary(
	rowid INTEGER AUTO_INCREMENT,
		label VARCHAR(255),
		code VARCHAR (32),
		fk_pays INTEGER (11),
		active tinyint(4),
		module varchar(32),
		position INTEGER (11),
        PRIMARY KEY (rowid)
)ENGINE=InnoDB;
