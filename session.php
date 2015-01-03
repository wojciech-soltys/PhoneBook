<?php
// Establishing Connection with Server by passing server_name, user_id and password as a parameter
$databaseAddress = 'db101.nano.pl:3306';
$databaseName = 'db4_aegee_pl';
$databaseUser = 'usr019691';
$databasePassword = 'aegee_20702';
$connection = mysql_connect($databaseAddress, $databaseUser, $databasePassword);
// Selecting Database
$db = mysql_select_db($databaseName, $connection);
session_start (); // Starting Session
                  // Storing Session
$user_check = $_SESSION ['login_user'];
if (! isset ( $user_check )) {
	mysql_close ( $connection ); // Closing Connection
	header ( 'Location: index.php' ); // Redirecting To Home Page
}
// SQL Query To Fetch Complete Information Of User
mysql_query("SET NAMES utf8");
$ses_sql = mysql_query ( "select firstName,lastName from Members,Login where username='$user_check' and member_id = id", $connection );
$row = mysql_fetch_array ( $ses_sql );
$login_session = $row ['firstName']. " ". $row ['lastName'];
if ($login_session == " ") {
	mysql_close ( $connection ); // Closing Connection
	header ( 'Location: index.php' ); // Redirecting To Home Page
}
?>