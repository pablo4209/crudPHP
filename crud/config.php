<?php


//USAR CLASE DE CONEXION PROPIA?
define( "USE_DB" , 1 );

//distinto de 0 para activarlo
define( 'CRUD_DEBUG' , 1 );

//directorios usados
define( "ROOT" , "/var/www/html/crudPHP/" );
define ( "MODEL" , ROOT . "model/" );
define ( "CRUD_AJAX" ,  ROOT . "crud/application/ajax/" );


 //constantes de conexion
define('C_DB_HOST', '127.0.0.1');
define('C_DB_NAME', 'bd_prueba');
define('C_DB_USER', 'root');
define('C_DB_PASS', 'root');
define('C_DB_CHAR', 'utf8');

if(USE_DB){
  require_once( MODEL . 'database.php' );
  require_once( ROOT . 'crud/application/functions/log.php' );
}
require_once( MODEL . 'tiposModel.php' );
require_once( ROOT . 'crud/formModel.php' );
require_once( ROOT . 'crud/crudModel.php' );


if(CRUD_DEBUG){
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
}

 ?>
