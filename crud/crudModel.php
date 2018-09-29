<?php
/*
	modo de iniciar la clase
	-------------------------
	IMPORTANTE!
	el indice de la tabla siempre debe ser el primer campo, porque asi se asume en la clase,
	sin importar si se visualiza o no
	- el campo indice siempre se mostrara disabled
	--------------

	$c = new Crud( 	nombre_tabla,		//string con el nombre de la tabla
					array(
									array( 	nombre_campo ,    //nombre del campo
													tipo_dato (constante) , 	//constante con el tipo del campo (ej: T_HIDDEN, input oculto)
													alias , 			//nombre a mostrar, si "" se usa nombre_tabla
													mostrar_listado ,  	//boolean, si se muestra en el listado
													editar , 			//boolean, si se edita en form
													requerido,			//boolean, requerido en caso de usarse en form
													value,
													type,
													minlenght,
													maxlenght,
													placeholder ,
													extraclass
										)
					),
			   "idtabla = 2"			//tambien se pueden aplicar un filtro simple a los resultados de la tabla
				);

value cuando tipo_dato = tipoDato::T_SELECT
-----------------------------------------------
* para que se muestre este control, debe recibir un array para la siguiente funcion:
		parent::crearSelectTabla($tabla, $id, $desc, $sel="", $desc2="", $where = "", $cssClass=" input-medium required", $toolTip = "Debes seleccionar un elemento." )

* tambien deberia recibir un array lista -> valor para que arme un selecto con ello, pero todavia no esta implementado

*/


