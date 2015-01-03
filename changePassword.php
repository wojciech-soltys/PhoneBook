
<!DOCTYPE html>
<html>
<meta charset="UTF-8"> 
<head>
<title>AEGEE Gliwice Members Portal</title>
<script src="jquery-2.1.3.js"></script>
<link href="style.css" rel="stylesheet" type="text/css">
<link href="custom.css" rel="stylesheet" type="text/css">
<link rel="Shortcut icon" href="http://aegee-gliwice.org/strona/wp-content/uploads/2013/12/favicon.png" />
<style>
#site-wrapper {
  background: #fff;
}
</style>
</head>
<body>
<?php
include ('session.php');

$databaseAddress = 'db101.nano.pl:3306';
$databaseName = 'db4_aegee_pl';
$databaseUser = 'usr019691';
$databasePassword = 'aegee_20702';
$connection =  mysql_connect($databaseAddress, $databaseUser, $databasePassword);
if (!$connection) {
	echo '<script language="javascript">';
	echo 'alert("Błąd połączenia z baza danych");';
	echo '</script>';
	exit (0);
}
mysql_query("SET NAMES utf8");
if (!mysql_select_db($databaseName, $connection)) {
	echo '<script language="javascript">';
	echo 'alert("Błąd otwarcia bazy danych");';
	echo '</script>';
	exit (0);
}

$ses_sql = mysql_query("SELECT login_id FROM Members, Login WHERE username='$user_check'", $connection);
$ses_row = mysql_fetch_array($ses_sql);
$loginId = $ses_row["login_id"];

$error = '';
$previousPassword = '';
$newPassword = '';
$confirmPassword = '';
if (isset($_POST ['submit_changes'])) {
	$previousPassword = $_POST ['previousPassword'];
	$previousPassword = stripslashes($previousPassword);
	$previousPassword = mysql_real_escape_string($previousPassword);
	$previousPassword = hash('sha256', $previousPassword);
	
	$newPassword = $_POST ['newPassword'];
	$newPassword = stripslashes($newPassword);
	$newPassword = mysql_real_escape_string($newPassword);
	
	$confirmPassword = $_POST ['confirmPassword'];
	$confirmPassword = stripslashes($confirmPassword);
	$confirmPassword = mysql_real_escape_string($confirmPassword);
	
	$error = ((strlen($newPassword) < 6) ? 'Hasło nie może być krótsze niż 6 znaków. ' : '');
	$error .= (($newPassword != $confirmPassword) ? 'Podane hasła różnią się. ' : '');
	$query = "SELECT * FROM Login WHERE login_id = $loginId AND password = '$previousPassword'";
	$result= mysql_query($query, $connection);
	$rows = mysql_num_rows($result);
	$error .= (($rows != 1) ? 'Niepoprawne poprzednie hasło. ' : '');
	
	if (strlen($error) == 0) {
		$newPassword = hash('sha256', $newPassword);
		$query = "UPDATE `Login` SET password = '$newPassword' WHERE login_id = $loginId";
		$retval = mysql_query($query, $connection);
		if(! $retval )
		{
			die('Błąd podczas zapisu danych: ' . mysql_error());
		}
		echo "<script type='text/javascript'>alert('Dane zostały poprawnie zapisane');</script>";
		echo "<script>";
		echo "$( document ).ready(function() {";
		echo "goBack();";
		echo "});";
		echo "</script>";
	}
}
?>
	<div id="site-wrapper">
	<div class="wrapper" id="site-header-wrapper">
		<div id="site-header" class="wrapper-content">
			<div id="site-logo">
				<a href="javascript:window.location.href='members.php';"><img src="logo.png"></a>	
			</div>
			<div id="site-header-right">		
				<div id="intranet_login">
					<p class="login_title">Portal członków</p>
					<form class="" action="logout.php" method="post" enctype="multipart/form-data">
						<p style="display: inline-block;width: 202px;">
							Zalogowany: <?php echo $login_session; ?>
						</p>
            			<p style="display: inline-block;">
           					<input name="submit" value="Wyloguj" class="redButton" type="submit"/>
       					</p>
           			</form>
           				<p>
       					</p>
				</div>	
			</div>
		</div>
	</div>
	<div id="site-container">
		<?php 
			
		?>
		<h3 class="colour blue">Zmiana hasła</h3>
		<form action="changePassword.php" id="member" method="post" enctype="multipart/form-data">
		<div>
			<label>Login: </label><?php echo $user_check ?>
		</div>
		<div>
			<label>Poprzednie hasło: </label><input type="password" id="previousPassword" name="previousPassword" maxlenght="32" >
		</div>
		<div>
			<label>Nowe hasło: </label><input type="password" id="newPassword" name="newPassword" minlenght="6" maxlenght="32" >
		</div>
		<div>
			<label>Powtórz nowe hasło: </label><input type="password" id="confirmPassword" name="confirmPassword" minlenght="6" maxlenght="32">
		</div>
		<div>
			<label class="invalid"><?php echo $error; ?></label>
		</div>
		<br>
		<input name="submit_changes" value="Zapisz zmiany" class="red" type="submit"/>
		</form>	
		<br>
	</div>
	</div>
	<script>
		function goBack() {
			window.location = "selectedMember.php";
		}
	</script>
	<?php 
	mysql_close($connection);
	?>
</body>
</html>