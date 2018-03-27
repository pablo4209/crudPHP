<?php 
//esto se carga en root junto a ajax-crud.php

if( isset($_POST["crud-edit"]) AND $_POST["crud-edit"] == 1 ){

	print_r( $_POST );
	
}

if( isset($_POST['datos'])  ){

	$datos = json_decode( $_POST["datos"] , true ) ; //con true devuelve array asociativo

	if( $datos[0]["crud-completar-formulario"] == 1 ){			

			require_once( 'crud/crudModel.php' );

			$crud = new Crud ( $datos[0]["tabla_bd"] ,
							   $datos[1]
						 ); //se pasan datos de tabla al constructor
			
			//configuraciones de la clase
			if(isset($datos[0]["setTitulo"])) $crud->setTitulo( $datos[0]["setTitulo"] );
			if(isset($datos[0]["setEliminar"])) $crud->setEliminar( $datos[0]["setEliminar"] );

			echo $crud->getEdit( $datos[0]["idprod"] );
	}
}


 ?>