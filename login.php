<?php
//TOOD !!!!!!!!!!!!!! After testing remove comment on below line !!!!!!!!!!! 
//error_reporting(E_ERROR | E_PARSE);
session_start (); // Starting Session
$error = ''; // Variable To Store Error Message
if (isset ( $_POST ['submit'] )) {
	if (empty ( $_POST ['username'] ) || empty ( $_POST ['password'] )) {
		$error = 'Pusty login lub hasło';
	} else {
		// Define $username and $password
		$username = $_POST ['username'];
		$password = $_POST ['password'];
		try {
			//Establishing Connection with Server by passing server_name, user_id and password as a parameter
			$databaseAddress = 'db101.nano.pl:3306';
			$databaseName = 'db4_aegee_pl';
			$databaseUser = 'usr019691';
			$databasePassword = 'aegee_20702';
			$connection = mysql_connect($databaseAddress, $databaseUser, $databasePassword);
			if ( !$connection ) {
				echo '<script language="javascript">';
				echo 'alert("Błąd połączenia z baza danych");';
				echo '</script>';
				exit (0);
			}
			// To protect MySQL injection for Security purpose
			$username = stripslashes ( $username );
			$password = stripslashes ( $password );
			$username = mysql_real_escape_string ( $username );
			$password = mysql_real_escape_string ( $password );
			$password = hash('sha256', $password);
 			// Selecting Database
			mysql_query("SET NAMES utf8");
			if (!mysql_select_db($databaseName, $connection)) {
				echo '<script language="javascript">';
				echo 'alert("Błąd otwarcia bazy danych");';
				echo '</script>';
				exit (0);
			}
			// SQL query to fetch information of registerd users and finds user match.
			$result = mysql_query ( "select id from Members, Login where Members.id = Login.member_id AND password='$password' AND username='$username'", $connection);
			$rows = mysql_num_rows($result);
			$row = mysql_fetch_array($result);
			if ($rows == 1) {
				$_SESSION ['login_user'] = $username; // Initializing Session
				$_SESSION ['userID'] = $row["id"];
				header ( "Location: members.php" ); // Redirecting To Other Page
			} else {
				$error = "Username or Password is invalid";
			}
			mysql_close ( $connection ); // Closing Connection
		} catch ( Exception $e ) {
			$error = "Caught exception: " + $e->getMessage () + "\n";
			mysql_close ( $connection ); // Closing Connection
		}
	}
}
?>