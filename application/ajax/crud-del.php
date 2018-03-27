<?php 
//esto se carga en root junto a ajax-crud.php

if( isset($_POST["crud-del"]) AND $_POST["crud-del"] == 1 ){

		$sql = "DELETE FROM `".$_POST["tabla_bd"]."` WHERE " . $_POST["campo_id"] . "=:" . $_POST["campo_id"] ;


		$cls = new Conectar();
		$con = $cls->getConn();
		$prepared = $con->prepare( $sql );
		
		$prepared->bindParam( ':'.$_POST["campo_id"] , $_POST["idprod"] );		
		
		$res = $prepared->execute();

		if( $res )
			echo 'Item eliminado!';
		else
			echo 'No se elimino item!';
		
}


 ?>