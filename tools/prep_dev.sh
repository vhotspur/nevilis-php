#!/bin/sh

DB_FILE="db/dev.db"
SQLITE=`which sqlite3`
PHP=`which php`

[ -z "$SQLITE" ] && exit 1
[ -z "$PHP" ] && exit 1

if [ "$1" == "--force" ]; then
	rm -f $DB_FILE
	rm -rf dev_files
fi

[ -e "dev_files" ] && { echo "dev_files/ already exists"; exit 1; }
[ -e "$DB_FILE" ] && { echo "$DB_FILE already exists"; exit 1; }

# Prepare the tables
$SQLITE $DB_FILE <db/schema.sql

# Insert the data
$SQLITE $DB_FILE <<'EOF_DEV_DATA'
BEGIN TRANSACTION;
-- Password will be fixed later
INSERT INTO "user" VALUES('admin','xxx','Super User','admin');
INSERT INTO "user" VALUES('user1','xxx','User One',NULL);

INSERT INTO "course" VALUES('c1','Example', 'example run');

INSERT INTO "courseusers" VALUES('user1','c1');

INSERT INTO "assignment" VALUES('hw1','Homework #1','This is some description');
INSERT INTO "assignment" VALUES('hw2','Second homework','Lorem Ipsum Dolor Sit Amet.');
INSERT INTO "assignment" VALUES('hw3','Last homework','Lorem Ipsum Dolor Sit Amet.');
INSERT INTO "assignmentfile" VALUES(1,'xxx','hw2','Picture','Your picture',45000,'jpeg');
INSERT INTO "assignmentfile" VALUES(2,'yyy','hw1','Some PNG','First assignment picture',500,'png');
INSERT INTO "assignmentfile" VALUES(3,'zzz','hw2','Two','Another picture',45000,'jpeg');
INSERT INTO "assignmentfile" VALUES(4,'aaa','hw3','Lorem Ipsum','Another picture',45000,'jpeg');

-- Deadlines will be fixed later
INSERT INTO "courseassignment" VALUES('c1','hw1', NULL, NULL);
INSERT INTO "courseassignment" VALUES('c1','hw2', NULL, NULL);
INSERT INTO "courseassignment" VALUES('c1','hw3', NULL, NULL);

INSERT INTO "submittedfile" VALUES(1,'user1',2, NULL, '.png', 'image/png');
INSERT INTO "submittedfile" VALUES(2,'user1',1, NULL, '.jpg', 'image/jpeg');
INSERT INTO "submittedfile" VALUES(3,'user1',3, NULL, '.jpeg', NULL);
INSERT INTO "grade" VALUES('user1','hw1',2,1,'Not bad, really :-).');
INSERT INTO "grade" VALUES('user1','hw2',null,0,'Fix this!');

DELETE FROM sqlite_sequence;
INSERT INTO "sqlite_sequence" VALUES('submittedfile',3);
INSERT INTO "sqlite_sequence" VALUES('assignmentfile',4);
COMMIT;
EOF_DEV_DATA

# Fix the password
for user in admin user1; do
	echo '<?php printf("UPDATE user SET password=\"%s\" WHERE uid=\"'"$user"'\";\n", password_hash("1234", PASSWORD_DEFAULT));' \
		| $PHP | $SQLITE $DB_FILE
done

# Fix the deadlines
for hw in "hw1 -2 -1" "hw2 -1 +1" "hw3 +1 +2"; do
	hw_id=`echo $hw | ( read a b c; echo $a )`
	hw_deadline=`echo $hw | ( read a b c; echo $b )`
	hw_deadline_noupload=`echo $hw | ( read a b c; echo $c )`
	hw_deadline=`date -d "$hw_deadline week" '+%Y-%m-%d %H:%M'`
	hw_deadline_noupload=`date -d "$hw_deadline_noupload week" '+%Y-%m-%d %H:%M'`
	echo "UPDATE courseassignment SET deadline=\"$hw_deadline\", deadline_noupload=\"$hw_deadline_noupload\" WHERE assignment=\"$hw_id\";" \
		| $SQLITE $DB_FILE
done

# Create the files
mkdir -p dev_files/user1/hw1/
chmod 0777 -R dev_files/
convert -size 20x20 xc:blue dev_files/user1/hw1/yyy.png

# Fix permissions
chmod a+w $DB_FILE
chmod a+w `dirname $DB_FILE`
chmod a+w dev_files
