<?php

abstract class crudArg{
  const C_NOMBRE_CAMPO = 0;	//nombre verdadero que figura en la tabla de la bd
  const C_TIPO_CAMPO = 1;	//usando la clase tipoDato, para saber de que tipo es
  const C_ALIAS = 2;		//nombre para mostrar en listado o label de los input
  const C_LISTAR = 3;		//mostrarlo en el listado
  const C_EDITAR = 4;		//el campo es editable
  const C_REQUERIDO = 5;	//validacion
  const C_VALUE = 6;  //  valor a mostrar por defecto. Si se trata de un select value contiene un array
                      //  con la forma: { tabla , id , descripcion , seleccionado , descripcion2 , where }  de donde se obtienen los datos
  const C_TYPE = 7;	//campo de texto, para la validacion (ej: "email")
  const C_MIN = 8;	//minlenght del input (ej: 2)
  const C_MAX = 9;	//maxlenght del input
  const C_PLACE = 10;	//placeholder del input
  const C_CLASS = 11;  //agregar alguna clase extra, para validar puede ser util


}

//para usar tipo enumeracion ej: if( $campo == tipoDato::INTEGER ) ...sentencias...;
abstract class tipoDato {
    const T_INT = 0;
    const T_STR = 1;
    const T_DATETIME = 2;
    const T_DATE = 3;
    const T_TIME = 4;
    const T_CHECK = 5;
    const T_HIDDEN = 6;
    const T_SELECT = 7;
    const T_EMAIL = 8;
    const T_PASSWORD = 9;
    const T_RESET = 10;
    const T_TEL = 11;
    const T_MONTH = 12;
    const T_RANGE = 13;
    const T_COLOR = 14;
    const T_SEARCH = 15;
    const T_URL = 16;
    const T_WEEK = 17;
    const T_BUTTON = 18;
    const T_NUMBER = 19;
    const T_TEXT = 20;
    const T_SUBMIT = 21;

    public static function getType($param){
        switch ($param) {
          case tipoDato::T_INT:
          case tipoDato::T_NUMBER:  return "number";
          case tipoDato::T_DATETIME:return "datetime";
          case tipoDato::T_DATE:    return "date";
          case tipoDato::T_TIME:    return "time";
          case tipoDato::T_EMAIL:   return "email";
          case tipoDato::T_PASSWORD:return "password";
          case tipoDato::T_RESET:   return "reset";
          case tipoDato::T_TEL:     return "tel";
          case tipoDato::T_MONTH:   return "month";
          case tipoDato::T_RANGE:   return "range";
          case tipoDato::T_COLOR:   return "color";
          case tipoDato::T_SEARCH:  return "search";
          case tipoDato::T_URL:     return "url";
          case tipoDato::T_WEEK:    return "week";
          case tipoDato::T_BUTTON:  return "button";
          case tipoDato::T_TEXT:
          case tipoDato::T_STR:     return "text";
          default:                  return "text";
        }
    }
}

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
require_once( ROOT . 'crud/formModel.php' );
require_once( ROOT . 'crud/crudModel.php' );


if(CRUD_DEBUG){
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
}

 ?>
