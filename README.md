stories
=======

A site for collecting stories.

Installation
============
Clone or download repository
Download the Yii framework from http://www.yiiframework.com (I'm using 1.1.10)
Extract Yii to repository root, so it now looks like:
	./public
	./protected
	./Yii-1.1.10.t3566
Set webserver's home directory to this public directory
Create a database using ./protected/data/schema.mysql.sql
Edit ./protected/config/main.php, section 'db' to connect to this database
Make sure the following directories are writable by your webserver user:
    ./protected/runtime
    ./public/assets
