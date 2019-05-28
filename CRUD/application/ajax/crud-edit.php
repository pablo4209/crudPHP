<?php
/*
*		-recibe un array que incluye  tabla_bd y campo_id como campos principales
*		el resto son nombres de campo>>valor para armar la consulta mysql y editar el registro
*
*		- el campo crud-edit es de verificacion
*/

if( isset($_POST["crud-edit"]) AND $_POST["crud-edit"] == 1 ){

	$listados = 0;
	$sql = "UPDATE `". $_POST["tabla_bd"] ."` SET ";

	//hay que armar el listado de campos restringiendo crud-edit y tabla_bd que no lo son(son los dos
	//primeros del array), tambien necesito saber el nombre del id, lo recibo en campo_id, de esa forma
	//lo puedo aislar de las asignaciones de valores y ubicarlo en el WHERE
	foreach ($_POST as $key => $value)
		if( $key != 'crud-edit' AND $key != 'tabla_bd' AND $key != 'campo_id' AND $key != $_POST["campo_id"] ) {
			$separador = ( $listados )? ', ' : ' ';
			$sql .= $separador . '`'.$key.'`=:'.$key;
			$listados++;
		}

	$sql .= " WHERE `". $_POST["tabla_bd"] ."`.".$_POST["campo_id"]."=:".$_POST["campo_id"].";";


	$cls = new Conectar();
	$con = $cls->getConn();
	$prepared = $con->prepare( $sql );


	foreach ($_POST as $key => &$value) //bindParam necesita puntero
		if( $key != 'crud-edit' AND $key != 'tabla_bd' AND $key != 'campo_id' )
			$prepared->bindParam( ':'.$key , $value );

	echo $prepared->execute();

}


 ?>
