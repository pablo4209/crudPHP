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
                $this->dbh=new PDO('mysql:host=' . DB_HOST .
                                   ';dbname=' . DB_NAME,
                                   DB_USER,
                                   DB_PASS,
                                   array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . DB_CHAR)
								   );
                
            }
            catch (PDOException $e) {
                print '<div class="alert alert-danger" role="alert">Mensaje:  '. $e->getMessage() . '</div>';                
                die();
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
    public function getRows($sql)
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
                print '<div style="padding-top:50px;">Error!<br/>Mensaje:  '. $e->getMessage() . "<br/></div>";         
                die();
        }
    }

    protected function debugSQL( $sql ){
        if( DEBUG ){
            $_SESSION['infoSQL'] = '<div class="row"><div class="col-md-10"><div class="alert alert-danger" role="alert">'.htmlspecialchars( $sql ).'</div></div></div>';             
        }else if( isset( $_SESSION['infoSQL'] ) ) 
                    unset( $_SESSION['infoSQL'] ); 
    }

    protected function debugParams( $prep ){
        
        if(DEBUG){
            
            ob_start();
            $prep->debugDumpParams();
            $r = ob_get_contents();
            ob_end_clean();           

            $_SESSION['infoSQL'] = '<div class="row"><div class="col-md-10"><div class="alert alert-danger" role="alert">'.htmlspecialchars( $r ).'</div></div></div>'; 

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
            $stmt->execute( array( $id ) );
            self::debugParams( $stmt );

            while($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $this->p[]=$row;
            }
            $stmt->closeCursor();
            return $this->p;

        }catch(PDOException $e) {
                print '<div style="padding-top:50px;">Error!<br/>Mensaje:  '. $e->getMessage() . "<br/></div>";         
                die();
        }
    }

    /*
    *   ejecutar consulta preparada y retorna true o false
    */
    protected function exePrepare($consulta){
        try{
            $r = $consulta->execute();
            self::debugSQL( $consulta );
            $consulta->closeCursor();
            if($r)
                return true;
            else
                return false;
        }catch(PDOException $e) {
                print '<div style="padding-top:100px;">Error!<br/>Mensaje:  '. $e->getMessage() . "<br/></div>";         
                die();
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
                self::debugParams( $consulta );
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
                print '<div style="padding-top:50px;">Error!<br/>Mensaje:  '. $e->getMessage() . "<br/></div>";         
                die();
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



    public static function crear_selects_fecha($dia="",$mes="",$anio="")
    {
        $f="";
		//dias
        $f.= '<select name="dia" class="select input">
                <option value="0" ';
        if ($dia=="") $f.='selected="selected"';
        $f.= '>-</option>';
    	   for($i=1;$i<32;$i++)
    	   {
    		  $f.= '<option value="'.$i.'" ';
              if ($i == $dia) $f.='selected="selected"';
              $f.= '>'.$i.'</option>';

    	   }
        $f.= '</select> / ';

        //meses
        $f.= '<select name="mes" class="select input">
                <option value="0" ';
        if ($mes=="") $f.='selected="selected"';
        $f.= '>-</option>';
    	   for($i=1;$i<13;$i++)
    	   {
    		  $f.= '<option value="'.$i.'" ';
              if ($i == $mes) $f.='selected="selected"';
              $f.= '>'.$i.'</option>';

    	   }
        $f.= '</select> / ';

        //a�os
        $f.= '<select name="anio" class="select input">
                <option value="0" ';
        if ($anio=="") $f.='selected="selected"';
        $f.= '>-</option>';
    	   for($i=date("Y");$i>=1930;$i--)
    	   {
    	       $f.= '<option value="'.$i.'" ';
               if ($i == $anio) $f.='selected="selected"';
               $f.= '>'.$i.'</option>';
    	   }
        $f.= '</select>';

        return $f;
    }

    //Genera un select html con nombre e id, si $sel tiene valor lo selecciona, sino imprime seleccionar opcion con val=0
     protected function crearSelectTabla($tabla, $id, $desc, $sel="", $desc2="", $where = "", $cssClass="form-control input-medium required")
    {
        $f=""; //se inicializan para evitar warnings
		if(empty($tabla) or empty($id) or empty($desc))
        {
            return "Error al generar Select de Tabla ".$tabla ;
        }

        $sql = "Select * From ".$tabla.$where;

		$datos = self::getRows($sql);

        if($datos)
        {
            //dias
            $f.= '<select name="'.$id.'" id="'.$id.'" min="1" title="Debes seleccionar un elemento." class="'.$cssClass.'">
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
