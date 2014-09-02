#!/bin/sh

echo .schema | sqlite3 db/dev.db >db/schema.sql
