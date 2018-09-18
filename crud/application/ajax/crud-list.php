<?php
/*
*		recibe el array de configuracion completo e instancia la clase Crud y asi
*		listar con getTabla
*
*/

if( isset( $_POST["datos"] )  ){

	//$datos[0] : contiene nombre de tabla, crud-list y cualquier otra propiedad de control que quiera usar
	//$datos[1] : contiene los campos del crud

	//aca se convierte el JSON en un array
	$datos = json_decode( $_POST["datos"] , true ) ; //con true devuelve array asociativo

	if( $datos[0]["crud-list"] == 1 ){

		require_once( 'crud/crudModel.php' );

		$crud = new Crud ( $datos[0]["tabla_bd"] ,
						   				 $datos[1],
											 $datos[0]["tabla_where"] 
					 ); //se pasan datos de tabla al constructor

		//configuraciones de la clase
		if(isset($datos[0]["setTitulo"])) $crud->setTitulo( $datos[0]["setTitulo"] );
		if(isset($datos[0]["setEliminar"])) $crud->setEliminar( $datos[0]["setEliminar"] );

		echo $crud->getTabla();
	}

}


 ?>
