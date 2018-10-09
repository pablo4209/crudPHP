<?php

/**
 * @author pablo
 * @copyright 2013
 */


class Conectar
{
    protected $dbh;
    protected $p;

    function __construct()
    {       try
            {
                $this->dbh=new PDO('mysql:host=' . C_DB_HOST .
                                   ';dbname=' . C_DB_NAME,
                                   C_DB_USER,
                                   C_DB_PASS,
                                   array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . C_DB_CHAR )
								                  );
                if( CRUD_DEBUG ){
                    $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                }else
                    $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);


            }
            catch (PDOException $e) {
                write_log( "Error estableciendo conexion: " , $e->getMessage() );
                print '<div class="alert alert-danger" role="alert">Mensaje:  '. $e->getMessage() . '</div>';
                //die();
            }

            $this->p=array();
    }

    protected function ClearArray()
    {
        unset($this->p);
        $this->p=array();
    }

    function __destruct()
    {
        $this->dbh = null;
        $this->p = null;
    }

    public function getConn(){
        return $this->dbh;
    }

    //La funcion devuelve un Array de dos dimensiones, como fetch_assocc, recibe como parametro la consulta select lista para ejecutarse
    protected function getRows($sql)
    {
      try{
          self::ClearArray();
          foreach($this->dbh->query($sql) as $row) //query retorna una fila asociada con los nombres de los campos
          {                                         //retorna false y hay error
              $this->p[]=$row;
          }

          self::debugSQL( $sql );
          return $this->p;

        }catch(PDOException $e) {
                write_log( "database::getRows() :" , $e->getMessage() );
                return null;
                //die();
        }
    }

    protected function debugSQL( $param ){
        $info="";
        $sql="";
        if( CRUD_DEBUG ){
            if( is_string($param) ){
                $sql = htmlspecialchars( $param );
                $info = '<div class="row"><div class="col-md-10"><div class="alert alert-danger" role="alert">'.$sql.'</div></div></div>';
            }elseif( is_a( $param, 'PDOStatement' ) ){

              ob_start();   //debugDumpParams no retorna nada, sino q visualiza, esta parte captura la visualizacion a un string
              $param->debugDumpParams();
              $r = ob_get_contents();
              ob_end_clean();

              $sql = htmlspecialchars( $r );
              $info = '<div class="row"><div class="col-md-10"><div class="alert alert-danger" role="alert">'.$sql.'</div></div></div>';
            }
            if($sql != "") write_log( "Valores CRUD_DEBUG: " , $sql );

            if( !empty($_POST) )
                    $info .= '<div class="row"><div class="col-md-10"><div class="alert alert-warning" role="alert">'.var_export( $_POST , true ).'</div></div></div>';

            $_SESSION['infoSQL'] = $info; //se guarda en variable de sesion

        }else if( isset( $_SESSION['infoSQL'] ) )
                    unset( $_SESSION['infoSQL'] );
    }


    public function close(){
      $this->dbh = null;
      $this->p = null;
    }

    protected function getRowsJson($sql)
    {
        self::ClearArray();
        $stmt=$this->dbh->prepare($sql);
            if($stmt->execute(  ) )
            {
                self::debugSQL( $sql );
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) //resultado asociado solo a nombres de campos
                {
                    $this->p[]=$row;
                }
                $stmt->closeCursor();
                return $this->p;
            }else
            {
                return false;
            }

    }


    //devuelve un array de dos dimensiones con una sola fila,
    //recibe la consulta sql con el parametro = ?, id=identificador es un String (acepta varios valores a reemplazar en sql si estan separados por comas)
    // para acceder echo $dato[0]["id"];
    public function getRowId($sql, $id)
    {
        try{

            self::ClearArray();
            $stmt=$this->dbh->prepare($sql);

            if( is_array($id) ){
                $stmt->execute( $id );
            }else {
                $stmt->execute( array($id) );
            }

            self::debugSQL( $stmt );

            while($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $this->p[]=$row;
            }
            $stmt->closeCursor();
            return $this->p;

        }catch(PDOException $e) {
                write_log( "database::getRowId: " , $e->getMessage() );
                return null;
                //die();
        }
    }



    /*
    *   ejecutar consulta preparada y retorna true o false
    */
    protected function exePrepare($consulta){
        try{
            $r = $consulta->execute();
            self::debugSQL( $consulta ); //esto es un prepare
            $consulta->closeCursor();
            if($r)
                return true;
            else
                return false;
        }catch(PDOException $e) {
                write_log( "Error en exePrepare: " , $e->getMessage() );
                return false;
        }
    }

    //
    //recibe una consulta preparada, la ejecutada y retorna un array assocc con los resultados obtenidos
    //
    protected function exePrepare_FetchAssoc( $consulta ){

      try{
          self::ClearArray();
          if( $consulta->execute() )
            {
                self::debugSQL( $consulta );
                while($row = $consulta->fetch(PDO::FETCH_ASSOC)) //resultado asociado solo a nombres de campos
                {
                    $this->p[]=$row;
                }
                $consulta->closeCursor();
                return $this->p;
            }else
            {
                return false;
            }
        }catch(PDOException $e) {
                write_log( "Error en exePrepare_FetchAssoc: " , $e->getMessage() );
                return false;
        }

    }



    //*******************************************************************************************************************************************************
    //
    //
 //fecha sql recibe una fecha en formato "dd/mm/aaaa" y lo transforma a formato mysql "#mm/dd/aaaa#"
 public static function fechaMysql($fecha = ""){
    list($dia,$mes,$ano)=explode("/",$fecha);
    if($dia != "" AND $mes != "" AND $ano !="")
        {return "'$ano-$mes-$dia'";     }
    else {
        return "";
    }
 }



    //Genera un select html con nombre e id, si $sel tiene valor lo selecciona, sino imprime seleccionar opcion con val=0
    /**
    *    $param: es un array asociativo: tabla, id y descripcion son los parametros obligatorios.
    *    tabla: nombre de la tabla
    *    id: el select toma el name e id con este valor, el valor del campo id es el value del select   *
    *    descripcion: el campo descripcion de la Tabla
    *    sel: id seleccionado por defecto
    *    descripcion2: valor de un campo que se quiera poner como acotacion (ej: dolar [3.40] )
    *    where: filtro de la $consulta
    *    cssClass: tiene las clases de control bootstrap por defecto y lo tilda como requerido para validate, cambiar el valor reemplaza el default
    *    prop: sirve para agregar propiedades al control para ser manipuladas desde js
    */
    protected function crearSelectTabla( $param ) //$tabla, $id, $desc, $sel="", $desc2="", $where = "", $cssClass=" input-medium required", $toolTip = "Debes seleccionar un elemento." )
    {
      $tabla = (isset($param["tabla"]))? $param["tabla"] : false;
      $id = (isset($param["id"]))? $param["id"] : false;
      $desc = (isset($param["descripcion"]))? $param["descripcion"] : false;
      $sel = (isset($param["sel"]))? $param["sel"] : "" ;
      $desc2= (isset($param["descripcion2"]))? $param["descripcion2"] : "";
      $where = (isset($param["where"]))? $param["where"] : "";
      $cssClass= (isset($param["cssClass"]))? $param["cssClass"] : " input-medium required";
      $toolTip = (isset($param["toolTip"]))? $param["toolTip"] : "Debes seleccionar un elemento.";
      $prop = (isset($param["prop"]))? $param["prop"] : "";
      $disabled = (isset($param["disabled"]))? $param["disabled"] : "";

        $f=""; //se inicializan para evitar warnings
  		if(empty($tabla) or empty($id) or empty($desc))
          {
              return "Error en parametros Select de Tabla: ".$tabla ;
          }

        $sql = "Select * From ".$tabla." ".$where;

		    $datos = self::getRows($sql);

        if($datos)
        {
            //dias
            $f.= '<select name="'.$id.'" id="'.$id.'" '.$prop.' min="1" title="'.$toolTip.'" class="form-control '.$cssClass.'" '.$disabled.'>
                    <option value="0" ';
            if ($sel=="") $f.='selected="selected"';
            $f.= '>Seleccionar</option>';
		    $cant = sizeof($datos);
			   for($i=0;$i<$cant;$i++)
        	   {
        		  $f.= '<option value="'.$datos[$i][$id].'" ';
                  if ($datos[$i][$id] == $sel) $f.='selected="selected"';
                  $f.= '>'.$datos[$i][$desc];
				  if(! empty($desc2)) $f.= ' ['.$datos[$i][$desc2].'] ';
				  $f.='</option>';

        	   }
            $f.= '</select>';

            return $f;

        }
    }


}

?>
