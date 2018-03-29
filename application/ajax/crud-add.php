<?php 
//esto se carga en root junto a ajax-crud.php

if( isset($_POST["crud-add"]) AND $_POST["crud-add"] == 1 ){

	
	$listados = 0;
	$sql = "INSERT INTO " . $_POST["tabla_bd"] . " ( ";
	$valores = '';	
	//hay que armar el listado de campos restringiendo crud-add y tabla_bd que no lo son(son los dos primeros del array), tambien uso campo_id para enviar el nombre del id, para aislarlo de la consulta
	foreach ($_POST as $key => $value)
		if( $key != 'crud-add' AND $key != 'tabla_bd' AND $key != $_POST["campo_id"] AND $key != "campo_id" ) {
			$separador = ( $listados )? ', ' : ' ';
			$sql .= $separador . '`'.$key.'`';	
			$valores .=	$separador . ':' . $key ;	
			$listados++;			
		}
	
	$sql .= ' ) VALUES ( '.$valores.' )';
	

	$cls = new Conectar();
	$con = $cls->getConn();
	$prepared = $con->prepare( $sql );
	
	
	foreach ($_POST as $key => &$value) //bindParam necesita puntero
		if( $key != 'crud-add' AND $key != 'tabla_bd' AND $key != $_POST["campo_id"] AND $key != "campo_id" )
			$prepared->bindParam( ':'.$key , $value );		
	
	$res = $prepared->execute();

	if( $res )
		echo 'Registro agregado con exito';
	else
		echo 'Error al agregar registro!';
		
}


 ?>