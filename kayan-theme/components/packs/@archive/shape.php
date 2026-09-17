<?
global $current_user;
$obj = get_queried_object();
$path = $CurrentDir.$obj->taxonomy.'.php';
//
if( file_exists($path) ) {
	require($CurrentDir.$obj->taxonomy.'.php');
}else {
	require($CurrentDir.'default.php');
}