<?php

	require_once( 'crud/config.php' );

										//$tabla, $id, $desc, $sel="", $desc2="", $where = "", $cssClass=" input-medium required", $toolTip = "Debes seleccionar un elemento." )
	$selectEntidad = array( "tbentidad", "idEntidad", "Nombre", "1" );

	//Crud( nom_tabla, array(array( nom_campo, tipo_dato , alias , listar , editar , requerido, value, type, minlenght, maxlenght, placeholder , extraclass  )) )
	$crud = new Crud ( "tbmoneda",
						array(
							array( "idMoneda" 	, tipoDato::T_INT 	 , "ID" 				, 1 , 1 , 0, ""							, "number"	, 2, 10	, "", ""  ),
							array( "Nombre" 		, tipoDato::T_STR 	 , "Nombre" 		, 1 , 1 , 1, "saraza"							, "text"		, 2, 50	, "ingresa un nombreb gil", ""  ),
				   		array( "Cambio" 		, tipoDato::T_INT 	 , "Cambio" 		, 1 , 1 , 1, "1"						,  "number"	, 1, 10	, "ingresa cambio", ""  ),
							array( "idEntidad"  , tipoDato::T_SELECT , "Modificado por"    , 0 , 1 , 1, $selectEntidad ,  "number"	, 1, 1	, "", ""  ),
							array( "Habilitada" , tipoDato::T_CHECK  , "Habilitada" , 0 , 1 , 1, "1"						,  "number"	, 1, 1	, "", ""  )
						)
					 ); //se pasan datos de tabla al constructor


	$crud->setTitulo("Monedas del Sistema");
	$crud->setEliminar(true);
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
		<div class="col-sm"><!-- COL -->
			<?php
				echo $html;
			?>
		</div><!-- END COL -->

	</div><!-- END CONTAINER -->
</body>

</html>
