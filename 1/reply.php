<?php
include_once('common.php');
$name = $_POST['name'];
$ci = (isset($_POST['ci']))? trim($_POST['ci']) : '';
$typ = (isset($_POST['all']))? trim($_POST['all']) : '';
$gcid = (isset($_POST['gcid']))? trim($_POST['gcid']) : '';
$msg = $_POST['vMessage'];
include_once($site_path.'classes'.DIRECTORY_SEPARATOR.'class.Reply.php');
$rpl_obj = new Reply();
$return_val = $rpl_obj->sendMessage($name, $ci, $msg, $typ, $gcid);
exit;
?>
