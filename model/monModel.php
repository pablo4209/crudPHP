<?php

class Moneda extends Conectar
{
    private $u;

    public function __construct()
    {
        parent::__construct();
        $this->u=array();
    }

    public function get_monedas()
    {
        $sql="select idMoneda, Nombre, Cambio, Simbolo, FechaAlta, HoraAlta, FechaMod, HoraMod, idUsuarioMod, Habilitada,
              Principal, Web from tbmoneda order by Nombre asc;";
        return parent::getRows($sql);
    }

	public function getSelMonedas($sel="")
    {
        return parent::crearSelectTabla("tbmoneda", "idMoneda", "Nombre", $sel, "Cambio" );
    }

    public function add()
    {
        //print_r($_POST);exit;
        /*
        if( empty($_POST["Nombre"]) or empty($_POST["Cambio"]) )
        {
            return false;
        }*/

        $sql="INSERT INTO tbmoneda ( `idMoneda` ,  `Nombre` ,  `Cambio` ,  `Simbolo` ,  `FechaAlta` ,  `HoraAlta` ,
             `idEntidad` ,  `Habilitada` ,  `Principal` ,  `Web`  )
              VALUES ( NULL ,? ,? ,? ,NOW() ,NOW(),? , ?, ?, ? )";

        //los checks no tildados no generan variable post!!
        $h = (isset($_POST["Habilitada"]))? 1:0;
        $p = (isset($_POST["Principal"]))? 1:0;
        $w = (isset($_POST["Web"]))? 1:0;
        $simbolo = (isset($_POST["Simbolo"]))? $_POST["Simbolo"]: '';
        $user = (isset($_SESSION["user_id"]))? $_SESSION["user_id"]: 0;

        $stmt=$this->dbh->prepare($sql);

        $stmt->bindValue(1, $_POST["Nombre"] ,PDO::PARAM_STR);
        $stmt->bindValue(2, $_POST["Cambio"] ,PDO::PARAM_INT);
        $stmt->bindValue(3, $simbolo ,PDO::PARAM_STR);
        $stmt->bindValue(4,$user,PDO::PARAM_INT);
        $stmt->bindValue(5,$h,PDO::PARAM_INT);
        $stmt->bindValue(6,$p,PDO::PARAM_INT);
        $stmt->bindValue(7,$w,PDO::PARAM_INT);

        $res = $stmt->execute();
        $this->dbh=null;
        
        if($res)
        {
            return true;
        }else{
            return false;
            print_r($stmt->errorInfo());
            //header("Location:".BASE_URL."user-add/3/");exit;
        }
        

    }

    public function get_moneda_id($id)
    {
        $sql="select idMoneda, Nombre, Cambio, Simbolo, FechaAlta, HoraAlta, FechaMod, HoraMod, idUsuarioMod, Habilitada,
              Principal, Web from tbmoneda where idMoneda=?";

        return parent::getRowId($sql, array($id));                                       
    }

    public function edit()
    {
        if(empty($_POST["Nombre"]) or empty($_POST["Cambio"]) or empty($_POST["Simbolo"]) or empty($_POST["id"]))
        {
            header("Location:".BASE_URL."?accion=mon-edit&id=".$_POST["id"]."&st=" . MSG_WARNING );exit;
        }

        $sql="update tbmoneda
              set
              Nombre=?, Cambio=?, Simbolo=?, FechaMod=NOW(), HoraMod=NOW(), idUsuarioMod=?, Habilitada=?, Principal=?, Web=?
              where
              idMoneda=?";

        //los checks no tildados no generan variable post!!
        $h = (isset($_POST["Habilitada"]))? 1:0;
        $p = (isset($_POST["Principal"]))? 1:0;
        $w = (isset($_POST["Web"]))? 1:0;

        $stmt=$this->dbh->prepare($sql);

        $stmt->bindValue(1,$_POST["Nombre"],PDO::PARAM_STR);
        $stmt->bindValue(2,$_POST["Cambio"],PDO::PARAM_INT);
        $stmt->bindValue(3,$_POST["Simbolo"],PDO::PARAM_STR);
        $stmt->bindValue(4,$_SESSION["user_id"],PDO::PARAM_INT);
        $stmt->bindValue(5,$h,PDO::PARAM_INT);
        $stmt->bindValue(6,$p,PDO::PARAM_INT);
        $stmt->bindValue(7,$w,PDO::PARAM_INT);
        $stmt->bindValue(8,$_POST["id"],PDO::PARAM_INT);

        if($stmt->execute())
        {
            header("Location:".BASE_URL."?accion=mon-edit&id=".$_POST["id"]."&st=" . MSG_SUCCESS );
        }else
        {
           header("Location:".BASE_URL."?accion=mon-edit&id=".$_POST["id"]."&st=" . MSG_DANGER );
        }

        $this->dbh=null;
    }

    public function delete()
    {
        //primero es necesario checkear la integridad referencial


            $sql="delete from tbmoneda where idMoneda=?";

    		$stmt=$this->dbh->prepare($sql);

            $id=$_GET["id"];
            $stmt->bindValue(1,$id,PDO::PARAM_INT);


    		if($stmt->execute())
            {
                header("Location: ".BASE_URL."?accion=mon&st=2");
            }else
            {
                header("Location: ".BASE_URL."?accion=mon&st=1");
            }
            $this->dbh=null;
    }
}

?>
