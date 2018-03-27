<?php 

/*
	modo de iniciar la clase
	-------------------------
	
	--------------
	IMPORTANTE!
	el indice de la tabla siempre debe ser el primer campo, porque asi se asume en la clase,
	sin importar si se visualiza o no
	--------------

	$c = new Crud( 	nombre_tabla,		//string con el nombre de la tabla
					array( 
						array( 	nombre_campo ,    //nombre del campo
								tipo(constante) , 	//constante con el tipo del campo
								alias , 			//nombre a mostrar, si "" se usa nombre_tabla
								mostrar_listado ,  	//boolean, si se muestra en el listado
								editar , 			//boolean, si se edita en form				
								requerido			//boolean, requerido en caso de usarse en form 
							)
					)
				);
*/

require_once( 'config.php' );
require_once( MODEL . 'database.php' );
require_once( 'formModel.php' );


Class Crud extends Conectar{
	private $campos_array; 		//array de arrays con toda la configuracion de los campos
	private $campos_sql;   			// listado de texto de campos para armar la consulta sql	
	private $tabla;				//nombre de la tabla para sql
	private $u;					//variable acumulador para guardar result consulta
	private $titulo;			//titulo de la pagina
	private $eliminar;			//si se muestra opcion eliminar o no en la tabla, boolean. por def:false
	
	const C_NOMBRE_CAMPO = 0;	//nombre verdadero que figura en la tabla de la bd
	const C_TIPO_CAMPO = 1;	//usando la clase tipoDato, para saber de que tipo es
	const C_ALIAS = 2;		//nombre para mostrar en listado o label de los input
	const C_LISTAR = 3;		//mostrarlo en el listado
	const C_EDITAR = 4;		//el campo es editable
	const C_REQUERIDO = 5;	//validacion
	const C_TYPE = 6;	//campo de texto, para la validacion (ej: "email")
	const C_MIN = 7;	//minlenght del input (ej: 2)
    const C_MAX = 8;	//maxlenght del imput
    const C_PLACE = 9;	//placeholder del input
    const C_CLASS = 10;  //agregar alguna clase extra, para validar puede ser util

	function __construct( $tabla , $campos ){

		parent::__construct();
        $this->u=array();
        $this->campos_array = $campos;
        $this->tabla = $tabla;                
        self::listar_campos_sql();
        $this->titulo = "Listado de tabla: " . $tabla; 
        $this->eliminar = false;
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

        $form_datos = "";$form_campos ="";
        foreach ( $this->campos_array as $id => $row ) 
			if( $row[self::C_LISTAR] AND $id > 0 ){  //mostrar en listado? / el id no se incluye
						$form_datos .= 'formData.append("'.$row[self::C_NOMBRE_CAMPO].'", $("#'.$row[self::C_NOMBRE_CAMPO].'").val() );
										';		
						$form_campos .=  $row[self::C_NOMBRE_CAMPO];	
				
			}	

        //fnAjaxRenderTabla::: envia todo por JSON, un array de dos objetos, uno contiene todas las propiedades
	    //que se necesiten, el otro array envia el array de configuracion de campos del CRUD.
		//$datos[0] : contiene nombre de tabla, crud-list y cualquier otra propiedad de control que quiera usar
		//$datos[1] : contiene los campos del crud
        $script =   '
        			<script type="text/javascript">                        
                       
                      
                        $(document).ready(function(){								

                        	$.getScript("js/validate/validar.js"); // add script


                        	$("body").on( "click" , "#guardar_edit" , function(){
                        		alert("edit");
                        	});
                        	//$("#guardar_edit").on( "click" , function(){		});

							$("#btn_test").on( "click" , function(){								
								alert("test funciona!!");
							});

                        	$("#guardar_add").on( "click" , function(){
                        		
                        		if( $("#form_add").valid() == true )
                        				  		fnAjaxAdd();
                        	});													                       	

                        	$(".btn_del").on( "click" , function(){
                        		
                        		if( confirm("Queres eliminar el item: " + $(this).attr("idprod") + "?" ) )
                        						fnAjaxEliminarItem( $(this).attr("idprod") );       			
                        	});
							
                        	$("#btnEdit").on( "click" , function(){
                        		
								var formData = new FormData();	
							
								formData.append( "crud-edit" , 1 );
								formData.append( "tabla_bd" , $("#tabla_bd").val() );						
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
										//fnAjaxRenderTabla();
										alert(data);
									},
									//si ha ocurrido un error
									error: function(){
										//$("#cargando").hide();						
									} 
                        		});
                        	});
							
							$(".btn_edit").on("click" , function(){
								
								//fnResetForm();
								fnAjaxCompletarFormulario($(this).attr("idprod"));
																								
                        	});

                        	$("#btnAdd").on("click" , function(){
								fnResetForm();
								$("#panel_add").modal("show");
								
                        	});


                        });

                        
						
						var fnResetForm = function(){
							$(".validar").each(function(){								
								$(this).removeClass("has-error has-success");
								$(this).children(":input").val("");
							});
							$("#form_add").validate().resetForm();							
						}
	                        
						var fnAjaxEliminarItem = function(idprod){
							
							var formData = new FormData();	
							
							formData.append( "crud-del" , 1 );
							formData.append( "tabla_bd" , $("#tabla_bd").val() );
							formData.append( "campo_id" , "'.$this->campos_array[0][self::C_NOMBRE_CAMPO].'" );
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
									//$("#cargando").hide();						
								}
							});

						};

                        var fnAjaxRenderTabla = function(){                   	
														
							var obj = {};
							var arreglo = [];

							obj["crud-list"] = "1";
							obj["tabla_bd"] = "tbmoneda";
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
							      	$("#panel_add").modal("hide");
							      	$("#panel_edit").modal("hide");
							      	$("#div_tabla").html(response);
							   }
							});
                        };

                        var fnAjaxAdd = function(){
                        	var formData = new FormData();	
							
							formData.append( "crud-add" , 1 );							
							formData.append( "tabla_bd" , $("#tabla_bd").val() );
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
							obj["tabla_bd"] = "tbmoneda";
							obj["idprod"] = id;
							obj["setTitulo"] = "'.self::getTitulo().'";
							obj["setEliminar"] = "'.self::getEliminar().'";
							
							arreglo.push(obj);
							arreglo.push( '.json_encode( $this->campos_array , JSON_FORCE_OBJECT ).' );

							jsonStr = JSON.stringify(arreglo);
																		
							
							$.ajax({
							   url: "ajax-crud.php/?mode=crud-edit",
							   data: { datos: jsonStr },
							   type: "POST",
							   success: function(response) {							      	
							      	
							      	$("#div_edit").html(response);							      	
							      	$("#panel_edit").modal("show");			   							      	
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

	public function getEdit( $id ){
	
		$clsEdit = new Formulario( $this->tabla , $this->campos_array , $id );
		$clsEdit->setTitulo("Editar Item (" . $id . ")");		
		
		return $clsEdit->renderEdit( );

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
			if( $this->campos_array[$i][self::C_LISTAR] ){ 
				$separador = ( $listados )? ", " : " ";
				$this->campos_sql .= $separador . $this->campos_array[$i][self::C_NOMBRE_CAMPO] ; 			
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
					'<div id="div_add"><!-- DIV_ADD -->' .
						    	//self::getAdd() .
				    '</div><!-- END DIV_ADD -->
				    <div id="div_edit"><!-- DIV_EDIT -->'.
				    			self::getEdit( 0 ) .
					'</div><!-- END DIV_EDIT -->					
				</div><!-- END TABLA_DIV -->' . self::renderAjax();	
	}


	public function getTabla(){
		
		$sql = "SELECT " . $this->campos_sql . " FROM " . $this->tabla;
		
		$this->u = parent::getRows( $sql );

		if( count($this->u) ){

			$filas = count($this->u);
			$col = 0;

			//thead
			$tabla = '<div class="table-responsive"><!-- DIV TABLE_RESPONSIVE -->
					   		<table class="table table-striped table-hover table-bordered"><thead class="thead-dark"><tr>';
			foreach ( $this->campos_array as $id => $row ) 
					if( $row[self::C_LISTAR] ){  //mostrar en listado?
						$col++;
						$clase = ( $id == 0 )? ' class="col-md-2 col-sm-2 col-xs-2" ': "" ;
						$tabla .= '<th scope="col" '.$clase.' >'.$row[self::C_ALIAS]."</th>" ; 			
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