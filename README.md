ChemDB
======

ChemDB is a PHP-based web application that assists in the collection of data
generated during the preparation of samples for cosmogenic nuclide
measurements. Currently it supports Al-26 and Be-10. ChemDB was developed for
use at the University of Washington's Cosmogenic Nuclide Laboratory. It is
open-source software, so you are free to use it and change it to adapt it to
your needs.

Features
--------

- Collect sample location and field notes.
- Organize samples by Project.
- Store all of your sample preparation data in one place.
- Collect weighings and measurements at each step in the chemistry procedure.
- Import ICPMS data.
- Generate reports by sample batch.

Installation
------------

ChemDB requires a working installation of php5 and has been tested with the
apache2 webserver. The chemdb directory should be placed in the web content
directory of the web server. You will need to be running an SQL server.
ChemDB is known to work with MySQL and MariaDB, but should support other
database servers that are supported by Doctrine1.

To install using git:

    git clone https://github.com/cosmolab/chemdb.git /path/to/chemdb
    cd /path/to/chemdb

Now we need to install our dependencies (Doctrine version 1 and CodeIgniter).
CodeIgniter is the web framework on which ChemDB is built. Doctrine is a
database abstraction layer and object-relational mapper used to interact
with the internal database. First, install Composer
(https://getcomposer.org/).

If you installed the composer executable somewhere in your path,
execute the following command in the project directory:

    composer install

Or if you downloaded the composer.phar, execute:

    php composer.phar install

Basic Configuration
-------------------

You will need to edit configuration files to initially set up the application.

Copy site-specific configuration template files to their appropriate locations:

    cp app/config/config.php.changeme app/config/config.php
    cp app/config/database.php.changeme app/config/database.php

The changeme files may be updated in future releases

In `app/config/config.php`, set the base URL of the site:

    $config['base_url'] = 'http://localhost/path/to/chemdb/'

In `chemdb/app/config/database.php`, set the hostname, username, password and
database name for your SQL server. The default SQL server is mysql. If you use
a different database you will need to change $db['default']['dbdriver'] to the
appropriate value according to the Doctrine 1.x connection documentation,
located at:

http://docs.doctrine-project.org/projects/doctrine1/en/latest/en/manual/introduction-to-connections.html

General Doctrine 1 documentation can be found at:

http://docs.doctrine-project.org/projects/doctrine1/en/latest/en/index.html

To configure your database to connect on a unix socket, set your hostname as:

    $db['default']['hostname'] = 'unix(/path/to/socket)';

Once your database is properly configured, run the setup script to create the
databases and tables. From the chemdb directory, run:

    app/setup

Additional command line line tools are available using the doctrine script. See
a summary of the commands by running:

    app/doctrine

Or for more detail:

    app/doctrine help

After installation, if the links on the welcome page are not working and
returning 404 errors, then you may have to change the `$config['index_page']`
variable from `index.php` to `index.php?`. For more information see:
https://www.codeigniter.com/user_guide/installation/troubleshooting.html

Finally, be sure that the webserver/php user has permission to write files to
the directory `app/sessions/`.

Logging
-------

Logging is disabled by default. To enable logging you will need to ensure that
the user under which PHP runs has write access to the `app/logs/` directory.

Enable logging by setting `$config['log_threshold']` in `app/config/config.php`
to a non-zero value. See the comments in that section for threshold values.

Encrypted Sessions
------------------

ChemDB uses encrypted sessions. You will need to set a unique encryption key.
In `app/config/config.php`, set `$config['encryption_key']` to a 32
character passphrase/key containing numbers and upper- and lower-case letters.

A Note of Logins and Security
-----------------------------

By default, ChemDB does not provide a login mechanism. Unless you want the
database to be publicly accessible (not recommended), you will need to
limit access only to users who you want to allow to edit the database.
If you are at a university or other large institution,
they may provide such a mechanism that you can put into your .htaccess
file in the base of the installation directory.
Otherwise you can configure limited access with
password protection by using an .htpasswd file.
See the documentation for your web server on how to create one.

Test the installation
---------------------

Once you have finished the above tasks, you should be able to navigate to the
location where you installed ChemDB on your webserver and load the front page.

Seeking help
------------

If you run in to problems, find a bug, or wish to see an improvement,
please create an issue on GitHub (https://github.com/cosmolab/chemdb/issues)
and I will do my best to help you get it resolved.
