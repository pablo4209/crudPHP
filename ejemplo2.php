<?php

	require_once( 'crud/config.php' );

										//$tabla, $id, $desc, $sel="", $desc2="", $where = "", $cssClass=" input-medium required", $toolTip = "Debes seleccionar un elemento." )
	$selectEntidad = [ "tabla" => "tbentidad",
											"id" => "idEntidad",
											"descripcion" => "Nombre",
											"sel" => "1" 	];

	//Crud( nom_tabla, array(array( nom_campo, tipo_dato , alias , listar , editar , requerido, value, type, minlenght, maxlenght, placeholder , extraclass  )) )
	$crud = new Crud ( "tbmoneda",
											[
												[ 		"campo" 	=> 	"idMoneda" ,
															"tipo"		=> 	tipoDato::T_INT,
															"alias"		=> 	"ID" ,
															"listar" 	=>	1 ,
															"editar"	=>	1
												],
												[ 			"campo"	=> 	"Nombre" 		,
																"tipo" 	=>	tipoDato::T_TEXT ,
																"alias"	=>	"Nombre",
															 "listar" => 1 ,
															 "editar" => 1 ,
														"requerido" => 1,
																"value"	=>	"nombre_campo" ,
														"minlenght"	=>	2,
														"maxlenght"	=> 50	,
													"placeholder"	=> "ingresa un nombre"
												],
				   							[ 			"campo"	=>	"Cambio",
																 "tipo" => 	tipoDato::T_NUMBER 	 ,
														 	  "alias"	=> 	"Cambio" 				,
															 "listar" => 1 ,
															 "editar" => 1 ,
														"requerido" => 1,
																"value" => 1,
														"minlenght" => 1,
														"maxlenght" => 10	,
													 "placehlder" => "ingresa cambio"
												],
												[ 			"campo"	=>	"idEntidad"  ,
																"tipo"	=> tipoDato::T_SELECT ,
																"alias"	=>	"Modificado por",
															"listar"	=>	0 ,
															"editar"	=>	1 ,
														"requerido"	=>	1,
																"value"	=>	$selectEntidad
												],
												[ 			"campo"	=>	"Habilitada" ,
																"tipo"	=>	tipoDato::T_CHECK  ,
																"alias"	=>	"Habilitada" 		,
															"listar"	=>	0 ,
															"editar"	=>	1 ,
														"requerido"	=>	1,
																"value"	=>	1	,
												]
											]
					 ); //se pasan datos de tabla al constructor


	$crud->setTitulo("Monedas del Sistema");
	$crud->setEliminar(true);
	$crud->
	$html = $crud->render();


 ?>
<!DOCTYPE html>
<html lang="es">
<head>
	<!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<title>Ejemplo clase CRUD</title>
	<link rel="stylesheet" href="http://localhost/crudPHP/js/bootstrap/css/bootstrap.min.css" >
	<script type="text/javascript" src="http://localhost/crudPHP/js/jquery-3.3.1.min.js"></script>
	<script src="http://localhost/crudPHP/js/bootstrap//js/bootstrap.min.js"></script>
	<script type="text/javascript" src="http://localhost/crudPHP/js/validate/jquery.validate.min.js"></script>
	<script type="text/javascript" src="http://localhost/crudPHP/js/validate/validar.js"></script>

</head>
<body>
	<div class="container"><!-- CONTAINER -->
		<div class="row"><p>en este caso, se crean registros usando la clase enviando paramatros directamente</p>
				<input type="button" class=" btn btn-success" value="insertar registro" id="nuevo_reg" name="nuevo_reg" />
		<div/>
		<div class="col-sm"><!-- COL -->
			<?php
				echo $html;
			?>
		</div><!-- END COL -->

	</div><!-- END CONTAINER -->
</body>

</html>