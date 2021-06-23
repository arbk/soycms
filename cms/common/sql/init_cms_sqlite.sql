
CREATE TABLE Site (
 id INTEGER PRIMARY KEY AUTOINCREMENT,
 site_id VARCHAR(255) UNIQUE,
 site_type INTEGER DEFAULT 1,
 site_name VARCHAR(255),
 isDomainRoot INTEGER DEFAULT 0,
 url TEXT,
 path TEXT,
 data_source_name TEXT UNIQUE
);

CREATE TABLE Administrator (
 id INTEGER PRIMARY KEY AUTOINCREMENT,
 user_id VARCHAR(255) NULL UNIQUE,
 user_password VARCHAR(255) NULL,
 default_user INTEGER DEFAULT 0,
 name VARCHAR(255),
 email VARCHAR(255),
 token VARCHAR(255) UNIQUE,
 token_issued_date INTEGER
);

CREATE TABLE AdministratorAttribute (
 admin_id INTEGER NOT NULL,
 admin_field_id VARCHAR(255) NOT NULL,
 admin_value TEXT,
 UNIQUE(admin_id, admin_field_id)
);

CREATE TABLE SiteRole (
 id INTEGER PRIMARY KEY AUTOINCREMENT,
 user_id INTEGER,
 site_id INTEGER,
 is_limit INTEGER DEFAULT 0,
 UNIQUE(user_id,site_id),
 FOREIGN KEY(user_id)
  REFERENCES Administrator(id),
 FOREIGN KEY(site_id)
  REFERENCES Site(id)
);

CREATE TABLE AppRole (
 id INTEGER PRIMARY KEY AUTOINCREMENT,
 app_id VARCHAR(255),
 user_id INTEGER,
 app_role INTEGER,
 app_role_config TEXT,
 UNIQUE(user_id,app_id),
 FOREIGN KEY(user_id)
  REFERENCES Administrator(id) 
);

CREATE TABLE soycms_admin_data_sets(
 id INTEGER PRIMARY KEY AUTOINCREMENT,
 class_name VARCHAR(255) UNIQUE,
 object_data TEXT
);

CREATE TABLE LoginErrorLog (
 id INTEGER PRIMARY KEY AUTOINCREMENT,
 ip VARCHAR(128) UNIQUE NOT NULL,
 count INTEGER NOT NULL DEFAULT 0,
 successed INTEGER NOT NULL DEFAULT 0,
 start_date INTEGER NOT NULL,
 update_date INTEGER NOT NULL
);

CREATE TABLE AutoLogin (
	user_id INTEGER NOT NULL,
	token CHAR(32) NOT NULL,
	time_limit INTEGER,
	UNIQUE(user_id, token)
);

CREATE TABLE Memo(
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	content TEXT,
	create_date INTEGER NOT NULL,
	update_date INTEGER NOT NULL
);
