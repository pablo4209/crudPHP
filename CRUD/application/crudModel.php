<?php
/**
*	modo de iniciar la clase
*	-------------------------
*	IMPORTANTE!
*	el indice de la tabla siempre debe ser el primer campo, porque asi se asume en la clase,
*	sin importar si se visualiza o no
*	- el campo indice siempre se mostrara disabled
*	--------------
*
*	$c = new Crud( 	nombre_tabla,		//string con el nombre de la tabla
*					campos,									// array clave => valor con la configuracion de campos
*			   "WHERE idtabla = 2"						//tambien se pueden aplicar un filtro simple a los resultados de la tabla, escribir WHERE completo o filtro ORDER BY
*				);
*
* 	Parametro campos
* 	----------------------------------------------
* 	Recibe un array asociativo de tipo ["clave" => "valor"]
* 	(campos obligatorios: campo, tipo)
* 				[
*										[ 		"campo" 	=> 	"nombre_campo" ,    //nombre del campo
*													"tipo"		=> 	"tipo_dato" (constante) , 	//constante con el tipo del campo (ej: T_HIDDEN, input oculto)
*													"alias"		=> 	"alias" , 			//nombre a mostrar, si "" se usa nombre_tabla
*													"listar" 	=>	0 ,  	//boolean, si se muestra en el listado
*													"editar"	=>	0 ,		//boolean, si se edita en form
*													"requerido"	=>	1	,			//boolean, requerido en caso de usarse en form
*													"value"		=>	"" ,	//string o en campo select ver documentacion
*													"type"		=>	"text"	,	//son los tipo para input
*													"minlenght"	=> 20	,	//numero
*													"maxlenght"	=> 50	,	//numero
*													"placeholder" => "frase para control vacio",
*													"extraclass"	=>	""	//clases extra!
*										]
*					]
*
*		value cuando tipo_dato = tipoDato::T_SELECT
* 	-----------------------------------------------
* 	para que se muestre este control, debe recibir un array asociativo para la siguiente funcion:
*		parent::crearSelectTabla($param)
*
*    $param: es un array asociativo: "tabla"=>, "id"=> y "descripcion"=> son los parametros obligatorios, deben
*    				 coincidir con su respectivo dato en la bd.*
*    tabla: nombre de la tabla
*	 nombre_control: el select toma el name e id html con este valor, si no existe se toma el id
*    id: el valor del campo id es el value los options del select
*    descripcion: el campo descripcion de la Tabla
*    sel: id seleccionado por defecto
*    descripcion2: valor de un campo que se quiera poner como acotacion (ej: dolar [3.40] )
*    where: filtro de la $consulta
*    cssClass: tiene las clases de control bootstrap por defecto y lo tilda como requerido para validate, cambiar el valor reemplaza el default
*    prop: sirve para agregar propiedades al control para ser manipuladas desde js
*
*/


