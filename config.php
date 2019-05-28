<?php


//USAR CLASE DE CONEXION PROPIA?
define( "USE_DB" , 1 );

//distinto de 0 para activarlo
define( 'CRUD_DEBUG' , 1 );

//activar generar log
define ( "CRUD_LOG" , 0 );

//directorios usados
define( "CRUD_ROOT" , "" ); // ACA INDICAR RUTA AL DIRECTORIO PRINCIPAL DEL CRUD EN EL PROYECTO DONDE SE USA
define ( "MODEL" , CRUD_ROOT . "model/" );
define ( "CRUD_AJAX" ,  CRUD_ROOT . "crud/application/ajax/" );


 //constantes de conexion
define('C_DB_HOST', '127.0.0.1');
define('C_DB_NAME', 'bd_prueba');
define('C_DB_USER', 'root');
define('C_DB_PASS', 'root');
define('C_DB_CHAR', 'utf8');

if(USE_DB){
  require_once( MODEL . 'database.php' );
  if(CRUD_LOG)require_once( CRUD_ROOT . 'crud/application/functions/log.php' );
}
require_once( MODEL . 'tiposModel.php' );
require_once( CRUD_ROOT . 'crud/formModel.php' );
require_once( CRUD_ROOT . 'crud/crudModel.php' );


if(CRUD_DEBUG){
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
}

 ?>
