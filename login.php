<?php
//TOOD !!!!!!!!!!!!!! After testing remove comment on below line !!!!!!!!!!! 
//error_reporting(E_ERROR | E_PARSE);
session_start (); // Starting Session
$error = ''; // Variable To Store Error Message
if (isset ( $_POST ['submit'] )) {
	if (empty ( $_POST ['username'] ) || empty ( $_POST ['password'] )) {
		$error = "Username or Password is invalid";
	} else {
		// Define $username and $password
		$username = $_POST ['username'];
		$password = $_POST ['password'];
		try {
			// TODO Establishing Connection with Server by passing server_name, user_id and password as a parameter
			//$connection = mysql_connect ( "localhost", "root", "" );
			// To protect MySQL injection for Security purpose
			$username = stripslashes ( $username );
			$password = stripslashes ( $password );
			//TODO uncomment after establishing Connection with Server
			//$username = mysql_real_escape_string ( $username );
			//$password = mysql_real_escape_string ( $password );
			
			if($username == "abc" && $password == "abc") {
				$_SESSION ['login_user'] = $username; // Initializing Session
				header ( "Location: profile.php" ); // Redirecting To Other Page
			} else {
				$error = "Username or Password is invalid";
			}
			
			// TODO create database
 			// Selecting Database
			//$db = mysql_select_db ( "company", $connection );
			// SQL query to fetch information of registerd users and finds user match.
			//$query = mysql_query ( "select * from login where password='$password' AND username='$username'", $connection );
			//$rows = mysql_num_rows ( $query );
			//if ($rows == 1) {
			//	$_SESSION ['login_user'] = $username; // Initializing Session
			//	header ( "location: profile.php" ); // Redirecting To Other Page
			//} else {
			//	$error = "Username or Password is invalid";
			//}
			//mysql_close ( $connection ); // Closing Connection 
		} catch ( Exception $e ) {
			$error = "Caught exception: " + $e->getMessage () + "\n";
		}
	}
}
?>