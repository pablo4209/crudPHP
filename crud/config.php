<?php


//para usar tipo enumeracion ej: if( $campo == tipoDato::INTEGER ) ...sentencias...;
abstract class tipoDato {
    const T_INT = 0;
    const T_STR = 1;
    const T_DATETIME = 2;
    const T_DATE = 3;
    const T_TIME = 4;
    const T_CHECK = 5;

}
//distinto de 0 para activarlo
define( 'CRUD-DEBUG' , 1 );

//directorios usados
define( "ROOT" , "/var/www/html/crudPHP/" );
define ( "MODEL" , ROOT . "model/" );
define ( "AJAX" ,  ROOT . "application/ajax/" );


 //constantes de conexion
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'bd_prueba');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_CHAR', 'utf8');

require_once( MODEL . 'database.php' );
require_once( ROOT . 'crud/formModel.php' );
require_once( ROOT . 'crud/crudModel.php' );



if(CRUD-DEBUG){
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
}

 ?>