Class Crud extends Conectar {
	private $campos_array; 		//array de arrays con toda la configuracion de los campos
	private $campos_sql;   		// listado de texto de campos para armar la consulta sql
	private $tabla;						//nombre de la tabla para sql
	private $where;						//para filtrar resultados de la tabla a editar
	private $u;								//variable acumulador para guardar result consulta
	private $titulo;					//titulo de la pagina
	private $eliminar;				//si se muestra opcion eliminar o no en la tabla, boolean. por def:false


	function __construct( $tabla , $campos , $where = "" , $edit_id = 0 ){

				parent::__construct();
        $this->u=array();
        $this->campos_array = $campos;
        $this->tabla = $tabla;
				$this->where = $where;
        self::listar_campos_sql();
        $this->titulo = "Listado de tabla: " . $tabla;
        $this->eliminar = false;
        $this->edit_id = $edit_id;

	}

	public function setEliminar( $valor ){
			$this->eliminar = $valor;
	}
	public function getEliminar(){
			return $this->eliminar;
	}
	function __destruct(){
		$this->u=null;
		$this->campos_array=null;
	}

	public function getJson(){
		return json_encode( $this->campos_array );
	}

	//imprime el script que escucha los formularios add y edit
    private function renderAjax(){

        $form_datos = ""; //utilizado para enviar los datos hacia ajax
        $form_response =""; //utilizado para listar el js para cargar la espuesta json de ajax
        foreach ( $this->campos_array as $id => $row )
						if( $id >= 0 ){  //$row[self::C_LISTAR] AND / el id no se incluye
									$form_datos .= 'formData.append("'.$row[crudArg::C_NOMBRE_CAMPO].'", $("#'.$row[crudArg::C_NOMBRE_CAMPO].'").val() );
													';
									switch ($row[crudArg::C_TIPO_CAMPO]) {
										case tipoDato::T_INT:
										case tipoDato::T_STR:
										case tipoDato::T_HIDDEN:
												//se carga cada asignacion de valor json a los campos del form
												$form_response .=  '
															$("#'.$row[crudArg::C_NOMBRE_CAMPO].'").attr( "value" , data[0].'.$row[crudArg::C_NOMBRE_CAMPO].');';
												break;
										case tipoDato::T_CHECK:
												$form_response .=  '
														$("#'.$row[crudArg::C_NOMBRE_CAMPO].'").attr( "value" , data[0].'.$row[crudArg::C_NOMBRE_CAMPO].');
														if( data[0].'.$row[crudArg::C_NOMBRE_CAMPO].' > 0 )
																	$("#'.$row[crudArg::C_NOMBRE_CAMPO].'").prop( "checked" , true );
														else
																	$("#'.$row[crudArg::C_NOMBRE_CAMPO].'").prop( "checked" , false );
														';

												break;
										case tipoDato::T_SELECT:
											$form_response .= '
																				$( "#'.$row[crudArg::C_NOMBRE_CAMPO].' option:selected" ).val();';
											break;
										default:
												// code...
												break;
									}



						}

        //fnAjaxRenderTabla::: envia todo por JSON, un array de dos objetos, uno contiene todas las propiedades
	    //que se necesiten, el otro array envia el array de configuracion de campos del CRUD.
		//$datos[0] : contiene nombre de tabla, crud-list y cualquier otra propiedad de control que quiera usar
		//$datos[1] : contiene los campos del crud
        $script =   '
        			<script type="text/javascript">


                        $(document).ready(function(){

                        	$.getScript("js/validate/validar.js"); // add script


                        	$("body").on( "click" , "#guardar" , function(){
	                        		if( $("#form_crud").valid() == true ){
	                        			if( $("#modal_mode").val() == "add" ){
																			fnAjaxAdd();
	                        			}else{
																			guardarEdit();
	                        			}
	                        		}

                        	});

													$("body").on( "change" , "input[type=checkbox]" , function(){
																if( $(this).is(":checked") )
																				$(this).attr( "value" , "1" );
																else
																				$(this).attr( "value" , "0" );

													});

                        	$("body").on( "click" , ".btn_del" , function(){

                        		if( confirm("Queres eliminar el item: " + $(this).attr("idprod") + "?" ) )
                        						fnAjaxEliminarItem( $(this).attr("idprod") );
                        	});




													$("body").on( "click" , ".btn_edit" , function(){
																	fnAjaxCompletarFormulario($(this).attr("idprod"));
						              });

                        	$("#btnAdd").on("click" , function(){

															MostrarPanel("add");

                        	});


                        });  //document ready

            var guardarEdit = function(){

            	var formData = new FormData();

							formData.append( "crud-edit" , 1 );
							formData.append( "tabla_bd" , $("#tabla_bd").val() );
							formData.append( "campo_id" , "'.$this->campos_array[0][crudArg::C_NOMBRE_CAMPO].'" ); //envio el nombre del campo_id para identificarlo
							'.$form_datos.'

							$.ajax({
								url: "ajax-crud.php/?mode=crud-edit",
								type: "POST",
								data: formData,
								cache: false,
								contentType: false,
								processData: false,
								//mientras enviamos el archivo
								beforeSend: function(){
									//$("#cargando").show();
								},
								//una vez finalizado correctamente
								success: function(data){

									if(parseInt(data) === 1)
										fnAjaxRenderTabla();
									else
										alert("Error al editar");

								},
								//si ha ocurrido un error
								error: function(){
									//$("#cargando").hide();
								}
							});

                        };

              var MostrarPanel = function( mode ){

                        	if( mode =="add"){
															fnResetForm();
															$("#modal_mode").val("add");
                        			$("#panel_titulo").text("Nuevo Item");
                        	}
                        	else{
															$("#panel_titulo").text("Edicion de Item");
															$("#modal_mode").val("edit");
                        	}

													$("#panel_crud").modal("show");
              };

						var fnResetForm = function(){
							$(".validar").each(function(){
								$(this).removeClass("has-error has-success");
								$(this).children(":input").val("");
							});
							$("#form_crud").validate().resetForm();
						};

						var fnAjaxEliminarItem = function(idprod){

									var formData = new FormData();

									formData.append( "crud-del" , 1 );
									formData.append( "tabla_bd" , $("#tabla_bd").val() );
									formData.append( "campo_id" , "'.$this->campos_array[0][crudArg::C_NOMBRE_CAMPO].'" );
									formData.append( "idprod" , idprod );

									$.ajax({
												url: "ajax-crud.php/?mode=crud-del",
												type: "POST",
												data: formData,
												cache: false,
												contentType: false,
												processData: false,
												//mientras enviamos el archivo
												beforeSend: function(){
													//$("#cargando").show();
												},
												//una vez finalizado correctamente
												success: function(data){
													fnAjaxRenderTabla();
													alert(data);
												},
												//si ha ocurrido un error
												error: function(){
													alert("Error, no se pudo eliminar.");
												}
									});

						};

            var fnAjaxRenderTabla = function(){

									var obj = {};
									var arreglo = [];

									obj["crud-list"] = "1";
									obj["tabla_bd"] = "'.$this->tabla.'";
									obj["tabla_where"] = "'.$this->where.'";
									obj["setTitulo"] = "'.self::getTitulo().'";
									obj["setEliminar"] = "'.self::getEliminar().'";

									arreglo.push(obj);
									arreglo.push( '.json_encode( $this->campos_array , JSON_FORCE_OBJECT ).' );

									jsonStr = JSON.stringify(arreglo);

									$.ajax({
									   url: "ajax-crud.php/?mode=crud-list",
									   data: { datos: jsonStr },
									   type: "POST",
									   success: function(response) {
									      	$("#panel_crud").modal("hide");
									      	$("#div_tabla").html(response);
									   }
									});
              };

              var fnAjaxAdd = function(){
                    	var formData = new FormData();

											formData.append( "crud-add" , 1 );
											formData.append( "tabla_bd" , $("#tabla_bd").val() );
											formData.append( "campo_id" , "'.$this->campos_array[0][crudArg::C_NOMBRE_CAMPO].'" ); //envio el nombre del campo_id para identificarlo
											'.$form_datos.'

											$.ajax({
													url: "ajax-crud.php/?mode=crud-add",
													type: "POST",
													data: formData,
													cache: false,
													contentType: false,
													processData: false,
													//mientras enviamos el archivo
													beforeSend: function(){
														//$("#cargando").show();
													},
													//una vez finalizado correctamente
													success: function(data){
														//alert(data);
														fnAjaxRenderTabla();
													},
													//si ha ocurrido un error
													error: function(){
														//$("#cargando").hide();
													}
											});
            	};

            var fnAjaxCompletarFormulario = function(id){
            	var obj = {};
							var arreglo = [];

							obj["crud-completar-formulario"] = "1";
							obj["tabla_bd"] = "'.$this->tabla.'";
							obj["idprod"] = id;

							arreglo.push(obj);
							arreglo.push( '.json_encode( $this->campos_array , JSON_FORCE_OBJECT ).' );

							jsonStr = JSON.stringify(arreglo);

							$.ajax({
							   url: "ajax-crud.php/?mode=crud-get",
							   data: { datos: jsonStr },
							   type: "POST",
								 cache: false,
							   success: function(response) {
													$("#div_modal").html(response);
													MostrarPanel("edit");
							   }
							});
                        };


                    </script>';

        return $script;

    }

	public function setTitulo( $t ){
		$this->titulo = $t;
	}
	public function getTitulo(){
			return $this->titulo;
	}

	//esta funcion retorna el modal bootstrap con el form a utilizar en add y edit
	public function getModal(){
		$clsEdit = new Formulario( $this->tabla , $this->campos_array , $this->where , $this->edit_id );

		return $clsEdit->renderModal();
	}


	public function getValores(  ){

		$clsEdit = new Formulario( $this->tabla , $this->campos_array , $this->where , $this->edit_id );

		return $clsEdit->listar_valores( );

	}

	public function getAdd(){

		$clsEdit = new Formulario( $this->tabla, $this->campos_array );
		$clsEdit->setTitulo("Nuevo Item");

		return $clsEdit->renderAdd();
	}



	private function listar_campos_sql(){

		$cant = count($this->campos_array);
		$listados =0 ;
		for( $i=0 ; $i<$cant ; $i++ )
			if( $this->campos_array[$i][crudArg::C_LISTAR] &&
					$this->campos_array[$i][crudArg::C_TIPO_CAMPO] != tipoDato::T_SELECT ){
				$separador = ( $listados )? ", " : " ";
				$this->campos_sql .= $separador . $this->campos_array[$i][crudArg::C_NOMBRE_CAMPO] ;
				$listados++;
			}


	}

	public function render(){
		return  '<div class="clearfix"></div>
				<div class="row" name="tabla_div" id="tabla_div">
					<div class="panel panel-default"><!-- PANEL -->
						<div class="panel-heading">
							<h2>'.$this->titulo.'</h2>
							<button type="button" class="btn btn-primary" name="btnAdd" id="btnAdd" title="Agregar item" ><span class="glyphicon glyphicon-plus" aria-hidden="true"> Nuevo</span>
		 					</button>
						</div>
						<div class="panel-body">' .
							'<div name="div_tabla" id="div_tabla"><!-- DIV_TABLA -->
								' .	self::getTabla() .
							'</div><!-- END DIV_TABLA -->' .
					'	</div>
					</div><!-- END PANEL -->' .
					'<div id="div_modal"><!-- DIV_MODAL -->' .
						    	self::getModal() .
				    '</div><!-- END DIV_MODAL -->
				</div><!-- END TABLA_DIV -->' . self::renderAjax();
	}


	public function getTabla(){

		$sql = "SELECT " . $this->campos_sql . " FROM " . $this->tabla;

		if( $this->where != "" ) $sql .= " WHERE " . $this->where;

		$this->u = parent::getRows( $sql );

		if( count($this->u) ){

			$filas = count($this->u);
			$col = 0;

			//thead
			$tabla = '<div class="table-responsive"><!-- DIV TABLE_RESPONSIVE -->
					   		<table class="table table-striped table-hover table-bordered"><thead class="thead-dark"><tr>';
			foreach ( $this->campos_array as $id => $row )
					if( $row[crudArg::C_LISTAR] ){  //mostrar en listado?
						$col++;
						$clase = ( $id == 0 )? ' class="col-md-2 col-sm-2 col-xs-2" ': "" ;
						$tabla .= '<th scope="col" '.$clase.' >'.$row[crudArg::C_ALIAS]."</th>" ;
					}


			$tabla .= '<th scope="col" class="col-md-2 col-sm-2 col-xs-2">Edicion</th>';//columna de control
 			$tabla .= '</tr></thead><tbody>';

 			//tbody
 			for( $i=0 ; $i < $filas ; $i++ ){
 				$tabla .= '<tr>';
 				for( $j=0 ; $j<$col ; $j++ )
 						$tabla .= '<td>'.$this->u[$i][$j].'</td>';
 				$tabla .= '<td>

 						   			<button type="button" class="btn btn-primary btn_edit" name="btnEdit" idprod="'.$this->u[$i][0].'" title="Editar item"  ><span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
 						   			</button>

 						   ';
 				$tabla .=($this->eliminar)? '<button type="button" class="btn btn-danger  btn_del" name="btnDel"  idprod="'.$this->u[$i][0].'" title="Eliminar item" ><span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
 						   			</button> ' : '';
 				$tabla .= '		</td>
 						   </tr>';
			}

  			$tabla .= '		</tbody></table>
  						</div><!-- END DIV TABLE_RESPONSIVE -->';

			return $tabla;
		}

		return "<p>No Existen Registros!</p>";

	}



}

 ?>
