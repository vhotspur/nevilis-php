#!/bin/sh

DB_FILE="db/dev.db"
SQLITE=`which sqlite3`

if [ "$1" == "--force" ]; then
	rm -f $DB_FILE
	rm -rf dev_files
fi

[ -e "dev_files" ] && exit 1
[ -e "$DB_FILE" ] && exit 1

# Prepare the tables
$SQLITE $DB_FILE <db/schema.sql

# Insert the data
$SQLITE $DB_FILE <<'EOF_DEV_DATA'
BEGIN TRANSACTION;
INSERT INTO "user" VALUES('admin','1234','Super User','admin');
INSERT INTO "user" VALUES('user1','1234','User One',NULL);

INSERT INTO "course" VALUES('c1','Example course');

INSERT INTO "courseusers" VALUES('user1','c1');

INSERT INTO "assignment" VALUES('hw1','Homework #1','This is some description');
INSERT INTO "assignment" VALUES('hw2','Second homework','Lorem Ipsum Dolor Sit Amet.');
INSERT INTO "assignmentfile" VALUES(1,'xxx.jpg','hw2','Picture','Your picture',45000,'jpeg');
INSERT INTO "assignmentfile" VALUES(2,'yyy.png','hw1','Some PNG','First assignment picture',500,'png');
INSERT INTO "assignmentfile" VALUES(3,'zzz.jpg','hw2','Two','Another picture',45000,'jpeg');

INSERT INTO "courseassignment" VALUES('c1','hw1');
INSERT INTO "courseassignment" VALUES('c1','hw2');

INSERT INTO "submittedfile" VALUES(1,'user1',2);
INSERT INTO "submittedfile" VALUES(2,'user1',1);
INSERT INTO "grade" VALUES('user1','hw1',2,'Not bad, really :-).');

DELETE FROM sqlite_sequence;
INSERT INTO "sqlite_sequence" VALUES('submittedfile',2);
INSERT INTO "sqlite_sequence" VALUES('assignmentfile',3);
COMMIT;
EOF_DEV_DATA

# Create the files
mkdir -p dev_files/user1/hw1/
convert -size 20x20 xc:blue dev_files/user1/hw1/yyy.png

# Fix permissions
chmod a+w $DB_FILE
chmod a+w `dirname $DB_FILE`
