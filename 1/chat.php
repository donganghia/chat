#!/usr/bin/php -q

<?php

error_reporting(E_ALL);

require_once 'lib/Server.class.php';
require_once 'lib/Client.class.php';

set_time_limit(0);

date_default_timezone_set('Asia/Calcutta');

// variables
$address = '127.0.0.1';
$port = 11171;
$verboseMode = true;
$rootpath = '/var/www';

$server = new Server($address, $port, $verboseMode, $rootpath);
$server->run();

?>