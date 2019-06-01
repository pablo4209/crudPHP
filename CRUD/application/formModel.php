<?php
/*

__construct( $tabla , $campos , $edit_id = 0 )
                |       |           |_____ si es mayor a cero la clase utiliza el valor como id para
                |       |                    recuperar en la bd los valores de los campos
                |       |_________________ es un array que contiene todos los datos de los campos con los
                |                            que se construye el form, mismo array que crudmodel
                |_________________________ nombre de la tabla en la bd


- a cada input editable se le agrega la clase "campo_editable" para seleccionar facilmente estos campos y manipularlos


*/
class Formulario extends Conectar {

	private $u;
	protected $titulo;
	protected $html_post_titulo;
	protected $html_dentro_form;
	protected $form_nombre;
	protected $controles ;
	  protected $panel_nombre;
	  protected $campos_array;
	  protected $campos_sql;
	  protected $tabla;
	protected $where;
  protected $edit_id;  //se carga en contruct con el id del registro a recuperar, por defecto = 0, cuando no se va a recuperar ningun registro


	public function __construct( $tabla , $campos , $where = "" , $edit_id = 0 )
    {
        parent::__construct();
        $this->u=array();
        $this->titulo = "Formulario CRUD";
        $this->controles = "";
        $this->html_pre_form = "";
        $this->html_dentro_form = "";
        $this->html_post_form = "";
        $this->campos_array = $campos;
        $this->tabla = $tabla;
		$this->form_nombre = "form_".$this->tabla;
		$this->titulo = "Formulario CRUD(".$this->tabla.")";
		$this->panel_nombre = "panel_".$this->tabla;
		$this->where = $where;
        $this->edit_id = $edit_id;
        self::listar_campos_sql();
        self::listar_controles();

    }

    public function listar_valores(){
        if( $this->edit_id ){ //si es >0 hay que recuperar los calores para cada campo
            $sql = "SELECT * FROM " . $this->tabla . " WHERE " . $this->campos_array[0]["campo"] . "=?";

            $con = new Conectar();
            $dato = $con->getRowId( $sql , $this->edit_id );
            return json_encode( $dato );
        }else
            return "";

    }


    /**
		*
		*  los controles se renderizan por completo cada vez
		*/
    private function listar_controles(){



        if( $this->edit_id > 0 ){ //si es >0 hay que recuperar los valores para cada campo
            $sql = "SELECT * FROM " . $this->tabla . " WHERE " . $this->campos_array[0]["campo"] . "=?";
            $con = new Conectar();
            $dato = $con->getRowId( $sql , $this->edit_id );
						//if(CRUD_DEBUG) write_log("listar_controles::getRowId(listar_controles): ", var_export($dato, true) );
				}

        $this->controles = '<input id="tabla_bd" name="tabla_bd" type="hidden" class="form-control" value="'.$this->tabla.'" >';
        $cant = count( $this->campos_array );
        $autofoco = '';
        foreach ( $this->campos_array as $id => $row )
            	if( $id >= 0 ){  //mostrar en listado?
                        $type = tipoDato::getType($row["tipo"]); //la funcion retorna el texto indicado
                        $disabled = ( !empty($row["editar"]) && $id != 0 )? '' : ' disabled'; //nunca editar id
                        $cls_editable = '';
						$alias = ( !empty($row["alias"]) )?	$row["alias"] : $row["campo"];
						$requerido = ( !empty($row["requerido"]) )? ' required':'' ;
                        $asterisco = ( !empty($row["requerido"]) )? ' (*)' : '';
                        $minlength = ( !empty($row["minlenght"]) )? ' minlength="'.$row["minlenght"].'"' : '';
                        $maxlength = ( !empty($row["maxlenght"]) )? ' maxlength="'.$row["maxlenght"].'"' : '';
                        $extraclass = ( !empty($row["extraclass"]) )? ' '.$row["extraclass"] : '';
                        $place = ( !empty($row["placeholder"]) )? 'placeholder="'.$row["placeholder"].'"' : '';

						$valorDefault=' valDefault=""';
						$valor=' value=""';

						if( isset($dato[0][$row["campo"]]) ){
									$valor = ' value="' . $dato[0][$row["campo"]] . '" ';
						}else
								if( isset($row["value"]) )
									if( !is_array($row["value"]) )
										{ //si es array se trata del value de un select
											$valor = ' value="' . $row["value"] . '" ';
											$valorDefault = ' valDefault = "' . $row["value"]  . '" ';
										}
						//en esta parte hay que determinar que tipo de control renderizar
						switch ($row["tipo"]) {
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
										$control = '<div class="form-group validar">
                                        <label for="'.$row["campo"].'" >'
                                                     .$alias.$asterisco.'</label>
                                        <input id="'.$row["campo"]
                                         .'" name="'.$row["campo"] . '"'
                                         . $minlength
                                         . $maxlength
                                         .' type="'.$type.'" class="form-control input-medium crudControl'
                                         .$cls_editable
                                         .$extraclass
                                         .$requerido.'" '
                                         .$place
                                         . $valor
										 . $valorDefault
                                         . $disabled
                                         .' >
                                    </div>';
										break;
							case tipoDato::T_HIDDEN:
										$control = '<input type="hidden" id="'.$row["campo"]
																.'" name="'.$row["campo"] . '" '
																.$valor.'>';
										break;
							case tipoDato::T_CHECK:
										$check="";
										if(empty($row["value"])){ //si value existe ya viene cargado con el valor default correcto
											 	$valorDefault = ' valDefault = "0" ';
												$valor = ' value="0" '; //cuando value no existe o vale 0
										}else {
											 	$check = " checked"; //cuando value existe y distinto de 0
										}

										if( isset($dato[0][$row["campo"]]) ){
													$valor = ' value="'.$dato[0][$row["campo"]].'" ';
													if($dato[0][$row["campo"]]>0)
																$check = " checked ";
													else
																$check = ""; //necesario porque sino se checkea si valueDefault=1
										}


										$control = '<div class="form-group validar">
																	<div class="checkbox">
																	  <label>
																			<input type="checkbox" '.
																			$valorDefault .
																			'id="'.$row["campo"]
																			.'" class="crudControl" name="'.$row["campo"] . '" '. $check . $valor . $disabled .' > '
																			.$alias.'
																		</label>
																	</div>
																</div>
																';
										break;
							case tipoDato::T_SELECT:
										if( is_array($row["value"]) ){

													if( !isset($row["value"]["sel"]) ) //si no existe se agrega
																		$row["value"] += [ "sel" => 0 ];
													if( !isset($row["value"]["valdefault"]) ) //si no existe se agrega
																		$row["value"] += [ "prop" => ' valdefault="'.$row["value"]["sel"].'" ' ];
													if( isset($dato[0][$row["campo"]]) && $dato[0][$row["campo"]] >= 0 ) //aca se carga valor si esta seteado $dato
																		$row["value"]["sel"] = $dato[0][$row["campo"]];
													if( !isset($row["value"]["disabled"]) )
																			$row["value"] += [ "disabled" => $disabled ];
                                                    if( empty($row["value"]["requerido"]) )
                                                                            $row["value"] += [ "requerido" => $row["requerido"] ];
													$control = '
																			<div class="form-group validar" >
																					<label for="'.$row["campo"].'">'.$alias.$asterisco.'</label>
																								';
													if( !isset($row["value"]["cssClass"]) )
													$control .= parent::crearSelectTabla( $row["value"] );
													$control .= '
																			</div>';
										}
										break;
							default:
										$control = "";
										break;
						}
			$this->controles .= $control;


              }// end if( id>=0)
    }

