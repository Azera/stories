<?php

/**
 * Database connection.
 * For SQLite:		'connectionString'=>'sqlite:protected/data/stories.db',
 * For MySQL:		'connectionString'=>'mysql:host=localhost;dbname=stories',
 * For PostgreSQL:	'connectionString'=>'pgsql:host=localhost;port=5432;dbname=stories',
 * For SQL Server:	'connectionString'=>'mssql:host=localhost;dbname=stories',
 * For Oracle:		'connectionString'=>'oci:dbname=//localhost:1521/stories',
 */
return array(
	'connectionString' => 'mysql:host=localhost;dbname=stories',
	'emulatePrepare' => true,	// MySQL only
	'username' => 'stories',	// Not for SQLite
	'password' => 'stories',	// Not for SQLite
);