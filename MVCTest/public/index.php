<?php
error_reporting(E_ALL ^ E_NOTICE);
include '../../MVC/App.php';

$app = \MVC\App::getInstance();

$app->run();

$db = new \MVC\DB\SimpleDB();
$a = $db->prepare('SELECT * FROM users')->execute()->fetchAllAssoc();

print_r($a)
//$app->getDBConnection();

?>