    private function listar_campos_sql(){

        $cant = count($this->campos_array);
        $listados =0 ;
        for( $i=0 ; $i<$cant ; $i++ )
            if( $this->campos_array[$i]["listar"] ){
                $separador = ( $i!=$cant-1 AND $listados )? ", " : " ";
                $this->campos_sql .= $separador . $this->campos_array[$i]["campo"] ;
                $listados++;
            }

    }

    public function setControles( $t ){
    	$this->controles = $t;
    }

    public function setTitulo( $t ){
    	$this->titulo = $t;
    }
    public function setNombrePanel( $t ){ //tambien es el id
        $this->panel_nombre = $t;
    }
    public function setNombreForm( $t ){
    	$this->form_nombre = $t;
    }
    public function setHtmlPreForm( $t ){
    	$this->html_pre_form = $t;
    }
    public function setHtmlDentroForm( $html ){
    	$this->html_dentro_form =  $html ;
    }
    public function setHtmlPostForm( $t ){
    	$this->html_post_form = $t;
    }

		/**
		 * retorna el panel modal
		 * - listar_controles() es la funcion que renderiza los controles del form
		 * 		retornando un form modal
		 */
    public function renderModal(){


        return '<div class="modal fade" id="'.$this->panel_nombre.'" role="dialog" tabindex="-1"  aria-hidden="true"><!-- PANEL MODAL -->
                  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document"><!-- modal-dialog -->
                    <form action="" method="POST" name="'.$this->form_nombre.'" id="'.$this->form_nombre.'">
                        <div class="modal-content">
                                <div class="modal-header"><!-- PANEL HEADER -->
                                    <h4 class="modal-title" id="panel_titulo">'.$this->titulo.'</h4>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    '.$this->html_pre_form.'
                                </div><!-- END PANEL HEADER -->
                                <div class="modal-body"><!-- PANEL BODY -->
                                    ' .$this->controles
                                     .$this->html_dentro_form
                                    .'
                                </div><!-- END PANEL BODY -->
                                <div class="modal-footer"><!-- PANEL FOOTER -->
                                    '.$this->html_post_form.'
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <input type="hidden" id="modal_mode" name="modal_mode" value="add" />
                                    <input type="button" class="btn btn-success pull-right" name="guardar" id="guardar" value="Guardar" title="Guardar cambios" />
                                </div><!-- END PANEL FOOTER -->
                        </div><!-- modal-content -->
                    </form>
                  </div><!-- END modal-dialog -->
                </div><!-- END PANEL MODAL -->';
    }


}

 ?>
