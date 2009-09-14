<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the "Database Connection"
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database type. ie: mysql.  Currently supported:
				 mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Active Record class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the "default" group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/

$active_group = 'default';
$active_record = FALSE;

// quartz_chem
$db['default']['hostname'] = 'ovid.u.washington.edu:23457';
$db['default']['username'] = 'root';
$db['default']['password'] = 'password';
$db['default']['database'] = 'dev_al_be_quartz_chem';
$db['default']['dbdriver'] = 'mysql';
$db['default']['dbprefix'] = '';
$db['default']['pconnect'] = TRUE;
$db['default']['db_debug'] = TRUE;
$db['default']['cache_on'] = FALSE;
$db['default']['cachedir'] = '';
$db['default']['char_set'] = 'latin1';
$db['default']['dbcollat'] = 'latin1_general_ci';

// Doctrine configuration

// Create dsn from the info above
$db[$active_group]['dsn'] = $db[$active_group]['dbdriver'] .
				'://' . $db[$active_group]['username'] .
				':' . $db[$active_group]['password'] .
				'@' . $db[$active_group]['hostname'] .
				'/' . $db[$active_group]['database'];

// Require Doctrine.php
require_once(realpath(dirname(__FILE__) . '/..') . DIRECTORY_SEPARATOR . 'libraries/Doctrine1/Doctrine.php');

// Set the autoloader
spl_autoload_register(array('Doctrine', 'autoload'));

// Load the Doctrine connection
Doctrine_Manager::connection($db[$active_group]['dsn'], $db[$active_group]['database']);

// Set the model loading to conservative/lazy loading
$manager = Doctrine_Manager::getInstance();
$manager->setAttribute('model_loading', 'conservative');
$manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);

// Load the models for the autoloader
Doctrine::loadModels(realpath(dirname(__FILE__) . '/..') . DIRECTORY_SEPARATOR . 'models');

/* End of file database.php */
/* Location: ./system/application/config/database.php */