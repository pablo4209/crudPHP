<?php
define( "SET_AJAX" , 1 );
require_once( 'config.php' );


if($_GET){

	switch ( $_GET["mode"] ) {
		case 'crud-add':
			require_once( CRUD_AJAX . "crud-add.php");
			break;
		case 'crud-edit':
			require_once( CRUD_AJAX . "crud-edit.php");
			break;
		case 'crud-list':
			require_once( CRUD_AJAX . "crud-list.php");
			break;
		case 'crud-del':
			require_once( CRUD_AJAX . "crud-del.php");
			break;
			case 'crud-get':
				require_once( CRUD_AJAX . "crud-get.php");
				break;
		default:
			# code...
			break;
	}

}

 ?>
