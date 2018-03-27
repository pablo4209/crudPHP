<?php 
//esto se carga en root junto a ajax-crud.php

if( isset($_POST["crud-add"]) AND $_POST["crud-add"] == 1 ){

	
	$listados = 0;
	$sql = "INSERT INTO " . $_POST["tabla_bd"] . " ( ";
	$valores = '';	
	//hay que armar el listado de campos restringiendo crud-add y tabla_bd que no lo son(son los dos primeros del array)
	foreach ($_POST as $key => $value)
		if( $key != 'crud-add' AND $key != 'tabla_bd' ) {
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
		if( $key != 'crud-add' AND $key != 'tabla_bd' )
			$prepared->bindParam( ':'.$key , $value );		
	
	$res = $prepared->execute();

	if( $res )
		echo 'ejecutado con exito';
	else
		echo 'consulta no ejecutada';
		
}


 ?>