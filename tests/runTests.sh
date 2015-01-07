#!/bin/bash

echo "starting up ..."
echo

echo "---- Config ----"
../vendor/bin/phpunit --bootstrap bootstrap.php ../src/config/Config config/ConfigTest.php
echo

echo "---- DatabaseAccess ----"
../vendor/bin/phpunit --bootstrap bootstrap.php ../src/database/DatabaseAccess database/DatabaseAccessTest.php
echo

echo "---- FieldSelection ----"
../vendor/bin/phpunit --bootstrap bootstrap.php ../src/database/FieldSelection database/FieldSelectionTest.php
echo

echo "---- MYSQL Selection ----"
../vendor/bin/phpunit --bootstrap bootstrap.php ../src/database/mysql/Selection database/MysqlSelectionTest.php
echo

echo "---- MYSQL Database ----"
../vendor/bin/phpunit --bootstrap bootstrap.php ../src/database/mysql/Database database/MysqlDatabaseTest.php
echo

echo "---- Cache ----"
../vendor/bin/phpunit --bootstrap bootstrap.php ../src/cache/Cache cache/CacheTest.php
echo

echo
echo "done"
