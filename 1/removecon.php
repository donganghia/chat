<?php
$cnm = (isset($_POST['c']))? $_POST['c'] : '';
$name = (isset($_POST['name']))? $_POST['name'] : '';
$cnm = preg_replace('/[^A-Za-z0-9]/', '', $cnm);
$cnm = trim($cnm);
$return_val = 'error';
if($cnm != '') {
	include_once('common.php');
	include_once($site_path.'classes'.DIRECTORY_SEPARATOR.'class.RemoveContacts.php');
	$rcs_obj = new RemoveContacts();
	$return_val = $rcs_obj->removeContact($name, $cnm);
}
echo $return_val; exit;
?>
