<?php
$name = (isset($_GET['name']))? $_GET['name'] : '';
$q = (isset($_GET['q']))? $_GET['q'] : '';
if(is_string($name)) { $name = trim($name); } else { $name = ''; }
if(is_string($q)) { $q = trim($q); } else { $q = ''; }
$name = preg_replace('/[^A-Za-z0-9]/', '', $name);
$name = trim($name);
$q = preg_replace('/[^A-Za-z0-9:-]/', '', $q);
$q = trim($q);
$eauth = false;
if($name != '' && q != '') {
	include_once('common.php');
	include_once($site_path.'classes'.DIRECTORY_SEPARATOR.'class.History.php');
	$h_obj = new ChatHistory();
	$eauth = $h_obj->getHistory($name, $q);
}
if($eauth === false) {
?>
<form name="frm" id="frm" action="" method="post">
<input type="password" name="pass" id="pass" value="" />
<input type="submit" name="submit" id="submit" value="Submit" />
</form>
<?php
}
?>