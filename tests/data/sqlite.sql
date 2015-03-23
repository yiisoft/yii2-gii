/**
 * This is the database schema for testing Sqlite support of Yii DAO and Active Record.
 * The database setup in config.php is required to perform then relevant tests:
 */

DROP TABLE IF EXISTS "customer";
DROP TABLE IF EXISTS "profile";

CREATE TABLE "profile" (
  id INTEGER NOT NULL,
  description varchar(128) NOT NULL,
  PRIMARY KEY (id)
);

CREATE TABLE "customer" (
  id INTEGER NOT NULL,
  email varchar(128) NOT NULL,
  name varchar(128),
  address text,
  status INTEGER DEFAULT 0,
  profile_id INTEGER,
  PRIMARY KEY (id)
);

INSERT INTO "profile" (description) VALUES ('profile customer 1');
INSERT INTO "profile" (description) VALUES ('profile customer 3');

INSERT INTO "customer" (email, name, address, status, profile_id) VALUES ('user1@example.com', 'user1', 'address1', 1, 1);
INSERT INTO "customer" (email, name, address, status) VALUES ('user2@example.com', 'user2', 'address2', 1);
INSERT INTO "customer" (email, name, address, status, profile_id) VALUES ('user3@example.com', 'user3', 'address3', 2, 2);
