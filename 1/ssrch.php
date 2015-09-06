<?php
$qk = (isset($_POST['qk']))? $_POST['qk'] : '';
$name = (isset($_POST['name']))? $_POST['name'] : '';
$name = preg_replace('/[^A-Za-z0-9]/', '', $name);
$name = trim($name);
$qk = preg_replace('/[^A-Za-z0-9]/', '', $qk);
$qk = trim($qk);
// $qk = trim($qk, '*');
$op = array();
if(trim($qk) != '' && $name != '') {
	include_once('common.php');
	include_once($site_path.'classes'.DIRECTORY_SEPARATOR.'class.SearchPeople.php');
	$sp_obj = new SearchPeople();
	$op = $sp_obj->findPeople($name, $qk);
}
echo json_encode($op); exit;
?>