CREATE TABLE `courseusers` (
	`user`	TEXT,
	`course`	TEXT
);
CREATE TABLE "course" (
	`cid`	TEXT NOT NULL,
	`name`	TEXT NOT NULL,
	`adminname` TEXT,
	PRIMARY KEY(cid)
);
CREATE TABLE `assignment` (
	`aid`	TEXT NOT NULL,
	`name`	TEXT NOT NULL,
	`description`	TEXT,
	PRIMARY KEY(aid)
);
CREATE TABLE `courseassignment` (
	`course`	TEXT,
	`assignment`	TEXT,
	`deadline`	DATETIME,
	`deadline_noupload`	DATETIME
);
CREATE TABLE "submittedfile" (
	`sfid`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
	`user`	TEXT NOT NULL,
	`file`	INTEGER NOT NULL,
	`upload_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
	`extension` TEXT,
	`mime` TEXT
);
CREATE TABLE `assignmentcomment` (
	`assignment`	TEXT,
	`user`	TEXT,
	`comment`	TEXT
);
CREATE TABLE "grade" (
	`user`	TEXT,
	`assignment`	TEXT,
	`grade`	INTEGER,
	`locked` INTEGER NOT NULL DEFAULT 0,
	`comment`	TEXT
);
CREATE TABLE "assignmentfile" (
	`afid`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
	`filename`	TEXT NOT NULL,
	`assignment`	TEXT NOT NULL,
	`name`	TEXT,
	`description`	TEXT NOT NULL,
	`maxsize`	INTEGER NOT NULL,
	`validation`	TEXT
);
CREATE TABLE "user" (
	`uid`	TEXT NOT NULL,
	`password`	TEXT NOT NULL,
	`name`	TEXT NOT NULL,
	`roles`	TEXT,
	PRIMARY KEY(uid)
);
