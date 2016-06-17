<?php

$db_url = "localhost";
$db_port = 3306;
$db_name = "laba4";
$db_login = "mineland";
$db_pass = "1V2s4T9v";

$db = new mysqli($db_url,$db_login,$db_pass,$db_name,$db_port);

// Look for errors or throw an exception
if ($db->connect_errno) {
    throw new Exception($db->connect_error, $db->connect_errno);
}

$db->set_charset("UTF8");

$public_key = "i93142681151";
$private_key = "uHIUiyfMT1371GZrSTtu3MB6gzDUZegNTQ7Q1Cjc";