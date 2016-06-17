<?php

$db_url = "localhost";
$db_port = 3306;
$db_name = "laba";
$db_login = "login";
$db_pass = "pass";

$db = new mysqli($db_url,$db_login,$db_pass,$db_name,$db_port);

// Look for errors or throw an exception
if ($db->connect_errno) {
    throw new Exception($db->connect_error, $db->connect_errno);
}

$db->set_charset("UTF8");

$public_key = "liqpay_pub";
$private_key = "liqpay_priv";
