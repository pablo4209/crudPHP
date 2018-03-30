<?php 	


	require_once( 'crud/config.php' );		

	
	//Crud( nom_tabla, array(array( nom_campo, tipo_dato , alias , listar , editar , requerido, minlenght, maxlenght, placeholder , extraclass  )) )
	$crud = new Crud ( "tbmoneda",
						array( 
							array( "idMoneda" 	, tipoDato::T_INT , "ID" 		, 1 , 1 , 0, "number"	, 2, 50, "", ""  ),
							array( "Nombre" 	, tipoDato::T_STR , "Nombre" 	, 1 , 1 , 1, "text"		, 2, 50, "ingresa un nombre", ""  ),
				   		  	array( "Cambio" 	, tipoDato::T_INT , "Cambio" 	, 1 , 1 , 1, "number"	, 1, 10, "ingresa cambio", ""  )
				   		)
					 ); //se pasan datos de tabla al constructor
	

	$crud->setTitulo("<---->");
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
	<link rel="stylesheet" href="http://localhost/claseCrud/js/bootstrap/css/bootstrap.min.css" >
	<script type="text/javascript" src="http://localhost/claseCrud/js/jquery-3.3.1.min.js"></script>		
	<script src="http://localhost/claseCrud/js/bootstrap//js/bootstrap.min.js"></script>
	<script type="text/javascript" src="http://localhost/claseCrud/js/validate/jquery.validate.min.js"></script>
	<script type="text/javascript" src="http://localhost/claseCrud/js/validate/validar.js"></script>
	
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