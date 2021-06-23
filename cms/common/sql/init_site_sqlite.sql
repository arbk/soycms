create table Entry(
 id INTEGER primary key AUTOINCREMENT,
 title VARCHAR(255),
 alias VARCHAR(255),
 content TEXT,
 more TEXT,
 cdate INTEGER,
 udate INTEGER,
 description TEXT,
 openPeriodStart INTEGER,
 openPeriodEnd INTEGER,
 isPublished INT default 0,
 style TEXT,
 author VARCHAR(255)
);
create index Entry_for_list on Entry(cdate desc, title asc, id desc);
create index Entry_for_list_by_title on Entry(title asc, cdate asc, id asc);
create index entry_udate on Entry(udate desc);

create table EntryHistory(
 id INTEGER primary key AUTOINCREMENT,
 entry_id INTEGER,
 title VARCHAR(255),
 content TEXT,
 more TEXT,
 additional TEXT,
 is_published INTEGER NOT NULL DEFAULT 0,
 cdate INTEGER not null,
 author VARCHAR(255),
 user_id INTEGER,
 action_type INTEGER NOT NULL DEFAULT 0,
 action_target INTEGER,
 change_title INTEGER NOT NULL DEFAULT 0,
 change_content INTEGER NOT NULL DEFAULT 0,
 change_more INTEGER NOT NULL DEFAULT 0,
 change_additional INTEGER NOT NULL DEFAULT 0,
 change_is_published INTEGER NOT NULL DEFAULT 0
);
create index entry_history_entry_id on EntryHistory(entry_id);

create table EntryComment(
 id INTEGER primary key AUTOINCREMENT,
 entry_id INTEGER references Entry(id),
 title VARCHAR(255),
 author VARCHAR(255),
 body TEXT,
 is_approved INTEGER default 0,
 mail_address VARCHAR(255),
 url VARCHAR(255),
 extra_values TEXT,
 submitdate INTEGER
);

create table EntryTrackback(
 id INTEGER primary key AUTOINCREMENT,
 entry_id INTEGER references Entry(id),
 title VARCHAR(255),
 url VARCHAR(255),
 blog_name VARCHAR(255),
 excerpt TEXT,
 extra_values TEXT,
 submitdate INTEGER,
 certification INTEGER default 0
);

create table EntryAttribute(
 entry_id INTEGER NOT NULL,
 entry_field_id VARCHAR(255) NOT NULL,
 entry_value TEXT,
 entry_extra_values TEXT,
 unique(entry_id,entry_field_id)
);

create index EntryAttribute_entry_id on EntryAttribute(entry_id);
create index EntryAttribute_entry_field_id on EntryAttribute(entry_field_id);

create table Label(
 id INTEGER primary key AUTOINCREMENT,
 caption VARCHAR(255),
 description TEXT,
 alias VARCHAR(255),
 icon VARCHAR(255),
 display_order INTEGER default 2147483647,
 color INTEGER default 0,
 background_color INTEGER default 16777215
);
create index Label_display_order on Label(display_order asc);

create table EntryLabel(
 entry_id INTEGER references Entry(id),
 label_id INTEGER references Label(id),
 display_order INTEGER default 2147483647,
 unique(entry_id,label_id)
);
create index EntryLabel_display_order on EntryLabel(display_order asc);

create table Template(
 id INTEGER primary key AUTOINCREMENT,
 name VARCHAR(255),
 contents VARCHAR(255),
 create_date INTEGER
);

create table Page(
 id INTEGER primary key AUTOINCREMENT,
 title VARCHAR(255),
 template TEXT,
 uri VARCHAR(255) unique,
 page_type INTEGER default 0,
 page_config TEXT,
 openPeriodStart INTEGER,
 openPeriodEnd INTEGER,
 isPublished int default 0,
 isTrash int default 0,
 parent_page_id INTEGER,
 udate INTEGER,
 icon VARCHAR(255)
);

create table TemplateHistory(
 id INTEGER primary key AUTOINCREMENT,
 page_id INTEGER references Page(id),
 contents TEXT,
 update_date INTEGER
);

create table Block(
 id INTEGER primary key AUTOINCREMENT,
 soy_id VARCHAR(255),
 page_id INTEGER references Page(id),
 class VARCHAR(255),
 object TEXT,
 unique(soy_id,page_id)
);

create table SiteConfig(
 name VARCHAR(255),
 description VARCHAR(255),
 siteConfig TEXT,
 charset INTEGER default 1
);

CREATE TABLE CmsMemo(
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	content TEXT,
	create_date INTEGER NOT NULL,
	update_date INTEGER NOT NULL
);

create table soycms_data_sets(
 id INTEGER primary key AUTOINCREMENT,
 class_name VARCHAR(255) unique,
 object_data TEXT
);
