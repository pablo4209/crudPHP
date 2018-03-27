<?php 


//para usar tipo enumeracion ej: if( $campo == tipoDato::INTEGER ) ...sentencias...;
abstract class tipoDato {
    const T_INT = 0;
    const T_STR = 1;
    const T_DATETIME = 2;
    const T_DATE = 3;
    const T_TIME = 4;

}
//distinto de 0 para activarlo
define( 'DEBUG' , 1 );

//directorios usados
define ( "MODEL" , "model/" );
define ( "AJAX" , "application/ajax/" );



 ?>