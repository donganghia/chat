<?php
/**
 * common site values
*/
date_default_timezone_set('Asia/Calcutta');
// $site = $_SERVER['SERVER_NAME'];
// $site = (isset($_SERVER['HTTPS']))? 'https://'.$site : 'http://'.$site;
if(DIRECTORY_SEPARATOR == '\\') {
    $_SERVER['DOCUMENT_ROOT'] = str_replace('/', '\\', $_SERVER['DOCUMENT_ROOT']);
    $_SERVER['PHP_SELF'] = str_replace('/', '\\', $_SERVER['PHP_SELF']);
}
$site_uri = $_SERVER['SERVER_NAME'].dirname(str_replace(DIRECTORY_SEPARATOR,'/',$_SERVER['PHP_SELF'])).'/';
$site_url = (isset($_SERVER['HTTPS']))? 'https://'.$site_uri : 'http://'.$site_uri;
$site_path = rtrim($_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR).dirname($_SERVER['PHP_SELF']).DIRECTORY_SEPARATOR;
$port = '11171';
$long_polling_inerval = 20000000;   // 20 seconds
$group_prefix = 'g~';
//
?>