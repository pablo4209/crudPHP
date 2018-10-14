<?php
    /**
     *  para usar tipo enumeracion ej: if( $campo == tipoDato::INTEGER ) ...sentencias...;
     *  tambien retorna el input:type correspondiente
     */

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

?>
