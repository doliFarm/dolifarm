-- Information about the production order
-- TODO(4)- we are supposing here that the final product need just one raw material

CREATE TABLE llx_dolifarm_certificates(
	rowid INTEGER AUTO_INCREMENT PRIMARY KEY,
		ref VARCHAR(128),
		label VARCHAR(255),
		fk_farm INTEGER (11),
		description VARCHAR(255),
		date_issue DATE,
		date_expire DATE,
		note_public VARCHAR(1024),
		note_private VARCHAR(1024),
        status INTEGER (11),
		file_ref  VARCHAR(255),
		date_validation  DATE,
		tms TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
		author VARCHAR(30)
) Engine=InnoDB;