CREATE TABLE iplog (
  ip_addr TEXT,
	regname TEXT,
  ac_time DATETIME DEFAULT (datetime('now'))
);

CREATE TABLE pubrg (
  name TEXT,
  prename TEXT,
  tagline TEXT,
  UNIQUE(name, prename, tagline)
);
