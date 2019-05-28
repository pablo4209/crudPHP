<?php
/*
*		-recibe un objeto json en $_POST['datos'] que se convierte en array $datos:
*         $datos[0]['tabla_bd'] y $datos[0]['idprod'] nombre de tabla e ID para obtener datos
*         $datos[1] es un array con toda la configuracion de los campos
*
*		-
*/


if( isset($_POST['datos'])  ){

	$datos = json_decode( $_POST["datos"] , true ) ; //con true devuelve array asociativo

	if( $datos[0]["crud-completar-formulario"] == 1 ){

			require_once( CRUD_ROOT.'crud/crudModel.php' );

			$crud = new Crud ( $datos[0]["tabla_bd"] ,
							   $datos[1] ,
								 "" ,
							   $datos[0]["idprod"]
						 ); //se pasan datos de tabla al constructor
			
			
			echo $crud->getModal();
	}
}

 ?>
