<?php
include_once('common.php');
include_once($site_path.'classes'.DIRECTORY_SEPARATOR.'class.Reply.php');
$rpl_obj = new Reply();
$return_val = $rpl_obj->fileCopy($_FILES['file']['name'], $_FILES['file']['tmp_name'], $_FILES['file']['type']);
echo json_encode(array('txt' => $return_val)); exit;
?>