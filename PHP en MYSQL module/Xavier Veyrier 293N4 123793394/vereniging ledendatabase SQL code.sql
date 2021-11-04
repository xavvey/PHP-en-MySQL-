CREATE TABLE postcode (
	postcode VARCHAR(10),
	adres VARCHAR(50),
	woonplaats VARCHAR(50),
	PRIMARY KEY(postcode)
);

CREATE TABLE lid (
	lidnummer int(10) UNSIGNED NOT NULL,
	naam VARCHAR(50),
	voornaam VARCHAR(50),
	huisnummer VARCHAR(15),
	postcode VARCHAR(10),
	PRIMARY KEY (lidnummer),
	FOREIGN KEY (postcode) REFERENCES postcode(postcode)
);

CREATE TABLE email (
	email VARCHAR(50),
	lidnummer int(10) UNSIGNED,
	PRIMARY KEY (email),
	FOREIGN KEY(lidnummer) REFERENCES lid(lidnummer)
);

CREATE TABLE telefoonnummer (
	telefoonnummer VARCHAR(50),
	lidnummer int(10) UNSIGNED,
	PRIMARY KEY (telefoonnummer),
	FOREIGN KEY(lidnummer) REFERENCES lid(lidnummer)
);