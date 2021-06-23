CREATE TABLE soyinquiry_form (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  form_id VARCHAR(128) UNIQUE,
  name VARCHAR(255),
  config TEXT
);

CREATE TABLE soyinquiry_column(
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  form_id VARCHAR(255),
  column_id VARCHAR(255),
  label VARCHAR(255),
  column_type VARCHAR(255),
  config TEXT,
  is_require INTEGER DEFAULT 0,
  display_order INTEGER DEFAULT 0
);

CREATE TABLE soyinquiry_inquiry (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  tracking_number VARCHAR(255),
  form_id VARCHAR(255) NOT NULL,
  ip_address VARCHAR(128),
  content TEXT,
  data TEXT,
  flag INTEGER DEFAULT 1,
  create_date INTEGER NOT NULL,
  form_url TEXT,
  UNIQUE(form_id, create_date)
);
CREATE INDEX soyinquiry_tracking_number_idx on soyinquiry_inquiry(tracking_number);

CREATE TABLE soyinquiry_serverconfig(
	config TEXT
);

CREATE TABLE soyinquiry_comment (
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	inquiry_id INTEGER NOT NULL,
	title VARCHAR(255),
	author VARCHAR(255),
	content TEXT,
	create_date INTEGER NOT NULL,
	UNIQUE(inquiry_id, create_date)
);

CREATE TABLE soyinquiry_data_sets(
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	class_name VARCHAR(255) UNIQUE,
	object_data TEXT
);

CREATE TABLE soyinquiry_ban_ip_address(
	ip_address VARCHAR(128) NOT NULL UNIQUE,
	log_date INTEGER
);