Class Crud extends Conectar {
	private $campos_array; 		//array de arrays con toda la configuracion de los campos
	private $campos_sql;   		// listado de texto de campos para armar la consulta sql
	private $tabla;						//nombre de la tabla para sql
	private $where;						//para filtrar resultados de la tabla a editar
	private $u;								//variable acumulador para guardar result consulta
	private $titulo;					//titulo de la pagina
	private $eliminar;				//si se muestra opcion eliminar o no en la tabla, boolean. por def:false
	private $requeridos;			//bool, comprobacion al principio si los campos: "campo, tipo" estan presentes

	function __construct( $tabla , $campos , $where = "" , $edit_id = 0 ){

		parent::__construct();
        $this->u=array();
        $this->campos_array = $campos;
        $this->tabla = $tabla;
		$this->where = $where;
		self::setRequeridos();
		self::listar_campos_sql();
        $this->titulo = "Listado de tabla: " . $tabla;
        $this->eliminar = false;
        $this->edit_id = $edit_id;

	}

	/**
	 * revisa si los campos obligatorios tienen parametros
	 */
	private function setRequeridos(){


		$this->requeridos = true;
			if(empty($this->tabla)) $this->requeridos = false;
			foreach ( $this->campos_array as $id => $row )
						if(  empty($row["campo"]) ||
								!isset($row["tipo"]) ) $this->requeridos = false;

			if($this->requeridos == false) write_log("setRequeridos: ", "campos requeridos incorrectos.");
	}

	private function getRequeridos(){
			if($this->requeridos)
					return true;
			else
					return false;
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
						if( $id >= 0 ){
									$form_datos .= 'formData.append("'.$row["campo"].'", $("#'.$row["campo"].'").val() );
													';
									switch ($row["campo"]) {
										case tipoDato::T_INT:
										case tipoDato::T_NUMBER:
										case tipoDato::T_DATETIME:
										case tipoDato::T_DATE:
										case tipoDato::T_TIME:
										case tipoDato::T_EMAIL:
										case tipoDato::T_PASSWORD:
										case tipoDato::T_RESET:
										case tipoDato::T_TEL:
										case tipoDato::T_MONTH:
										case tipoDato::T_RANGE:
										case tipoDato::T_COLOR:
										case tipoDato::T_SEARCH:
										case tipoDato::T_URL:
										case tipoDato::T_WEEK:
										case tipoDato::T_BUTTON:
										case tipoDato::T_TEXT:
										case tipoDato::T_STR:
										case tipoDato::T_HIDDEN:
												//se carga cada asignacion de valor json a los campos del form
												$form_response .=  '
															$("#'.$row["campo"].'").attr( "value" , data[0].'.$row["campo"].');';
												break;
										case tipoDato::T_CHECK:
												$form_response .=  '
														$("#'.$row["campo"].'").attr( "value" , data[0].'.$row["campo"].');
														if( data[0].'.$row["campo"].' > 0 )
																	$("#'.$row["campo"].'").prop( "checked" , true );
														else
																	$("#'.$row["campo"].'").prop( "checked" , false );
														';

												break;
										case tipoDato::T_SELECT:
											$form_response .= '
																				$( "#'.$row["campo"].' option:selected" ).val();';
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

                        	$.getScript("'.CRUD_PATH_JS.'validar.js"); // add script
                        	script_hola();
                        	
                        	$("body").on( "click" , "#guardar" , function(){
	                        		if( $("#form_'.$this->tabla.'").valid() == true ){
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
							formData.append( "campo_id" , "'.$this->campos_array[0]["campo"].'" ); //envio el nombre del campo_id para identificarlo
							'.$form_datos.'

							$.ajax({
								url: "'.CRUD_ROOT.CRUD_FOLDER.'ajax-crud.php/?mode=crud-edit",
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

							$("#panel_'.$this->tabla.'").modal("show");
              };

						var fnResetForm = function(){
									$(".crudControl").each(function(){
											$(this).parent().removeClass("is-valid is-invalid");

											if( $(this).is("input[type=text] , input[type=number] , input[type=hidden]") ){
													$(this).val( $(this).attr("valdefault") );
											}
											if( $(this).is("select") ){
													console.log("seleccionar opcion " + $(this).attr("valdefault"));
													$(this).val( $(this).attr("valdefault") );
											}
											if( $(this).is("input[type=checkbox]") )
													if( $(this).attr("valdefault") == 1 ){
																	this.checked = true;
																	$(this).attr( "value" , "1" );
													}else{
																	$(this).attr( "value" , "0" );
																	this.checked = false;
													}


									});
																									
									
								    $("#form_'.$this->tabla.'").resetForm();
						};

						var fnAjaxEliminarItem = function(idprod){

									var formData = new FormData();

									formData.append( "crud-del" , 1 );
									formData.append( "tabla_bd" , $("#tabla_bd").val() );
									formData.append( "campo_id" , "'.$this->campos_array[0]["campo"].'" );
									formData.append( "idprod" , idprod );

									$.ajax({
												url: "'.CRUD_ROOT.CRUD_FOLDER.'ajax-crud.php/?mode=crud-del",
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
									   url: "'.CRUD_ROOT.CRUD_FOLDER.'ajax-crud.php/?mode=crud-list",
									   data: { datos: jsonStr },
									   type: "POST",
									   success: function(response) {
									      	$("#panel_'.$this->tabla.'").modal("hide");
									      	$("#div_tabla").html(response);
									   }
									});
              };

              var fnAjaxAdd = function(){
                    	var formData = new FormData();

											formData.append( "crud-add" , 1 );
											formData.append( "tabla_bd" , $("#tabla_bd").val() );
											formData.append( "campo_id" , "'.$this->campos_array[0]["campo"].'" ); //envio el nombre del campo_id para identificarlo
											'.$form_datos.'

											$.ajax({
													url: "'.CRUD_ROOT.CRUD_FOLDER.'ajax-crud.php/?mode=crud-add",
													type: "POST",
													data: formData,
													cache: false,
													contentType: false,
													processData: false,
													success: function(data){
														//alert(data);
														fnAjaxRenderTabla();
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
							   url: "'.CRUD_ROOT.CRUD_FOLDER.'ajax-crud.php/?mode=crud-get",
							   data: { datos: jsonStr },
							   type: "POST",
								 cache: false,
							   success: function(response) {													
													$("#div_modal").html(response);
													MostrarPanel("edit");
													configura_validar();
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

		if(!self::getRequeridos()) return;
		$cant = count($this->campos_array);
		$listados =0 ;
		for( $i=0 ; $i<$cant ; $i++ )
			if( $this->campos_array[$i]["listar"] &&
					$this->campos_array[$i]["campo"] != tipoDato::T_SELECT ){
				$separador = ( $listados )? ", " : " ";
				$this->campos_sql .= $separador . $this->campos_array[$i]["campo"] ;
				$listados++;
			}

	}

	public function render(){
		if(!self::getRequeridos())
							return '<div class="row"><div class="col-md-10">
																<div class="alert alert-info" role="alert"><strong>Errores en CRUD</strong>....ver log</div>
																</div>
											</div>';
		return  '<div class="clearfix"></div>
				<div  name="tabla_div" id="tabla_div">
					<div class="card"><!-- CARD -->
						<div class="card-header">
							<h2 class="card-title">'.$this->titulo.'</h2>
							<button type="button" class="btn btn-primary" name="btnAdd" id="btnAdd" title="Agregar item" ><i class="fa fa-file"></i> Nuevo
		 					</button>
						</div>
						<div class="card-body">' .
							'<div name="div_tabla" id="div_tabla"><!-- DIV_TABLA -->
								' .	self::getTabla() .
							'</div><!-- END DIV_TABLA -->' .
					'	</div>
					</div><!-- END CARD -->' .
					'<div id="div_modal"><!-- DIV_MODAL -->' .
						    	self::getModal() .
				    '</div><!-- END DIV_MODAL -->
				</div><!-- END TABLA_DIV -->' . self::renderAjax();
	}


	public function getTabla(){

		$sql = "SELECT " . $this->campos_sql . " FROM " . $this->tabla;

		if( $this->where != "" ) $sql .= " " . $this->where;

		$this->u = parent::getRows( $sql );

		if( count($this->u) ){

			$filas = count($this->u);
			$col = 0;

			//thead
			$tabla = '<div class="content"><!-- DIV TABLE_RESPONSIVE -->
					   		<table class="table table-sm table-striped table-hover table-bordered"><thead class="thead-dark"><tr>';
			foreach ( $this->campos_array as $id => $row )
					if( $row["listar"] ){  //mostrar en listado?
						$col++;
						$clase = ( $id == 0 )? ' class="col-md-2 col-sm-2 col-xs-2" ': "" ;
						$tabla .= '<th scope="col" '.$clase.' >'.$row["alias"]."</th>" ;
					}


			$tabla .= '<th scope="col" class="col-md-2 col-sm-2 col-xs-2">Edicion</th>';//columna de control
 			$tabla .= '</tr></thead><tbody>';

 			//tbody
 			for( $i=0 ; $i < $filas ; $i++ ){
 				$tabla .= '<tr>';
 				for( $j=0 ; $j<$col ; $j++ )
 						$tabla .= '<td>'.$this->u[$i][$j].'</td>';
 				$tabla .= '<td>

 						   			<button type="button" class="btn btn-primary btn_edit" name="btnEdit" idprod="'.$this->u[$i][0].'" title="Editar item"  ><i class="fa fa-edit"></i>
 						   			</button>

 						   ';
 				$tabla .=($this->eliminar)? '<button type="button" class="btn btn-danger  btn_del" name="btnDel"  idprod="'.$this->u[$i][0].'" title="Eliminar item" ><i class="fa fa-eraser"></i>
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
