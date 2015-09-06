<?php
$grpcon = (isset($_POST['grpcon']))? trim($_POST['grpcon']) : '';
$grpnms = (isset($_POST['grpnms']))? trim($_POST['grpnms']) : '';
$name = (isset($_POST['name']))? trim($_POST['name']) : '';
$grpnms = preg_replace('/[^A-Za-z0-9,]/', '', $grpnms);
$grpnms = trim(trim($grpnms,','));
$return_val = 'error';
if(trim($grpnms) != '' && trim($grpcon) != '') { 	//
	include_once('common.php');
	include_once($site_path.'classes'.DIRECTORY_SEPARATOR.'class.ManageGroups.php');
	$mg_obj = new ManageGroups();
	$return_val = $mg_obj->manageUserGroups($name, $grpnms, $grpcon);
}
echo $return_val; exit;
?>
