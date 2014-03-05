CREATE TABLE activity (
   id INTEGER PRIMARY KEY AUTOINCREMENT,
   title varchar(200) NOT NULL,
   type char DEFAULT 'p',
   track tinyint DEFAULT '0',
   abstract text,
   user_id INT unsigned DEFAULT NULL,
   duration time default NULL,
   location_id INT unsigned DEFAULT NULL,
   day tinyint unsigned DEFAULT NULL,
   notes text, 
	 starttime time DEFAULT NULL, 
   url_slides TEXT DEFAULT NULL, 
   url_paper TEXT DEFAULT NULL, 
   url_stream TEXT DEFAULT NULL, 
   url_audio TEXT DEFAULT NULL, 
   url_misc TEXT DEFAULT NULL, 
   url_image TEXT DEFAULT NULL, 
   status INTEGER DEFAULT 1,
   serial INTEGER DEFAULT 1,
   editlock datetime default 0,
   UNIQUE (title)
);

CREATE TABLE location (
   id INTEGER PRIMARY KEY AUTOINCREMENT,
   name varchar(50) NOT NULL,
   editlock datetime default 0
);

CREATE TABLE user (
   id INTEGER PRIMARY KEY AUTOINCREMENT,
   name text NOT NULL,
   reghandle text DEFAULT NULL,
   editlock datetime default 0,
   email TEXT DEFAULT NULL,
   vip INT DEFAULT 1, 
   flags INT DEFAULT 0, 
   bio TEXT DEFAULT NULL,
   tagline TEXT DEFAULT NULL,
   url_image TEXT DEFAULT NULL, 
   url_person TEXT DEFAULT NULL, 
   url_institute TEXT DEFAULT NULL, 
   url_project TEXT DEFAULT NULL, 
   udate datetime DEFAULT 0,
   UNIQUE (name)
);

CREATE TABLE auth (
   user_id INTEGER NOT NULL,
   passhash TEXT DEFAULT NULL,
   provider TEXT DEFAULT NULL,
   handle   TEXT DEFAULT NULL,
   ukey     TEXT DEFAULT NULL,
   udate    datetime DEFAULT 0,
   flags INT DEFAULT 0, 
   UNIQUE (user_id),
   UNIQUE (ukey),
	 FOREIGN KEY(user_id) REFERENCES user(id) ON DELETE CASCADE
);

CREATE TABLE usermap (
   activity_id INTEGER,
   user_id INTEGER,
   position INTEGER,
   UNIQUE (activity_id, user_id),
	 FOREIGN KEY(user_id) REFERENCES user(id) ON DELETE CASCADE,
	 FOREIGN KEY(activity_id) REFERENCES activity(id) ON DELETE CASCADE
);
