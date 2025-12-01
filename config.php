<?php
session_start();

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'root');
define('DB_NAME', 'italian');

$tilkobling = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($tilkobling->connect_error) {
    die("<b>TILKOBLINGS FEIL: </b> " . $tilkobling->connect_error);
}
$tilkobling->set_charset("utf8");
?>