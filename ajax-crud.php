<?php 




require_once( 'crud/config.php');
require_once( MODEL . 'database.php' );

if(DEBUG){
	error_reporting(E_ALL);
	ini_set('display_errors', '1');	
}

if($_GET){

	switch ( $_GET["mode"] ) {
		case 'crud-add':
			require_once( AJAX . "crud-add.php");
			break;
		case 'crud-edit':
			require_once( AJAX . "crud-edit.php");
			break;
		case 'crud-list':
			require_once( AJAX . "crud-list.php");
			break;
		case 'crud-del':
			require_once( AJAX . "crud-del.php");
			break;
		default:
			# code...
			break;
	}

}

 ?>