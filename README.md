Stories
=======

A site for collecting stories.

Installation
------------

* Clone or download and extract repository
* Upload this to your webserver
* Download the latest v1.1.* Yii framework from http://www.yiiframework.com (I'm using 1.1.10)
* Upload this to your webserver as well, so it now looks like:
	* ./public
	* ./protected
	* ./Yii-1.1.10.t3566 (or whatever version you downloaded)
* Make sure the following directories are writable by your webserver:
    ./protected/runtime
    ./public/assets
* Set webserver's webroot to this public directory
* Edit configuration files to your situation at:
	* ./protected/config/db.php
	* ./protected/config/mail.php
	* ./protected/config/params.php
* Create a database using ./protected/data/schema.mysql.sql
* Browse to your webserver's address
* Login with username=admin, password=admin
