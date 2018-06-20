<?php 

require_once( 'crud/config.php');


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