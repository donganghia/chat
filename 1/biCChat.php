<?php
$cnm = (isset($_POST['ci']))? $_POST['ci'] : '';
$name = (isset($_POST['name']))? $_POST['name'] : '';
$cnm = preg_replace('/[^A-Za-z0-9]/', '', $cnm);
$cnm = trim($cnm);
$return_val = 'error';
if($cnm != '') {
	include_once('common.php');
	include_once($site_path.'classes'.DIRECTORY_SEPARATOR.'class.InitContactChat.php');
	$icc_obj = new InitContactChat();
	$return_val = $icc_obj->initCChat($name, $cnm);
}
echo $return_val; exit;
?>