<?php

$host='localhost';
$db = 'adise_test';


$user='root';
$pass='';


if(gethostname()=='users.iee.ihu.gr') {
	$mysqli = new mysqli($host, $user, $pass, $db,null,'/home/student/it/2018/it185351/mysql/run/mysql.sock');
} else {
    $mysqli = new mysqli($host, $user, $pass, $db);
}

if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . 
    $mysqli->connect_errno . ") " . $mysqli->connect_error;
}?>